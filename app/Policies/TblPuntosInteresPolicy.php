<?php

namespace App\Policies;

use App\Models\TblPuntosInteres;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

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
        return $tblUsuario->getPermisosMenu('sites.index')->view;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return $tblUsuario->getPermisosMenu('sites.index')->create;
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
        return $tblUsuario->getPermisosMenu('sites.index')->update;
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
        return $tblUsuario->getPermisosMenu('sites.index')->export;
    }

    public function import(TblUsuario $tblUsuario, TblPuntosInteres $tblPuntosInteres) {
        return $tblUsuario->getPermisosMenu('sites.index')->import;
    }
}
