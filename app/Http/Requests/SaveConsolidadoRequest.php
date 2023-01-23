<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveConsolidadoRequest extends FormRequest
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
        if(trim($this->get('mes')) != '') {
            $meses = ['enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4, 'mayo' => 5, 'junio' => 6,
                'julio' => 7, 'agosto' => 8, 'septiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12
            ];

            $date = explode('-', $this->get('mes'));
            $month = mb_strtolower($date[1]);

            $this->merge([
                'mes' => $date[0].'-'.$meses[$month].'-'.date('d')
            ]);
        }

        $this->merge([
            // 'mes' => (trim($this->get('mes')) != '' ? date('Y-m-d', strtotime($this->get('mes'))) : null),
            'id_dominio_estado' => session('id_dominio_consolidado_creado'),
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
            'id_tercero_cliente' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'id_tercero_responsable' => [
                'required',
                'exists:tbl_terceros,id_tercero'
            ],
            'mes' => [
                'required',
                'date'
            ],
            'observacion' => [
                'nullable',
                'max:255'
            ],
            'id_dominio_estado' => [
                'required',
                'exists:tbl_dominios,id_dominio'
            ],
            'id_actividad' => [
                'required',
                Rule::unique('tbl_consolidados_detalle')->ignore($this->route('deal')),
                'exists:tbl_actividades,id_actividad'
            ],
            'id_usuareg' => [
                'required'
            ]
        ];
    }

    public function messages()
    {
        return [
            'id_tercero_cliente.required' => 'El campo cliente es obligatorio.',
            'id_tercero_responsable.required' => 'El campo encargado cliente es obligatorio.',
            'id_actividad.required' => 'Debe agregar una actividad al consolidado.',
            'id_actividad.unique' => 'La actividad ya fue registrada.'
        ];
    }
}
