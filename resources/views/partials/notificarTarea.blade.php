<div class="modal fade" id="notificarModal{{ $tarea->id_tareas }}" tabindex="-1" aria-labelledby="notificarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="notificarModalLabel">
                    <i class="bi bi-whatsapp"></i> Enviar Notificación por WhatsApp
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Cliente:</strong> {{ $tarea->cliente->nombre_cliente }}</p>
                <p><strong>Trabajo:</strong> {{ $tarea->nombre }}</p>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-chat-dots"></i> Mensaje</label>
                    <textarea id="mensajeNotificar{{ $tarea->id_tareas }}" class="form-control" rows="4">Estimado/a {{ $tarea->cliente->nombre_cliente }}, su trabajo "{{ $tarea->nombre }}" ha sido realizado con éxito. ¡Gracias por su confianza!</textarea>
                </div>
                @if($tarea->archivo)
                    <p class="text-muted small"><i class="bi bi-paperclip"></i> Se enviará el archivo adjunto como PDF.</p>
                @else
                    <p class="text-muted small"><i class="bi bi-file-earmark"></i> No hay archivo adjunto.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnNotificar{{ $tarea->id_tareas }}"
                    onclick="enviarNotificacionTarea({{ $tarea->id_tareas }})">
                    <i class="bi bi-send"></i> Enviar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function enviarNotificacionTarea(tareaId) {
    var mensaje = document.getElementById('mensajeNotificar' + tareaId).value.trim();
    if (!mensaje) {
        Swal.fire({ icon: 'warning', title: 'Escribe un mensaje', showConfirmButton: false, timer: 1200 });
        return;
    }

    var btn = document.getElementById('btnNotificar' + tareaId);
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';

    fetch('/tareas/' + tareaId + '/notificar', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ mensaje: mensaje })
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        bootstrap.Modal.getInstance(document.getElementById('notificarModal' + tareaId)).hide();
        if (data.success) {
            Swal.fire({ icon: 'success', title: '¡Enviado!', text: data.message, showConfirmButton: false, timer: 2000 });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        }
    })
    .catch(function() {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo conectar con el servidor' });
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send"></i> Enviar';
    });
}
</script>
