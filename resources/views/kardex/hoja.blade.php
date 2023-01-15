<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr class="col-12">
                <th rowspan="2" class="col-1 text-nowrap align-middle text-center rounded-start rounded-bottom-0 bg-primary text-white fs-6">#</th>
                <th rowspan="2" colspan="2" class="col-2 text-nowrap align-middle text-center bg-primary text-white fs-6">FECHA</th>
                <th colspan="2" class="col-3 text-nowrap align-middle text-center bg-primary text-white fs-6">RESPONSABLES</th>
                <th colspan="3" class="col-3 text-nowrap align-middle text-center bg-primary text-white fs-6">DETALLE</th>
                <th colspan="3" class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">ENTRADA</th>
                <th colspan="3" class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">SALIDAS</th>
                <th colspan="3" class="col-1 text-nowrap align-middle text-center bg-primary rounded-end rounded-bottom-0 text-white fs-6">SALDOS</th>
            </tr>
            <tr class="col-12">
                <td class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">ENTREGA</td>
                <td class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">RECIBE</td>
                <td colspan="2" class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">CONCEPTO</td>
                <td class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6"># MOV.</td>
                <td class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">CANTIDAD</td>
                <td class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">VR. UNITARIO</td>
                <td class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">VR. TOTAL</td>
                <td class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">CANTIDAD</td>
                <td class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">VR. UNITARIO</td>
                <td class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">VR. TOTAL</td>
                <td class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">CANTIDAD</td>
                <td class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">VR. UNITARIO</td>
                <td class="col-1 text-nowrap align-middle text-center bg-primary text-white fs-6">VR. TOTAL</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($kardex as $idx => $registroKardex)
                <tr class="col-12">
                    <td class="align-middle text-end">{{ $idx + 1 }}</td>
                    <td colspan="2" class="text-nowrap align-middle text-end">{{ $registroKardex->fecha_kardex }}</td>
                    <td class="align-middle">{{ $registroKardex->tblmovimientodetalle->tblmovimiento->tblterceroentrega->full_name }}</td>
                    <td class="align-middle">{{ $registroKardex->tblmovimientodetalle->tblmovimiento->tbltercerorecibe->full_name }}</td>
                    <td colspan="2" class="text-nowrap align-middle">{{ $registroKardex->concepto }}</td>
                    <td class="align-middle text-end">{{ $registroKardex->tblmovimientodetalle->tblmovimiento->id_movimiento }}</td>
                    <td class="align-middle text-end">
                        @if ($registroKardex->tblmovimientodetalle->tblmovimiento->tbltipomovimiento->tbldominio->id_dominio == session('id_dominio_entrada'))
                            {{ $registroKardex->cantidad  }}
                        @endif
                    </td>
                    <td class="align-middle text-end">
                        @if ($registroKardex->tblmovimientodetalle->tblmovimiento->tbltipomovimiento->tbldominio->id_dominio == session('id_dominio_entrada'))
                            {{ $registroKardex->valor_unitario }}
                        @endif
                    </td>
                    <td class="align-middle text-end">
                        @if ($registroKardex->tblmovimientodetalle->tblmovimiento->tbltipomovimiento->tbldominio->id_dominio == session('id_dominio_entrada'))
                            {{ $registroKardex->valor_total }}
                        @endif
                    </td>
                    <td class="align-middle text-end">
                        @if ($registroKardex->tblmovimientodetalle->tblmovimiento->tbltipomovimiento->tbldominio->id_dominio == session('id_dominio_salida'))
                            {{ $registroKardex->cantidad }}
                        @endif
                    </td>
                    <td class="align-middle text-end">
                        @if ($registroKardex->tblmovimientodetalle->tblmovimiento->tbltipomovimiento->tbldominio->id_dominio == session('id_dominio_salida'))
                            {{ $registroKardex->valor_unitario }}
                        @endif
                    </td>
                    <td class="align-middle text-end">
                        @if ($registroKardex->tblmovimientodetalle->tblmovimiento->tbltipomovimiento->tbldominio->id_dominio == session('id_dominio_salida'))
                            {{ $registroKardex->valor_total }}
                        @endif
                    </td>
                    <td class="align-middle text-end">{{ $registroKardex->saldo_cantidad }}</td>
                    <td class="align-middle text-end">{{ $registroKardex->saldo_valor_unitario }}</td>
                    <td class="align-middle text-end">{{ $registroKardex->saldo_valor_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>