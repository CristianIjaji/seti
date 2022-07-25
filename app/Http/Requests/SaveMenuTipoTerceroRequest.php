<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SaveMenuTipoTerceroRequest extends FormRequest
{
    protected $rules = [];

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
        $this->rules = [
            'id_menu' => [
                'required',
            ],
            'id_tipo_tercero' => [
                'required',
                'exists:tbl_dominios,id_dominio',
                Rule::unique('tbl_menu_tipo_tercero')->ignore($this->route('profiles'))
            ],
            'crear' => [
                'nullable',
            ],
            'editar' => [
                'nullable',
            ],
            'ver' => [
                'nullable',
            ],
            'importar' => [
                'nullable',
            ],
            'exportar' => [
                'nullable',
            ],
        ];
        
        if(isset(request()->route('profile')->id_menu_tipo_tercero)) {
            unset($this->rules['id_tipo_tercero'][2]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->rules;
    }

    public function messages()
    {
        return [
            'id_menu.required' => 'Debe seleccionar un menú.',
            'id_tipo_tercero.required' => 'El campo tipo tercero es obligatorio.',
            'id_tipo_tercero.unique' => 'Ya se registraron los permisos de este tipo tercero.',
        ];
    }
}
