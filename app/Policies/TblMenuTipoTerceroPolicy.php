<?php

namespace App\Policies;

use App\Models\TblMenuTipoTercero;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class TblMenuTipoTerceroPolicy
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
     * @param  \App\Models\TblMenuTipoTercero  $tblMenuTipoTercero
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblMenuTipoTercero $tblMenuTipoTercero)
    {
        return isset($tblUsuario->getPermisosMenu('profiles.index')->view) ? $tblUsuario->getPermisosMenu('profiles.index')->view : false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return isset($tblUsuario->getPermisosMenu('profiles.index')->create) ? $tblUsuario->getPermisosMenu('profiles.index')->create : false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblMenuTipoTercero  $tblMenuTipoTercero
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblMenuTipoTercero $tblMenuTipoTercero)
    {
        return isset($tblUsuario->getPermisosMenu('profiles.index')->update) ? $tblUsuario->getPermisosMenu('profiles.index')->update : false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblMenuTipoTercero  $tblMenuTipoTercero
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblMenuTipoTercero $tblMenuTipoTercero)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblMenuTipoTercero  $tblMenuTipoTercero
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblMenuTipoTercero $tblMenuTipoTercero)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblMenuTipoTercero  $tblMenuTipoTercero
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblMenuTipoTercero $tblMenuTipoTercero)
    {
        //
    }
}
