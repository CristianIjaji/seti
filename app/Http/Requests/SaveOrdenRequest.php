<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveOrdenRequest extends FormRequest
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
            'valor_unitario' => str_replace(',', '', $this->get('valor_unitario')),
            'id_dominio_tipo' => session('id_dominio_orden_compra'),
            'id_dominio_estado' => session('id_dominio_orden_abierta'),
            'cupo_actual' => 0,
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
            'id_tercero_almacen' => [
                'required',
                'exists:tbl_terceros,id_tercero',
            ],
            'id_tercero_proveedor' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'descripcion' => [
                'required',
                'string',
                'max:255'
            ],
            'id_dominio_modalidad_pago' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_dominio_iva' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_dominio_tipo' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_dominio_estado' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_tercero_asesor' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'vencimiento' => [
                'required',
                'date'
            ],
            'cupo_actual' => [
                'required'
            ],
            'id_usuareg' => [
                'required',
                'exists:tbl_usuarios,id_usuario'
            ],
            'id_item' => [
                'required'
            ],
            'id_item.*' => [
                'exists:tbl_inventario,id_inventario'
            ],
            'descripcion_item.*' => [
                'required',
                'string',
                'min:1'
            ],
            'cantidad.*' => [
                'required',
                'numeric',
                'min:1',
            ],
            'valor_unitario.*' => [
                'required',
                'numeric',
                'min:1'
            ],
        ];
    }

    public function messages()
    {
        return [
            'id_tercero_almacen.required' => 'El campo almacén es obligatorio.',
            'id_tercero_proveedor.required' => 'El campo proveedor es obligatorio.',
            'id_dominio_modalidad_pago.required' => 'El campo modalidad de pago es obligatorio.',
            'id_dominio_iva.required' => 'El campo IVA es obligatorio',
            'id_tercero_asesor.required' => 'El campo asesor es obligatorio.',
            'descripcion.required' => 'El campo despachar a es obligatorio',
            'id_item.required' => 'Debe agregar un ítem a la cotización.',
            'descripcion_item.*.required' => 'Debe agregar una descripción para todos los productos.',
            'cantidad.*.required' => 'Debe indicar la cantidad de todos los productos',
            'cantidad.*.min' => 'La cantidad de los productos debe ser mayor a 0.',
            'valor_unitario.*.required' => 'Debe indicar el valor unitario de todos los productos.',
            'valor_unitario.*.min' => 'Por favor validar el valor unitario de los productos.'
        ];
    }
}
