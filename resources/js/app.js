/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Push from 'push.js';
window.Pusher = require('pusher-js');

import moment from 'moment';

const Swal = require('sweetalert2');
const tempusDominus = require('@eonasdan/tempus-dominus');

require('inputmask/dist/jquery.inputmask');
require('bootstrap-fileinput/js/fileinput.min.js');
require('bootstrap-fileinput/js/locales/es.js')
require('bootstrap-fileinput/themes/explorer/theme.min.js');

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});

window.regexCurrencyToFloat = /[^0-9.-]+/g;
window.formatCurrency = { alias : "currency", prefix: '', min: 0 };

var pusher = new Pusher('c27a0f7fb7b0efd70263', {
    cluster: 'us2'
});

window.timer = () => {
    let date_time_now = moment(new Date(), 'YYYY-MM-DD HH:mm:ss');

    $('.timer').each(function() {
        if(typeof $(this).data('value') !== 'undefined'){
            let date_time = $(this).data('value').trim();
            let diff = moment.utc(moment(date_time_now, 'YYYY-MM-DD HH:mm:ss').diff(moment(date_time, 'YYYY-MM-DD HH:mm:ss'))).format("HH:mm:ss");
            $(this).html(diff);
        }
    });

    setTimeout(timer, 1000);
}

window.datePicker = (minDate = null) => {
    $('.input-date input').each((i, element) => {
        let initialDate = $(element).val();
        (minDate !== null
            ? setupDatePicker(element, initialDate, false, false, minDate)
            : setupDatePicker(element, initialDate, false, false)
        );
        $(element).val(initialDate);
    });

    let picker1, picker2;
    $('.input-daterange input').each((i, element) => {
        if(i == 0) {
            picker1 = (minDate !== null
                ? setupDatePicker(element, '', false, true, minDate)
                : setupDatePicker(element, '', false, true));
        }
        if(i== 1) {
            picker2 = (minDate !== null
                ? setupDatePicker(element, '', false, false, minDate)
                : setupDatePicker(element, '', false, false));

            picker1.subscribe(tempusDominus.Namespace.events.change, (e) => {
                picker2.updateOptions({
                    restrictions: {
                        minDate: e.date
                    },
                    localization: {
                        locale: 'es',
                        dayViewHeaderFormat: { month: 'long', year: 'numeric' }
                    },
                    display: {
                        components: {
                            clock: false,
                        },
                        buttons: {
                            clear: true,
                            today: true
                        },
                        keepOpen: false,
                    },
                });
            });

            picker2.subscribe(tempusDominus.Namespace.events.change, (e) => {
                picker1.updateOptions({
                    restrictions: {
                        maxDate: e.date
                    },
                    localization: {
                        locale: 'es',
                        dayViewHeaderFormat: { month: 'long', year: 'numeric' }
                    },
                    display: {
                        components: {
                            clock: false,
                        },
                        buttons: {
                            clear: true,
                            today: true
                        },
                        keepOpen: false,
                    },
                });
            });
        }
    });

    $('.input-datetimerange input').each((i, element) => {
        if(i == 0) {
            picker1 = (minDate !== null
                ? setupDatePicker(element, '', true, false, minDate)
                : setupDatePicker(element, '', true, false)
            );
        }
        if(i== 1) {
            picker2 = (minDate !== null
                ? setupDatePicker(element, '', true, false, minDate)
                : setupDatePicker(element, '', true, false)
            );

            picker1.subscribe(tempusDominus.Namespace.events.change, (e) => {
                picker2.updateOptions({
                    localization: {
                        locale: 'es',
                        dayViewHeaderFormat: { month: 'long', year: 'numeric' },
                    },
                    display: {
                        components: {
                            clock: true,
                        },
                        buttons: {
                            clear: true,
                            today: true
                        },
                        keepOpen: false,
                    },
                    restrictions: {
                        minDate: e.date
                    },
                });
            });

            picker2.subscribe(tempusDominus.Namespace.events.change, (e) => {
                picker1.updateOptions({
                    localization: {
                        locale: 'es',
                        dayViewHeaderFormat: { month: 'long', year: 'numeric' },
                    },
                    display: {
                        components: {
                            clock: true,
                        },
                        buttons: {
                            clear: true,
                            today: true
                        },
                        keepOpen: false,
                    },
                    restrictions: {
                        maxDate: e.date
                    },
                });
            });
        }
    });
}

