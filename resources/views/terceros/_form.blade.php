<head>
    {{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
    <script type="text/javascript" src="https://www.tutorialrepublic.com/examples/js/typeahead/0.11.1/typeahead.bundle.js"></script>
</head>
<?php
    $create = isset($tercero->id_tercero) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
    $tipo_documento = (isset($tipo_documento) && $tipo_documento != '') ? $tipo_documento : false;
    $tipo_tercero = (isset($tipo_tercero) && $tipo_tercero != '') ? $tipo_tercero : false;
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('clients.store') : route('clients.update', $tercero) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-4">
            <label for="id_dominio_tipo_documento" class="required">Tipo documento</label>
            @if ($edit)
                @if (!$tipo_documento)
                    <select class="form-control" name="id_dominio_tipo_documento" id="id_dominio_tipo_documento" style="width: 100%" @if ($edit) required @else disabled @endif>
                        <option value="">Elegir tipo documento</option>
                        @foreach ($tipo_documentos as $id => $nombre)
                            <option value="{{ $id }}" {{ old('id_dominio_tipo_documento', $tercero->id_dominio_tipo_documento) == $id ? 'selected' : '' }}>
                                {{$nombre}}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" value="{{ $tipo_documento->nombre }}" disabled readonly>
                    <input type="hidden" name="id_dominio_tipo_documento" id="id_dominio_tipo_documento" value="{{ $tipo_documento->id_dominio }}">
                @endif
            @else
                <input type="text" class="form-control" value="{{ $tercero->tbldominiodocumento->nombre }}" disabled>
                <input type="hidden" id="id_dominio_tipo_documento" value="{{ $tercero->id_dominio_tipo_documento }}">
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="documento" class="required">Documento</label>
            <input type="text" class="form-control" @if ($edit) name="documento" @endif id="documento" value="{{ old('documento', $tercero->documento) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div id="div_dv" class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="dv">DV</label>
            <input type="text" class="form-control" @if ($edit) name="dv" @endif id="dv" value="{{ old('dv', $tercero->dv) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div id="div_razon_social" class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="razon_social">Razón social</label>
            <input type="text" class="form-control" @if ($edit) name="razon_social" @endif id="razon_social" value="{{ old('dv', $tercero->razon_social) }}" @if ($edit) required @else disabled @endif>
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="nombres" class="required">Nombres</label>
            <input type="text" class="form-control" @if ($edit) name="nombres" @endif id="nombres" value="{{ old('nombres', $tercero->nombres) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="apellidos" class="required">Apellidos</label>
            <input type="text" class="form-control" @if ($edit) name="apellidos" @endif id="apellidos" value="{{ old('apellidos', $tercero->apellidos) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="ciudad" class="required">Ciudad</label>
            <input type="text" class="form-control" list="list-ciudades" @if ($edit) name="ciudad" @endif id="ciudad" value="{{ old('ciudad', $tercero->ciudad) }}" @if ($edit) required @else disabled @endif>
            @if ($edit)
                <datalist id="list-ciudades">
                    @foreach ($ciudades as $ciudad)
                        <option value="{{ $ciudad }}">{{ $ciudad }}</option>
                    @endforeach
                </datalist>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="direccion" class="required">Dirección</label>
            <input type="text" class="form-control" @if ($edit) name="direccion" @endif id="direccion" value="{{ old('direccion', $tercero->direccion) }}" @if ($edit) required @else disabled @endif>
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="correo" class="required">Correo</label>
            <input type="email" class="form-control" @if ($edit) name="correo" @endif id="correo" value="{{ old('correo', $tercero->correo) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="telefono" class="required">Teléfono / Celular</label>
            <input type="tel" class="form-control" @if ($edit) name="telefono" @endif id="telefono" value="{{ old('telefono', $tercero->telefono) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 {{ $tipo_tercero ? 'd-none' : '' }}">
            <label for="id_dominio_tipo_tercero" class="required">Tipo tercero</label>
            @if ($edit)
                @if (!$tipo_tercero)
                    <select class="form-control" name="id_dominio_tipo_tercero" id="id_dominio_tipo_tercero" style="width: 100%" @if ($edit) required @else disabled @endif>
                        <option value="">Elegir tipo tercero</option>
                        @foreach ($tipo_terceros as $id => $nombre)
                            <option value="{{ $id }}" {{ old('id_dominio_tipo_tercero', $tercero->id_dominio_tipo_tercero) == $id ? 'selected' : '' }}>
                                {{$nombre}}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" value="{{ $tipo_tercero->nombre }}" disabled readonly>
                    <input type="hidden" name="id_dominio_tipo_tercero" id="id_dominio_tipo_tercero" value="{{ $tipo_tercero->id_dominio }}">
                @endif
            @else
                <input type="text" class="form-control" value="{{ $tercero->tbldominiotercero->nombre }}" disabled>
                <input type="hidden" id="id_dominio_tipo_tercero" value="{{ $tercero->id_dominio_tipo_tercero }}">
            @endif
        </div>
        <div id="div_dependencia" class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="id_responsable_cliente" class="required">Dependencia</label>
            @if ($edit)
                <select class="form-control" name="id_responsable_cliente" id="id_responsable_cliente" data-minimuminputlength="3" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir dependencia</option>
                    @foreach ($terceros as $dependencia)
                        <option value="{{ $dependencia->id_tercero }}" {{ old('id_responsable_cliente', $tercero->id_responsable_cliente) == $dependencia->id_tercero ? 'selected' : '' }}>
                            {{$dependencia->full_name}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="id_responsable_cliente" value="{{ isset($tercero->tblterceroresponsable->full_name) ? $tercero->tblterceroresponsable->full_name : 'Sin dependencia' }}" disabled>
            @endif
        </div>
        
        @if(!$create)
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
                <label for="estado" class="required">Estado</label>
                @if ($edit)
                    <select class="form-control" name="estado" id="estado" style="width: 100%" @if ($edit) required @else disabled @endif>
                        @foreach ($estados as $id => $name)
                            <option value="{{ $id }}" {{ old('estado', $tercero->estado_form) == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="estado" value="{{ $tercero->estado_form = 1 ? 'Activo' : 'Inactivo' }}" disabled>
                @endif
            </div>
        @endif
        <div id="div_logo" class="form-group col-12 col-sm-12 col-md-6 col-lg-4 {{ in_array($tercero->id_dominio_tipo_tercero, [session('id_dominio_cliente'), session('id_dominio_proveedor')]) ? '' : 'd-none' }} ">
            <label for="logo" class="required col-12">Logo cotización</label>
            {{-- @if ($edit) --}}
                <div class="file-loading">
                    <input id="logo" name="logo" type="file" class="file" data-allowed-file-extensions='["img", "jpg", "jpeg", "png"]' accept=".jpg, .jpeg, .png">
                </div>
            {{-- @else
                @if ($tercero->logo != '')
                    <img src="/storage/{{ $tercero->logo }}" class="img-thumbnail" alt="Logo cotizaciones">
                @endif
            @endif --}}
        </div>
        @if(!$create && !$edit)
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
                <label for="creado_por">Creado por</label>
                <input type="text" id="creado_por" class="form-control" disabled value="{{ $tercero->tblusuario->usuario }}">
            </div>
        
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
                <label for="fecha_creacion">Fecha creación</label>
                <input type="text" id="fecha_creacion" class="form-control" disabled value="{{ $tercero->created_at }}">
            </div>
        @endif
    </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear tercero' : 'Editar tercero'])

    <style>
        .file-preview{
            padding: 0px !important;
        }
        .file-drop-zone-title {
            padding: 0px !important;
            margin: 30px 0px;
        }
        .file-drop-zone {
            margin: 0px !important;
            padding: 0px !important;
            min-height: 100px !important;
        }
    </style>
    <script type="application/javascript">
        $('#id_dominio_tipo_documento').change(function() {
            $('#div_dv, #div_razon_social').addClass('d-none');
            let valor = parseInt($(this).val());
            let nit = {!! session('id_dominio_nit') !!};

            if($.inArray(valor, [nit]) > -1) {
                $('#div_dv, #div_razon_social').removeClass('d-none');
            }
        });

        $('#id_dominio_tipo_tercero').change(function() {
            $('#div_logo, #div_dependencia').addClass('d-none');
            let valor = parseInt($(this).val());
            let cliente = parseInt({!! session('id_dominio_cliente') !!});
            let representante_cliente = parseInt({!! session('id_dominio_representante_cliente') !!});
            let coordinador = parseInt({!! session('id_dominio_coordinador') !!});
            let proveedor = parseInt({!! session('id_dominio_contratista') !!});

            if($.inArray(valor, [cliente, proveedor]) > -1) {
                $('#div_logo').removeClass('d-none');
            }

            if($.inArray(valor, [representante_cliente, coordinador]) > -1) {
                $('#div_dependencia').removeClass('d-none');
            }
        });

        if($('#id_dominio_tipo_documento').length) {
            $('#id_dominio_tipo_documento').change();
        }

        if($('#id_dominio_tipo_tercero').length) {
            $('#id_dominio_tipo_tercero').change();
        }

        var previewImage = null;
    
        if("{!! $tercero->logo !!}" !== '') {
            previewImage = "storage/{!! $tercero->logo !!}"
        }
    
        var show = (!"{!! $create !!}" && !"{!! $edit !!}") ? false : true;
    
        $("#logo").fileinput({
            language: 'es',
            theme: "explorer",
            showCaption: show,
            showBrowse: show,
            showRemove: false,
            showUpload: false,
            showCancel: false,
            showClose: false,
            showDescriptionClose: false,
            // allowedPreviewTypes : [ 'image' ],
            allowedFileExtensions : ['jpg', 'jpeg', 'png'],
            initialPreviewShowDelete: false,
            fileActionSettings: {
                showRemove: false,
                showDrag: false
            },
            initialPreview: [
                previewImage
            ],
            initialPreviewAsData: true,
            initialPreviewConfig: {}
        });
    </script>