<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Exception;

class Bitacora extends Model
{
    use HasFactory;

    protected $table = 'bitacoras';

    protected $fillable = [
        'bitacorable_type',
        'bitacorable_id',
        'tipo',
        'prioridad',
        'titulo',
        'contenido',
        'estado',
        'archivo_adjunto',
        'user_id',
    ];

    protected $casts = [
        'tipo' => 'string',
        'prioridad' => 'string',
        'estado' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function bitacorable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Métodos con try-catch para operaciones seguras
    public static function crearBitacora(array $data): array
    {
        try {
            $bitacora = self::create($data);
            return ['success' => true, 'bitacora' => $bitacora, 'message' => 'Bitácora creada correctamente.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al crear la bitácora: ' . $e->getMessage()];
        }
    }

    public function actualizarBitacora(array $data): array
    {
        try {
            $this->update($data);
            return ['success' => true, 'bitacora' => $this, 'message' => 'Bitácora actualizada correctamente.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al actualizar la bitácora: ' . $e->getMessage()];
        }
    }

    public function eliminarBitacora(): array
    {
        try {
            $this->delete();
            return ['success' => true, 'message' => 'Bitácora eliminada correctamente.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al eliminar la bitácora: ' . $e->getMessage()];
        }
    }
}