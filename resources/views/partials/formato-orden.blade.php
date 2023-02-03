@php
    $rows = 15;
@endphp
<table border="2" style="font-family: Arial; font-size: 10px; border: 2px single black; border-collapse: collapse;">
    <tr>
        <td style="width: 80px; height: 19px;"></td>
        <td style="width: 80px;"></td>
        <td style="width: 107px;"></td>
        <td style="width: 80px;"></td>
        <td style="width: 174px;"></td>
        <td style="width: 134px;"></td>
        <td style="width: 108px;"></td>
        <td style="width: 98px;"></td>
        <td style="width: 83px;"></td>
    </tr>
    <tr>
        <td style="height: 19px;"></td>
        <td colspan="8" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom }}"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="8" rowspan="6" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom }}"></td>
    </tr>
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr>
        <td style="height: 19px;"></td>
        <td colspan="8" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom }}"></td>
    </tr>

    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td colspan="2" style="{{ $textcenter.$bold.$black }}">ORDE DE COMPRA No.{{ str_pad($orden->id_orden_compra, 4, '0', STR_PAD_LEFT) }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="{{ $bordernone.$borderright }}"></td>
    </tr>

    {!! $row !!}

    <tr>
        <td style="height: 25px"></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderbottom }}">Señores:</td>
        <td colspan="2" style="{{ $bordernone.$bordertop.$borderbottom.$bold }}">{{ $orden->tblproveedor->full_name }}</td>
        <td style="{{ $bordernone.$bordertop.$borderbottom }}">NIT.:</td>
        <td colspan="2" style="{{ $bordernone.$bordertop.$borderright.$borderbottom.$bold }}">{{ $orden->tblproveedor->documento.'-'.$orden->tblproveedor->dv }}</td>
        <td style="{{ $bordernone.$borderright }}"></td>
    </tr>

    <tr>
        <td style="height: 25px"></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderbottom }}">Atn.:</td>
        <td colspan="2" style="{{ $bordernone.$bordertop.$borderbottom.$bold }}">{{ strtoupper($orden->tblasesor->nombres.' '.$orden->tblasesor->apellidos) }}</td>
        <td style="{{ $bordernone.$bordertop.$borderbottom }}">Fecha de solicitud:</td>
        <td colspan="2" style="{{ $bordernone.$bordertop.$borderright.$borderbottom.$bold }}">{{ date('d/m/Y', strtotime($orden->created_at)) }}</td>
        <td style="{{ $bordernone.$borderright }}"></td>
    </tr>

    <tr>
        <td style="height: 45px;"></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td colspan="7" style="{{ $bordernone.$borderright }}">
            Se solicita sea despachado los siguientes elementos, según cotización elaborada previamente:
        </td>
    </tr>

    <tr>
        <td style="height: 41px;"></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">
            CANTIDAD
        </td>
        <td colspan="3" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">
            DESCRIPCION O ITEM
        </td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">
            VALOR<br>UNITARIO
        </td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">
            VALOR<br>TOTAL
        </td>
        <td style="{{ $bordernone.$borderright }}"></td>
    </tr>

    @foreach ($orden->tbldetalleorden as $detalle)
        <tr>
            <td style="height: 27px;"></td>
            <td style="{{ $bordernone.$borderleft }}"></td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">
                {{ $detalle->cantidad }}
            </td>
            <td colspan="3" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom }}">
                {{ $detalle->tblinventario->descripcion }}
            </td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom }}">
                {{ $detalle->valor_unitario }}
            </td>
            <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom }}">
                {{ $detalle->valor_total }}
            </td>
            <td style="{{ $bordernone.$borderright }}"></td>
        </tr>
    @endforeach

    @php
        $rows += count($orden->tbldetalleorden);
    @endphp

    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td colspan="5"></td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom }}">
            =sum(H16:H{{ $rows }})
        </td>
        <td style="{{ $bordernone.$borderright }}"></td>
    </tr>

    {!! $row !!}

    <tr>
        <td style="height: 32px"></td>
        <td colspan="2" style="{{ $bordernone.$borderleft.$borderright }}"></td>
        <td colspan="4" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">
            VALOR SUBTOTAL
        </td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom }}">
            =H{{ ($rows + 1) }}
        </td>
        <td style="{{ $bordernone.$borderright }}"></td>
    </tr>

    <tr>
        <td style="height: 32px"></td>
        <td colspan="2" style="{{ $bordernone.$borderleft.$borderright }}"></td>
        <td colspan="4" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">
            VALOR IVA {{ $orden->iva }}
        </td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom }}">
            =H{{ ($rows + 3) }}*{{ doubleval(isset($orden->tblIva) ? $orden->tblIva->descripcion : 0) / 100 }}
        </td>
        <td style="{{ $bordernone.$borderright }}"></td>
    </tr>

    <tr>
        <td style="height: 32px"></td>
        <td colspan="2" style="{{ $bordernone.$borderleft.$borderright }}"></td>
        <td colspan="4" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom.$textcenter }}">
            VALOR TOTAL
        </td>
        <td style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom }}">
            =H{{ ($rows + 3) }}+H{{ ($rows + 4) }}
        </td>
        <td style="{{ $bordernone.$borderright }}"></td>
    </tr>

    {!! $row !!}

    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td style="{{ $bordernone }}">Despacharlo a:</td>
        <td colspan="6" style="{{ $bordernone.$borderright }}"></td>
    </tr>

    <tr>
        <td style="height: 17px"></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td colspan="6" rowspan="3">
            {{ $orden->descripcion }}
        </td>
        <td style="{{ $bordernone.$borderright }}"></td>
    </tr>

    <tr>
        <td style="height: 17px"></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td style="{{ $bordernone.$borderright }}"></td>
    </tr>

    <tr>
        <td style="height: 17px"></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td style="{{ $bordernone.$borderright }}"></td>
    </tr>

    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft }}"></td>
        <td>Cordialmente,</td>
        <td colspan="6" style="{{ $bordernone.$borderright }}"></td>
    </tr>

    {!! $row !!}

    <tr>
        <td></td>
        <td style="{{ $bordernone.$borderleft.$borderbottom }}"></td>
        <td colspan="4" style="{{ $bordernone.$borderbottom.$bold }}; height: 96px;">
            YEISON JAVIER QUINTERO MUÑOZ<br>
            Representante Legal<br>
            SERVICIOS ELECTRICOS TECNICOS INGENIERIA SETI LTDA.<br>
            Nit: 830.045.439-4
        </td>
        <td colspan="3" style="{{ $bordernone.$borderright.$borderbottom }}"></td>
    </tr>

    <tr>
        <td style="height: 64px;"></td>
        <td colspan="8" rowspan="3" style="{{ $bordernone.$borderleft.$bordertop.$borderright.$borderbottom }}"></td>
    </tr>

    <tr><td style="height: 43px;"></td></tr>
    <tr><td style="height: 49px;"></td></tr>
</table>