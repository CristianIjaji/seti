/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./multiselect.min');
// require('typeahead.js');

import Push from 'push.js';
window.Pusher = require('pusher-js');

import moment from 'moment';
import { TempusDominus, Namespace } from '@eonasdan/tempus-dominus';

const Swal = require('sweetalert2');

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

// Pusher.logToConsole = true;
var pusher = new Pusher('c27a0f7fb7b0efd70263', {
    cluster: 'us2'
});

let url_cotizacion = 'quotes';
let form_cotizacion = 'form_quotes';

const createChannel = (evento, canal, text, location, urlGrid, formData, audio = '') => {
    var channel = pusher.subscribe(`user-${canal}`);
    channel.bind(evento, function(data) {
        if(audio !== '') {
            var sound = new Audio(audio);
            sound.play();
        }

        if(window.location.pathname === `/${location}`) {
            getGrid(urlGrid, formData);
        }

        Push.create(text, {
            body: data.descripcion,
            icon: 'images/icon.png',
            vibrate: true,
            timeout: 10000,
            onClick: function() {
                if(window.location.pathname !== `/${location}`) {
                    window.location.href = location;
                }

                window.focus();
                this.close();
            }
        });
    });
}

window.listener = (canal) => {
    createChannel('quote-created', canal, "Revisar cotización!", url_cotizacion, url_cotizacion, form_cotizacion, 'sounds/notification1.mp3');
    createChannel('quote-deny', canal, "Cotización devuelta!", url_cotizacion, url_cotizacion, form_cotizacion, 'sounds/notification1.mp3');
    createChannel('quote-check', canal, "Cotización revisada!", url_cotizacion, url_cotizacion, form_cotizacion, 'sounds/notification1.mp3');
    createChannel('quote-wait', canal, "Cotización en espera de aprobación!", url_cotizacion, url_cotizacion, form_cotizacion, 'sounds/notification1.mp3');
    createChannel('quote-aprove', canal, "Cotización aprobada!", url_cotizacion, url_cotizacion, form_cotizacion, 'sounds/notification1.mp3');
    createChannel('quote-reject', canal, "Cotización rechazada por cliente!", url_cotizacion, url_cotizacion, form_cotizacion, 'sounds/notification1.mp3');
    createChannel('quote-cancel', canal, "Cotización cancelada!", url_cotizacion, url_cotizacion, form_cotizacion, 'sounds/notification1.mp3');
}

window.closeConnection = () => {
    pusher.disconnect();
}

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

window.datePicker = () => {
    $('.input-date input, .input-months input').each((i, element) => {
        let setup = {
            localization: {
                locale: 'es',
                dayViewHeaderFormat: { month: 'long', year: 'numeric' },
            },
        };

        let initialDate = (typeof $(element).data('default-date') !== 'undefined'
            ? $(element).data('default-date')
            : ($(element).val() !== '' ? $(element).val() : '')
        );
        let format = (typeof $(element).data('format') !== 'undefined' ? $(element).data('format') : 'YYYY-MM-DD');
        let minDate = (typeof $(element).data('minDate') !== 'undefined' ? $(element).data('minDate') : '');
        let maxDate = (typeof $(element).data('max-date') !== 'undefined' ? $(element).data('max-date') : '');
        let useCurrent = (initialDate === '' ? true : false);

        setup.display = {
            components: {
                clock: false,
                date: ($(element).parent().hasClass('input-date') ? true : false),
                calendar: true,
            },
            buttons: {
                clear: true,
                today: true,
            },
            keepOpen: false,
            viewMode: ($(element).parent().hasClass('input-date') ? 'calendar' : 'months')
        };

        if(!useCurrent && initialDate !== '') {
            setup.defaultDate = moment(initialDate, 'YYYY-MM-DD').add(12, 'hour').format();
        }

        if(minDate !== '') {
            setup.restrictions = {
                minDate: moment(minDate, 'YYYY-MM-DD', true).format()
            };
        }

        if(maxDate !== '') {
            setup.restrictions = {
                maxDate: moment(maxDate, 'YYYY-MM-DD', true).add(1, 'day').format()
            };
        }

        setupDatePicker(element, setup, format);
    });

    /*
    let picker1, picker2;
    $('.input-daterange input').each((i, element) => {
        let format = (typeof $(element).data('format') !== 'undefined' ? $(element).data('format') : 'YYYY-MM-DD');
        if(i == 0) {
            picker1 = (minDate !== null
                ? setupDatePicker(element, '', false, true, format, minDate)
                : setupDatePicker(element, '', false, true, format));
        }
        if(i== 1) {
            picker2 = (minDate !== null
                ? setupDatePicker(element, '', false, false, format, minDate)
                : setupDatePicker(element, '', false, false, format));

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
        let format = (typeof $(element).data('format') !== 'undefined' ? $(element).data('format') : 'YYYY-MM-DD');
        if(i == 0) {
            picker1 = (minDate !== null
                ? setupDatePicker(element, '', true, false, format, minDate)
                : setupDatePicker(element, '', true, false, format)
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
    });*/
}

