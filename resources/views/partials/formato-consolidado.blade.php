<table>
    <tr>
        <td style="text-align: center; font-weight: bold;" colspan="4">PROYECTO</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td style="text-align: center; font-weight: bold;">MES</td>
        <td style="text-align: center; font-weight: bold;" colspan="2">{{ mb_strtoupper($deal[0]->nombre_mes) }}</td>
        <td></td>
        <td style="font-weight: bold;">{{ (isset($deal[0]->tblresponsablecliente->tblterceroresponsable)
            ? $deal[0]->tblresponsablecliente->tblterceroresponsable->full_name
            : $deal[0]->tblresponsablecliente->full_name
            ) }}
        </td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <th style="text-align: center; font-weight: bold;">ITEM</th>
        <th style="text-align: center; font-weight: bold;">ZONA</th>
        <th style="text-align: center; font-weight: bold;">OT</th>
        <th style="text-align: center; font-weight: bold;">ESTACIÓN</th>
        <th style="text-align: center; font-weight: bold;">FECHA DE EJECUCIÓN</th>
        <th style="text-align: center; font-weight: bold;">ACTIVIDAD</th>
        <th style="text-align: center; font-weight: bold;">VALOR COTIZADO</th>
        <th style="text-align: center; font-weight: bold;">ASIGNA</th>
        <th style="text-align: center; font-weight: bold;">OBSERVACIONES</th>
    </tr>
    @foreach ($deal[0]->tblconsolidadodetalle as $detalle)
        <tr>
            <td style="text-align: center;">{{ $item++ }}</td>
            <td style="text-align: center;">{{ $detalle->tblactividad->tblestacion->tbldominiozona->nombre }}</td>
            <td>{{ $detalle->tblactividad->ot }}</td>
            <td>{{ $detalle->tblactividad->tblestacion->nombre }}</td>
            <td>{{ $detalle->tblactividad->fecha_ejecucion }}</td>
            <td style="text-align: center;">{{ $detalle->tblactividad->descripcion }}</td>
            <td style="text-align: right;">{{ $detalle->tblactividad->valor }}</td>
            <td style="text-align: center;">{{ $deal[0]->tblresponsablecliente->full_name }}</td>
            <td>{{ $detalle->observacion }}</td>
            @php
                $total += $detalle->tblactividad->valor;
            @endphp
        </tr>
    @endforeach
    <tr>
        <td colspan="5"></td>
        <td style="text-center; font-weight: bold;">TOTALES</td>
        <td style="text-center; font-weight: bold;">{{ $total }}</td>
    </tr>
</table>