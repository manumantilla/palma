<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use MatanYadaev\EloquentSpatial\Objects\Geometry;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;
use Throwable;

/**
 * Auditoría de coherencia entre el esquema real de PostgreSQL y los modelos Eloquent.
 *
 * Para cada modelo de app/Models verifica:
 *  - que la tabla exista;
 *  - $fillable: columnas inexistentes, columnas generadas (storedAs) y columnas editables faltantes;
 *  - $casts: casts sobre columnas inexistentes y columnas de fecha, decimal, JSON,
 *    booleano o geometría sin cast;
 *  - relaciones: que las llaves locales/foráneas existan en la tabla correcta y que
 *    la consulta de la relación se ejecute sin error SQL.
 *
 * Además lista las tablas de negocio que no tienen modelo.
 */
class ModelosEsquemaTest extends TestCase
{
    use RefreshDatabase;

    /** Modelos del framework (Jetstream) que gestionan sus columnas por su cuenta. */
    private const MODELOS_FRAMEWORK = ['User', 'Team', 'Membership', 'TeamInvitation'];

    /** Columnas que nunca deben ir en $fillable ni requieren cast explícito. */
    private const COLUMNAS_SISTEMA = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /** Tablas de infraestructura que no necesitan modelo propio. */
    private const TABLAS_SIN_MODELO = [
        'migrations', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'sessions',
        'password_reset_tokens', 'personal_access_tokens', 'team_user', 'team_invitations',
        'permissions', 'roles', 'model_has_permissions', 'model_has_roles', 'role_has_permissions',
        'spatial_ref_sys', 'geography_columns', 'geometry_columns',
    ];

    private const RELACIONES = 'belongsTo|hasMany|hasOne|belongsToMany|morphTo|morphMany|morphOne|morphToMany|morphedByMany|hasManyThrough|hasOneThrough';

    private array $errores = [];

    private array $avisos = [];

    private array $columnasCache = [];

    public function test_los_modelos_coinciden_con_el_esquema(): void
    {
        $modelos = $this->descubrirModelos();
        $tablasConModelo = [];

        foreach ($modelos as $clase) {
            $modelo = new $clase;
            $nombre = class_basename($clase);
            $tabla = $modelo->getTable();
            $tablasConModelo[] = $tabla;

            $columnas = $this->columnas($tabla);
            if ($columnas === []) {
                $this->errores[] = "[$nombre] la tabla '$tabla' no existe";
                continue;
            }

            $this->revisarFillable($nombre, $modelo, $columnas);
            $this->revisarCasts($nombre, $modelo, $columnas);
            if (! in_array($nombre, self::MODELOS_FRAMEWORK, true)) {
                $this->revisarRelaciones($nombre, $clase, $modelo);
            }
        }

        $this->revisarTablasSinModelo($tablasConModelo);

        $reporte = "\n";
        if ($this->errores) {
            $reporte .= "ERRORES (" . count($this->errores) . "):\n  - " . implode("\n  - ", $this->errores) . "\n";
        }
        if ($this->avisos) {
            $reporte .= "AVISOS (" . count($this->avisos) . "):\n  - " . implode("\n  - ", $this->avisos) . "\n";
        }
        fwrite(STDERR, $reporte);

        $this->assertSame([], $this->errores, 'Hay incoherencias entre modelos y esquema.');
        $this->assertSame([], $this->avisos, 'Hay columnas sin $fillable/$casts o tablas sin modelo.');
    }

    private function descubrirModelos(): array
    {
        $clases = [];
        foreach (glob(app_path('Models/*.php')) as $archivo) {
            $clase = 'App\\Models\\' . basename($archivo, '.php');
            if (! class_exists($clase)) {
                $this->errores[] = "[" . basename($archivo) . "] no declara la clase $clase";
                continue;
            }
            $ref = new ReflectionClass($clase);
            if ($ref->isAbstract() || ! $ref->isSubclassOf(Model::class)) {
                continue;
            }
            $clases[] = $clase;
        }

        return $clases;
    }

    /** @return array<string, object> nombre de columna => metadatos */
    private function columnas(string $tabla): array
    {
        return $this->columnasCache[$tabla] ??= collect(DB::select(
            "SELECT column_name, data_type, udt_name, is_generated
               FROM information_schema.columns
              WHERE table_schema = 'public' AND table_name = ?",
            [$tabla]
        ))->keyBy('column_name')->all();
    }

