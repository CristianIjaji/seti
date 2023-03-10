<?php

namespace App\Policies;

use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class TblTerceroPolicy
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
     * @param  \App\Models\TblTercero  $tblTercero
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblTercero $tblTercero)
    {
        return isset($tblUsuario->getPermisosMenu('clients.index')->view) ? $tblUsuario->getPermisosMenu('clients.index')->view : false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return isset($tblUsuario->getPermisosMenu('clients.index')->create) ? $tblUsuario->getPermisosMenu('clients.index')->create : false;
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
        return isset($tblUsuario->getPermisosMenu('clients.index')->update) ? $tblUsuario->getPermisosMenu('clients.index')->update : false;
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

    public function export(TblUsuario $tblUsuario, TblTercero $tblTercero) {
        return isset($tblUsuario->getPermisosMenu('clients.index')->export) ? $tblUsuario->getPermisosMenu('clients.index')->export : false;
    }

    public function import(TblUsuario $tblUsuario, TblTercero $tblTercero) {
        return isset($tblUsuario->getPermisosMenu('clients.index')->import) ? $tblUsuario->getPermisosMenu('clients.index')->import : false;
    }
}
