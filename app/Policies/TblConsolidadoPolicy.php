<?php

namespace App\Policies;

use App\Models\TblConsolidado;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class TblConsolidadoPolicy
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
     * @param  \App\Models\TblConsolidado  $tblConsolidado
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblConsolidado $tblConsolidado)
    {
        return isset($tblUsuario->getPermisosMenu('deals.index')->view) ? $tblUsuario->getPermisosMenu('deals.index')->view : false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return isset($tblUsuario->getPermisosMenu('deals.index')->create) ? $tblUsuario->getPermisosMenu('deals.index')->create : false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblConsolidado  $tblConsolidado
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblConsolidado $tblConsolidado)
    {
        return isset($tblUsuario->getPermisosMenu('deals.index')->update) ? $tblUsuario->getPermisosMenu('deals.index')->update : false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblConsolidado  $tblConsolidado
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblConsolidado $tblConsolidado)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblConsolidado  $tblConsolidado
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblConsolidado $tblConsolidado)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblConsolidado  $tblConsolidado
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblConsolidado $tblConsolidado)
    {
        //
    }
}