    private function revisarFillable(string $nombre, Model $modelo, array $columnas): void
    {
        $fillable = $modelo->getFillable();

        foreach ($fillable as $campo) {
            if (! isset($columnas[$campo])) {
                $this->errores[] = "[$nombre] \$fillable incluye '$campo', que no existe en '{$modelo->getTable()}'";
            } elseif ($columnas[$campo]->is_generated === 'ALWAYS') {
                $this->errores[] = "[$nombre] \$fillable incluye '$campo', que es una columna generada (storedAs)";
            }
        }

        if (in_array($nombre, self::MODELOS_FRAMEWORK, true) || $modelo->getGuarded() !== ['*']) {
            return;
        }

        foreach ($columnas as $col => $meta) {
            if (in_array($col, self::COLUMNAS_SISTEMA, true) || $meta->is_generated === 'ALWAYS') {
                continue;
            }
            if (! in_array($col, $fillable, true)) {
                $this->avisos[] = "[$nombre] la columna '$col' no está en \$fillable";
            }
        }
    }

    private function revisarCasts(string $nombre, Model $modelo, array $columnas): void
    {
        $casts = $modelo->getCasts();

        foreach ($casts as $campo => $cast) {
            if ($campo !== $modelo->getKeyName() && ! isset($columnas[$campo]) && ! method_exists($modelo, 'get' . Str::studly($campo) . 'Attribute')) {
                $this->errores[] = "[$nombre] \$casts define '$campo', que no existe en '{$modelo->getTable()}'";
            }
        }

        if (in_array($nombre, self::MODELOS_FRAMEWORK, true)) {
            return;
        }

        foreach ($columnas as $col => $meta) {
            if (in_array($col, self::COLUMNAS_SISTEMA, true)) {
                continue;
            }
            $esperado = $this->castEsperado($meta);
            if ($esperado === null) {
                continue;
            }
            $cast = $casts[$col] ?? null;
            if (! $this->castValido($esperado, $cast)) {
                $actual = $cast ? "'$cast'" : 'sin cast';
                $this->avisos[] = "[$nombre] '$col' ({$meta->data_type}) debería tener cast $esperado ($actual)";
            }
        }
    }

    private function castEsperado(object $meta): ?string
    {
        return match (true) {
            $meta->data_type === 'date' => 'date',
            str_starts_with($meta->data_type, 'timestamp') => 'datetime',
            $meta->data_type === 'numeric' => 'decimal',
            in_array($meta->data_type, ['json', 'jsonb'], true) => 'array/json',
            $meta->data_type === 'boolean' => 'boolean',
            in_array($meta->udt_name, ['geometry', 'geography'], true) => 'geometría',
            default => null,
        };
    }

    private function castValido(string $esperado, ?string $cast): bool
    {
        if ($cast === null) {
            return false;
        }
        $base = Str::before($cast, ':');

        return match ($esperado) {
            'date' => in_array($base, ['date', 'immutable_date', 'datetime', 'immutable_datetime'], true),
            'datetime' => in_array($base, ['datetime', 'immutable_datetime', 'timestamp'], true),
            'decimal' => in_array($base, ['decimal', 'float', 'double', 'real'], true),
            'array/json' => in_array($base, ['array', 'json', 'object', 'collection'], true) || class_exists($base),
            'boolean' => in_array($base, ['boolean', 'bool'], true),
            'geometría' => class_exists($base) && is_a($base, Geometry::class, true),
            default => false,
        };
    }

