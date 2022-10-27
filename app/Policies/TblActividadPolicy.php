<?php

namespace App\Policies;

use App\Models\TblActividad;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class TblActividadPolicy
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
     * @param  \App\Models\TblActividad  $tblActividad
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblActividad $tblActividad)
    {
        return isset($tblUsuario->getPermisosMenu('activities.index')->ver) ? $tblUsuario->getPermisosMenu('activities.index')->ver : false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return isset($tblUsuario->getPermisosMenu('activities.index')->create) ? $tblUsuario->getPermisosMenu('activities.index')->create : false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblActividad  $tblActividad
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblActividad $tblActividad)
    {
        return isset($tblUsuario->getPermisosMenu('activities.index')->update) ? $tblUsuario->getPermisosMenu('activities.index')->update : false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblActividad  $tblActividad
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblActividad $tblActividad)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblActividad  $tblActividad
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblActividad $tblActividad)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblActividad  $tblActividad
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblActividad $tblActividad)
    {
        //
    }

    public function export(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        return isset($tblUsuario->getPermisosMenu('activities.inde')->export) ? $tblUsuario->getPermisosMenu('activities.inde')->export : false;
    }

    public function import(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        return isset($tblUsuario->getPermisosMenu('activities.index')->import) ? $tblUsuario->getPermisosMenu('activities.index')->import : false;
    }



    public function createComment(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        
    }
}
