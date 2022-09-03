<?php

namespace App\Policies;

use App\Models\TblCotizacion;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class TblCotizacionPolicy
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
     * @param  \App\Models\TblCotizacion  $tblCotizacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion)
    {
        return isset($tblUsuario->getPermisosMenu('quotes.index')->view) ? $tblUsuario->getPermisosMenu('quotes.index')->view : false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return isset($tblUsuario->getPermisosMenu('quotes.index')->create) ? $tblUsuario->getPermisosMenu('quotes.index')->create : false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblCotizacion  $tblCotizacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion)
    {
        return isset($tblUsuario->getPermisosMenu('quotes.index')->update) ? $tblUsuario->getPermisosMenu('quotes.index')->update : false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblCotizacion  $tblCotizacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblCotizacion  $tblCotizacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblCotizacion  $tblCotizacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion)
    {
        //
    }

    public function aproveQuote(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        if(!in_array($tblCotizacion->estado, [session('id_dominio_cotizacion_creada')])) {
            return false;
        }

        return true;
    }

    public function rejectQuote(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        if(!in_array($tblCotizacion->estado, [session('id_dominio_cotizacion_creada')])) {
            return false;
        }

        return true;
    }

    public function sendQuote(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        if($tblCotizacion->estado == NULL || in_array($tblCotizacion->estado, [session('id_dominio_cotizacion_creada'), session('id_dominio_cotizacion_devuelta')])) {
            return false;
        }

        return true;
    }
}