const setupDatePicker = (element, initialDate, clock, useCurrent, minDate) => {
    let picker = new tempusDominus.TempusDominus(element, {
        useCurrent,
        localization: {
            locale: 'es',
            dayViewHeaderFormat: { month: 'long', year: 'numeric' },
        },
        display: {
            components: {
                clock: clock,

            },
            buttons: {
                clear: true,
                today: true
            },
            keepOpen: false,
        },
        restrictions: {
            minDate
        }
    });

    picker.clear();

    picker.dates.formatInput = (date) => {
        return (date !== undefined && date !== null
            ? clock ? moment(date, 'h:mm:ss A').format('YYYY-MM-DD HH:mm') : moment(date).format('YYYY-MM-DD')
            : null
        );
    };

    if(initialDate !== '') {console.log(446);
        picker.dates.setValue(new tempusDominus.DateTime(moment(initialDate).add(1, 'days').format('YYYY-MM-DD')))
    }

    picker.subscribe(tempusDominus.Namespace.events.hide, (event) => {
        $(element).trigger('change');
    });

    return picker;
}

const sendAjaxForm = (action, data, reload, select) => {
    $.ajax({
        url: action,
        method: 'POST',
        enctype: 'multipart/form-data',
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        beforeSend: function() {
            $('.alert-success, .alert-danger').fadeOut().html('');
            showLoader(true);
        },
        success: function(response) {
            $('#modalForm').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Cambio realizado',
                text: response.success,
                confirmButtonColor: 'var(--bs-primary)',
            }).then(function() {
                if(reload.toString() !== 'false') {
                    location.reload();
                } else {
                    if($(`#${select}`).length && typeof response.response !== 'undefined') {
                        var record = response.response;
                        var option = `<option selected value="${record.value}">${record.option}</option>`;
                        $(`#${select}`).append(option);
                        $(`#${select}`).trigger('change');
                    }
                }
            });
        },
        error: function(response) {
            let errors = '';
            $.each(response.responseJSON.errors, function(i, item){
                errors += `<li>${item}</li>`;
            });
            $('.alert-danger')
                .fadeIn(1000)
                .html(`
                    <p>Por favor corrija los siguientes campos: </p>
                    <ul>${errors}</ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                `);
        }
    }).always(function () {
        showLoader(false);
    });
}

$('body').tooltip({
    selector: '[data-toggle="tooltip"]'
});

