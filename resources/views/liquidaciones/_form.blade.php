@php
    $create = $liquidate && isset($liquidacion->id_liquidacion) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
@endphp

@if ($liquidate)
<form action="{{ $create ? route('closeouts.store') : route('closeouts.update', $liquidacion) }}" method="POST">
    @csrf
    @if (!$create)
        @method('PATCH')
    @endif
@endif
    <div class="row">
        <input type="hidden" id="id_cotizacion" name="id_cotizacion" value="{{ $quote->id_cotizacion }}">
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
            <label>OT</label>
            <input type="text" class="form-control text-uppercase" value="{{ $quote->ot_trabajo }}" disabled>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
            <label>Cliente</label>
            <input type="text" class="form-control" value="{{ $quote->tblCliente->full_name }} {{ (isset($quote->tblCliente->tblterceroresponsable) ? ' - '.$quote->tblCliente->tblterceroresponsable->razon_social : '') }}" disabled>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
            <label>Punto interés</label>
            <input type="text" class="form-control" value="{{ $quote->tblEstacion->nombre }}" disabled>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
            <label>Tipo trabajo</label>
            <input type="text" class="form-control" value="{{ $quote->tblTipoTrabajo->nombre }}" disabled>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2 input-date">
            <label>Fecha solicitud</label>
            <input type="text" class="form-control" value="{{ $quote->fecha_solicitud }}" disabled>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
            <label>Prioridad</label>
            <input type="text" class="form-control" id="id_dominio_prioridad" value="{{ $quote->tblPrioridad->nombre }}" disabled>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
            <label>IVA %</label>
            <input type="text" class="form-control text-end" id="id_dominio_iva" value="{{ $quote->tblIva->nombre }}" disabled>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
            <label>Aprobador</label>
            <input type="text" class="form-control" value="{{ $quote->tblContratista->full_name.(isset($quote->tblContratista->razon_social) ? ' - '.$quote->tblContratista->nombres.' '.$quote->tblContratista->apellidos : '') }}" disabled>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <label>Descripción orden</label>
            <textarea class="form-control" rows="2" style="resize: none" disabled>{{ $quote->descripcion }}</textarea>
        </div>

        <div class="clearfix"><hr></div>
    </div>
    @include('partials._detalle', ['edit' => $liquidate, 'tipo_carrito' => 'liquidacion', 'detalleCarrito' => $carrito])
    @if ($liquidate)
        @include('partials.buttons', [$create, $liquidate, 'label' => $create ? 'Crear liquidación' : 'Editar liquidación'])
    @endif
@if ($liquidate)
</form>
@endif