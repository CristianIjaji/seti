@php
    $create = isset($consolidado->id_consolidado) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
@endphp

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('deals.store') : route('deals.update', $consolidado) }}">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        <input type="hidden" id="id_consolidado" value="{{ $consolidado->id_consolidado }}">

        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-5">
            <label for="id_tercero_cliente" class="required">Cliente</label>
            @if ($edit)
                <select name="id_tercero_cliente" id="id_tercero_cliente" class="form-control" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir cliente</option>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id_tercero }}" {{ old('id_tercero_cliente', $consolidado->id_tercero_cliente) == $cliente->id_tercero ? 'selected' : '' }}>
                            {{ $cliente->full_name }} {{ (isset($cliente->tblterceroresponsable) ? ' - '.$cliente->tblterceroresponsable->razon_social : '' ) }}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" value="{{ $consolidado->tblCliente->full_name }} {{ (isset($consolidado->tblCliente->tblterceroresponsable) ? ' - '.$consolidado->tblCliente->tblterceroresponsable->razon_social : '') }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-5">
            <label for="id_tercero_responsable" class="required">Encargado cliente</label>
            @if ($edit)
                <select name="id_tercero_responsable" id="id_tercero_responsable" class="form-control" style="width: 100%" @if ($edit) required @else disabled @endif>
                    @forelse ($contratistas as $contratista)
                        <option
                            value="{{ $contratista->id_tercero }}" {{ old('id_responsable', $consolidado->id_tercero_responsable) == $contratista->id_tercero ? 'selected' : '' }}>
                            {{ $contratista->full_name }} {{ (isset($contratista->tblterceroresponsable) ? ' - '.$contratista->tblterceroresponsable->razon_social : '' ) }}
                        </option>
                    @empty
                        <option value="">Elegir contratista</option>
                    @endforelse
                </select>
            @else
                <input type="text" class="form-control" value="{{ $consolidado->tblresponsablecliente->full_name }} {{ (isset($consolidado->tblresponsablecliente->tblterceroresponsable) ? ' - '.$consolidado->tblresponsablecliente->tblterceroresponsable->razon_social : '') }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2 input-months">
            <label for="mes">Mes</label>
            <input type="text" class="form-control text-capitalize" @if ($edit) name="mes" @endif id="mes" value="{{ old('mes', $consolidado->mes_form) }}" data-format="YYYY-MMMM" data-viewMode="months"  @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 text-center">
            @if ($edit)
                <button id="btn-get-activities" class="btn bg-info bg-gradient text-white">
                    <i class="fa-solid fa-magnifying-glass"></i> Consultar actividades
                </button>
            @endif

            @if (!$create)
                <button id="btn_download_consolidado" type="button" class="btn btn-outline-success " data-consolidado="{{ $consolidado->id_consolidado }}">
                    <i class="fa-solid fa-file-excel fs-4"></i> Descargar consolidado
                </button>
            @endif
        </div>
        <div class="clearfix"></div>
        <hr>
        <div id="div_detalle_consolidado" class="table-responsive">
            @include('consolidados.detalle', ['model' => $detalle_consolidado, 'edit' => $edit])
        </div>
    </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear consolidado' : 'Editar consolidado'])

<script type="application/javascript">
    datePicker();
</script>