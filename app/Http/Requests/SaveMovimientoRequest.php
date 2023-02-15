<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveMovimientoRequest extends FormRequest
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
            'total' => 0,
            'id_dominio_estado' => session('id_dominio_movimiento_pendiente'),
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
            'id_dominio_tipo_movimiento' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_tercero_recibe' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'id_tercero_entrega' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'documento' => [
                !in_array($this->get('id_dominio_tipo_movimiento'), [session('id_dominio_movimiento_salida_traslado')])
                ? 'required' : 'nullable',
                $this->get('id_dominio_tipo_movimiento') == session('id_dominio_movimiento_salida_actividad')
                    ? 'exists:tbl_actividades,id_actividad'
                    : 'min:0'
            ],
            'observaciones' => [
                'required'
            ],
            'id_dominio_iva' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'total' => [
                'required'
            ],
            'id_dominio_estado' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_usuareg' => [
                'required',
                'exists:tbl_usuarios,id_usuario'
            ],
            'id_item' => [
                'required',
            ],
            'id_item.*' => [
                'exists:tbl_inventario,id_inventario'
            ],
            'cantidad.*' => [
                'required',
                'numeric',
                in_array($this->get('id_dominio_tipo_movimiento'), [session('id_dominio_movimiento_entrada_devolucion'), session('id_dominio_movimiento_salida_traslado')])
                    ? 'min:0'
                    : 'min:1'
            ],
            'valor_unitario.*' => [
                'required',
                'min:1'
            ],
        ];
    }

    public function messages()
    {
        return [
            'id_dominio_tipo_movimiento.required' => 'El campo tipo movimiento es obligatorio.',
            'id_tercero_entrega.required' => 'El campo quien entrega es obligatorio.',
            'id_tercero_recibe.required' => 'El campo quien recibe es obligatorio.',
            'id_dominio_iva.required' => 'El campo IVA es obligatorio.',
            'id_dominio_estado.required' => 'El estado del movimiento es obligatorio.',
            'id_item.required' => 'Debe agregar un ítem al movimiento.',
            'cantidad.*.required' => 'Debe indicar la cantidad de todos los productos',
            'cantidad.*.min' => 'La cantidad de los productos debe ser mayor a 0.',
            'valor_unitario.*.required' => 'Debe indicar el valor unitario de todos los productos.',
            'valor_unitario.*.min' => 'Por favor validar el valor unitario de los productos.'
        ];
    }
}
