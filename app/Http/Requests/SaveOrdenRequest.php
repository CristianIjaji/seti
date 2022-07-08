<?php

namespace App\Http\Requests;

use App\Models\TblHabitacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SaveOrdenRequest extends FormRequest
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
            'id_tercero_cliente' => [
                'required',
                'integer',
                'exists:tbl_terceros,id_tercero'
            ],
            'id_dominio_tipo_orden' => [
                'required',
                'integer',
                'exists:tbl_dominios,id_dominio'
            ],
            'descripcion' => [
                'required',
                'string',
                'max:255'
            ],
            'fecha_inicio' => [
                'required',
                'date'
            ],
            'datos_cliente' => [
                'required',
                'string',
                'max:255'
            ],
            'valor' => [
                'required'
            ],
            'pedir_domiciliario' => [
                'nullable'
            ],
            'id_dominio_tiempo_llegada' => [
                'nullable'
            ],
            'estado' => [
                'nullable'
            ],
            'id_usuareg' => [
                'required',
                'exists:tbl_usuarios,id_usuario'
            ]
        ];

        switch ($this->get('id_dominio_tipo_orden')) {
            case session('id_dominio_reserva_hotel'):
                $this->rules['fecha_fin'] = [
                    'required',
                    'date'
                ];
                $this->rules['id_habitacion'] = [
                    'required',
                    'integer',
                    'exists:tbl_habitaciones,id_habitacion'
                ];
                if($this->get('id_habitacion')) {
                    $habitacion = TblHabitacion::findOrFail($this->get('id_habitacion'));
                    if($habitacion) {
                        $this->merge([
                            'habitaciones' => $habitacion->cantidad - 1
                        ]);

                        if($habitacion->cantidad - 1 < 0) {
                            $this->rules['habitaciones'] = [
                                'integer',
                                'min:0'
                            ];
                        }
                    }
                }
                $this->rules['metodo_pago'] = [
                    'required',
                ];

                break;
            default:
                $this->rules['metodo_pago'] = [
                    'nullable',
                ];

                # code...
                break;
        }

        if(trim($this->get('nombre_cliente')) !== '' && trim($this->get('direccion_cliente')) !== '' && trim($this->get('telefono_cliente')) !== '') {
            $datos_cliente = $this->get('nombre_cliente')."\n".$this->get('direccion_cliente')."\n".$this->get('telefono_cliente');

            $this->merge([
                'datos_cliente' => $datos_cliente,
            ]);
        }

        if($this->get('id_dominio_tipo_orden') == session('id_dominio_domicilio')) {
            $this->merge([
                'fecha_inicio' => date('Y-m-d H:i:s')
            ]);
        }

        $this->merge([
            'estado' => session('id_dominio_orden_cola'),
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
        return $this->rules;
    }

    public function messages()
    {
        return [
            'id_tercero_cliente.required' => 'El campo aliado es obligatorio.',
            'id_dominio_tipo_orden.required' => 'El campo tipo pedido es obligatorio.',
            'datos_cliente.required' => 'Los datos del cliente son obligatorios.',
            'id_habitacion.required' => 'El campo habitación es obligatorio',
            'habitaciones.min' => 'No hay habitaciones disponibles'
        ];
    }
}
