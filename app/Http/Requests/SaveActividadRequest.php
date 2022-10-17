<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SaveActividadRequest extends FormRequest
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
            'ot' => mb_strtoupper($this->get('ot')),
            'valor' => str_replace(',', '', $this->get('valor')),
            'id_usuareg' => (Auth::id() === null ? 1 : Auth::id()),
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
            'ot' => [
                'string',
                'nullable',
                'max:255',
                Rule::unique('tbl_actividades')->ignore($this->route('activity'))
            ],
            'id_encargado_cliente' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'id_estacion' => [
                'required',
                'exists:tbl_puntos_interes,id_punto_interes'
            ],
            'id_tipo_actividad' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_subsistema' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'fecha_solicitud' => [
                'required',
                'date'
            ],
            'fecha_programacion' => [
                'required',
                'date'
            ],
            'permiso_acceso' => [
                'string',
                'max:20',
                'nullable'
            ],
            'valor' => [
                'required',
            ],
            'id_resposable_contratista' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'descripcion' => [
                'required',
                'string',
            ],
            
            'fecha_reprogramacion' => [
                'nullable',
                'date'
            ],
            'fecha_ejecucion' => [
                'nullable',
                'date'
            ],
            'id_estado_actividad' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_cotizacion' => [
                'nullable',
                Rule::unique('tbl_actividades')->ignore($this->route('activity')),
                'exists:tbl_cotizaciones,id_cotizacion'
            ],
            'id_orden_compra' => [
                'nullable',
                'exists:tbl_ordenes_compra,id_orden_compra'
            ],
            'id_informe' => [
                'nullable',
                'exists:tbl_informes_actividades,id_informe_actividad'
            ],
            'fecha_liquidado' => [
                'nullable',
                'date'
            ],
            'liquidado' => [
                'nullable',
                'boolean'
            ],
            'mes_consolidado' => [
                'nullable',
                'date'
            ],
            'observaciones' => [
                'string'
            ],
            'id_usuareg' => [
                'required',
                'exists:tbl_usuarios,id_usuario'
            ]
        ];
    }

    public function messages()
    {
        return [
            'id_encargado_cliente.required' => 'El campo cliente es obligatorio.',
            'id_estacion.required' => 'El campo punto de interés es obligatorio.',
            'id_tipo_actividad.required' => 'El campo tipo de trabajo es obligatorio.',
            'id_subsistema.required' => 'El campo subsistema es obligatorio.',
            'fecha_solicitud.required' => 'El campo fecha de solicitud es obligatorio.',
            'fecha_solicitud.date' => 'La fecha de solicitud no es valida.',
            'fecha_programacion.required' => 'El campo fecha de programación es obligatorio.',
            'fecha_programacion.date' => 'La fecha de programación no es valida.',
            'permiso_acceso.max' => 'ID permiso no debe ser mayor que 20 caracteres.',
            'descripcion.required' => 'El campo descripción es obligatorio.',
            'id_estado_actividad.required' => 'El campo estado actividad es obligatorio.',
            'id_cotizacion.unique' => 'Ya existe una actividad asociada a está cotización.',
        ];
    }
}
