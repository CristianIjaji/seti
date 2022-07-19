<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveListaPrecioRequest extends FormRequest
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
            'id_cliente'=> [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'id_tipo_item'=>[
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'codigo'=>[
                'required',
                'string',
                'max:20',
                Rule::unique('tbl_lista_precios')->ignore($this->route('priceList'))
            ],
            'descripcion'=>[
                'required',
                'string',
            ],
            'unidad'=>[
                'required',
                'string',
                'max:50',
            ],
            'cantidad'=>[
                'required',
            ],
            'valor_unitario'=>[
                'required',
            ],
            'estado' => [
                'nullable'
            ],
            'id_usuareg' => [
                'required',
                'exists:tbl_usuarios,id_usuario'
            ]
        ];
    }
}
