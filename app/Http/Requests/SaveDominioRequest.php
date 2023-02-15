<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveDominioRequest extends FormRequest
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
            'id_usuareg' => (auth()->id() === null ? 1 : auth()->id()),
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
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_dominios')->where(function($query) {
                    return $query->where('id_dominio_padre', '=', $this->get('id_dominio_padre'));
                })->ignore($this->route('domain'))
            ],
            'descripcion' => [
                'required',
                'string',
            ],
            'id_dominio_padre' => [
                'nullable',
                'integer',
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
