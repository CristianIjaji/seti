<?php

namespace App\Http\Requests;

use App\Models\TblOrden;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaveHabitacionRequest extends FormRequest
{
    protected $rules = [];
    protected $min = 0;

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
                'exists:tbl_terceros,id_tercero',
            ],
            'nombre' => [
                'required',
                'string',
                'max:255'
            ],
            'cantidad' => [
                'required',
                'integer',
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

        if(!in_array(Auth::user()->role, [session('id_dominio_super_administrador'), session('id_dominio_administrador')])) {
            $this->merge([
                'id_tercero_cliente' => Auth::user()->id_tercero,
            ]);
        }

        if(isset($this->route('room')->id_habitacion)) {
            $room = $this->route('room');
            $habitaciones = DB::select(
                "SELECT
                    COUNT(o.id_orden) AS ocupadas
                FROM tbl_ordenes AS o
                INNER JOIN tbl_habitaciones AS h ON(o.id_habitacion = h.id_habitacion)
                WHERE o.id_habitacion = $room->id_habitacion
                AND o.id_dominio_tipo_orden = ".session('id_dominio_reserva_hotel')."
                AND o.fecha_fin >= NOW()
                AND o.estado IN(".session('id_dominio_orden_cola').", ".session('id_dominio_orden_aceptada').");
                "
            );

            $cantidad = $this->get('cantidad') > 0 ? $this->get('cantidad') : 0;

            if(isset($habitaciones[0]->ocupadas)) {
                if($habitaciones[0]->ocupadas > $cantidad) {
                    $this->rules['cantidad'] = [
                        'required',
                        'integer',
                        'min:'.$habitaciones[0]->ocupadas
                    ];
                    $this->min = $habitaciones[0]->ocupadas;
                }
            }
        }

        $this->merge([
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
        return $this->rules;
    }
    
    public function messages()
    {
        return [
            'id_tercero_cliente.required' => 'El campo asociado es obligatorio.',
            'cantidad.min' => 'El inventario de habitaciones no puede ser menor a '.$this->min,
        ];
    }
}
