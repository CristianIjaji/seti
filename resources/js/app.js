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
import { TempusDominus, Namespace } from '@eonasdan/tempus-dominus';
import { random } from 'lodash';

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

let url_actividad = 'activities';
let form_actividad = 'form_activities';

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
        $(element).focus();
        $(element).trigger('change');
    });

    picker.dates.formatInput = (date) => {
        return date !== null ? moment(date).locale('es').format(format) : null;
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

const procesarErrores = (modal, title, errores) => {
    let errors = '';
    $.each(errores, function(i, item){
        errors += `<li>${item}</li>`;
    });
    $(`#${modal} .alert-danger`).html(
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
    });
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
                    if(typeof reload === 'undefined' || reload.toString() !== 'false') {
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
            procesarErrores(modal, 'Por favor corrija los siguientes campos:', response.responseJSON.errors);
        }
    }).always(function () {
        showLoader(false);
    });
}

const createModal = () => {
    // Se genera id aleatorio
    let id = new Date().getUTCMilliseconds();

    let modal = `
        <div class="modal fade" id="${id}" tabindex="-1" aria-labelledby="modalTitle-${id}" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header border-bottom border-2">
                        <h5 class="modal-title fw-bold" id="modalTitle-${id}"></h5>
                        <i class="fa-solid fa-xmark fs-3 px-2" data-bs-dismiss="modal" style="cursor: pointer"></i>
                    </div>
                    <div class="modal-body px-4 pt-4"></div>
                </div>
            </div>
        </div>
    `;

    $('body').append(modal);
    return id;
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
    modal = createModal();

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
                html: true,
                selector: '[data-toggle="tooltip"]'
            });

            $('[data-toggle="tooltip"]').on('click', function () {
                $(this).tooltip('hide')
            });

            setTimeout(() => {
                $(`#${modal}`).focus();
                $('.money').inputmask(formatCurrency);
                setupSelect2(`${modal}`);

                updateThemeColor();
            }, 300);
        }).always(function() {
            showLoader(false);
        });
    }

    $(`#${modal} .modal-dialog`).removeClass().addClass(`modal-dialog modal-dialog-centered ${size}`);
    $(`#${modal} .modal-title`).html(title);
    $(`#${modal} .modal-header`).attr('class', 'modal-header border-bottom border-2');
    $(`#${modal} .modal-header`).addClass(headerClass);

    $(`#${modal}`).modal('handleUpdate');
    $(`#${modal}`).modal('show');
}

const getSwalConfig = (icon, title, text, reverseButtons, showCancelButton, confirmButtonColor, confirmButtonText, cancelButtonText = 'Cancelar') => {
    return {
        icon,
        title,
        text,
        reverseButtons,
        showCancelButton,
        confirmButtonColor,
        confirmButtonText,
        cancelButtonText
    };
}

const downloadExcel = (url, data, defaultName) => {
    $.ajax({
        xhrFields: {
            responseType: 'blob',
        },
        type: 'GET',
        url: url,
        data: data,
        beforeSend: () => {
            showLoader(true);
        },
        success: (result, status, xhr) => {
            let disposition = xhr.getResponseHeader('content-disposition');
            let matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
            let filename = (matches != null && matches[1] ? matches[1] : defaultName).replace(/"/g,'');

            // The actual download
            let blob = new Blob([result], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });

            let link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            link.text = filename;

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }).always(() => {
        showLoader(false);
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
        updateThemeColor();
    });
};

window.carrito = [];
window.updateTextAreaSize = () => {
    $('.resize-textarea').each(function(index, element){
        element.style.height = "1px";
        element.style.height = `${25 + element.scrollHeight}px`;
    });
}

const responsiveTd = (id, i) => {
    let clase = 'border';//((i + 1) % 2 == 0 ? 'bg-light' : '');
    if ($(window).width() <= 768) {
        $(`#${id} td:nth-child(${(i + 1)})`).removeClass('border-0').addClass(clase);
        $(`#${id} td.td-delete > span > i`).addClass('text-white');
        $(`#${id} td.td-delete`).addClass('btn bg-danger text-white').removeClass(clase);
        
    } else {
        $(`#${id} td:nth-child(${(i + 1)})`).removeClass('bg-light').addClass('border-0');
        $(`#${id} td.td-delete > span > i`).removeClass('text-white');
        $(`#${id} td.td-delete`).removeClass('btn bg-danger text-white');
        
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
            $(this).find('tr.show').each(function(i) {
                $(`#${id} tr:nth-child(${(i + 1)})`).addClass('border border-light border-rounded');
            });
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
            $(this).find('tr.show').each(function(i) {
                $(`#${id} tr:nth-child(${(i + 1)})`).removeClass('border border-light border-rounded');
            });
            $(this).find("th").each(function(i) {
                responsiveTd(id, i);
            });
        });
    }
}      