$(document).ready(function() {
    AOS.init();

    window.showLoader = function(show = false) {
        $('#lds-loader').toggle(show);
    }

    window.setupSelect2 = function(modal = '') {
        if(modal !== '') {
            $(`#${modal} select`).select2({
                dropdownParent: $(`#${modal}`)
            });
            $('.money').inputmask(formatCurrency);
        } else {
            $('select').select2();
        }

        $('.select2-selection').addClass('form-control');

        $('#select2-lista_tipo_movimientos-container, #select2-lista_clientes-container')
            .parent().removeClass('border-left-0 border-top-0 border-right-0').addClass('form-control border');
        
        $('.select2-selection__rendered').data('toggle', 'tooltip');
    }

    $('.nav-item > .nav-link').click(function(e) {
        if($('.navbar-toggler').is(":visible")){
            setTimeout(() => {
                $('.navbar-toggler').click(); 
            }, 550);
        }
    });

    $('#contacto-form > .d-flex > .form-control, #login-form > .d-flex > .form-control').focus(function() {
        $(this).parent().find('i').addClass('focus');
    });

    $('#contacto-form > .d-flex > .form-control, #login-form > .d-flex > .form-control').blur(function() {
        $(this).parent().find('i').removeClass('focus');
    });

    if($('#map').length) {
        initMap();
    }

    const procesarErrores = (title, errores) => {
        let errors = '';
        $.each(errores, function(i, item){
            errors += `<li>${item}</li>`;
        });
        $('.alert-danger')
            .fadeIn(1000)
            .html(
                `<p>${title}: </p>
                <ul>${errors}</ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                `
            );
    }

    $('#contacto-form').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: 'contact',
            method: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                $('.alert-success, .alert-danger').fadeOut().html('');
                showLoader(true);
            },
            success: function(response) {
                if(response.success) {
                    $('.alert-success').fadeIn(2000).html(response.success);
                    $('#contacto-form').trigger("reset");
                    setTimeout(() => {
                        $('.alert-success').fadeOut(2000);
                    }, 1000);
                }

                if(response.errors) {
                    procesarErrores('Error enviando correo!', response.errors);
                }
            },
            error: function(response) {
                
            }
        }).always(function () {
            showLoader(false);
        });
    });

    $('#login-form').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: 'login',
            method: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                $('#login-form #email, #login-form #password').removeClass('is-invalid');
                showLoader(true);
            },
            success: function(response) {
                window.location.href = "home";
            },
            error: function(response) {
                procesarErrores('Error de inicio de sesión', (typeof response.responseJSON.errors !== 'undefined' ? response.responseJSON.errors : ['Por favor actualice la página']));
                $('#login-form #email, #login-form #password').addClass('is-invalid');
            }
        }).always(function () {
            showLoader(false);
        });
    });

    $('[data-toggle="tooltip"]').tooltip();
    setupSelect2();
    timer();
    datePicker();
});

jQuery(window).ready(function () {
    showLoader(false);
});

document.addEventListener("DOMContentLoaded", function(){
    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            $('#navbar_top').addClass('bg-white sticky-top shadow-sm');
        } else {
            $('#navbar_top').removeClass('bg-white sticky-top shadow-sm');
        }
    });
});

function initMap() {
    // The location of Uluru
    const uluru = { lat: -25.344, lng: 131.036 };
    // The map, centered at Uluru
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 4,
        center: uluru,
    });
    // The marker, positioned at Uluru
    const marker = new google.maps.Marker({
        position: uluru,
        map: map,
    });
}

document.addEventListener("DOMContentLoaded", function(event) {
    const showNavbar = (toggleId, navId, bodyId, headerId) =>{
        const toggle = document.getElementById(toggleId),
        nav = document.getElementById(navId),
        bodypd = document.getElementById(bodyId),
        headerpd = document.getElementById(headerId)
        
        // Validate that all variables exist
        if(toggle && nav && bodypd && headerpd) {
            toggle.addEventListener('click', ()=>{
                // show navbar
                nav.classList.toggle('show-panel');
                // change icon
                toggle.classList.toggle('fa-xmark');
                // add padding to body
                bodypd.classList.toggle('body-pd');
                // add padding to header
                headerpd.classList.toggle('body-pd');
                $('.customer, .connection').toggleClass('d-none');
            });
        }
    }
    
    showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header');
});

$(document).on('click', '.modal-form', function(e) {
    e.preventDefault();

    const button = $(this);

    let size = new String(button.data('size')).trim();
    let title = new String(button.data('title')).trim();
    let action = new String(button.data('action')).trim();
    let reload = new String(button.data('reload')).trim();
    let select = new String(button.data('select')).trim();
    let callback = new String(button.data('callback')).trim();

    let btnCancel = new String(button.data('cancel')).trim();
    let btnSave = new String(button.data('save')).trim();

    size = (size !== 'undefined' ? size : 'modal-md');
    btnCancel = (btnCancel !== 'undefined' ? btnCancel : 'Cancelar');
    btnSave = (btnSave !== 'undefined' ? btnSave : 'Guardad');
    reload = (reload !== 'undefined' ? reload : 'true');
    select = (select !== 'undefined' ? select : '');

    if(action !== 'undefined') {
        $.ajax({
            url: action,
            method: 'GET',
            beforeSend: function() {
                $('#modalForm .modal-body').html('');
                showLoader(true);
            }
        }).done(function(view) {
            $('#modalForm .modal-body').html(view);
            $('#modalForm .btn-modal-cancel').html(btnCancel);
            $('#modalForm .btn-modal-save').html(btnSave);

            $('#modalForm').data('reload', reload);
            if(select !== '') {
                $('#modalForm').data('select', select);
            }
            if(callback !== ''){
                $('#modalForm').data('callback', callback);
            }

        }).always(function() {
            showLoader(false);
        });
    }

    $('#modalForm .modal-dialog').removeClass('modal-sm modal-md modal-lg modal-xl').addClass(size);
    $('#modalForm .modal-title').html(title);

    $('#modalForm').modal('handleUpdate');
    $('#modalForm').modal('show');
});

