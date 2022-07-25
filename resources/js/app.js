/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./multiselect.min');

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

    if(initialDate !== '') {
        picker.dates.setValue(new tempusDominus.DateTime(moment(initialDate).add(1, 'days').format('YYYY-MM-DD')))
    }

    picker.subscribe(tempusDominus.Namespace.events.hide, (event) => {
        $(element).trigger('change');
    });

    return picker;
}

const sendAjaxForm = (action, data, reload, select, modal) => {
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
            if(typeof response.errors === 'undefined') {
                $(`#${modal}`).modal('hide');
            }
            
            Swal.fire({
                icon: `${typeof response.errors === 'undefined' ? 'success' : 'error'}`,
                title: `${typeof response.errors === 'undefined' ? 'Cambio realizado' : 'No se pudo completar la acción'}`,
                text: `${typeof response.errors === 'undefined' ? response.success : response.errors}`,
                confirmButtonColor: 'var(--bs-primary)',
            }).then(function() {
                if(typeof response.errors === 'undefined') {
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
                }
            });
        },
        error: function(response) {
            let errors = '';
            $.each(response.responseJSON.errors, function(i, item){
                errors += `<li>${item}</li>`;
            });
            $(`#${modal} .alert-danger`)
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

const handleModal = (button) => {
    let size = new String(button.data('size')).trim();
    let title = new String(button.data('title')).trim();
    let headerClass = new String(button.data('header-class')).trim();
    let action = new String(button.data('action')).trim();
    let reload = new String(button.data('reload')).trim();
    let select = new String(button.data('select')).trim();
    let callback = new String(button.data('callback')).trim();
    let modal = new String(button.data('modal')).trim();

    let btnCancel = new String(button.data('cancel')).trim();
    let btnSave = new String(button.data('save')).trim();

    size = (size !== 'undefined' ? size : 'modal-md');
    headerClass = (headerClass !== 'undefined' ? headerClass : '');
    btnCancel = (btnCancel !== 'undefined' ? btnCancel : 'Cancelar');
    btnSave = (btnSave !== 'undefined' ? btnSave : 'Guardad');
    reload = (reload !== 'undefined' ? reload : 'true');
    select = (select !== 'undefined' ? select : '');
    modal = (modal !== 'undefined' ? modal : 'modalForm');

    if(action !== 'undefined') {
        $.ajax({
            url: action,
            method: 'GET',
            beforeSend: function() {
                $(`#${modal} .modal-body`).html('');
                showLoader(true);
            }
        }).done(function(view) {
            $(`#${modal} .modal-body`).html(view);
            $(`#${modal} .btn-modal-cancel`).html(btnCancel);
            $(`#${modal} .btn-modal-save`).html(btnSave);

            $(`#${modal} #btn-form-action`).data('modal', modal);
            setupSelect2(modal);
            $(`#${modal}`).data('reload', reload);

            if(select !== '') {
                $(`#${modal}`).data('select', select);
            }
            if(callback !== ''){
                $(`#${modal}`).data('callback', callback);
            }

            $('body').tooltip({
                selector: '[data-toggle="tooltip"]'
            });
            setTimeout(() => {
                $(`#${modal} input:text, #${modal} textarea`).first().focus();
            }, 100);
        }).always(function() {
            showLoader(false);
        });
    }

    $(`#${modal} .modal-dialog`).removeClass('modal-sm modal-md modal-lg modal-xl').addClass(size);
    $(`#${modal} .modal-title`).html(title);
    $(`#${modal} .modal-header`).attr('class', 'modal-header border-bottom border-2');
    $(`#${modal} .modal-header`).addClass(headerClass);

    $(`#${modal}`).modal('handleUpdate');
    $(`#${modal}`).modal('show');
}

window.carrito = [];

window.drawItems = (edit = true) => {
    // type: tipo de item: mano de obra, transporte o suministro
    // item: ítem de la lista de precios
    if(!edit) {
        $('#th-delete').remove();
    }

    $.each(carrito, (type, item) => {
        let total = 0;
        $.each(item, (index, element) => {
            if(typeof element !== 'undefined') {
                if(!$(`#tr_${type}_${index}`).length) {
                    $(`
                        <tr id="tr_${type}_${index}" class="tr_cotizacion">
                            <td>
                                <input type='hidden' name="id_tipo_item[]" value="${type}" />
                                <input type='hidden' name="id_lista_precio[]" value="${index}" />
                                <input type="text" class="form-control text-center text-uppercase border-0" id="item_${index}" value="${element['item']}" disabled>
                            </td>
                            <td>
                                <textarea class="form-control border-0" rows="2" data-toggle="tooltip" title="${element['descripcion']}" name="descripcion_item[]" id="descripcion_item_${index}" required ${edit ? '' : 'disabled'}>${element['descripcion']}</textarea>
                            </td>
                            <td>
                                <input type="text" class="form-control text-center border-0" data-toggle="tooltip" title="${element['unidad']}" name="unidad[]" id="unidad_${index}" value="${element['unidad']}" ${edit ? '' : 'disabled'}>
                            </td>
                            <td>
                                <input type="number" min="0" class="form-control text-center border-0 txt-cotizaciones" name="cantidad[]" id="cantidad_${index}" value="${element['cantidad']}" required ${edit ? '' : 'disabled'}>
                            </td>
                            <td>
                                <input type="text" class="form-control text-end border-0 txt-cotizaciones money" data-toggle="tooltip" title="${Inputmask.format(element['valor_unitario'], formatCurrency)}" name="valor_unitario[]" id="valor_unitario_${index}" value="${element['valor_unitario']}" required ${edit ? '' : 'disabled'}>
                            </td>
                            <td>
                                <input type="text" class="form-control text-end border-0 txt-cotizaciones money" name="valor_total[]" id="valor_total_${index}" value="${element['valor_total']}" disabled>
                            </td>
                            ${edit == true
                                ? `<td class="text-center"><i id="${type}_${index}" class="fa-solid fa-trash-can text-danger fs-5 fs-bold btn btn-delete-item"></i></td>`
                                : ``
                            }
                        </tr>
                    `).insertAfter(`#tr_${type}`);
                    $('.money').inputmask(formatCurrency);
                } else {
                    $(`#tr_${type}_${index} #valor_total_${index}`).val(element['valor_total']);
                }

                total += element['valor_total'];
            }
        });

        $(`#lbl_${type}`).text(Inputmask.format(total, formatCurrency));
    });
}

const getItem = (item) => {
    let cantidad = parseFloat($(item).data('cantidad'));
    let valor = parseFloat($(item).data('valor_unitario'));

    return {
        'item': $(item).data('item'),
        'descripcion': $(item).data('descripcion'),
        'cantidad': cantidad,
        'unidad': $(item).data('unidad'),
        'valor_unitario': valor,
        'valor_total': parseFloat(cantidad * valor, 2),
    };
};

const addItems = (items) => {
    $.each(items, (index, item) => {
        if(typeof carrito[$(item).data('type')] === 'undefined') {
            carrito[$(item).data('type')] = {};
        }

        if(typeof carrito[$(item).data('type')][$(item).val()] === 'undefined') {
            carrito[$(item).data('type')][$(item).val()] = getItem(item);
        }
    });

    drawItems();
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

        $('#lista_items, #id_tercero_dependencia').select2('destroy');

        $('#lista_items, #id_tercero_dependencia').select2({
            minimumInputLength: 2,
            language: {
                inputTooShort: function (args) {
                    var remainingChars = args.minimum - args.input.length;
                    var message = 'Por favor ingrese ' + remainingChars + ' o más carácteres';
                    return message;
                },
                noResults: function() {
                    return 'No existen resultados';
                },
            },
            closeOnSelect: false
        });

        $('#select2-lista_items-container, #select2-id_tercero_dependencia-container').data('toggle', 'tooltip').data('html', true);

        $('#select2-lista_items-container, #select2-id_tercero_dependencia-container')
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
    handleModal($(this));
});