    private function revisarRelaciones(string $nombre, string $clase, Model $modelo): void
    {
        $ref = new ReflectionClass($clase);

        foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $metodo) {
            if ($metodo->class !== $clase || $metodo->getNumberOfRequiredParameters() > 0 || $metodo->isStatic()) {
                continue;
            }
            $cuerpo = implode('', array_slice(
                file($metodo->getFileName()),
                $metodo->getStartLine() - 1,
                $metodo->getEndLine() - $metodo->getStartLine() + 1
            ));
            if (! preg_match('/\$this->(' . self::RELACIONES . ')\s*\(/', $cuerpo)) {
                continue;
            }

            $etiqueta = "[$nombre::{$metodo->name}()]";
            try {
                $relacion = $modelo->{$metodo->name}();
            } catch (Throwable $e) {
                $this->errores[] = "$etiqueta no se pudo construir: " . $e->getMessage();
                continue;
            }
            if (! $relacion instanceof Relation) {
                $this->errores[] = "$etiqueta no devuelve una relación Eloquent";
                continue;
            }

            foreach ($this->llavesDeRelacion($relacion, $modelo) as [$tabla, $columna, $rol]) {
                if ($columna !== null && ! isset($this->columnas($tabla)[$columna])) {
                    $this->errores[] = "$etiqueta la $rol '$columna' no existe en '$tabla'";
                }
            }

            // belongsTo debe apuntar a la misma tabla que la FK real de Postgres
            if ($relacion instanceof BelongsTo && ! $relacion instanceof MorphTo) {
                $destinoFk = $this->tablaReferenciada($modelo->getTable(), $relacion->getForeignKeyName());
                $destinoModelo = $relacion->getRelated()->getTable();
                if ($destinoFk !== null && $destinoFk !== $destinoModelo) {
                    $this->errores[] = "$etiqueta apunta a '$destinoModelo', pero la FK '{$relacion->getForeignKeyName()}' referencia '$destinoFk'";
                }
            }

            if (! $relacion instanceof MorphTo) {
                try {
                    // savepoint: en Postgres un error aborta la transacción del test
                    DB::transaction(fn () => $relacion->getQuery()->toBase()->limit(1)->get());
                } catch (Throwable $e) {
                    $this->errores[] = "$etiqueta la consulta falla: " . Str::limit($e->getMessage(), 220);
                }
            }
        }
    }

    /** Tabla a la que referencia la FK de una sola columna, o null si no hay FK. */
    private function tablaReferenciada(string $tabla, string $columna): ?string
    {
        $fila = DB::selectOne(
            "SELECT ref.relname AS destino
               FROM pg_constraint c
               JOIN pg_class t   ON t.oid = c.conrelid
               JOIN pg_class ref ON ref.oid = c.confrelid
               JOIN pg_attribute a ON a.attrelid = c.conrelid AND a.attnum = c.conkey[1]
              WHERE c.contype = 'f' AND array_length(c.conkey, 1) = 1
                AND t.relname = ? AND a.attname = ?",
            [$tabla, $columna]
        );

        return $fila?->destino;
    }

    /** @return array<int, array{0:string,1:?string,2:string}> [tabla, columna, rol] */
    private function llavesDeRelacion(Relation $r, Model $padre): array
    {
        $tPadre = $padre->getTable();
        $tRel = $r->getRelated()->getTable();

        return match (true) {
            $r instanceof MorphTo => [
                [$tPadre, $r->getForeignKeyName(), 'llave polimórfica (id)'],
                [$tPadre, $r->getMorphType(), 'llave polimórfica (type)'],
            ],
            $r instanceof BelongsTo => [
                [$tPadre, $r->getForeignKeyName(), 'llave foránea'],
                [$tRel, $r->getOwnerKeyName(), 'llave del dueño'],
            ],
            $r instanceof MorphOneOrMany => [
                [$tRel, $r->getForeignKeyName(), 'llave polimórfica (id)'],
                [$tRel, $r->getMorphType(), 'llave polimórfica (type)'],
                [$tPadre, $r->getLocalKeyName(), 'llave local'],
            ],
            $r instanceof HasOneOrMany => [
                [$tRel, $r->getForeignKeyName(), 'llave foránea'],
                [$tPadre, $r->getLocalKeyName(), 'llave local'],
            ],
            $r instanceof HasManyThrough, $r instanceof HasOneThrough => [
                [$r->getParent()->getTable(), $r->getFirstKeyName(), 'llave intermedia (firstKey)'],
                [$tRel, $r->getForeignKeyName(), 'llave foránea (secondKey)'],
                [$tPadre, $r->getLocalKeyName(), 'llave local'],
                [$r->getParent()->getTable(), $r->getSecondLocalKeyName(), 'llave local intermedia'],
            ],
            $r instanceof BelongsToMany => [
                [$r->getTable(), $r->getForeignPivotKeyName(), 'llave pivote del padre'],
                [$r->getTable(), $r->getRelatedPivotKeyName(), 'llave pivote del relacionado'],
            ],
            default => [],
        };
    }

    private function revisarTablasSinModelo(array $tablasConModelo): void
    {
        $tablas = collect(DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE'"))
            ->pluck('table_name');

        foreach ($tablas->diff($tablasConModelo)->diff(self::TABLAS_SIN_MODELO)->sort() as $tabla) {
            $this->avisos[] = "la tabla '$tabla' no tiene modelo";
        }
    }
}
