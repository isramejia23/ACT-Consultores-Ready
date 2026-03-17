@php
    $diasRestantes = now()->diffInDays($cliente->fecha_facturacion);
    $bgClass = $diasRestantes <= 7 ? 'list-group-item-danger' :
              ($diasRestantes <= 15 ? 'list-group-item-secondary' : '');
    $iconClass = $diasRestantes <= 7 ? 'bi-exclamation-triangle-fill text-danger' :
               ($diasRestantes <= 15 ? 'bi-exclamation-circle-fill text-secondary' : 'bi-calendar-check text-primary');
@endphp

<a href="#" class="list-group-item list-group-item-action {{ $bgClass }} py-2 ps-3 pe-2"
   data-bs-toggle="modal" data-bs-target="#modalNotificacionFacturacion{{ $cliente->id_clientes }}">
    <div class="d-flex align-items-center">
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between">
                <h7 class="mb-0 cliente-nombre">{{ $cliente->nombre_cliente }}</h7>
                <span class="badge bg-dark rounded-pill">{{ round($diasRestantes) }}d</span>
            </div>
            <small class="text-muted d-block">
                Factura vence: @formatoFecha($cliente->fecha_facturacion)
            </small>
        </div>
    </div>
</a>

<!-- Modal de Notificación -->
<div class="modal fade" id="modalNotificacionFacturacion{{ $cliente->id_clientes }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Notificar Facturación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('notificar.cliente') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_cliente" value="{{ $cliente->id_clientes }}">
                    
                    <div class="mb-3">
                        <p><strong>Cliente:</strong> {{ $cliente->nombre_cliente }}</p>
                        <p><strong>Fecha de facturación:</strong> @formatoFecha($cliente->fecha_facturacion) (en {{ round($diasRestantes) }} días)</p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mensajeFacturacion{{ $cliente->id_clientes }}" class="form-label">Mensaje:</label>
                        <textarea class="form-control" name="mensaje" id="mensajeFacturacion{{ $cliente->id_clientes }}" rows="5" required>Estimado/a {{ $cliente->nombre_cliente }},

Le informamos que su plan de facturación electrónica vence en {{ round($diasRestantes) }} días (@formatoFecha($cliente->fecha_facturacion)).
Por favor contáctenos para evitar contratiempos.</textarea>
                    </div>
                    
                    <!-- <div class="mb-3">
                        <label for="archivoFacturacion{{ $cliente->id_clientes }}" class="form-label">Adjuntar archivo (opcional):</label>
                        <input type="file" class="form-control" name="archivo" id="archivoFacturacion{{ $cliente->id_clientes }}">
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
