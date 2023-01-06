<?php

namespace App\Policies;

use App\Models\TblListaPrecio;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class TblListaPrecioPolicy
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
     * @param  \App\Models\TblListaPrecio  $tblListaPrecio
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblListaPrecio $tblListaPrecio)
    {
        return $tblUsuario->getPermisosMenu('priceList.index')->view;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return $tblUsuario->getPermisosMenu('priceList.index')->create;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblListaPrecio  $tblListaPrecio
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblListaPrecio $tblListaPrecio)
    {
        return $tblUsuario->getPermisosMenu('priceList.index')->update;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblListaPrecio  $tblListaPrecio
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblListaPrecio $tblListaPrecio)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblListaPrecio  $tblListaPrecio
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblListaPrecio $tblListaPrecio)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblListaPrecio  $tblListaPrecio
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblListaPrecio $tblListaPrecio)
    {
        //
    }

    public function export(TblUsuario $tblUsuario, TblListaPrecio $tblListaPrecio) {
        return $tblUsuario->getPermisosMenu('sites.index')->export;
    }

    public function import(TblUsuario $tblUsuario, TblListaPrecio $tblListaPrecio) {
        return $tblUsuario->getPermisosMenu('sites.index')->import;
    }
}
