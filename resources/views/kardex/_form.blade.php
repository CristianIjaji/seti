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
    <hr>
    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
        <label>Detalle producto</label>
        <table class="table table-bordered rounded">
            <thead>
                <tr>
                    <th class="w-25">Producto</th>
                    <td colspan="4">{{ $kardex->tblinventario->descripcion }}</td>
                </tr>
                <tr>
                    <th>Cantidad</th>
                    <td class="text-end">{{ $kardex->cantidad }}</td>
                </tr>
                <tr>
                    <th>Valor unitario</th>
                    <td class="text-end">{{ $kardex->valor_unitario }}</td>
                </tr>
                <tr>
                    <th>IVA</th>
                    <td class="text-end">{{ $kardex->iva }}</td>
                </tr>
                <tr>
                    <th>Subtotal</td>
                    <td class="text-end">{{ $kardex->valor_total }}</td>
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
        <table class="table table-bordered rounded">
            <thead>
                <tr>
                    <th class="w-25">Producto</th>
                    <td colspan="4">{{ $kardex->tblinventario->descripcion }}</td>
                </tr>
                <tr>
                    <th>Cantidad</th>
                    <td class="text-end">{{ $kardex->saldo_cantidad }}</td>
                </tr>
                <tr>
                    <th>Valor unitario</th>
                    <td class="text-end">{{ $kardex->saldo_valor_unitario }}</td>
                </tr>
                {{-- <tr>
                    <th>IVA</th>
                    <td class="text-end">{{ $kardex->iva }}</td>
                </tr>
                <tr>
                    <th>Subtotal</td>
                    <td class="text-end">{{ $kardex->valor_total }}</td>
                </tr>
                <tr>
                    <th>Total</td>
                    <td class="text-end">{{ $kardex->valor_total }}</td>
                </tr> --}}
            </thead>
        </table>
    </div>
</div>