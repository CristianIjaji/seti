<?php

namespace App\Policies;

use App\Models\TblOrdenCompra;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class TblOrdenCompraPolicy
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
     * @param  \App\Models\TblOrdenCompra  $tblOrdenCompra
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblOrdenCompra $tblOrdenCompra)
    {
        return $tblUsuario->getPermisosMenu('purchases.index')->view;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return $tblUsuario->getPermisosMenu('purchases.index')->create;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblOrdenCompra  $tblOrdenCompra
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblOrdenCompra $tblOrdenCompra)
    {
        return $tblUsuario->getPermisosMenu('purchases.index')->update;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblOrdenCompra  $tblOrdenCompra
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblOrdenCompra $tblOrdenCompra)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblOrdenCompra  $tblOrdenCompra
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblOrdenCompra $tblOrdenCompra)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblOrdenCompra  $tblOrdenCompra
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblOrdenCompra $tblOrdenCompra)
    {
        //
    }
}
