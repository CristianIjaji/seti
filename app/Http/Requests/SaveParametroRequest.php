<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveParametroRequest extends FormRequest
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
            'valor' => implode(',', ($this->get('valor') !== null ? $this->get('valor') : [''])),
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
            'llave' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_parametros_aplicacion')->ignore($this->route('param')),
            ],
            'valor' => [
                'required',
                'string',
                'max:255'
            ],
            'descripcion' => [
                'required',
                'string',
                'max:255'
            ],
            'id_parametro_aplicacion' => [
                'nullable',
                'integer'
            ],
            'estado' => [
                'nullable',
                'integer'
            ],
            'id_usuareg' => [
                'required',
                'exists:tbl_usuarios,id_usuario'
            ]
        ];
    }
}
