<?php

namespace App\Policies;

use App\Models\TblInventario;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class TblInventarioPolicy
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
     * @param  \App\Models\TblInventario  $tblInventario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblInventario $tblInventario)
    {
        return $tblUsuario->getPermisosMenu('stores.index')->view;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return $tblUsuario->getPermisosMenu('stores.index')->create;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblInventario  $tblInventario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblInventario $tblInventario)
    {
        return $tblUsuario->getPermisosMenu('stores.index')->update;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblInventario  $tblInventario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblInventario $tblInventario)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblInventario  $tblInventario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblInventario $tblInventario)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblInventario  $tblInventario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblInventario $tblInventario)
    {
        //
    }

    public function export(TblUsuario $tblUsuario, TblInventario $tblInventario) {
        return $tblUsuario->getPermisosMenu('stores.index')->export;
    }

    public function import(TblUsuario $tblUsuario, TblInventario $tblInventario) {
        return $tblUsuario->getPermisosMenu('stores.index')->import;
    }
}
