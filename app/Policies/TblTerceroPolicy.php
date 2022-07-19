<?php

namespace App\Policies;

use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class TblTerceroPolicy
{
    use HandlesAuthorization;

    public function before($tblUsuario, $ability) {
        if($tblUsuario->tbltercero->id_dominio_tipo_tercero == session('id_dominio_super_administrador')) {
            return true;
        }
    }

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
     * @param  \App\Models\TblTercero  $tblTercero
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblTercero $tblTercero)
    {
        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_administrador')]);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_administrador')]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblTercero  $tblTercero
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblTercero $tblTercero)
    {
        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_administrador')]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblTercero  $tblTercero
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblTercero $tblTercero)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblTercero  $tblTercero
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblTercero $tblTercero)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblTercero  $tblTercero
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblTercero $tblTercero)
    {
        //
    }
}
