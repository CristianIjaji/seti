@if ($create || $edit)
        <div class="modal-footer">
            <button type="button" class="btn bg-danger bg-gradient text-white" data-bs-dismiss="modal">
                <i class="fa-regular fa-circle-xmark"></i> Cerrar
            </button>
            <button id="btn-form-action" data-modal="{{ isset($modal) ? $modal : 'modalForm' }}" class="btn bg-primary bg-gradient text-white">
                <i class="fa-regular fa-circle-check"></i> {{ $label }}
            </button>
        </div>
    </form>
@else
    <div class="modal-footer">
        <button type="button" class="btn bg-danger bg-gradient text-white" data-bs-dismiss="modal">
            <i class="fa-regular fa-circle-xmark"></i> Cerrar
        </button>
    </div>
@endif