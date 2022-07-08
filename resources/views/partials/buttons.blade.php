@if ($create || $edit)
        <div class="modal-footer text-end">
            <button type="button" class="btn bg-danger bg-gradient text-white" data-bs-dismiss="modal">
                <i class="fa-solid fa-ban"></i> Cancelar
            </button>
            <button id="btn-form-action" class="btn @if($create) bg-primary @else bg-primary @endif bg-gradient text-white">
                <i class="fa-regular fa-circle-check"></i> {{ $label }}
            </button>
        </div>
    </form>
@else
    <div class="modal-footer text-end">
        <button class="btn bg-danger bg-gradient text-white" data-bs-dismiss="modal">
            Cerrar
        </button>
    </div>
@endif