const setupDatePicker = (element, setup, format) => {
    let picker = new TempusDominus(element, setup);

    picker.subscribe(Namespace.events.hide, (event) => {
        $(element).trigger('change');
    });

    picker.dates.formatInput = (date) => {
        return date !== null ? moment(date).format(format) : null;
    }

    if(setup.defaultDate) {
        let parseDate = picker.dates.parseInput(new Date(setup.defaultDate));
        picker.dates.setValue(parseDate, picker.dates.lastPickedIndex);
    }
    
    /*
    let picker = new TempusDominus(element, {
        useCurrent,
        localization: {
            locale: 'es',
            dayViewHeaderFormat: { month: 'long', year: 'numeric' },
        },
        display: {
            components: {
                // clock: clock,
                calendar: true
            },
            buttons: {
                clear: true,
                today: true,
            },
            keepOpen: false,
            viewMode: 'months'
        },
        // restrictions: {
        //     minDate: minDate,
        //     maxDate: maxDate
        // },
    });

    // picker.clear();

    picker.dates.formatInput = (date) => {
        return (date !== undefined && date !== null
            ? clock ? moment(date, 'h:mm:ss A').format('YYYY-MM-DD HH:mm') : moment(date).format(format)
            : null
        );
    };

    if(initialDate !== '') {
        picker.dates.setValue(new tempusDominus.DateTime(moment(initialDate).add(1, 'days').format(format)))
    }

    picker.subscribe(tempusDominus.Namespace.events.hide, (event) => {
        if(picker.dates.lastPicked !== undefined) {
            $(element).trigger('change');
        }
    });

    $(element).focus(() => {
        picker.show();
    });

    $(element).blur(() => {
        // $(element).trigger('change');
        // picker.hide();
    });

    */
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
                .html(`
                    <h6 class="alert-heading fw-bold">Por favor corrija los siguientes campos:</h6>
                    <ol>${errors}</ol>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                `)
                .fadeTo(10000, 1000)
                .slideUp(1000, function(){
                    $(`#${modal} .alert-danger`).slideUp(1000);
                });
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
                $('.money').inputmask(formatCurrency);
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
const updateTextAreaSize = () => {
    $('#table_items textarea').each(function(index, element){
        element.style.height = "1px";
        element.style.height = `${25 + element.scrollHeight}px`;
    });
}

const responsiveTd = (id, i) => {
    let clase = ((i + 1) % 2 == 0 ? 'bg-light' : '');
    if ($(window).width() <= 768) {
        $(`#${id} td:nth-child(${(i + 1)})`).addClass(clase);
        $(`#${id} td.btn-delete-item > i`).addClass('text-white');
        $(`#${id} td.btn-delete-item`).addClass('btn bg-danger text-white').removeClass(clase);
        
    } else {
        $(`#${id} td:nth-child(${(i + 1)})`).removeClass('bg-light');
        $(`#${id} td.btn-delete-item > i`).removeClass('text-white');
        $(`#${id} td.btn-delete-item`).removeClass('btn bg-danger text-white');
    }
}

window.table = () => {
    $('.table-responsive-stack').each(function (i) {
        let id = $(this).attr('id');
        $(`#${id} .table-responsive-stack-thead`).remove();
        $(this).find("th").each(function(i) {
            $(`#${id} td:nth-child(${(i + 1)})`).prepend(`<span class="table-responsive-stack-thead">${$(this).text()}</span>`);
            $('.table-responsive-stack-thead').hide();
        });
    });
}

window.flexTable = () => {
    if ($(window).width() < 768) {
        $(".table-responsive-stack").each(function (i) {
            let id = $(this).attr('id');
            $(this).find(".table-responsive-stack-thead").show();
            $(this).find('thead').hide();
            $(this).find("th").each(function(i) {
                responsiveTd(id, i);
            });
        });
        // window is less than 768px
    } else {
        $(".table-responsive-stack").each(function (i) {
            let id = $(this).attr('id');
            $(this).find(".table-responsive-stack-thead").hide();
            $(this).find('thead').show();
            $(this).find("th").each(function(i) {
                responsiveTd(id, i);
            });
        });
    }
    // flextable
}      

flexTable();
window.onresize = function(event) {
    flexTable();
};
// document ready

window.drawItems = (edit = true) => {
    // type: tipo de item: mano de obra, transporte o suministro
    // item: ítem de la lista de precios
    if(!edit) {
        $('#th-delete').remove();
    }

    $.each(carrito, (type, item) => {
        if(typeof item !== 'undefined' && carrito[type]['update'] === false) {
            let total = 0;

            $.each(item, (index, element) => {
                if(typeof element !== 'undefined' && typeof element === 'object') {
                    if(!$(`#tr_${type}_${index}`).length) {
                        let classname = `${$(`#caret_${type}`).hasClass(showIcon) ? 'show' : ''}`;
                        $(`
                            <tr id="tr_${type}_${index}" class="tr_cotizacion collapse ${classname} item_${type}">
                                <td class="col-1 my-auto">
                                    <input type='hidden' name="id_tipo_item[]" value="${type}" />
                                    <input type='hidden' name="id_lista_precio[]" value="${index}" />
                                    <input type="text" class="form-control text-md-center text-end text-uppercase border-0" id="item_${index}" value="${element['item']}" disabled>
                                </td>
                                <td class="col-4 my-auto">
                                    <textarea class="form-control border-0" rows="2" name="descripcion_item[]" id="descripcion_item_${index}" required ${edit ? '' : 'disabled'}>${element['descripcion']}</textarea>
                                </td>
                                <td class="col-1 my-auto">
                                    <input type="text" class="form-control text-md-center text-end border-0" data-toggle="tooltip" title="${element['unidad']}" name="unidad[]" id="unidad_${index}" value="${element['unidad']}" ${edit ? '' : 'disabled'}>
                                </td>
                                <td class="col-1 my-auto">
                                    <input type="number" min="0" class="form-control text-end border-0 txt-cotizaciones" name="cantidad[]" id="cantidad_${index}" value="${element['cantidad']}" required ${edit ? '' : 'disabled'}>
                                </td>
                                <td class="col-2 my-auto">
                                    <input type="text" class="form-control text-end border-0 txt-cotizaciones money" data-toggle="tooltip" title="${Inputmask.format(element['valor_unitario'], formatCurrency)}" name="valor_unitario[]" id="valor_unitario_${index}" value="${element['valor_unitario']}" required ${edit ? '' : 'disabled'}>
                                </td>
                                <td class="col-2 my-auto">
                                    <input type="text" class="form-control text-end border-0 txt-cotizaciones money" name="valor_total[]" id="valor_total_${index}" value="${element['valor_total']}" disabled>
                                </td>
                                ${edit == true
                                    ? `<td id="${type}_${index}" class="text-center col-1 my-auto btn-delete-item"><i id="${type}_${index}" class="fa-solid fa-trash-can text-danger fs-5 fs-bold btn btn-delete-item"></i></td>`
                                    : ``
                                }
                            </tr>
                        `).insertAfter(`#tr_${type}`);
                        $('.money').inputmask(formatCurrency);
                    } else {
                        $(`#tr_${type}_${index} #valor_total_${index}`).val(element['valor_total']);
                    }
    
                    total += parseFloat(element['valor_total'], 2);
                }
                carrito[type]['update'] = true;
            });

            $(`#lbl_${type}`).text(Inputmask.format(total, formatCurrency));
        }
    });

    let iva = parseFloat(
        $('#iva option:selected').length > 0
            ? $('#iva option:selected').text().trim().replace('IVA ', '').replace('%', '')
            : $('#iva').val().replace('IVA ', '').replace('%', '')
        , 0
    );
    let total_material = parseFloat($('.lbl_total_material').text().replace(regexCurrencyToFloat, ""), 2);
    let total_suministro = parseFloat($('.lbl_total_mano_obra').text().replace(regexCurrencyToFloat, ""), 2);
    let total_transporte = parseFloat($('.lbl_total_transporte').text().replace(regexCurrencyToFloat, ""), 2);

    let total_sin_iva = (total_material + total_suministro + total_transporte);
    let total_iva = ((total_sin_iva * iva) / 100);
    let total_con_iva = (total_sin_iva + total_iva);

    $('#lbl_total_sin_iva').text(Inputmask.format(total_sin_iva, formatCurrency));
    $('#lbl_total_iva').text(Inputmask.format(total_iva, formatCurrency));
    $('#lbl_total_con_iva').text(Inputmask.format(total_con_iva, formatCurrency));

    setTimeout(() => {
        updateTextAreaSize();
    }, 100);
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
            carrito[$(item).data('type')]['update'] = false;
            carrito[$(item).data('type')][$(item).val()] = getItem(item);
            drawItems();
            table();
            flexTable();
        }
    });
}

