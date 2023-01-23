<div class="row">
    <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-4">
        <label>Fecha registro</label>
        <input type="text" class="form-control" value="{{ $kardex->fecha_kardex }}" disabled readonly>
    </div>
    <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-4">
        <label>Entrega</label>
        <input type="text" class="form-control" value="{{ $kardex->tblmovimientodetalle->tblmovimiento->tblterceroentrega->full_name }}" disabled readonly>
    </div>
    <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-4">
        <label>Recibe</label>
        <input type="text" class="form-control" value="{{ $kardex->tblmovimientodetalle->tblmovimiento->tbltercerorecibe->full_name }}" disabled readonly>
    </div>
    <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-4">
        <label>Tipo movimiento</label>
        <input type="text" class="form-control" value="{{ $kardex->tblmovimientodetalle->tblmovimiento->tbltipomovimiento->tbldominio->nombre }}" disabled readonly>
    </div>
    <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-4">
        <label>Concepto</label>
        <input type="text" class="form-control" value="{{ $kardex->concepto }}" disabled readonly>
    </div>
    <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-4">
        <label>Movimiento</label>
        <input type="text" class="form-control" value="{{ $kardex->tblmovimientodetalle->id_movimiento }}" disabled readonly>
    </div>
    <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-4">
        <label>Producto</label>
        <input type="text" class="form-control" value="{{ $kardex->tblinventario->descripcion }}" disabled readonly>
    </div>
    <hr>
    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
        <label>Detalle movimiento</label>
        <table class="table table-striped-columns table-bordered rounded">
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <td class="text-end">{{ $kardex->cantidad }}</td>
                </tr>
                <tr>
                    <th>Valor unitario</th>
                    <td class="text-end">{{ $kardex->valor_unitario }}</td>
                </tr>
                <tr>
                    <th>Total</td>
                    <th class="text-end">{{ $kardex->valor_total }}</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
        <label>Saldo producto</label>
        <table class="table table-striped-columns table-bordered rounded">
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <td class="text-end">{{ $kardex->saldo_cantidad }}</td>
                </tr>
                <tr>
                    <th>Valor unitario</th>
                    <td class="text-end">{{ $kardex->saldo_valor_unitario }}</td>
                </tr>
                <tr>
                    <th>Total</td>
                    <th class="text-end">{{ $kardex->saldo_valor_total }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>