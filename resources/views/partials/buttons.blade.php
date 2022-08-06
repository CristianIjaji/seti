@if ($create || $edit)
        <div class="modal-footer text-end">
            <button type="button" class="btn bg-danger bg-gradient text-white" data-bs-dismiss="modal">
                <i class="fa-regular fa-circle-xmark"></i> Cerrar
                {{-- <i class="fa-solid fa-ban"></i>  --}}
            </button>
            <button id="btn-form-action" data-modal="{{ isset($modal) ? $modal : 'modalForm' }}" class="btn @if($create) bg-primary @else bg-primary @endif bg-gradient text-white">
                <i class="fa-regular fa-circle-check"></i> {{ $label }}
            </button>
        </div>
    </form>
@else
    <div class="modal-footer text-end">
        <button class="btn bg-danger bg-gradient text-white" data-bs-dismiss="modal">
            <i class="fa-regular fa-circle-xmark"></i> Cerrar
        </button>
    </div>
@endif