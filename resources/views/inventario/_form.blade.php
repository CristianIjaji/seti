@php
    $create = !isset($inventario->id_inventario);
    $edit = isset($edit) ? $edit : $create;
@endphp

@if (!$create)
    <ul class="nav nav-tabs" id="storeTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="store-tab" data-bs-toggle="tab" data-bs-target="#store" type="button" role="tab" aria-controls="store" aria-selected="true">Producto</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="track-tab-kardex" data-bs-toggle="tab" data-bs-target="#track-kardex" type="button" role="tab" aria-controls="track-kardex" aria-selected="true">Kardex</button>
        </li>
    </ul>

    <div class="tab-content pt-3" id="storeTab">
        <div class="tab-pane fade show active" id="store" role="tabpanel" aria-labelledby="store-tab">
@endif

    @if ($create || $edit)
        <div class="alert alert-success" role="alert"></div>
        <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

        <form action="{{ $create ? route('stores.store') : route('stores.update', $inventario) }}" method="POST">
            @csrf
            @if (!$create)
                @method('PATCH')
            @endif
    @endif
        <div class="row">
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label for="id_tercero_almacen" class="required">Almacén</label>
                @if ($create)
                    <select class="form-control" name="id_tercero_almacen" id="id_tercero_almacen" style="width: 100%" @if ($edit) required @else disabled @endif>
                        <option value="">Elegir almacén</option>
                        @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->id_tercero }}" {{ old('id_tercero_almacen', $inventario->id_tercero_almacen) == $almacen->id_tercero ? 'selected' : '' }}>
                                {{ $almacen->full_name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" value="{{ $inventario->tblterceroalmacen->full_name }}" disabled readonly>
                    <input type="hidden" name="id_tercero_almacen" id="id_tercero_almacen" value="{{ $inventario->id_tercero_almacen }}">
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label for="id_dominio_clasificacion" class="required">Clasificación</label>
                @if ($create || $edit)
                    <select class="form-control" name="id_dominio_clasificacion" id="id_dominio_clasificacion" style="width: 100%" @if ($edit) required @else disabled @endif>
                        <option value="">Elegir clasificación</option>
                        @foreach ($clasificaciones as $id => $nombre)
                            <option value="{{ $id }}" {{ old('id_dominio_clasificacion', $inventario->id_dominio_clasificacion) == $id ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" value="{{ $inventario->tblclasificacion->nombre }}" disabled readonly>
                    <input type="hidden" name="id_dominio_clasificacion" id="id_dominio_clasificacion" value="{{ $inventario->id_dominio_clasificacion }}">
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label for="descripcion" class="required">Descripción</label>
                <input type="text" class="form-control" @if ($edit) name="descripcion" @endif id="descripcion" value="{{ old('descripcion', $inventario->descripcion) }}" @if ($edit) required @else disabled @endif>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label for="marca" class="required">Marca</label>
                <input type="text" class="form-control" list="list_marcas" @if ($edit) name="marca" @endif id="marca" value="{{ old('marca', $inventario->marca) }}" @if ($edit) required @else disabled @endif>
                @if ($edit)
                    <datalist id="list_marcas">
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca }}">{{ $marca }}</option>
                        @endforeach
                    </datalist>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label for="cantidad" class="required">Cantidad</label>
                <input type="number" min="0" class="form-control text-end" @if ($edit) name="cantidad" @endif id="cantidad" value="{{ old('cantidad', $inventario->cantidad) }}" @if ($edit) required @else disabled @endif>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label for="unidad" class="required">Unidad</label>
                <input type="text" class="form-control" list="list_unidades" @if ($edit) name="unidad" @endif id="unidad" value="{{ old('unidad', $inventario->unidad) }}" @if ($edit) required @else disabled @endif>
                @if ($edit)
                    <datalist id="list_unidades">
                        @foreach ($unidades as $unidad)
                            <option value="{{ $unidad }}">{{ $unidad }}</option>
                        @endforeach
                    </datalist>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label for="valor_unitario" class="required">Valor unitario</label>
                <input type="text" class="form-control money" @if ($edit) name="valor_unitario" @endif id="valor_unitario" value="{{ old('valor_unitario', $inventario->valor_unitario) }}" @if ($edit) required @else disabled @endif>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label for="ubicacion" class="required">Ubicación</label>
                <input type="text" class="form-control" list="list_ubicacion" @if ($edit) name="ubicacion" @endif id="ubicacion" value="{{ old('ubicacion', $inventario->ubicacion) }}" @if ($edit) required @else disabled @endif>
                @if ($edit)
                    <datalist id="list_ubicacion">
                        @foreach ($ubicaciones as $ubicacion)
                            <option value="{{ $ubicacion }}">{{ $ubicacion }}</option>
                        @endforeach
                    </datalist>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label for="cantidad_minima" class="required">Cantidad mínima</label>
                <input type="number" min="0" class="form-control text-end" @if ($edit) name="cantidad_minima" @endif id="cantidad_minima" value="{{ old('cantidad_minima', $inventario->cantidad_minima) }}" @if ($edit) required @else disabled @endif>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label for="cantidad_maxima" class="required">Cantidad máxima</label>
                <input type="number" min="0" class="form-control text-end" @if ($edit) name="cantidad_maxima" @endif id="cantidad_maxima" value="{{ old('cantidad_maxima', $inventario->cantidad_maxima) }}" @if ($edit) required @else disabled @endif>
            </div>
            @if(!$create)
                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                    <label for="estado" class="required">Estado</label>
                    @if ($edit)
                        <select class="form-control" name="estado" id="estado" style="width: 100%" @if ($edit) required @else disabled @endif>
                            @foreach ($estados as $id => $name)
                                <option value="{{ $id }}" {{ old('estado', $inventario->estado_form) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" class="form-control" id="estado" value="{{ $inventario->estado_form = 1 ? 'Activo' : 'Inactivo' }}" disabled>
                    @endif
                </div>
            @endif
            @if(!$create && !$edit)
                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                    <label for="creado_por">Creado por</label>
                    <input type="text" id="creado_por" class="form-control" disabled value="{{ $inventario->tblusuario->usuario }}">
                </div>
            
                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
                    <label for="fecha_creacion">Fecha creación</label>
                    <input type="text" id="fecha_creacion" class="form-control" disabled value="{{ $inventario->created_at }}">
                </div>
            @endif
        </div>

        @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear producto' : 'Editar producto'])
    @if ($create || $edit)
        </form>
    @endif
@if (!$create)
        </div>
        <div class="tab-pane" id="track-kardex" role="tabpanel" aria-labelledby="track-tab-kardex">
            @include('kardex.hoja', ['kardex' => $kardex])
        </div>
    </div>
@endif