window.onresize = function(event) {
    flexTable();
};

window.drawItems = (edit = true, tipo_carrito, type_item, id_item) => {
    // type: tipo de item: mano de obra, transporte o suministro
    // item: ítem de la lista de precios
    if(!edit) {
        $('#th-delete').remove();
    }

    if(typeof carrito[tipo_carrito][type_item][id_item] !== 'undefined') {
        let element = carrito[tipo_carrito][type_item][id_item];
        let tooltip = (edit === true ? 'data-toggle="tooltip"' : '');

        if(typeof element !== 'undefined' && typeof element === 'object') {
            if(!$(`#tr_${type_item}_${id_item}`).length) {
                let classname = `${$(`#caret_${type_item}`).hasClass(showIcon) ? 'show' : ''}`;
                $(`
                    <tr id="${tipo_carrito}_${type_item}_${id_item}" class="border-bottom collapse ${classname} item_${type_item} detail-${type_item}">
                        <td class="col-1 my-auto border-0">
                            <input type='hidden' name="id_tipo_item[]" value="${type_item}" />
                            <input type='hidden' name="id_lista_precio[]" value="${id_item}" />
                            <input type="text" class="form-control text-md-center text-end text-uppercase border-0" id="item_${id_item}" value="${element['item']}" disabled>
                        </td>
                        <td class="col-4 my-auto border-0">
                            <textarea class="form-control border-0 resize-textarea" rows="2" name="descripcion_item[]" id="descripcion_item_${id_item}" required ${edit ? '' : 'disabled'}>${element['descripcion']}</textarea>
                        </td>
                        <td class="col-1 my-auto border-0">
                            <input type="text" class="form-control text-md-start text-end border-0" ${tooltip} title="${element['unidad']}" name="unidad[]" id="unidad_${id_item}"
                                value="${element['unidad']}" ${edit ? '' : 'disabled'}>
                        </td>
                        <td class="col-1 my-auto border-0">
                            <input type="number" min="1" data-id-tr="${tipo_carrito}_${type_item}_${id_item}"
                                class="form-control text-end border-0 txt-totales" name="cantidad[]" id="cantidad_${id_item}" value="${element['cantidad']}" required ${edit ? '' : 'disabled'}>
                        </td>
                        <td class="col-2 my-auto border-0">
                            <input type="text" data-id-tr="${tipo_carrito}_${type_item}_${id_item}"
                                class="form-control text-end border-0 txt-totales money" ${tooltip} title="${Inputmask.format(element['valor_unitario'], formatCurrency)}" name="valor_unitario[]"
                                id="valor_unitario_${id_item}" value="${element['valor_unitario']}" required ${edit ? '' : 'disabled'}>
                        </td>
                        <td class="col-2 my-auto border-0">
                            <input type="text" data-id-tr="${tipo_carrito}_${type_item}_${id_item}" class="form-control text-end border-0 txt-totales txt_total_item_${type_item} money"
                                name="valor_total[]" id="valor_total_${id_item}" value="${element['valor_total']}" disabled>
                        </td>
                        ${edit == true
                            ? `<td class="text-center col-1 my-auto border-0 td-delete btn-delete-item" ${tooltip} title='Quitar ítem' data-id-tr="${tipo_carrito}_${type_item}_${id_item}"><span class="btn btn-delete-item" data-id-tr="${tipo_carrito}_${type_item}_${id_item}"><i class="fa-solid fa-trash-can text-danger fs-5 fs-bold"></i></span></td>`
                            : ``
                        }
                    </tr>
                `).insertAfter($(`.detail-${type_item}`).last());
                $('.money').inputmask(formatCurrency);
            } else {
                $(`#tr_${type_item}_${id_item} #valor_total_${id_item}`).val(element['valor_total']);
            }
        }
    }

    setTimeout(() => {
        updateTextAreaSize();
    }, 100);
}

