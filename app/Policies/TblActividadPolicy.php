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
        return $tblUsuario->getPermisosMenu('activities.inde')->export;
    }

    public function import(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        return isset($tblUsuario->getPermisosMenu('activities.index')->import) ? $tblUsuario->getPermisosMenu('activities.index')->import : false;
    }

    public function resheduleActivity(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        if(!in_array($tblActividad->estado, [session('id_dominio_actividad_reprogramado'), session('id_dominio_actividad_ejecutado'), session('id_dominio_actividad_liquidado'), session('id_dominio_actividad_conciliado')])) {
            return true;
        }

        return false;
    }

    public function pauseActivity(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        if(!in_array($tblActividad->estado, [session('id_dominio_actividad_pausada'), session('id_dominio_actividad_ejecutado'), session('id_dominio_actividad_liquidado'), session('id_dominio_actividad_conciliado')])) {
            return true;
        }

        return false;
    }

    public function executedActivity(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        if(!in_array($tblActividad->estado, [
            session('id_dominio_actividad_ejecutado'),
            session('id_dominio_actividad_liquidado'),
            session('id_dominio_actividad_conciliado')
        ])) {
            return true;
        }

        return false;
    }

    public function viewLiquidate(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        $mostrar_liquidacion = [
            session('id_dominio_actividad_informe_cargado'),
            session('id_dominio_actividad_liquidado')
        ];

        if(in_array($tblActividad->estado, $mostrar_liquidacion) && $tblActividad->id_cotizacion) {
            return true;
        }

        return false;
    }

    public function liquidatedActivity(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        $mostrar_liquidacion = [
            session('id_dominio_actividad_informe_cargado'),
            session('id_dominio_actividad_liquidado')
        ];

        if(in_array($tblActividad->estado, $mostrar_liquidacion)
            && $tblActividad->id_cotizacion
            && in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [
                session('id_dominio_analista'),
                session('id_dominio_coordinador'),
                session('id_dominio_contratista')
            ])) {
            return true;
        }

        return false;
    }

    public function reconciledActivity(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        return true;
    }

    public function viewReport(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        $mostrar_liquidacion = [
            session('id_dominio_actividad_ejecutado'),
            session('id_dominio_actividad_informe_cargado'),
            session('id_dominio_actividad_liquidado')
        ];

        if(in_array($tblActividad->estado, $mostrar_liquidacion) && $tblActividad->id_cotizacion) {
            return true;
        }

        return false;
    }

    public function uploadReport(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        $mostrar_liquidacion = [
            session('id_dominio_actividad_ejecutado'),
            session('id_dominio_actividad_informe_cargado')
        ];

        if(in_array($tblActividad->estado, $mostrar_liquidacion)
            && $tblActividad->id_cotizacion
            && in_array($tblUsuario->tbltercero->id_dominio_tipo_tercero, [
                session('id_dominio_analista'),
                session('id_dominio_coordinador'),
                session('id_dominio_contratista')
            ])) {
            return true;
        }

        return false;
    }

    public function createComment(TblUsuario $tblUsuario, TblActividad $tblActividad) {
        $estados_inactivos = [
            session('id_dominio_actividad_ejecutado'),
            session('id_dominio_actividad_liquidado'),
            session('id_dominio_actividad_conciliado'),
            session('id_dominio_actividad_informe_cargado')
        ];

        if(in_array($tblActividad->estado, $estados_inactivos)) {
            return false;
        }

        return true;
    }
}
