<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveCotizacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'ot_trabajo' => mb_strtoupper($this->get('ot_trabajo')),
            'id_dominio_estado' => session('id_dominio_cotizacion_creada'),
            'codigo' => mb_strtoupper($this->get('codigo')),
            'valor' => 0,
            // 'valor_total' => 0,
            'valor_unitario' => str_replace(',', '', $this->get('valor_unitario')),
            'valor_total' => str_replace(',', '', $this->get('valor_total')),
            'id_usuareg' => (auth()->guest() ? 1 : auth()->id()),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ot_trabajo' => [
                'string',
                'nullable',
                'max:255',
                Rule::unique('tbl_cotizaciones')->ignore($this->route('quote'))
            ],
            'id_tercero_cliente' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'id_estacion' => [
                'required',
                'exists:tbl_puntos_interes,id_punto_interes'
            ],
            'id_dominio_tipo_trabajo' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'descripcion' => [
                'required',
                'max:255'
            ],
            'fecha_solicitud' => [
                'required',
                'date'
            ],
            'fecha_envio' => [
                'nullable',
                'date'
            ],
            'id_dominio_prioridad' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_dominio_estado' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_tercero_responsable' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'valor' => [
                'nullable'
            ],
            'id_dominio_iva' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'valor_reasignado' => [
                'nullable'
            ],
            'id_usuareg' => [
                'required',
                'exists:tbl_usuarios,id_usuario'
            ],
            'id_dominio_tipo_item' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_item' => [
                'required',
            ],
            'id_item.*' => [
                'exists:tbl_lista_precios,id_lista_precio'
            ],
            'descripcion_item.*' => [
                'required',
                'string',
                'min:1'
            ],
            'unidad.*' => [
                'required',
                'string',
                'min:2'
            ],
            'cantidad.*' => [
                'required',
                'numeric',
                'min:1'
            ],
            'valor_unitario.*' => [
                'required',
                'numeric',
                'min:0'
            ],
            'valor_total.*' => [
                'required',
                'numeric',
                'min:0'
            ]
        ];
    }

    public function messages()
    {
        return [
            'id_tercero_cliente.required' => 'El campo cliente es obligatorio.',
            'id_estacion.required' => 'El campo punto de inter??s es obligatorio.',
            'id_dominio_tipo_trabajo.required' => 'El campo tipo de trabajo es obligatorio.',
            'descripcion.required' => 'El campo descripci??n de la orden es obligatorio.',
            'descripcion.max' => 'El campo descripci??n de la orden no puede ser mayor a 255 car??cteres.',
            'id_dominio_prioridad.required' => 'El campo prioridad es obligatorio.',
            'id_proceso.required' => 'El campo proceso es obligatorio.',
            'id_tercero_responsable.required' => 'El campo contratista es obligatorio.',
            'id_dominio_tipo_item.required' => 'Debe agregar un ??tem a la cotizaci??n.',
            'id_item.required' => 'Debe agregar un ??tem a la cotizaci??n.',
            'descripcion_item.*.required' => 'El campo descripci??n del ??tem es obligarorio.',
            'unidad.*.required' => 'El campo unidad es obligatorio.',
            'unidad.*.min' => 'Por favor validar el nombre de la unidad de los ??tems.',
            'cantidad.*.required' => 'Debe indicar la cantidad de todos los ??tems',
            'cantidad.*.min' => 'La cantidad de los ??tems debe ser mayor a 0',
            'valor_unitario.*.required' => 'Debe indicar el valor unitario de todos los ??tems',
            'valor_unitario.*.min' => 'Por favor validar el valor unitario de los ??tems.'
        ];
    }
}
