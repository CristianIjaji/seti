<?php

namespace App\Policies;

use App\Models\TblMovimiento;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class TblMovimientoPolicy
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
     * @param  \App\Models\TblMovimiento  $tblMovimiento
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblMovimiento $tblMovimiento)
    {
        return isset($tblUsuario->getPermisosMenu('moves.index')->view) ? $tblUsuario->getPermisosMenu('moves.index')->view : false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return isset($tblUsuario->getPermisosMenu('moves.index')->create) ? $tblUsuario->getPermisosMenu('moves.index')->create : false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblMovimiento  $tblMovimiento
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblMovimiento $tblMovimiento)
    {
        return isset($tblUsuario->getPermisosMenu('moves.index')->update) ? $tblUsuario->getPermisosMenu('moves.index')->update : false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblMovimiento  $tblMovimiento
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblMovimiento $tblMovimiento)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblMovimiento  $tblMovimiento
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblMovimiento $tblMovimiento)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblMovimiento  $tblMovimiento
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblMovimiento $tblMovimiento)
    {
        //
    }

    public function export(TblUsuario $tblUsuario, TblMovimiento $tblMovimiento) {
        return isset($tblUsuario->getPermisosMenu('moves.index')->export) ? $tblUsuario->getPermisosMenu('moves.index')->export : false;
    }

    public function import(TblUsuario $tblUsuario, TblMovimiento $tblMovimiento) {
        return isset($tblUsuario->getPermisosMenu('moves.index')->import) ? $tblUsuario->getPermisosMenu('moves.index')->import : false;
    }
}
