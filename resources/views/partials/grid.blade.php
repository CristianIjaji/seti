<div class="row my-4">
    <div class="col-sm-12 col-md-6">
        @isset($title)
            <div class="h1">{{ $title }}</div class="h1">
        @endisset

        @if ($errors->any())
            <div class="alert-danger alert-dismissible text-start p-4 rounded">
                <h6 class="text-danger fw-bold">Se encontraron errores en el archivo.</h5>
                <ol>
                    @foreach ($errors->all() as $error)
                        <li>{{__($error)}}</li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
    <div id="div-submenus" class="col-sm-12 col-md-6 text-center text-md-end">
        <div class="d-inline-block">
            @isset($view)
                @if ($view)
                    <a style="margin: 0.9px 0px 0.9px 0.9px;" href="{{ route($btnRefresh ?? "$route.index") }}" data-toggle="tooltip" title="Actualizar" class="btn btn-outline-info border font-weight-bolder fs-4 bg-update">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                @endif
            @endisset
            @isset($export)
                @if ($export)
                    <a style="margin: 0.9px 0px 0.9px 0px;" data-route="{{$route}}" data-toggle="tooltip" title="Exportar a Excel" class="btn btn-outline-success border font-weight-bolder fs-4 px-3 btn-export">
                        <i class="fas fa-file-excel"></i>
                    </a>
                @endif
            @endisset
            @isset($import)
                @if ($import)
                    <div class="dropdown d-inline">
                        <a class="dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span style="margin: 0.9px 0.9px 0.9px 0px;" data-toggle="tooltip" title="Importar" class="btn btn-outline-secondary border font-weight-bolder fs-4">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <span id="btn_upload" class="dropdown-item pl-3 btn">
                                    Subir archivo
                                </span>
                                <form action="{{ route("$route.import") }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="input_file" id="input_file" accept=".xlsx,.xls" class="d-none">
                                    <button class="d-none">Enviar</button>
                                </form>
                            </li>
                            <li>
                                <a data-route="{{$route}}" class="dropdown-item pl-3 btn btn-download">
                                    Descargar plantilla
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
            @endisset
            @isset($create)
                @if ($create)
                    <button
                        style="margin: 0.9px 0px 0.9px 0.9px;"
                        class="btn btn-outline-primary border font-weight-bolder fs-4 modal-form"
                        data-title="{{ $btnOptionsCreate['title'] }}"
                        data-header-class="{{ isset($btnOptionsCreate['header-class']) ? $btnOptionsCreate['header-class'] : '' }}"
                        data-size="{{ $btnOptionsCreate['modal-size'] }}"
                        data-action={{ $btnOptionsCreate['route'] }}
                        data-toggle="tooltip"
                        data-placement="top"
                        data-modal='modalForm'
                        title="{{ $btnOptionsCreate['title'] }}"
                    >
                        <i class="fa-solid fa-plus"></i>
                    </button>
                @endif
            @endisset
        </div>
    </div>
</div>

<p>
    Mostrando {{ $models->firstItem() > 0 ? $models->firstItem() : 0 }} - {{ $models->lastItem() > 0 ? $models->lastItem() : 0 }} de {{$models->total()}} resultado{{$models->total() > 1 || $models->total() == 0 ? 's' : ''}}.
</p>

