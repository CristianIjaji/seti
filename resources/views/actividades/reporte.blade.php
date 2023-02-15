@php
    $linkReporte = isset($activity->tblinforme) ? $activity->tblinforme->link : '';
@endphp

<style>
    /* .kv-avatar .krajee-default.file-preview-frame,.kv-avatar .krajee-default.file-preview-frame:hover {
        margin: 0;
        padding: 0;
        border: none;
        box-shadow: none;
        text-align: center;
    }
    .kv-avatar {
        display: inline-block;
        width: calc(100vw - 3vw);
    }
    .kv-avatar .file-input {
        display: table-cell;
        width: calc(100vw - 3vw);
    }
    .kv-avatar .krajee-default.file-preview-frame,
    .kv-avatar .krajee-default.file-preview-frame .kv-file-content {
        width: calc(100vw - 10vw);
        padding: 10px;
    }

    .kv-avatar .krajee-default.file-preview-frame .kv-file-content,
    .kv-avatar .kv-file-content .kv-preview-data{
        height: 50vh !important;
    }

    .kv-reqd {
        color: red;
        font-family: monospace;
        font-weight: normal;
    } */
</style>


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

    var show = (!"{!! $create !!}" && !"{!! $edit !!}") ? false : true;
    var id = "{!! $activity->id_actividad !!}";

    $("#file_report").fileinput({
        language: 'es',
        overwriteInitial: true,
        showCaption: false,
        showBrowse: show,
        showRemove: show,
        showUpload: show,
        showCancel: false,
        maxFileSize: 1500,
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
    // if($(this).val() !== '') {
    //     $('.kv-avatar .fileinput-upload-button').removeClass('d-none');
    // }
</script>