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
        return isset($tblUsuario->getPermisosMenu('priceList.index')->view) ? $tblUsuario->getPermisosMenu('priceList.index')->view : false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return isset($tblUsuario->getPermisosMenu('priceList.index')->create) ? $tblUsuario->getPermisosMenu('priceList.index')->create : false;
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
        return isset($tblUsuario->getPermisosMenu('priceList.index')->update) ? $tblUsuario->getPermisosMenu('priceList.index')->update : false;
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
        return isset($tblUsuario->getPermisosMenu('sites.index')->export) ? $tblUsuario->getPermisosMenu('sites.index')->export : false;        
    }

    public function import(TblUsuario $tblUsuario, TblListaPrecio $tblListaPrecio) {
        return isset($tblUsuario->getPermisosMenu('sites.index')->import) ? $tblUsuario->getPermisosMenu('sites.index')->import : false;        
    }
}