$('body').tooltip({
    selector: '[data-toggle="tooltip"]'
});

window.showLoader = function(show = false) {
    $('#lds-loader').toggle(show);
}

window.setupSelect2 = function(modal = '') {
    modal = (modal !== '' ? `#${modal} ` : '');

    $(`${modal}select`).each((index, element) => {
        let minimumInputLength = $(element).data('minimuminputlength');
        let maximumSelectionLength = $(element).data('maximumselectionlength');
        let closeOnSelect = $(element).data('closeonselect');

        closeOnSelect = (typeof closeOnSelect !== 'undefined' ? (closeOnSelect === 'true' ? true : false) : true);

        $(element).select2({
            dropdownParent: modal,
            minimumInputLength,
            maximumSelectionLength,
            closeOnSelect,
            language: {
                inputTooShort: function (args) {
                    var remainingChars = args.minimum - args.input.length;
                    var message = 'Por favor ingrese ' + remainingChars + ' o más carácteres';
                    return message;
                },
                noResults: function() {
                    return 'No existen resultados';
                },
                maximumSelected: function (args) {
                    var t = `Puedes seleccionar hasta ${args.maximum} ítem`;
                    args.maximum != 1 && (t += "s");
                    return t;
                }
            },
        });
    });

    $('.select2-selection').addClass('form-control');
    $('.select2-selection__rendered').data('toggle', 'tooltip');
}

