<table border="2" style="font-family: Arial; font-size: 10px; border: 2px single black; border-collapse: collapse;">
    <tr>
        <td></td>
        <td style="width: 59px;"></td>
        <td style="width: 80px;"></td>
        <td style="width: 96px;"></td>
        <td style="width: 291px;"></td>
        <td style="width: 80px;"></td>
        <td style="width: 80px;"></td>
        <td style="width: 88px;"></td>
        <td style="width: 97px;"></td>
    </tr>
    <tr>
        <td></td>
        <td style="">
            {{-- <img src="{{ public_path('storage/images/equans.png') }}" style="height: 1.38px; width: 4.11px;"> --}}
        </td>
        <td colspan="5" style="">
            SOLUCIONES MOVILES
        </td>
        <td style=""></td>
        <td style=""></td>
    </tr>
    <tr>
        <td></td>
        <td style=""></td>
        <td colspan="5" style="">
            FORMATO LIQUIDACION
        </td>
        <td></td>
        <td style=""></td>
    </tr>
    <tr>
        <td></td>
        <td style=""></td>
        <td colspan="5" style="">
            {{ $quote[0]->tblEstacion->nombre }}
        </td>
        <td style=""></td>
        <td style=""></td>
    </tr>
    
    {!! $row !!}

    <tr>
        <td></td>
        <td colspan="2" style="{{ $bordernone.$borderleft.$borderbottom.$bordertop.$bold.$black }}">
            ESTACIÓN BASE
        </td>
        <td colspan="6" style="{{ $bordernone.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$red }}">
            {{ $quote[0]->tblEstacion->nombre }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="{{ $bordernone.$borderbottom.$borderleft.$bold.$black }}">
            TIPO DE TRABAJO
        </td>
        <td colspan="6" style="{{ $bordernone.$borderbottom.$borderright.$textcenter.$bold.$black }}">
            {{ $quote[0]->descripcion }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3" style="{{ $bordernone.$borderleft.$borderbottom.$bold.$black }}">
            ORDEN DE TRABAJO
        </td>
        <td colspan="4" style="{{ $bordernone.$borderbottom.$textcenter.$bold.$black }}">
            {{ $quote[0]->ot_trabajo }}
        </td>
        <td style="{{ $bordernone.$borderright.$borderbottom }}"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="{{ $bordernone.$borderleft.$borderbottom.$bold.$black }}">
            CONTRATISTA
        </td>
        <td colspan="6" style="{{ $bordernone.$borderbottom.$borderright.$textcenter.$bold.$black }}">
            {{ $quote[0]->tblContratista->full_name }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$borderbottom.$bold.$black }}">FECHA</td>
        <td style="{{ $bordernone.$borderbottom }}"></td>
        <td colspan="6" style="{{ $bordernone.$borderbottom.$borderright.$textcenter.$bold.$black }}">
            {{ $quote[0]->fecha_cotizacion }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$borderbottom.$bold.$black }}">REGIÓN</td>
        <td style="{{ $bordernone.$borderbottom }}"></td>
        <td colspan="6" style="{{ $bordernone.$borderbottom.$borderright.$textcenter.$bold.$black }}">
            {{ $quote[0]->tblEstacion->tbldominiozona->nombre }}
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

    {!! $row !!}

    <tr>
        <td></td>
        <td colspan="8" style="{{  $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$bgblue }}">SUMINISTRO DE MATERIALES</td>
    </tr>
    @foreach ($quote[0]->getmaterialescotizacion($quote[0]->id_cotizacion) as $detalle)
        <tr>
            <td></td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->tblListaprecio->codigo }}</td>
            <td colspan="3" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$nowrap }}">{{ $detalle->descripcion }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->unidad }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->cantidad }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$nowrap }}">{{ $detalle->valor_unitario }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$nowrap }}">{{ $detalle->valor_total }}</td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$bold }}">
            {{ $quote[0]->total_material }}
        </td>
    </tr>
    {!! $row !!}

    <tr>
        <td></td>
        <td colspan="8" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$bgblue }}">MANO DE OBRA</td>
    </tr>
    @foreach ($quote[0]->getmanoobracotizacion($quote[0]->id_cotizacion) as $detalle)
        <tr>
            <td></td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->tblListaprecio->codigo }}</td>
            <td colspan="3" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$nowrap }}">{{ $detalle->descripcion }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->unidad }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->cantidad }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$nowrap }}">{{ $detalle->valor_unitario }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$nowrap }}">{{ $detalle->valor_total }}</td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$bold }}">
            {{ $quote[0]->total_mano_obra }}
        </td>
    </tr>
    {!! $row !!}

    <tr>
        <td></td>
        <td colspan="8" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$bgblue }}">TRANSPORTE Y PEAJES</td>
    </tr>
    @foreach ($quote[0]->gettransportecotizacion($quote[0]->id_cotizacion) as $detalle)
        <tr>
            <td></td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->tblListaprecio->codigo }}</td>
            <td colspan="3" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$nowrap }}">{{ $detalle->descripcion }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->unidad }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">{{ $detalle->cantidad }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$nowrap }}">{{ $detalle->valor_unitario }}</td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$nowrap }}">{{ $detalle->valor_total }}</td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$bold }}">
            {{ $quote[0]->total_transporte }}
        </td>
    </tr>
    {!! $row !!}

    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td colspan="6" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold }}">TOTAL SIN IVA</td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$bold }}">
            {{ $quote[0]->total_sin_iva }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td colspan="6" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold }}">IVA 19%</td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$bold }}">
            {{ $quote[0]->total_iva }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$borderbottom }}"></td>
        <td colspan="6" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter.$bold.$bgblue }}">TOTAL CON IVA</td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textright.$bold }}">
            {{ $quote[0]->total_con_iva }}
        </td>
    </tr>
</table>