$(document).on('submit', '.search_form', function(e){
    e.preventDefault();
});

$(document).on('change', '.search_form', function() {
    let form = $(this).closest('form').attr('id');
    let url = form.split('_'); 

    // $('.search_form select').each(function() {
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
    // });
});

$(document).on('click', '#btn-form-action', function(e){
    e.preventDefault();
    let button = $(this);

    let action = button.closest('form').attr('action');
    let modal = (button.data('modal') !== 'undefined' ? button.data('modal') : 'modalForm');
    let reload = $(`#${modal}`).data('reload');
    let form = button.closest('form');
    let select = $(`#${modal}`).data('select');
    
    if($('#campos_reporte').length){
        $('#campos_reporte option').prop('selected', true);
    }
    
    let data = new FormData(form[0]);
    sendAjaxForm(action, data, reload, select, modal);
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

$(document).on('click', '#btn_add_items', function() {
    if($("#lista_items option:selected").length) {
        addItems($('#lista_items option:selected'));
    }
    
    $(`#modalForm-2`).modal('hide');
});

$(document).on('click', '.btn-delete-item', function() {
    let id_tr = new String($(this).attr('id'));
    $(`#tr_${id_tr}`).remove();
    id_tr = id_tr.split('_');

    delete carrito[id_tr[0]][id_tr[1]];
    drawItems();
});

const fnc_totales_cot = (id) => {
    let id_tr = new String(id).split('_');
    if(typeof carrito[id_tr[1]][id_tr[2]] !== 'undefined') {
        let id_row = `tr_${id_tr[1]}_${id_tr[2]}`;
        let descripcion = $(`#${id_row} #descripcion_${id_tr[2]}`).val();
        let cantidad = parseFloat($.isNumeric($(`#${id_row} #cantidad_${id_tr[2]}`).val()) ? $(`#${id_row} #cantidad_${id_tr[2]}`).val() : 0);
        let valor_unitario = parseFloat($.isNumeric($(`#${id_row} #valor_unitario_${id_tr[2]}`).val().replace(regexCurrencyToFloat, "")) ? $(`#${id_row} #valor_unitario_${id_tr[2]}`).val().replace(regexCurrencyToFloat, "") : 0);
        let valor_total = (cantidad) * (valor_unitario);

        carrito[id_tr[1]][id_tr[2]]['descripcion'] = descripcion;
        carrito[id_tr[1]][id_tr[2]]['cantidad'] = cantidad;
        carrito[id_tr[1]][id_tr[2]]['valor_unitario'] = valor_unitario;
        carrito[id_tr[1]][id_tr[2]]['valor_total'] = valor_total;

        drawItems();
    }
}

$(document).on('keydown', '.txt-cotizaciones', function() {
    fnc_totales_cot($(this).parent().parent().attr('id'));
});

$(document).on('keyup', '.txt-cotizaciones', function() {
    fnc_totales_cot($(this).parent().parent().attr('id'));
});

$(document).on('change', '.txt-cotizaciones', function() {
    fnc_totales_cot($(this).parent().parent().attr('id'));
});

$(document).on('change', '#id_cliente', function() {    
    if($(this).closest('form').attr('action').indexOf('quotes') > -1) {
        $('#table-cotizaciones').addClass('d-none');

        $('#id_estacion').empty();
        $('#id_estacion').append(`<option value=''>Elegir punto ínteres</option>`);

        if($(this).val() !== '') {
            $('#table-cotizaciones').removeClass('d-none');

            $(`.tr_cotizacion`).each((index, item) => {
                let action = new String($(item).data('action')).split('/');
                action[action.length - 1] = $(this).val();

                action = action.join('/');
                $(item).data('action', action);
            });

            $.ajax({
                url: `sites/${$(this).val()}/get_puntos_interes_client`,
                method: 'GET',
                beforeSend: function() {
                    showLoader(true);
                }
            }).done(function(response) {
                $.each(response.estaciones, (index, item) => {
                    $('#id_estacion').append(`<option value='${index}'>${item}</option>`);
                });
            }).always(function() {
                showLoader(false);
            });
        }
    }
});