$(document).ready(function() {
    AOS.init();

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
            .html(
                `<h6 class="alert-heading fw-bold">${title}: </h6>
                <ul>${errors}</ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                `
            )
            .fadeTo(10000, 1000)
            .slideUp(1000, function(){
                $(`.alert-danger`).slideUp(1000);
            });;
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
                closeConnection();
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

    $('#input_file').change(function(e){
        $('#lbl_input_file')
            .text(typeof e.target.files[0] !== 'undefined' ? e.target.files[0].name : '');
        
        if(typeof e.target.files[0] !== 'undefined') {
            $('#lbl_input_file').addClass('file_selected');

            Swal.fire({
                icon: 'question',
                title: 'Subir archivo al servidor?',
                text: e.target.files[0].name,
                showCancelButton: true,
                confirmButtonText: 'Continuar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                confirmButtonColor: '#fe0115c4',
                cancelButtonColor: '#6e7d88',
            }).then((result) => {
                if(result.isConfirmed) {
                    showLoader(true);
                    $('#input_file').parent().submit();
                }
            });
        } else {
            $('#lbl_input_file').removeClass('file_selected');
        }
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
            toggle.addEventListener('click', () => {
                // show navbar
                nav.classList.toggle('show-panel');
                $('.nav_list *[title]').tooltip(`${$('#nav-bar').hasClass('show-panel') ? 'disable' : 'enable'}`);
                // change icon
                toggle.classList.toggle('fa-xmark');
                // add padding to body
                bodypd.classList.toggle('body-pd');
                // add padding to header
                headerpd.classList.toggle('body-pd');
            });
        }
    }
    
    showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header');
});