const totalItemType = (tipo_carrito, type) => {
    let total = 0;
    let items = 0;
    if(typeof carrito[tipo_carrito][type] !== 'undefined') {
        $.each(carrito[tipo_carrito][type], (item, valores) => {
            items++;
            if($.isNumeric(valores['valor_total'])) {
                total += parseFloat(valores['valor_total'], 2);
            }
        });
    }

    return {
        total,
        items
    };
}

window.totalCarrito = (tipo_carrito) => {
    let iva = parseFloat(
        $('#iva option:selected').length > 0
            ? $('#iva option:selected').text().trim().replace('IVA ', '').replace('%', '')
            : $('#iva').val().replace('IVA ', '').replace('%', '')
        , 0
    );

    let id_materiales = ($(`#${tipo_carrito} .lbl_total_material`).length ? $(`#${tipo_carrito} .lbl_total_material`).attr('id').replace('lbl_', '') : 0);
    let id_mano_obra = ($(`#${tipo_carrito} .lbl_total_mano_obra`).length ? $(`#${tipo_carrito} .lbl_total_mano_obra`).attr('id').replace('lbl_', '') : 0);
    let id_transporte = ($(`#${tipo_carrito} .lbl_total_transporte`).length ? $(`#${tipo_carrito} .lbl_total_transporte`).attr('id').replace('lbl_', '') : 0);

    let total_material = totalItemType(tipo_carrito, id_materiales);
    let total_suministro = totalItemType(tipo_carrito, id_mano_obra);
    let total_transporte = totalItemType(tipo_carrito, id_transporte);

    let total_sin_iva = (total_material.total + total_suministro.total + total_transporte.total);
    let total_iva = ((total_sin_iva * iva) / 100);
    let total_con_iva = (total_sin_iva + total_iva);

    $(`#${tipo_carrito} #lbl_${id_materiales}`).text(`$ ${Inputmask.format(total_material.total, formatCurrency)}`);
    $(`#${tipo_carrito} #lbl_${id_mano_obra}`).text(`$ ${Inputmask.format(total_suministro.total, formatCurrency)}`);
    $(`#${tipo_carrito} #lbl_${id_transporte}`).text(`$ ${Inputmask.format(total_transporte.total, formatCurrency)}`);

    $(`#${tipo_carrito} #lbl_total_items_materiales`).text(`Total Ítems: ${total_material.items}`);
    $(`#${tipo_carrito} #lbl_total_items_mano_obra`).text(`Total Ítems: ${total_suministro.items}`);
    $(`#${tipo_carrito} #lbl_total_items_transporte`).text(`Total Ítems: ${total_transporte.items}`);

    $(`#${tipo_carrito}_lbl_total_sin_iva`).text(`$ ${Inputmask.format(total_sin_iva, formatCurrency)}`);
    $(`#${tipo_carrito}_lbl_total_iva`).text(`$ ${Inputmask.format(total_iva, formatCurrency)}`);
    $(`#${tipo_carrito}_lbl_total_con_iva`).text(`$ ${Inputmask.format(total_con_iva, formatCurrency)}`);

    if($('#cupo').length > 0) {
        let total_sin_transporte = total_material.total + total_suministro.total;
        let total_iva_sin_transporte = ((total_sin_transporte * iva) / 100)
        $('#cupo').val(total_sin_transporte + total_iva_sin_transporte);
    }
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

const addItems = (tipo_carrito, items) => {
    $.each(items, (index, item) => {
        if(typeof carrito[tipo_carrito][$(item).data('type')] === 'undefined') {
            carrito[tipo_carrito][$(item).data('type')] = {};
        }

        if(typeof carrito[tipo_carrito][$(item).data('type')][$(item).val()] === 'undefined') {
            carrito[tipo_carrito][$(item).data('type')][$(item).val()] = getItem(item);
            drawItems(true, tipo_carrito, $(item).data('type'), $(item).val());
            totalCarrito(tipo_carrito);
            table();
            flexTable();
        }
    });
}

const fnc_totales = (id) => {
    let id_tr = new String(id).split('_');
    if(typeof carrito[id_tr[0]][id_tr[1]][id_tr[2]] !== 'undefined') {
        let id_row = `${id_tr[0]}_${id_tr[1]}_${id_tr[2]}`;

        // // let descripcion = $(`#${id_row} #descripcion_${id_tr[2]}`).val();
        let cantidad = parseFloat($.isNumeric($(`#${id_row} #cantidad_${id_tr[2]}`).val()) ? $(`#${id_row} #cantidad_${id_tr[2]}`).val() : 0);
        let valor_unitario = parseFloat($.isNumeric($(`#${id_row} #valor_unitario_${id_tr[2]}`).val().replace(regexCurrencyToFloat, "")) ? $(`#${id_row} #valor_unitario_${id_tr[2]}`).val().replace(regexCurrencyToFloat, "") : 0);
        let valor_total = (cantidad) * (valor_unitario);

        // carrito[id_tr[0]][id_tr[1]][id_tr[2]]['descripcion'] = descripcion;
        carrito[id_tr[0]][id_tr[1]][id_tr[2]]['cantidad'] = cantidad;
        carrito[id_tr[0]][id_tr[1]][id_tr[2]]['valor_unitario'] = valor_unitario;
        carrito[id_tr[0]][id_tr[1]][id_tr[2]]['valor_total'] = valor_total;

        $(`#${id_tr[0]} #valor_total_${id_tr[2]}`).val(valor_total);

        totalCarrito(id_tr[0]);
    }
}

$('body').tooltip({
    html: true,
    selector: '[data-toggle="tooltip"]'
});

window.showLoader = function(show = false) {
    $('#lds-loader').toggle(show);
}

window.setupSelect2 = function(modal = '') {
    modal = (modal !== '' ? `#${modal} ` : '');

    $(`${modal}select`).each((index, element) => {
        let dir = (typeof $(element).data('dir') !== 'undefined' ? $(element).data('dir') : 'ltr');
        let minimumInputLength = $(element).data('minimuminputlength');
        let maximumSelectionLength = $(element).data('maximumselectionlength');
        let closeOnSelect = $(element).data('closeonselect');

        closeOnSelect = (typeof closeOnSelect !== 'undefined' ? (closeOnSelect === 'true' ? true : false) : true);

        $(element).select2({
            dir: dir,
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

window.RandomString = (length = 10, uc = false, n = false, sc = false) => {
    let source = '123456789';
    let str = '';

    if(!uc) {
        source += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    if(n) {
        source += 'abcdefghijklmnopqrstuvwxyz';
    }
    if(sc) {
        source += '|@#~$%()=^*+[]{}-_';
    }
    if(length > 0) {
        for (let index = 0; index < length; index++) {
            str += source[random(0, source.length - 1)];
        }
    }

    return str;
}

$(function() {
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
                procesarErrores('login-form', 'Error de inicio de sesión', (typeof response.responseJSON.errors !== 'undefined' ? response.responseJSON.errors : ['Por favor actualice la página']));
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
                } else {
                    $('#input_file').val('');
                }
            });
        } else {
            $('#lbl_input_file').removeClass('file_selected');
        }
    });

    $('[data-toggle="tooltip"]').on('click', function () {
        $(this).tooltip('hide')
    });

    setupSelect2();
    timer();
    datePicker();

    updateThemeColor();
    openMainSubMenu();
});

jQuery(window).ready(function () {
    showLoader(false);
});

const openMainSubMenu = () => {
    let activemenu = 'activemenu';

    $('.submenu_icon').each((i, e) => {
        let menu = document.getElementById($(e).closest('a').data('bs-target').replace('#', ''));
        menu.addEventListener('hide.bs.collapse', event => {
            $(e).removeClass(showIcon).addClass(hideIcon);
            $(e).closest('a').removeClass(activemenu);
        });

        menu.addEventListener('show.bs.collapse', event => {
            $(e).removeClass(hideIcon).addClass(showIcon);
            $(e).closest('a').addClass(activemenu);
        });
    });
}

$(document).on('click', '.modal-form', function(e) {
    e.preventDefault();
    handleModal($(this));
});

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

    let action = $(this).closest('form').attr('action');
    let modal = $(this).closest('.modal').attr('id');
    let reload = $(`#${modal}`).data('reload');
    let form = $(this).closest('form');
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

    downloadExcel(`${action}/export`, data, 'Reporte.xlsx');
});