<form class="search_form table-responsive" id="form_{{$route}}">
    <table id="table__{{$route}}" class="table table-hover table-sm">
        <thead class="col-12">
            <tr>
            @isset($headers)
                @forelse ($headers as $header)
                    <th
                        scope="col"
                        class="text-nowrap align-middle text-center bg-primary bg-opacity-75 ps-2 py-2 text-white
                            {{ $header === reset($headers) ? 'rounded-start' : '' }}
                            {{ $header === end($headers) ? 'rounded-end' : '' }}
                        "
                    >
                        {{ isset($header['label']) ? $header['label'] : '' }}
                    </th>
                @empty
                    <td class="bg-danger font-weight-bolder text-white">No se han definido las cabeceras de la tabla</td>
                @endforelse
            @endisset
            </tr>
        </thead>
        <tbody class="col-12">
            @isset($filters)
                @if ($filters)
                    <tr>
                        @foreach ($headers as $header)
                            @if (!isset($header['actions']))
                                <th class="{{ isset($header['col']) ? $header['col'] : 'col-1' }} {{ isset($header['class']) ? $header['class'] : '' }}">
                                    @if (!isset($header['type']) && (!isset($header['filter']) || $header['filter'] == true))
                                        @if (isset($header['options']))
                                            <select class="form-control" name="{{ $header['name'] }}" style="width: 100%">
                                                <option value="">&nbsp;</option>
                                                @foreach ($header['options'] as $id => $name)
                                                    <option value="={{$id}}" @if (isset($request[$header['name']])) {{ $request[$header['name']] == "=$id" ? 'selected' : '' }} @endif>
                                                        {{$name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            @php
                                                $data_value = '';
                                                if(isset($header['data'])) {
                                                    foreach ($header['data'] as $data => $value) {
                                                        $data_value .= "data-$data=$value ";
                                                    }
                                                }
                                            @endphp
                                            <input
                                                type="text"
                                                class="form-control {{ isset($header['class']) ? $header['class'] : '' }}"
                                                {{$data_value}}
                                                name="{{ !isset($header['foreign']) ? $header['name'] : $header['foreign'] }}"
                                                value="{{ !isset($header['foreign'])
                                                    ? (isset($request[$header['name']]) ? $request[$header['name']] : '')
                                                    : (isset($request[$header['foreign']]) ? $request[$header['foreign']] : '') }}"
                                            >
                                        @endif
                                    @endif
                                </th>
                            @else
                                <th class="col-1 align-middle text-center">
                                    <button type="submit" class="d-none" class="btn bg-success bg-gradient bg-block">Buscar</button>
                                </th>
                            @endif
                        @endforeach
                    </tr>
                @endif
            @endisset
            @forelse ($models as $key => $model)
                <tr class="{{ isset($status) ? $status[$model->estado] : '' }}">
                    @forelse ($headers as $header)
                        <td 
                            data-value="{{ $model->{$header['name']} }}"
                            class="align-middle {{ isset($header['align']) ? $header['align'] : '' }} {{ isset($header['col']) ? $header['col'] : '' }} {{ isset($header['class']) && isset($status) ? $header['class'] : '' }}">
                            @if (isset($model->{$header['name']}))
                                @if (!isset($header['options']))
                                    @if (!isset($header['foreign']))
                                        @if (isset($header['html']) && $header['html'])
                                            {!! $model->{$header['name']} !!}
                                        @else
                                            @if (isset($header['diffForHumans']) && $header['diffForHumans'] == true)
                                                {{ $model->{$header['name']}->diffForHumans() }}
                                            @else
                                                {{ $model->{$header['name']} }}
                                            @endif
                                        @endif
                                    @else
                                        @if (isset($header['html']) && $header['html'])
                                            {!! $model->{$header['name']}->{$header['foreign']} !!}
                                        @else
                                            {{ $model->{$header['name']}->{$header['foreign']} }}
                                        @endif
                                    @endif
                                @else
                                    @if (count($header['options']))
                                        @if (isset($header['html']) && $header['html'])
                                            {!! $model->{$header['name']} !!}
                                        @else
                                            {{ isset($header['options'][$model->{$header['name']}]) ? $header['options'][$model->{$header['name']}] : '' }}
                                        @endif
                                    @endif
                                @endif
                            @else
                                @isset($header['actions'])
                                    <div class="h-100 w-100 text-center">
                                        @isset($header['actions']['btnOptions']['view'])
                                            <i
                                                class="fas fa-eye btn modal-form text-info fs-5 fw-bold"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                data-toggle="modal"
                                                data-title="{{ $header['actions']['btnOptions']['modal-view-title'] }}"
                                                data-size="{{ $header['actions']['btnOptions']['modal-view-size'] }}"
                                                data-header-class="{{ isset($header['actions']['btnOptions']['header-view-class']) ? $header['actions']['btnOptions']['header-view-class'] : '' }}"
                                                data-reload="false"
                                                data-action={{ route("$route.show", $model) }}
                                                title="Ver">
                                            </i>
                                        @endisset
                                        @isset($header['actions']['btnOptions']['edit'])
                                            <i
                                                class="fas fa-pencil-alt btn modal-form text-warning fs-5 fw-bold"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                data-toggle="modal"
                                                data-title="{{ $header['actions']['btnOptions']['modal-edit-title'] }}"
                                                data-size="{{ $header['actions']['btnOptions']['modal-edit-size'] }}"
                                                data-header-class="{{ isset($header['actions']['btnOptions']['header-edit-class']) ? $header['actions']['btnOptions']['header-edit-class'] : '' }}"
                                                data-action={{ route("$route.edit", $model) }}
                                                title="Editar">
                                            </i>
                                        @endisset
                                        @isset($header['actions']['btnOptions']{'delete'})
                                            <i
                                                class="fa-solid fa-trash-can btn modal-form-2 text-danger fs-5 fw-bold"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                data-toggle="modal"
                                                data-title="{{ $header['actions']['btnOptions']['modal-delete-title'] }}"
                                                {{-- data-size="{{ $header['actions']['btnOptions']['modal-delete-size'] }}" --}}
                                                {{-- data-header-class="{{ isset($header['actions']['btnOptions']['header-delete-class']) ? $header['actions']['btnOptions']['header-delete-class'] : '' }}" --}}
                                                {{-- data-action={{ route("$route.edit", $model) }} --}}
                                                title="Eliminar"
                                                >
                                            </i>
                                        @endisset
                                    </div>
                                @endisset
                            @endif 
                        </td>
                    @empty
                        
                    @endforelse
                </tr>
            @empty
                <td colspan="{{ count($headers) }}"><b>{{ "No hay registros para mostrar" }}</b></td>
            @endforelse
        </tbody>
    </table>
    @csrf
    <input type="hidden" name="table" value="{{$route}}">
    <input type="hidden" name="page" id="page" value="1">
    {{ $models->links("pagination::bootstrap-4")}}
</form>

<script type="application/javascript">
    if(typeof datePicker !== 'undefined') {
        datePicker();
    }
</script>