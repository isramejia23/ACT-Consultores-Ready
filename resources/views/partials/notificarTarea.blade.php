<div class="modal fade" id="notificarModal{{ $tarea->id_tareas }}" tabindex="-1" aria-labelledby="notificarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="notificarModalLabel">
                    <i class="bi bi-whatsapp"></i> Enviar Notificación por WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tareas.notificarCliente', $tarea->id_tareas) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p><strong>Cliente:</strong> {{ $tarea->cliente->nombre_cliente }}</p>
                    <p><strong>Trabajo:</strong> {{ $tarea->nombre }}</p>

                    <div class="mb-3">
                        <label for="mensaje" class="form-label"><i class="bi bi-chat-dots"></i> Mensaje</label>
                        <textarea name="mensaje" id="mensaje" class="form-control" rows="4">
Estimado/a  {{ $tarea->cliente->nombre_cliente }}, su trabajo "{{ $tarea->nombre }}" ha sido realizado con éxito. ¡Gracias por su confianza!
                        </textarea>
                    </div>

                    @if ($tarea->archivo)
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-paperclip"></i> Archivo Adjunto</label>
                            <p>
                                <a href="{{ asset('storage/' . $tarea->archivo) }}" target="_blank" class="text-primary">
                                    <i class="bi bi-file-earmark"></i> Ver Archivo
                                </a>
                            </p>
                        </div>
                    @else
                        <p class="text-muted"><i class="bi bi-file-earmark"></i> No hay archivo adjunto</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>