$(document).on("click", ".btn-download", function(e) {
    e.preventDefault();
    
    let action = $(this).data('route');

    downloadExcel(`${action}/template`, '', 'Template.xlsx');
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
        let tipo_carrito = $(this).data('tipo_carrito');
        addItems(tipo_carrito, $('#lista_items option:selected'));
    }

    let id = $('#btn_add_items').closest('.modal').attr('id');
    $(`#${id}`).modal('hide');
});

$(document).on('click', '#btn_select_quote', function() {
    if($('#lista_items').val() !== '') {
        $('#id_cotizacion_actividad').val($('#lista_items').val());
        let id = $('#btn_select_quote').closest('.modal').attr('id');
        $(`#${id}`).modal('hide');
    }
});

$(document).on('click', '.btn-delete-item', function() {
    if(typeof $(this).data('id-tr') !== 'undefined') {
        let id_tr = $(this).data('id-tr');

        $(`#${id_tr}`).remove();
        id_tr = id_tr.split('_');

        delete carrito[id_tr[0]][id_tr[1]][id_tr[2]];
        totalCarrito(id_tr[0]);

        $('[data-toggle="tooltip"]').on('click', function () {
            $(this).tooltip('hide')
        });
    }
});

$(document).on('keydown', '.txt-totales', function() {
    fnc_totales($(this).data('id-tr'));
});

