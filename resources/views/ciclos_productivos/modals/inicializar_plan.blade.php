<div class="modal fade" id="modalInicializarPlan" tabindex="-1" aria-labelledby="modalInicializarPlanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalInicializarPlanLabel">
                    ⚡ Inicializar Plan Fenológico
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                </button>
            </div>

            <form action="{{ route('ciclo-etapas.inicializar') }}" method="POST">

                @csrf

                <div class="modal-body">

                    <div class="alert alert-info">
                        <strong>{{ $ciclo->cultivo->nombre ?? 'Cultivo' }}</strong>
                        <br>
                        Se generará automáticamente el plan fenológico
                        utilizando las etapas configuradas para este cultivo.
                    </div>

                    <div class="mb-3">
                        <label for="fecha_inicio_ciclo" class="form-label fw-bold">
                            Fecha de inicio del ciclo
                        </label>

                        <input
                            type="date"
                            name="fecha_inicio_ciclo"
                            id="fecha_inicio_ciclo"
                            class="form-control"
                            value="{{ old('fecha_inicio_ciclo', $ciclo->fecha_inicio ?? now()->format('Y-m-d')) }}"
                            required
                        >

                        <div class="form-text">
                            Esta fecha será utilizada como referencia para calcular
                            las fechas estimadas de cada etapa.
                        </div>
                    </div>

                    <input
                        type="hidden"
                        name="ciclo_productivo_id"
                        value="{{ $ciclo->id }}"
                    >

                    <div class="alert alert-warning mb-0">
                        <strong>Importante:</strong>
                        una vez inicializado el plan, las etapas serán creadas
                        automáticamente según la plantilla fenológica del cultivo.
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        ⚡ Inicializar Plan
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>