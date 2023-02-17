@php
    $linkReporte = isset($activity->tblinforme) ? $activity->tblinforme->link : '';
@endphp

<div class="row">
    <form class="col-12" action="{{ route('activities.uploadReport', $activity->id_actividad) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group col-12 text-center">
            <div class="kv-avatar">
                <div class="file-loading">
                    <input id="file_report" name="file_report" type="file" accept=".pdf" required>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="application/javascript">
    var previewImage = null;
    if("{!! $linkReporte !!}" !== '') {
        previewImage = "{!! $linkReporte !!}";
    }

    var show = ("{!! $uploadReport !!}" === '1' && "{!! $edit !!}" === '1' ? true : false);
    var id = "{!! $activity->id_actividad !!}";
    console.log(show, "{!! $uploadReport !!}", "{!! $edit !!}")

    $("#file_report").fileinput({
        language: 'es',
        overwriteInitial: true,
        showCaption: false,
        showBrowse: show,
        showRemove: show,
        showUpload: show,
        showCancel: false,
        maxFileSize: 10000,
        showClose: false,
        browseLabel: '',
        removeLabel: '',
        browseIcon: '<i class="bi-folder2-open"></i>',
        removeIcon: '<i class="bi-x-lg"></i>',
        removeTitle: 'Cancel or reset changes',
        elErrorContainer: '#kv-avatar-errors-1',
        msgErrorClass: 'alert alert-block alert-danger',
        // uploadClass: "btn btn-info",
        uploadLabel: "",
        uploadIcon: '<i class="bi-upload"></i>',
        layoutTemplates: {main2: '{preview} {upload} {remove} {browse}'},
        allowedFileExtensions: ["pdf"],
        initialPreviewShowDelete: false,
        initialPreview: [previewImage],
        initialPreviewAsData: true,
        initialPreviewConfig: [
            {type: "pdf", caption: `Reporte actividad ${id}.pdf`, url: previewImage}, // disable download
        ]
    });

    $('.kv-avatar .fileinput-remove-button').click(function() {
        $('#file_report').change();
    });

    $('.kv-avatar .fileinput-upload-button').addClass('d-none');
</script>