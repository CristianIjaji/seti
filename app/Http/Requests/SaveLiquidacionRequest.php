<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveLiquidacionRequest extends FormRequest
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
            'valor' => 0,
            'id_dominio_estado' => session('id_dominio_liquidacion_creada'),
            'id_usuareg' => (auth()->guest() ? 1 : auth()->id())
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
            'id_actividad' => [
                'required',
                'exists:tbl_actividades,id_cotizacion',
                Rule::unique('tbl_liquidaciones')->ignore($this->route('closeout'))
            ],
            'id_dominio_estado' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_usuareg' => [
                'required',
                'exists:tbl_usuarios,id_usuario'
            ],
            'id_dominio_tipo_item' => [
                'nullable',
                'exists:tbl_dominios,id_dominio'
            ],
            // 'id_item' => [
            //     'required',
            // ],
            // 'id_item.*' => [
            //     'exists:tbl_lista_precios,id_lista_precio'
            // ],
            // 'cantidad.*' => [
            //     'required',
            //     'numeric',
            //     'min:0'
            // ],
        ];
    }

    public function messages()
    {
        return [
            'id_item.required' => 'Debe agregar un ítem a la liquidación.',
            'cantidad.*.required' => 'La cantidad no puede ser menor a 0.',
            'cantidad.*.min' => 'La cantidad de los ítems debe ser mayor o igual a 0.',
        ];
    }
}
