<?php

namespace App\Policies;

use App\Models\TblHabitacion;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class TblHabitacionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(TblUsuario $tblUsuario)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblHabitacion  $tblHabitacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblHabitacion $tblHabitacion)
    {
        if(in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador')])) {
            return true;
        }

        $servicios = explode(',', $tblUsuario->tblconfiguracion->servicios);
        return in_array(session('id_dominio_reserva_hotel'), $servicios);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador'), session('id_dominio_asociado')]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblHabitacion  $tblHabitacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblHabitacion $tblHabitacion)
    {
        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador'), session('id_dominio_asociado')]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblHabitacion  $tblHabitacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblHabitacion $tblHabitacion)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblHabitacion  $tblHabitacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblHabitacion $tblHabitacion)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblHabitacion  $tblHabitacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblHabitacion $tblHabitacion)
    {
        //
    }
}
