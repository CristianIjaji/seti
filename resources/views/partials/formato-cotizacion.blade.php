@php
    $rows = 13;
@endphp
<table border="2" style="font-family: Arial; font-size: 10px; border: 2px single black; border-collapse: collapse;">
    <tr>
        <td style="width: 80px;"></td>
        <td style="width: 59px;"></td>
        <td style="width: 80px;"></td>
        <td style="width: 96px;"></td>
        <td style="width: 291px;"></td>
        <td style="width: 80px;"></td>
        <td style="width: 80px;"></td>
        <td style="width: 89px;"></td>
        <td style="width: 97px;"></td>
    </tr>
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$bordertop }}"></td>
        <td colspan="5" style="{{ $bordernone.$bordertop.$textcenter.$bold.$black }}">
            SOLUCIONES MOVILES
        </td>
        <td style="{{ $bordernone.$bordertop }}"></td>
        <td style="{{ $bordernone.$bordertop.$borderright }}"></td>
    </tr>
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td colspan="5" style="{{ $textcenter.$bold.$black }}">
            FORMATO LIQUIDACION
        </td>
        <td></td>
        <td style="{{ $bordernone.$borderright }}"></td>
    </tr>
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$borderbottom }}"></td>
        <td colspan="5" style="{{ $borderbottom.$textcenter.$bold.$red }}">
            {{ $quote->tblEstacion->nombre }}
        </td>
        <td style="{{ $bordernone.$borderbottom }}"></td>
        <td style="{{ $bordernone.$borderbottom.$borderright }}"></td>
    </tr>
    
    {!! $row !!}

    <tr>
        <td></td>
        <td colspan="2" style="{{ $bordernone.$borderleft.$borderbottom.$bordertop.$bold.$black }}">
            ESTACIÓN BASE
        </td>
        <td colspan="6" style="{{ $bordernone.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$red }}">
            {{ $quote->tblEstacion->nombre }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="{{ $bordernone.$borderbottom.$borderleft.$bold.$black }}">
            TIPO DE TRABAJO
        </td>
        <td colspan="6" style="{{ $bordernone.$borderbottom.$borderright.$textcenter.$bold.$black }}">
            {{ $quote->descripcion }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3" style="{{ $bordernone.$borderleft.$borderbottom.$bold.$black }}">
            ORDEN DE TRABAJO
        </td>
        <td colspan="4" style="{{ $bordernone.$borderbottom.$textcenter.$bold.$black }}">
            {{ $quote->ot_trabajo }}
        </td>
        <td style="{{ $bordernone.$borderright.$borderbottom }}"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="{{ $bordernone.$borderleft.$borderbottom.$bold.$black }}">
            CONTRATISTA
        </td>
        <td colspan="6" style="{{ $bordernone.$borderbottom.$borderright.$textcenter.$bold.$black }}">
            {{ isset($quote->tblContratista->tblterceroresponsable) ? $quote->tblContratista->tblterceroresponsable->full_name : $quote->tblContratista->full_name }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$borderbottom.$bold.$black }}">FECHA</td>
        <td style="{{ $bordernone.$borderbottom }}"></td>
        <td colspan="6" style="{{ $bordernone.$borderbottom.$borderright.$textcenter.$bold.$black }}">
            {{ $quote->fecha_cotizacion }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$borderbottom.$bold.$black }}">REGIÓN</td>
        <td style="{{ $bordernone.$borderbottom }}"></td>
        <td colspan="6" style="{{ $bordernone.$borderbottom.$borderright.$textcenter.$bold.$black }}">
            {{ $quote->tblEstacion->tbldominiozona->nombre }}
        </td>
    </tr>

    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$bgblue }}">Ítem</td>
        <td colspan="3" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$bgblue }}">Descripción</td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$bgblue }}">Un.</td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$bgblue }}">Cant.</td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$bgblue }}">VR UNIT</td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$bgblue }}">VR TOTAL</td>
    </tr>

    @foreach ($quote->getmaterialescotizacion($quote->id_cotizacion) as $detalle)
        <tr>
            <td></td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->tblListaprecio->codigo }}</td>
            <td colspan="3" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$nowrap }} height: {{ ((strlen(trim($detalle->descripcion)) / 60) > 1 ? (strlen(trim($detalle->descripcion)) / 60) : 1) * 28 }}px;">
                {{ trim($detalle->descripcion) }}
            </td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->unidad }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->cantidad }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$nowrap }}">{{ $detalle->valor_unitario }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$nowrap }}">{{ $detalle->cantidad * $detalle->valor_unitario }}</td>
        </tr>
        @php
            $rows++;
        @endphp
    @endforeach

    @foreach ($quote->getmanoobracotizacion($quote->id_cotizacion) as $detalle)
        <tr>
            <td></td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->tblListaprecio->codigo }}</td>
            <td colspan="3" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$nowrap }} height: {{ ((strlen(trim($detalle->descripcion)) / 60) > 1 ? (strlen(trim($detalle->descripcion)) / 60) : 1) * 28 }}px;">
                {{ trim($detalle->descripcion) }}
            </td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->unidad }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->cantidad }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$nowrap }}">{{ $detalle->valor_unitario }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$nowrap }}">{{ $detalle->cantidad * $detalle->valor_unitario }}</td>
        </tr>
        @php
            $rows++;
        @endphp
    @endforeach

    @foreach ($quote->gettransportecotizacion($quote->id_cotizacion) as $detalle)
        <tr>
            <td></td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->tblListaprecio->codigo }}</td>
            <td colspan="3" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$nowrap }} height: {{ ((strlen(trim($detalle->descripcion)) / 60) > 1 ? (strlen(trim($detalle->descripcion)) / 60) : 1) * 28 }}px;">
                {{ trim($detalle->descripcion) }}
            </td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->unidad }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->cantidad }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$nowrap }}">{{ $detalle->valor_unitario }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$nowrap }}">{{ $detalle->cantidad * $detalle->valor_unitario }}</td>
        </tr>
        @php
            $rows++;
        @endphp
    @endforeach

    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td colspan="6" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold }}">TOTAL SIN IVA</td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$bold }}">
            =sum(I13:I{{ ($rows - 1) }})
        </td>
    </tr>
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td colspan="6" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold }}">{{ $quote->tblIva->nombre }}</td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$bold }}">
            =I{{ $rows }}*{{ doubleval($quote->tblIva->descripcion) / 100 }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$borderbottom }}"></td>
        <td colspan="6" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$bgblue }}">TOTAL CON IVA</td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$bold }}">
            =I{{ $rows }}+I{{ ( $rows + 1 ) }}
        </td>
    </tr>
</table>