$(document).on('submit', '.search_form', function(e){
    e.preventDefault();
});

$(document).on('change', '.search_form', function() {
    let form = $(this).closest('form').attr('id');
    let url = form.split('_'); 

    $('.search_form select').each(function() {
        $.ajax({
            url: `${url[1]}/grid`,
            method: 'POST',
            data: $(`#${form}`).serialize(),
            beforeSend: function() {
                showLoader(true);
            }
        }).done(function(view) {
            $('#container').html(view);
        }).always(function() {
            showLoader(false);
            setupSelect2();
        });
    });
});

$(document).on('click', '#btn-form-action', function(e){
    e.preventDefault();

    let button = $(this);

    let action = button.closest('form').attr('action');
    let reload = $('#modalForm').data('reload');
    let form = button.closest('form');
    let select = $('#modalForm').data('select');

    if($('#campos_reporte').length){
        $('#campos_reporte option').prop('selected', true);
    }
    
    let data = new FormData(form[0]);
    
    sendAjaxForm(action, data, reload, select);
});

$(document).on('click', 'button.close', function() {
    if($(this).parent().hasClass('alert')) {
        $(this).parent().fadeOut().html('');
    }
});

$(document).on('click', '.page-item', function(e) {
    e.preventDefault();

    if(!$(this).hasClass('disabled')) {
        $('.page-item').removeClass('active');
        $(this).addClass('active');
        let form = $(this).closest('form').attr('id');
        let page = $.urlParam('page', $(this).children().attr('href'));

        $(`#${form} > #page`).val(page);
        $('.search_form').change();
    }
});

$.urlParam = function(name, url){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(url);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}

$(document).on("click", ".btn-export", function(e) {
    e.preventDefault();

    let action = $(this).data('route');
    let data = $(`#form_${action}`).serialize();

    $.ajax({
        xhrFields: {
            responseType: 'blob',
        },
        type: 'GET',
        url: `${action}/export`,
        data: data,
        beforeSend: () => {
            showLoader(true);
        },
        success: (result, status, xhr) => {
            var disposition = xhr.getResponseHeader('content-disposition');
            var matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
            var filename = (matches != null && matches[1] ? matches[1] : 'Reporte.xlsx').replace(/"/g,'');

            // The actual download
            var blob = new Blob([result], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });

            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            link.text = filename;

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }).always(function () {
        showLoader(false);
    });

});

$(document).on('select2:open', () => {
    document.querySelector('.select2-search__field').focus();
});

let specialkeypress = false;
$(document).keydown(function (e) {
    if(e.ctrlKey && e.which === 13 || e.ctrlKey && e.which === 65) {
        e.preventDefault();
    }

    if($('.btn-update').length) {
        if(e.which == 116 || e.keyCode == 116) {
            e.preventDefault();
            $('.btn-update').click();
        }
    }

    specialkeypress = ($.inArray(e.which, [1, 16, 17]) ? true : false);

    if(e.which == 27 || e.keyCode == 27) {
        $('#modalForm').modal('hide');
    }

    if(e.which === 65 && e.ctrlKey){
        $('.btn-primary.btn-md.modal-form').click();
    }

    if(e.which === 13 && e.altKey) {
        $('#btn-form-action').click();
    }
});

$(document).on('click', '#kvFileinputModal .btn-kv-close', function(e) {
    $('#kvFileinputModal').modal('hide');
});

$(document).on('mousemove', '.blink_me', function() {
    $(this).removeClass('blink_me');
});