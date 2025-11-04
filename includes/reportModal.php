<!-- Modal de Reporte reutilizable -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reportModalLabel">Reportar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="reportForm">
          <input type="hidden" id="reportTargetType" name="target_type" value="post">
          <input type="hidden" id="reportTargetId" name="target_id" value="">
          <div class="mb-3">
            <label for="reportReason" class="form-label">Motivo del reporte</label>
            <textarea class="form-control" id="reportReason" name="reason" rows="3" required></textarea>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">Enviar reporte</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
