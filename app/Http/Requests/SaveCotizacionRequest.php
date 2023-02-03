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
            'valor_total' => 0,
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
                'exists:tbl_lista_precios,id_lista_precio'
            ],
            'id_item.*' => [
                'required',
                'exists:tbl_lista_precios,id_lista_precio'
            ],
            'descripcion_item.*' => [
                'required'
            ],
            'unidad.*' => [
                'required.*'
            ],
            'cantidad.*' => [
                'required',
                'numeric',
                'min:1'
            ],
            'valor_unitario.*' => [
                'required'
            ],
            'valor_total.*' => [
                'required'
            ]
        ];
    }

    public function messages()
    {
        return [
            'id_tercero_cliente.required' => 'El campo cliente es obligatorio.',
            'id_estacion.required' => 'El campo punto de interés es obligatorio.',
            'id_dominio_tipo_trabajo.required' => 'El campo tipo de trabajo es obligatorio.',
            'descripcion.required' => 'El campo descripción de la orden es obligatorio.',
            'descripcion.max' => 'El campo descripción de la orden no puede ser mayor a 255 carácteres.',
            'id_dominio_prioridad.required' => 'El campo prioridad es obligatorio.',
            'id_proceso.required' => 'El campo proceso es obligatorio.',
            'id_tercero_responsable.required' => 'El campo contratista es obligatorio.',
            'id_dominio_tipo_item.required' => 'Debe agregar un ítem a la cotización.',
            'id_item.required' => 'Debe agregar un ítem a la cotización.',
            'descripcion_item.*.required' => 'El campo descripción del ítem es obligarorio.',
            'unidad.*.required' => 'El campo unidad es obligatorio.'
        ];
    }
}
