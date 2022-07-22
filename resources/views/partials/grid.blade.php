<div class="row my-4">
    <div class="col-sm-12 col-md-6">
        @isset($title)
            <div class="h1">{{ $title }}</div class="h1">
        @endisset
    </div>
    <div class="col-sm-12 col-md-6 text-end">
        @isset($export)
            @if ($export)
                <a href="#" data-route="{{$route}}" data-toggle="tooltip" title="Exportar a Excel" class="btn bg-outline-info bg-success bg-gradient text-white font-weight-bolder btn-export">
                    <i class="fas fa-file-excel"></i>
                </a>
            @endif
        @endisset
        <a href="{{ route($btnRefresh ?? "$route.index") }}" data-toggle="tooltip" title="Actualizar" class="btn btn-outline-light text-info font-weight-bolder fs-3 bg-update">
            <i class="fas fa-sync-alt"></i>
        </a>
        @isset($upload)
            @if ($upload)
                <form class="btn px-0" action="{{ route("$route.import") }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <span id="btn_upload" class="btn bg-success bg-gradient">
                        {{ $btnOptionsUpload['title'] }}
                    </span>
                    <input type="file" name="input_file" id="input_file" accept=".xlsx,.xls" class="d-none">
                    <button class="d-none">Enviar</button>
                </form>
            @endif
        @endisset
        @isset($create)
            @if ($create)
                <button
                    class="btn bg-primary bg-gradient bg-md modal-form text-white rounded-pill px-4 py-2"
                    data-title="{{ $btnOptionsCreate['title'] }}"
                    data-header-class="{{ isset($btnOptionsCreate['header-class']) ? $btnOptionsCreate['header-class'] : '' }}"
                    data-size="{{ $btnOptionsCreate['modal-size'] }}"
                    data-action={{ $btnOptionsCreate['route'] }}
                    data-toggle="tooltip"
                    data-placement="top"
                    data-modal='modalForm'
                    title="{{ $btnOptionsCreate['title'] }}"
                >
                    <i class="fa-solid fa-plus"></i> Crear
                </button>
            @endif
        @endisset
    </div>
</div>
<form class="search_form table-responsive" id="form_{{$route}}">
    <table class="table table-hover table-sm">
        <thead class="col-12">
            <tr>
            @isset($headers)
                @forelse ($headers as $header)
                    <th scope="col" class="{{ isset($header['actions']) ? 'text-center' : '' }}">{{ isset($header['label']) ? $header['label'] : '' }}</th>
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
                                            <input
                                                type="text"
                                                class="form-control"
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
                            data-value="{{ $model[$header['name']] }}"
                            class="
                                align-middle
                                {{ isset($header['align']) ? $header['align'] : '' }} {{ isset($header['col']) ?  $header['col'] : '' }}
                                {{ isset($header['class'])
                                    && isset($status)
                                    ? $header['class'] : ''
                                }}">
                            @if (isset($model[$header['name']]))
                                @if (!isset($header['options']))
                                    @if (!isset($header['foreign']))
                                        @if (isset($header['html']) && $header['html'])
                                            {!! $model[$header['name']] !!}
                                        @else
                                            @if (isset($header['diffForHumans']) && $header['diffForHumans'] == true)
                                                {{ $model[$header['name']]->diffForHumans() }}
                                            @else
                                                {{ $model[$header['name']] }}
                                            @endif
                                        @endif
                                    @else
                                        {{ $model[$header['name']][$header['foreign']] }}
                                    @endif
                                @else
                                    @if (count($header['options']))
                                        @if (isset($header['html']) && $header['html'])
                                            <?= $model[$header['name']] ?>
                                        @else
                                            {{ isset($header['options'][$model[$header['name']]]) ? $header['options'][$model[$header['name']]] : '' }}
                                        @endif
                                    @endif
                                @endif
                            @else
                                @isset($header['actions'])
                                    <div class="h-100 w-100 text-center">
                                        @if ($header['actions']['btnOptions']['view'])
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
                                        @endif
                                        @if ($header['actions']['btnOptions']['edit'])
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
                                        @endif
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