$(document).on('keyup', '.txt-totales', function() {
    fnc_totales($(this).data('id-tr'));
});

$(document).on('change', '.txt-totales', function() {
    fnc_totales($(this).data('id-tr'));
});

$(document).on('change', '#id_cliente_cotizacion, #id_encargado_cliente', function() {    
    if(typeof $(this).closest('form').attr('action') !== 'undefined') {
        $('#table-cotizaciones').addClass('d-none');

        $('#id_estacion').empty();
        $('#id_estacion').append(`<option value=''>Elegir punto ínteres</option>`);
        let id_cliente = $(this).find(':selected').data('id_cliente');

        if(id_cliente !== '') {
            $('#table-cotizaciones').removeClass('d-none');

            $(`.tr_suministros`).each((index, item) => {
                let action = new String($(item).data('action')).split('/');
                action[5] = id_cliente;

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
        totalCarrito('cotizacion');
    }
});

let showIcon = 'fa-solid fa-angle-down';
let hideIcon = 'fa-solid fa-angle-up';

$(document).on('click', '.show-more', function() {    
    $(this).find('i').toggleClass(`${showIcon} ${hideIcon}`);
});

$(document).on('change', '#estado_seguimiento', function() {
    $('#div_fecha_seguimiento').addClass('d-none');

    if($.inArray($(this).val(), ['btn-reshedule-activity', 'btn-executed-activity']) > -1){
        $('#div_fecha_seguimiento').removeClass('d-none');
    }
});

$(document).on('click', '#btn-quote, #btn-send-quote', function(e) {
    e.preventDefault();

    let action = '';
    let form = $(this).closest('form');

    let data = new FormData(form[0]);
    let cambio = ($(this).attr('id') === 'btn-send-quote'
        ? $(this).attr('id')
        : $('#estado_seguimiento').val()
    );

    let setupSwal = {};
    let url = '';

    switch (cambio) {
        case 'btn-check-quote':
            setupSwal = getSwalConfig(
                'question',
                `<h2 class='fw-bold text-success'>Aprobar cotización</h2>`,
                `¿Seguro quiere aprobar está cotización?`,
                true,
                true,
                `var(--bs-success)`,
                `Sí, aprobar cotización`
            );
            action = 'check';
            url = `${url_cotizacion}/${$('#id_cotizacion').val()}/handleQuote`;
            break;
        case 'btn-deny-quote':
            setupSwal = getSwalConfig(
                'question',
                `<h2 class='fw-bold text-danger'>Regresar cotización</h2>`,
                `¿Seguro quiere regresar está cotización?`,
                true,
                true,
                `var(--bs-danger)`,
                `Sí, regresar cotización`
            );
            action = 'deny';
            url = `${url_cotizacion}/${$('#id_cotizacion').val()}/handleQuote`;
            break;
        case 'btn-wait-quote':
            setupSwal = getSwalConfig(
                'question',
                `<h2 class='fw-bold text-success'>Cotización pendiente aprobación</h2>`,
                `¿Seguro quiere dejar la cotización en pendiente aprobación?`,
                true,
                true,
                `var(--bs-success)`,
                `Sí, dejar en Cotización pendiente aprobación`
            );
            action = 'wait';
            url = `${url_cotizacion}/${$('#id_cotizacion').val()}/handleQuote`;
            break;
        case 'btn-aprove-quote':
            setupSwal = getSwalConfig(
                'question',
                `<h2 class='fw-bold text-success'>Cotización aprobada cliente</h2>`,
                `¿Seguro quiere dejar la cotización en aprobada cliente?`,
                true,
                true,
                `var(--bs-success)`,
                `Sí, dejar en Cotización aprobada cliente`,
            );
            action = 'aprove';
            url = `${url_cotizacion}/${$('#id_cotizacion').val()}/handleQuote`;
            break;
        case 'btn-reject-quote':
            setupSwal = getSwalConfig(
                'question',
                `<h2 class='fw-bold text-danger'>Cliente rechaza cotización</h2>`,
                `¿Seguro quiere dejar la cotización rechazada?`,
                true,
                true,
                `var(--bs-danger)`,
                `Sí, dejar en Cotización rechazada`
            );
            action = 'reject';
            url = `${url_cotizacion}/${$('#id_cotizacion').val()}/handleQuote`;
            break;
        case 'btn-cancel-quote':
            setupSwal = getSwalConfig(
                'question',
                `<h2 class='fw-bold text-danger'>Cotización cancelada</h2>`,
                `¿Seguro quiere cancelar la cotización?`,
                true,
                true,
                `var(--bs-danger)`,
                `Sí, cancelar cotización`
            );
            action = 'cancel';
            url = `${url_cotizacion}/${$('#id_cotizacion').val()}/handleQuote`;
            break;
        case 'btn-send-quote':
            action = 'send';
            break;
        case 'btn-create-activity':
            setupSwal = getSwalConfig(
                'question',
                `<h2 class='fw-bold text-primary'>Crear actividad</h2>`,
                `¿Seguro quiere crear la actividad?`,
                true,
                true,
                `var(--bs-primary)`,
                `Sí, crear actividad`
            );
            action = 'create-activity';
            url = `${url_cotizacion}/${$('#id_cotizacion').val()}/handleQuote`;
            break;
        case 'btn-reshedule-activity':
            setupSwal = getSwalConfig(
                'question',
                `<h2 class='fw-bold text-primary'>Reprogramar actividad</h2>`,
                `¿Seguro quiere reprogramar la actividad?`,
                true,
                true,
                `var(--bs-primary)`,
                `Sí, reprogramar actividad`
            );
            action = 'reshedule-activity';
            url = `${url_actividad}/${$('#id_actividad').val()}/handleActivity`;
            break;
        case 'btn-pause-activity':
            setupSwal = getSwalConfig(
                'question',
                `<h2 class='fw-bold text-primary'>Pausar actividad</h2>`,
                `¿Seguro quiere pausar la actividad?`,
                true,
                true,
                `var(--bs-primary)`,
                `Sí, pausar actividad`
            );
            action = 'pause-activity';
            url = `${url_actividad}/${$('#id_actividad').val()}/handleActivity`;
            break;
        case 'btn-executed-activity':
            setupSwal = getSwalConfig(
                'question',
                `<h2 class='fw-bold text-primary'>Ejecutar actividad</h2>`,
                `¿Seguro quiere ejecutar la actividad?`,
                true,
                true,
                `var(--bs-primary)`,
                `Sí, ejecutar actividad`
            );
            action = 'executed-activity';
            url = `${url_actividad}/${$('#id_actividad').val()}/handleActivity`;
            break;
        default:
            break;
    }

    if(action === '') return false;

    if(action === 'send') {
        downloadExcel(`${url_cotizacion}/exportQuote?quote=${$('#id_cotizacion').val()}`, '', 'Reporte.xlsx');
        return false;
    }

    if(action === 'reshedule-activity') {
        $('#input_fecha').removeClass('is-invalid');
        if($('#input_fecha').val().trim() === '') {
            $('#input_fecha').addClass('is-invalid');
            return false;
        }
    }

    if(jQuery.inArray(action, ['cancel', 'deny', 'reshedule-activity', 'pause-activity']) > -1) {
        $('#comentario').removeClass('is-invalid');
        if($('#comentario').val().trim() === '') {
            $('#comentario').addClass('is-invalid');
            return false;
        }
    }

    data.append('action', action);
    data.delete('_method');

    let modal = $(this).closest('.modal').attr('id');
    let reload = $(`#${modal}`).data('reload');

    Swal.fire(setupSwal).then((result) => {
        if(result.isConfirmed) {
            if(action == 'create-activity') {
                url = 'activities';
                data.append('id_encargado_cliente', $('#id_cliente').val());
                data.append('id_estacion', $('#id_punto_interes').val());
                data.append('id_tipo_actividad', $('#id_tipo_actividad').val());
                data.append('fecha_solicitud', $('#fecha_solicitud').val());
                data.append('valor', $('#valor_actividad').val());
                data.append('id_resposable_contratista', $('#id_resposable_contratista').val());
                data.append('descripcion', $('#descripcion').val());
                data.append('id_estado_actividad', $('#id_estado_actividad').val());
                data.append('id_cotizacion', $('#id_cotizacion').val());
                data.append('observaciones', $('#descripcion').val());
            }

            $.ajax({
                url: url,
                method: 'POST',
                data,
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function() {
                    $(`#${modal} .alert-success, .alert-danger`).fadeOut().html('');
                    showLoader(true);
                },
                success: function(response, status, xhr) {
                    if(response.success) {
                        $(`#${modal}`).modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Cambio realizado',
                            text: response.success,
                            confirmButtonColor: 'var(--bs-primary)'
                        }).then(function() {
                            if(typeof reload === 'undefined' || reload.toString() !== 'false') {
                                location.reload();
                            }
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
                    $(`#${modal} .alert-danger`)
                        .html(`<h6 class="alert-heading fw-bold">Por favor corrija los siguientes campos:</h6> <ol>${errors}</ol>`)
                        .fadeTo(10000, 1000)
                        .slideUp(1000, function(){
                            $(`.alert-danger`).slideUp(1000);
                        });
                }
            }).always(function () {
                let url_grid = (url.indexOf(url_cotizacion) > -1 ? url_cotizacion : url_actividad);
                let form_grid = (url.indexOf(url_cotizacion) > -1 ? form_cotizacion : form_actividad);

                if(action !== 'create-activity') {
                    getGrid(url_grid, form_grid);
                }

                showLoader(false);
            });
        }
    });
});

$(document).on('change', '#id_cotizacion_actividad', function() {
    $('#ot').val('');
    $('#id_encargado_cliente').val('');
    $('#id_estacion').empty();
    $('#id_estacion').append(`<option value=''>Elegir punto ínteres</option>`);
    $('#id_tipo_actividad').val('');
    $('#fecha_solicitud').val('');
    $('#id_resposable_contratista').val('');
    $('#descripcion').val('');
    $('#id_encargado_cliente, #id_tipo_actividad, #id_subsistema').change();

    if($(this).val() !== '') {
        $.ajax({
            url: `quotes/${$(this).val()}/getquote`,
            method: 'GET',
            beforeSend: function() {
                showLoader(true);
            }
        }).done(function(response) {
            $('#ot').val(response.ot);
            $('#id_encargado_cliente').val(response.id_cliente);
            $('#id_encargado_cliente').change();
            $('#id_estacion').append(`<option value='${response.id_estacion}'>${response.tbl_estacion.nombre}</option>`);
            $('#id_estacion').val(response.id_estacion);
            $('#id_estacion').change();
            $('#id_tipo_actividad').val(response.id_tipo_trabajo);
            $('#id_tipo_actividad').change();
            $('#fecha_solicitud').val(response.fecha_solicitud);
            $('#id_resposable_contratista').val(response.id_responsable_cliente);
            $('#id_resposable_contratista').change();
            $('#descripcion').val(response.descripcion);
        }).always(function() {
            showLoader(false);
        });
    }
});

$(document).on('click', '#btn-get-activities', (e) => {
    e.preventDefault();

    let id_cliente = $('#id_cliente').val();
    let id_encargado = $('#id_responsable_cliente').val();
    let id_consolidado = $('#id_consolidado').val();

    if(id_cliente !== '' && id_encargado !== '') {
        $.ajax({
            url: `deals/getActivities`,
            method: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                id_cliente,
                id_encargado,
                id_consolidado,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                showLoader(true);
            }
        }).done(function(view) {
            $('#div_detalle_consolidado').html(view);
        }).always(function() {
            showLoader(false);
        });
    }
});

$(document).on('click', '.delete-item', function() {
    let id = $(this).attr('id');
    if($(`#tr_${id}`).length) {
        $(`#tr_${id}`).remove()
    }
});

$(document).on('click', '.menuToggle', () => {
    $('.menuToggle').toggleClass('active');
});

$(document).on('click', '#btn_download_consolidado', () => {
    let id_consolidado = $('#btn_download_consolidado').data('consolidado');

    downloadExcel(`deals/exportDeal?deal=${id_consolidado}`, '', 'Reporte.xlsx');
});

$(document).on('hide.bs.modal', '.modal', function() {
    if($(this).attr('id').length) {
        $(this).remove();

        $('.modal').each((i, element) => {
            $(element).focus();
        });
    }
});