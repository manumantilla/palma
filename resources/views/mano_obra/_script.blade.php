{{-- Estado del formulario de mano de obra (crear y editar). --}}
<script>
    function formManoObra(inicial) {
        return {
            modo: inicial.modo ?? 'labor',
            tipo: inicial.tipo ?? 'jornal_dia_completo',
            cantidad: inicial.cantidad ?? 1,
            valor: inicial.valor ?? 0,
            costo: inicial.costo ?? 0,
            costoEditado: inicial.costoEditado ?? false,
            trabajadorId: inicial.trabajadorId ?? '',
            sesionId: inicial.sesionId ?? '',
            kilos: inicial.kilos ?? null,
            pesajes: inicial.pesajes ?? 0,

            init() {
                this.$watch('cantidad', () => this.recalcular());
                this.$watch('valor', () => this.recalcular());
                this.$watch('sesionId', () => this.buscarKilos());
                this.$watch('trabajadorId', () => this.buscarKilos());
                this.$watch('modo', (m) => { if (m === 'destajo_cosecha') { this.tipo = 'destajo'; } this.buscarKilos(); });
                if (!this.costoEditado && !inicial.costo) { this.recalcular(); }
            },
            sugerido() {
                return Math.round((Number(this.cantidad) || 0) * (Number(this.valor) || 0));
            },
            ajuste() {
                return (Number(this.costo) || 0) - this.sugerido();
            },
            recalcular() {
                if (!this.costoEditado) { this.costo = this.sugerido(); }
            },
            async buscarKilos() {
                if (this.modo !== 'destajo_cosecha' || !this.sesionId || !this.trabajadorId) { this.kilos = null; return; }
                const url = new URL(@json(route('mano-obra.kilos')));
                url.searchParams.set('sesion', this.sesionId);
                url.searchParams.set('trabajador', this.trabajadorId);
                const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) { this.kilos = null; return; }
                const datos = await r.json();
                this.kilos = Number(datos.kilos);
                this.pesajes = datos.pesajes;
                this.cantidad = this.kilos;
                if (this.$refs.unidad && !this.$refs.unidad.value) { this.$refs.unidad.value = 'kg'; }
            },
        };
    }
</script>
