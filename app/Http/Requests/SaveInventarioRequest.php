<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveInventarioRequest extends FormRequest
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
            'marca' => !empty($this->get('marca')) ? trim($this->get('marca')) : '',
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
            'id_tercero_almacen' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'clasificacion' => [
                'required',
                'string',
                'max:255'
            ],
            'descripcion' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_inventario')->where(function($query) {
                    return $query->where('clasificacion', '=', $this->get('clasificacion'))
                        ->where('descripcion', '=', $this->get('descripcion'))
                        ->where('marca', '=', $this->get('marca'));
                })->ignore($this->route('store'))
            ],
            'marca' => [
                'nullable',
                'string',
                'max:255'
            ],
            'cantidad' => [
                'required'
            ],
            'unidad' => [
                'required',
                'string',
                'max:255'
            ],
            'valor_unitario' => [
                'required'
            ],
            'IVA' => [
                'nullable'
            ],
            'ubicacion' => [
                'nullable',
                'string',
                'max:255'
            ],
            'cantidad_minima' => [
                'required',
                'min:0'
            ],
            'cantidad_maxima' => [
                'required',
                'min:0'
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

    public function messages()
    {
        return [
            'id_tercero_almacen.required' => 'El campo almacén es obligatorio.',
            'clasificacion.required' => 'El campo clasificación es obligatorio.',
            'descripcion.unique' => 'El producto ya está registrado.',
        ];
    }
}
