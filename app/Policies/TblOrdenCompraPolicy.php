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
        return isset($tblUsuario->getPermisosMenu('purchases.index')->view) ? $tblUsuario->getPermisosMenu('purchases.index')->view : false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return isset($tblUsuario->getPermisosMenu('purchases.index')->create) ? $tblUsuario->getPermisosMenu('purchases.index')->create : false;
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
        return isset($tblUsuario->getPermisosMenu('purchases.index')->update) ? $tblUsuario->getPermisosMenu('purchases.index')->update : false;
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

    public function export(TblUsuario $tblUsuario, TblOrdenCompra $tblOrdenCompra) {
        return isset($tblUsuario->getPermisosMenu('purchases.index')->export) ? $tblUsuario->getPermisosMenu('purchases.index')->export : false;
    }

    public function import(TblUsuario $tblUsuario, TblOrdenCompra $tblOrdenCompra) {
        return false;
    }

    public function cancelPurchase(TblUsuario $tblUsuario, TblOrdenCompra $tblOrdenCompra) {
        if(in_array($tblOrdenCompra->id_dominio_estado, [session('id_dominio_orden_cerrada'), session('id_dominio_orden_parcial')])) {
            return false;
        }

        return true;
    }

    public function parcialPurchase(TblUsuario $tblUsuario, TblOrdenCompra $tblOrdenCompra) {
        if(in_array($tblOrdenCompra->id_dominio_estado, [session('id_dominio_orden_cerrada'), session('id_dominio_orden_cancelada')])) {
            return false;
        }

        return true;
    }

    public function createComment(TblUsuario $tblUsuario, TblOrdenCompra $tblOrdenCompra) {
        if(in_array($tblOrdenCompra->id_dominio_estado, [session('id_dominio_orden_abierta')])) {
            return true;
        }

        return false;
    }

    public function cancelOrden(TblUsuario $tblUsuario, TblOrdenCompra $tblOrdenCompra) {
        if(in_array($tblOrdenCompra->id_dominio_estado, [session('id_dominio_orden_abierta')])) {
            return true;
        }

        return false;
    }
}
