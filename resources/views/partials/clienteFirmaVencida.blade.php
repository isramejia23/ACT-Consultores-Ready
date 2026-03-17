@php
    $diasVencidos = now()->diffInDays($cliente->fecha_firma);
    $bgClass = $diasVencidos <= 7 ? 'list-group-item-danger' :
               ($diasVencidos <= 15 ? 'list-group-item-secondary' : '');
@endphp

<a href="#" class="list-group-item list-group-item-action {{ $bgClass }} py-2 ps-3 pe-2"
   data-bs-toggle="modal" data-bs-target="#modalNotificacionVencida{{ $cliente->id_clientes }}">
    <div class="d-flex align-items-center">
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
                <h7 class="mb-0 cliente-nombre">{{ $cliente->nombre_cliente }}</h7>
                <span class="badge bg-danger rounded-pill">{{ round($diasVencidos) }}d</span>
            </div>
            <small class="text-muted d-block">
                Venció: @formatoFecha($cliente->fecha_firma)
            </small>
        </div>
    </div>
</a>

<!-- Modal de Notificación -->
<div class="modal fade" id="modalNotificacionVencida{{ $cliente->id_clientes }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Notificar Vencimiento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('notificar.cliente') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_cliente" value="{{ $cliente->id_clientes }}">
                    
                    <div class="mb-3">
                        <p><strong>Cliente:</strong> {{ $cliente->nombre_cliente }}</p>
                        <p><strong>Fecha de vencimiento:</strong> @formatoFecha($cliente->fecha_firma) (hace {{ round($diasVencidos) }} días)</p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mensajeVencido{{ $cliente->id_clientes }}" class="form-label">Mensaje:</label>
                        <textarea class="form-control" name="mensaje" id="mensajeVencido{{ $cliente->id_clientes }}" rows="5" required>Estimado/a {{ $cliente->nombre_cliente }},

Le informamos que su Firma venció el día @formatoFecha($cliente->fecha_firma) (hace {{ round($diasVencidos) }} días).
Por favor contáctenos para renovación.</textarea>
                    </div>
                    
                    <!-- <div class="mb-3">
                        <label for="archivoVencido{{ $cliente->id_clientes }}" class="form-label">Adjuntar archivo (opcional):</label>
                        <input type="file" class="form-control" name="archivo" id="archivoVencido{{ $cliente->id_clientes }}">
                    </div> -->
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-whatsapp"></i> Enviar Notificación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
