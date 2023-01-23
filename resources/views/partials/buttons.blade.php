<div class="modal-footer justify-content-lg-end justify-content-center">
    @if ($create || $edit)
        <button type="button" class="btn bg-danger bg-gradient text-white" data-bs-dismiss="modal">
            <i class="fa-regular fa-circle-xmark"></i> Cerrar
        </button>
        <button id="btn-form-action" class="btn bg-primary bg-gradient text-white">
            <i class="fa-regular fa-circle-check"></i> {{ $label }}
        </button>
    @else
        <button type="button" class="btn bg-danger bg-gradient text-white" data-bs-dismiss="modal">
            <i class="fa-regular fa-circle-xmark"></i> Cerrar
        </button>
    @endif
</div>