<?php

namespace App\Policies;

use App\Models\TblOrden;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class TblOrdenPolicy
{
    use HandlesAuthorization;

    public function before($tblUsuario, $ability) {
        // if($tblUsuario->tbltercero->id_dominio_tipo_tercero == session('id_dominio_super_administrador')) {
        //     return true;
        // }
    }

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
     * @param  \App\Models\TblOrden  $tblOrden
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblOrden $tblOrden)
    {
        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador'), session('id_dominio_agente'), session('id_dominio_asociado')]);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador'), session('id_dominio_agente')]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblOrden  $tblOrden
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblOrden $tblOrden)
    {
        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador'), session('id_dominio_agente'), session('id_dominio_asociado')]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblOrden  $tblOrden
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblOrden $tblOrden)
    {
        if(!in_array($tblOrden->estado, [session('id_dominio_orden_rechazada'), session('id_dominio_orden_devuelta')]) || !empty($tblOrden->deleted_at)) {
            return false;
        }

        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_administrador'), session('id_dominio_agente'), session('id_dominio_asociado')]);   
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblOrden  $tblOrden
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblOrden $tblOrden)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblOrden  $tblOrden
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblOrden $tblOrden)
    {
        //
    }

    public function closeOrden(TblUsuario $tblUsuario, TblOrden $tblOrden) {
        if($tblOrden->estado !== session('id_dominio_orden_cola')) {
            return false;
        }

        return !in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_asociado')]);
    }

    public function rejectedOrden(TblUsuario $tblUsuario, TblOrden $tblOrden) {
        if($tblOrden->estado !== session('id_dominio_orden_cola')) {
            return false;
        }

        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_asociado')]);
    }

    public function askDomiciliary(TblUsuario $tblUsuario, TblOrden $tblOrden) {
        if(!in_array($tblOrden->estado, [
            session('id_dominio_orden_cola'),
            session('id_dominio_orden_aceptada'),
            session('id_dominio_orden_camino'),
            session('id_dominio_orden_aceptada_domiciliario')
            ]) || $tblOrden->id_dominio_tipo_orden !== session('id_dominio_domicilio')) {
            return false;
        }

        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador'), session('id_dominio_agente'), session('id_dominio_asociado')]);
    }

    public function sendOrden(TblUsuario $tblUsuario, TblOrden $tblOrden){
        if(!in_array($tblOrden->estado, [session('id_dominio_orden_aceptada'), session('id_dominio_orden_aceptada_domiciliario')])
            || $tblOrden->id_dominio_tipo_orden != session('id_dominio_domicilio')) {
            return false;
        }

        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador'), session('id_dominio_agente'), session('id_dominio_asociado')]);
    }

    public function completeOrden(TblUsuario $tblUsuario, TblOrden $tblOrden) {
        if(!in_array($tblOrden->estado, [session('id_dominio_orden_aceptada'),])
            || !in_array($tblOrden->id_dominio_tipo_orden, [session('id_dominio_reserva_hotel'), session('id_dominio_reserva_restaurante')])) {
            return false;
        }

        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador'), session('id_dominio_agente'), session('id_dominio_asociado')]);
    }

    public function deliverOrden(TblUsuario $tblUsuario, TblOrden $tblOrden) {
        if(!in_array($tblOrden->estado, [session('id_dominio_orden_camino')])) {
            return false;
        }

        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador'), session('id_dominio_agente'), session('id_dominio_asociado')]);
    }

    public function export(TblUsuario $tblUsuario, TblOrden $tblOrden) {
        return in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [session('id_dominio_super_administrador'), session('id_dominio_administrador')]);
    }
}
