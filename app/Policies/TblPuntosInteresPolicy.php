<?php

namespace App\Policies;

use App\Models\TblPuntosInteres;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class TblPuntosInteresPolicy
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
     * @param  \App\Models\TblPuntosInteres  $tblPuntosInteres
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblPuntosInteres $tblPuntosInteres)
    {
        return isset($tblUsuario->getPermisosMenu('sites.index')->view) ? $tblUsuario->getPermisosMenu('sites.index')->view : false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return isset($tblUsuario->getPermisosMenu('sites.index')->create) ? $tblUsuario->getPermisosMenu('sites.index')->create : false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblPuntosInteres  $tblPuntosInteres
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblPuntosInteres $tblPuntosInteres)
    {
        return isset($tblUsuario->getPermisosMenu('sites.index')->update) ? $tblUsuario->getPermisosMenu('sites.index')->update : false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblPuntosInteres  $tblPuntosInteres
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblPuntosInteres $tblPuntosInteres)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblPuntosInteres  $tblPuntosInteres
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblPuntosInteres $tblPuntosInteres)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblPuntosInteres  $tblPuntosInteres
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblPuntosInteres $tblPuntosInteres)
    {
        //
    }

    public function export(TblUsuario $tblUsuario, TblPuntosInteres $tblPuntosInteres) {
        return isset($tblUsuario->getPermisosMenu('sites.index')->export) ? $tblUsuario->getPermisosMenu('sites.index')->export : false;
    }

    public function import(TblUsuario $tblUsuario, TblPuntosInteres $tblPuntosInteres) {
        return isset($tblUsuario->getPermisosMenu('sites.index')->import) ? $tblUsuario->getPermisosMenu('sites.index')->import : false;
    }
}