$(document).on('click', '.modal-form', function(e) {
    e.preventDefault();
    handleModal($(this));
});

const updateGrid = (url, data) => {
    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        beforeSend: () => {
            showLoader(true);
        }
    }).done(function(view) {
        $('#container').html(view);
    }).always(function() {
        showLoader(false);
        setupSelect2();
    });
}

const getGrid = (url, id_form) => {
    $.ajax({
        url: `${url}/grid`,
        method: 'POST',
        data: $(`#${id_form}`).serialize(),
        beforeSend: function() {
            showLoader(true);
        }
    }).done(function(view) {
        $(`#${id_form}`).parent().html(view);
    }).always(function() {
        showLoader(false);
        setupSelect2();
    });
};

$(document).on('submit', `.search_form`, function(e){
    e.preventDefault();
});

$(document).on('change', '.search_form', function() {
    let id_form = $(this).closest('form').attr('id');
    let url = id_form.split('_');

    getGrid(url[1], id_form);
});

$(document).on('click', '.page-item', function(e) {
    e.preventDefault();

    if(!$(this).hasClass('disabled')) {
        $('.page-item').removeClass('active');
        $(this).addClass('active');
        let id_form = $(this).closest('form').attr('id');
        let page = $.urlParam('page', $(this).children().attr('href'));
        let url = id_form.split('_');

        if(typeof page === 'string') {
            $(`#${id_form} > #page`).val(page);
            getGrid(url[1], id_form);
        }
    }
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

$(document).on("click", ".btn-download", function(e) {
    e.preventDefault();
    
    let action = $(this).data('route');
    $.ajax({
        xhrFields: {
            responseType: 'blob',
        },
        type: 'GET',
        url: `${action}/template`,
        beforeSend: () => {
            showLoader(true);
        },
        success: (result, status, xhr) => {
            var disposition = xhr.getResponseHeader('content-disposition');
            var matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
            var filename = (matches != null && matches[1] ? matches[1] : 'Template.xlsx').replace(/"/g,'');

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

$(document).on('click', '#btn_upload', function() {
    $('#input_file').trigger('click');
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

    carrito[id_tr[0]]['update'] = false;
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

        carrito[id_tr[1]]['update'] = false;

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

$(document).on('change', '#id_cliente_cotizacion', function() {    
    if(typeof $(this).closest('form').attr('action') !== 'undefined' && $(this).closest('form').attr('action').indexOf(url_cotizacion) > -1) {
        $('#table-cotizaciones').addClass('d-none');

        $('#id_estacion').empty();
        $('#id_estacion').append(`<option value=''>Elegir punto ínteres</option>`);
        let id_cliente = $(this).find(':selected').data('id_cliente');

        if(id_cliente !== '') {
            $('#table-cotizaciones').removeClass('d-none');

            $(`.tr_cotizacion`).each((index, item) => {
                let action = new String($(item).data('action')).split('/');
                action[action.length - 1] = id_cliente;

                action = action.join('/');
                $(item).data('action', action);
            });

            $.ajax({
                url: `sites/${id_cliente}/get_puntos_interes_client`,
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

$(document).on('change', '#iva', function() {
    if($(this).closest('form').attr('action').indexOf(url_cotizacion) > -1) {
        $.each(carrito, (type, item) => {
            if(typeof item !== 'undefined' && carrito[type]['update'] === false) {
                carrito[type]['update'] = true;
            }
        });

        drawItems();
    }
});

let showIcon = 'fa-caret-down';
let hideIcon = 'fa-caret-up';

$(document).on('click', '.show-more', function() {    
    setTimeout(() => {
        $(this).toggleClass(`${showIcon} ${hideIcon}`);
    }, 100);
});

$(document).on('click', '.btn-quote', function(e) {
    e.preventDefault();

    let action = '';
    let title = '';
    let text = '';
    let buttonColor = '';
    let confirmButtonColor = '';
    let confirmButtonText = '';

    let button = $(this);
    let form = button.closest('form');

    let data = new FormData(form[0]);

    switch ($(this).attr('id')) {
        case 'btn-check-quote':
            action = 'check';
            title = `<h2 class='fw-bold text-success'>Aprobar cotización</h2>`;
            text = `¿Seguro quiere aprobar está cotización?`;
            confirmButtonColor = `var(--bs-success)`;
            confirmButtonText = `Sí, aprobar cotización`;
            break;
        case 'btn-deny-quote':
            action = 'deny';
            title = `<h2 class='fw-bold text-danger'>Regresar cotización</h2>`;
            text = `¿Seguro quiere regresar está cotización?`;
            confirmButtonColor = `var(--bs-danger)`;
            confirmButtonText = `Sí, regresar cotización`;
            break;
        case 'btn-wait-quote':
            action = 'wait';
            title = `<h2 class='fw-bold text-success'>Cotización pendiente aprobación</h2>`;
            text = `¿Seguro quiere dejar la cotización en pendiente aprobación?`;
            confirmButtonColor = `var(--bs-success)`;
            confirmButtonText = `Sí, dejar en Cotización pendiente aprobación`;
            break;
        case 'btn-aprove-quote':
            action = 'aprove';
            title = `<h2 class='fw-bold text-success'>Cotización aprobada cliente</h2>`;
            text = `¿Seguro quiere dejar la cotización en aprobada cliente?`;
            confirmButtonColor = `var(--bs-success)`;
            confirmButtonText = `Sí, dejar en Cotización aprobada cliente`;
            break;
        case 'btn-reject-quote':
            action = 'reject';
            title = `<h2 class='fw-bold text-danger'>Cotización rechazada</h2>`;
            text = `¿Seguro quiere dejar la cotización rechazada?`;
            confirmButtonColor = `var(--bs-danger)`;
            confirmButtonText = `Sí, dejar en Cotización rechazada`;
            break;
        case 'btn-cancel-quote':
            action = 'cancel';
            title = `<h2 class='fw-bold text-danger'>Cotización cancelada</h2>`;
            text = `¿Seguro quiere cancelar la cotización?`;
            confirmButtonColor = `var(--bs-danger)`;
            confirmButtonText = `Sí, cancelar cotización`;
            break;
        case 'btn-send-quote':
            action = 'send';
            break;
        default:
            break;
    }

    if(action === '') return false;

    if(action === 'send') {
        $.ajax({
            xhrFields: {
                responseType: 'blob',
            },
            type: 'GET',
            url: `${url_cotizacion}/exportQuote?quote=${$('#id_cotizacion').val()}`,
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
        return false;
    }

    if(action === 'cancel') {
        $('#comentario').removeClass('is-invalid');
        if($('#comentario').val().trim() === '') {
            $('#comentario').addClass('is-invalid');
            return false;
        }
    }

    data.append('action', action);
    data.delete('_method');

    Swal.fire({
        icon: 'question',
        title,
        text,
        reverseButtons: true,
        showCancelButton: true,
        confirmButtonColor,
        confirmButtonText,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if(result.isConfirmed) {
            $.ajax({
                url: `${url_cotizacion}/${$('#id_cotizacion').val()}/handleQuote`,
                method: 'POST',
                data,
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function() {
                    $('.alert-success, .alert-danger').fadeOut().html('');
                    showLoader(true);
                },
                success: function(response, status, xhr) {
                    if(response.success) {
                        $('#modalForm').modal('hide');
                        if(action === 'send') {
                            window.open(`${url_cotizacion}/exportQuote?quote=${$('#id_cotizacion').val()}`, '_blank');  
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Cambio realizado',
                            text: response.success,
                            confirmButtonColor: 'var(--bs-primary)'
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: response.error,
                            confirmButtonColor: 'var(--bs-primary)'
                        });
                    }
                },
                error: function(response) {
                    let errors = '';
                    $.each(response.responseJSON.errors, function(i, item){
                        errors += `<li>${item}</li>`;
                    });
                    $('.alert-danger')
                        .html(`<h6 class="alert-heading fw-bold">Por favor corrija los siguientes campos:</h6> <ol>${errors}</ol>`)
                        .fadeTo(10000, 1000)
                        .slideUp(1000, function(){
                            $(`.alert-danger`).slideUp(1000);
                        });
                }
            }).always(function () {
                getGrid(url_cotizacion, form_cotizacion);
                showLoader(false);
            });

            return false;
        }
    });
});