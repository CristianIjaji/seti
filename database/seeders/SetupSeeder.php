<?php

namespace Database\Seeders;

use App\Models\TblConsolidado;
use App\Models\TblCotizacion;
use App\Models\TblDetalleConsolidado;
use App\Models\TblDominio;
use App\Models\TblFactura;
use App\Models\TblHallazgo;
use App\Models\TblListaPrecio;
use App\Models\TblMenu;
use App\Models\TblMenuTipoTercero;
use App\Models\TblOrdenCompra;
use App\Models\TblParametro;
use App\Models\TblPuntosInteres;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } catch (\Throwable $th) {
            //throw $th;
        }

        $plantilla_correo = '
            <!DOCTYPE html>
            <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width,initial-scale=1">
                <meta name="x-apple-disable-message-reformatting">
                <title></title>
                <!--[if mso]>
                <noscript>
                    <xml>
                        <o:OfficeDocumentSettings>
                            <o:PixelsPerInch>96</o:PixelsPerInch>
                        </o:OfficeDocumentSettings>
                    </xml>
                </noscript>
                <![endif]-->
                <style>
                    table, td, div, h1, p {font-family: SF Pro Text, Roboto, Segoe UI, helvetica neue, helvetica, arial, sans-serif;}
                </style>
            </head>
            <body style="margin:0;padding:0;">
                <table role="presentation" style="width:100%; border-collapse:collapse; border:0; border-spacing:0; background:#ffffff;">
                    <tr>
                        <td align="center" style="padding:0;">
                            <table role="presentation" style="width:602px; border-collapse:collapse; border-spacing:0; text-align:left;">
                                <tr>
                                    <td align="center" style="padding: 40px 0 30px 0; background-image: linear-gradient(195deg, #0578a4 0, #1b1c3a 100%); border-bottom: 1px solid #cccccc;">
                                        <img src="http://customer-connection.herokuapp.com/images/logo2.png" alt="Logo Customer Connection" width="300" style="height:auto; display: block;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:36px 30px 42px 30px; font-size: 14px;">
                                        $body
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:30px; background: #64c2d2;">
                                        <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;font-size:9px; ">
                                            <tr>
                                                <td style="padding:0; width:50%;" align="left">
                                                    <p style="margin:0; font-size:14px; line-height:16px;   color:#ffffff;">
                                                        &reg; Customer Connection, Pitalito $year<br/>
                                                    </p>
                                                </td>
                                                <td style="padding:0;width:50%;" align="right">
                                                    <table role="presentation" style="border-collapse:collapse;border:0;border-spacing:0;">
                                                        <tr>
                                                            <td style="padding:0 0 0 10px;width:38px;">
                                                                <a href="https://www.instagram.com/cuco_2032/">
                                                                    <img src="https://customer-connection.herokuapp.com/images/icon-instagram-circle.png" alt="Twitter" width="38" style="height:auto;display:block;border:0;" />
                                                                </a>
                                                            </td>
                                                            <td style="padding:0 0 0 10px;width:38px;">
                                                                <a href="https://www.facebook.com/CUCO123456789/?ref=page_internal">
                                                                    <img src="https://customer-connection.herokuapp.com/images/icon-facebook-circle.png" alt="Facebook" width="38" style="height:auto;display:block;border:0;" />
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
        ';

        TblDetalleConsolidado::truncate();
        TblConsolidado::truncate();
        TblHallazgo::truncate();
        TblFactura::truncate();
        TblOrdenCompra::truncate();
        TblMenuTipoTercero::truncate();
        TblMenu::truncate();
        TblPuntosInteres::truncate();
        TblListaPrecio::truncate();
        TblCotizacion::truncate();
        TblTercero::truncate();
        TblParametro::truncate();
        TblDominio::truncate();
        TblUsuario::truncate();

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Throwable $th) {
            //throw $th;
        }

        // Se crea usuario admin
        $user = TblUsuario::create([
            'usuario' => 'admin',
            'password' => "Colombia.2022",
            'email' => 'candres651@gmail.com',
            'id_usuareg' => 1
        ]);

        // Se crean los dominios padres de la tabla tbl_dominios
        $tipo_documentos = TblDominio::create([
            'nombre' => 'Tipo documentos',
            'descripcion' => 'Lista con los tipos de documentos',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario,
        ]);
        $tipo_terceros = TblDominio::create([
            'nombre' => 'Tipo terceros',
            'descripcion' => 'Lista con los tipos de usuarios',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario,
        ]);
        $tipo_plantilla_correo = TblDominio::create([
            'nombre' => 'Tipo plantillas correo',
            'descripcion' => 'Lista con los tipos de plantillas de correo',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario,
        ]);
        $tipos_impuestos = TblDominio::create([
            'nombre' => 'Listado de impuestos',
            'descripcion' => 'Listado de impuestos',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        $lista_zonas = TblDominio::create([
            'nombre' => 'Listado de zonas de las estaciones',
            'descripcion' => 'Listado de zonas de las estaciones',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        $lista_transportes = TblDominio::create([
            'nombre' => 'Listado de transportes de las estaciones',
            'descripcion' => 'Listado de transportes de las estaciones',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        $lista_accesos = TblDominio::create([
            'nombre' => 'Listado de accesos de las estaciones',
            'descripcion' => 'Listado de accesos de las estaciones',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        $lista_tipo_items = TblDominio::create([
            'nombre' => 'Listado de los tipos de items',
            'descripcion' => 'Listado de los tipos de items',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        $lista_tipo_trabajo = TblDominio::create([
            'nombre' => 'Listado de los tipos de trabajo',
            'descripcion' => 'Listado de los tipos de trabajo',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        $lista_prioridad = TblDominio::create([
            'nombre' => 'Listado de los tipos de prioridades',
            'descripcion' => 'Listado de los tipos de prioridades',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        $lista_procesos = TblDominio::create([
            'nombre' => 'Listado de los estados de la cotización',
            'descripcion' => 'Listado de los estados de la cotización',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        $lista_tipo_items = TblDominio::create([
            'nombre' => 'Listado de los tipos de items',
            'descripcion' => 'Listado de los tipos de items',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        $lista_subsistemas = TblDominio::create([
            'nombre' => 'Listado de los subsistemas',
            'descripcion' => 'Listado de los subsistemas',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        $lista_estados_actividad = TblDominio::create([
            'nombre' => 'Listado de los estados de la actividad',
            'descripcion' => 'Listado de los estados de la actividad',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        
        /* Creacioón dominios hijos tipo documentos */
            $cedula = TblDominio::create([
                'nombre' => 'Cédula',
                'id_dominio_padre' => $tipo_documentos->id_dominio,
                'descripcion' => 'Tipo documento cédula',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $cedula_extranjeria = TblDominio::create([
                'nombre' => 'Cédula extranjeria',
                'id_dominio_padre' => $tipo_documentos->id_dominio,
                'descripcion' => 'Tipo documento cédula extranjeria',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $pasaporte = TblDominio::create([
                'nombre' => 'Pasaporte',
                'id_dominio_padre' => $tipo_documentos->id_dominio,
                'descripcion' => 'Tipo documento cédula',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $nit = TblDominio::create([
                'nombre' => 'NIT',
                'id_dominio_padre' => $tipo_documentos->id_dominio,
                'descripcion' => 'Tipo documento NIT',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación dominios hijo tipo documentos */

        /* Creación dominios hijos tipo terceros */
            $superAdministrador = TblDominio::create([
                'nombre' => 'Super Administrador',
                'id_dominio_padre' => $tipo_terceros->id_dominio,
                'descripcion' => 'Tipo usuario super administrador',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $administrador = TblDominio::create([
                'nombre' => 'Administrador',
                'id_dominio_padre' => $tipo_terceros->id_dominio,
                'descripcion' => 'Tipo usuario administrador',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $proveedor = TblDominio::create([
                'nombre' => 'Proveedor',
                'id_dominio_padre' => $tipo_terceros->id_dominio,
                'descripcion' => 'Tipo usuario proveedor',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $cliente = TblDominio::create([
                'nombre' => 'Cliente',
                'id_dominio_padre' => $tipo_terceros->id_dominio,
                'descripcion' => 'Tipo usuario cliente',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $representante_cliente = TblDominio::create([
                'nombre' => 'Representante cliente',
                'id_dominio_padre' => $tipo_terceros->id_dominio,
                'descripcion' => 'Tipo usuario representante cliente',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $contratista = TblDominio::create([
                'nombre' => 'Contratista',
                'id_dominio_padre' => $tipo_terceros->id_dominio,
                'descripcion' => 'Tipo usuario contratista',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $coordinador = TblDominio::create([
                'nombre' => 'Coordinador',
                'id_dominio_padre' => $tipo_terceros->id_dominio,
                'descripcion' => 'Tipo usuario coordinador',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $analista = TblDominio::create([
                'nombre' => 'Analista',
                'id_dominio_padre' => $tipo_terceros->id_dominio,
                'descripcion' => 'Tipo usuario analista',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación dominios hijos tipo terceros */

        /* Creación dominios hijos tipo plantllas*/
            $correo = TblDominio::create([
                'nombre' => 'Correo',
                'id_dominio_padre' => $tipo_plantilla_correo->id_dominio,
                'descripcion' => $plantilla_correo,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación dominios hijos tipo plantllas */

        /* Creación dominios hijos tipo de impuestos */
            TblDominio::create([
                'nombre' => 'IVA 19%',
                'id_dominio_padre' => $tipos_impuestos->id_dominio,
                'descripcion' => '19%',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creacipon dominios hijos tipo de impuestos */

        /* Creación dominios hijos zonas */
            TblDominio::create([
                'nombre' => 'Oriente',
                'id_dominio_padre' => $lista_zonas->id_dominio,
                'descripcion' => 'Oriente',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Suroccidente',
                'id_dominio_padre' => $lista_zonas->id_dominio,
                'descripcion' => 'Suroccidente',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Centro',
                'id_dominio_padre' => $lista_zonas->id_dominio,
                'descripcion' => 'Centro',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Noroccidente',
                'id_dominio_padre' => $lista_zonas->id_dominio,
                'descripcion' => 'Noroccidente',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación dominios hijos zonas */

        /* Creación dominios hijos transportes */
            TblDominio::create([
                'nombre' => 'Mular',
                'id_dominio_padre' => $lista_transportes->id_dominio,
                'descripcion' => 'Mular',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Aéreo',
                'id_dominio_padre' => $lista_transportes->id_dominio,
                'descripcion' => 'Aéreo',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Marítimo',
                'id_dominio_padre' => $lista_transportes->id_dominio,
                'descripcion' => 'Marítimo',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Fluvial',
                'id_dominio_padre' => $lista_transportes->id_dominio,
                'descripcion' => 'Fluvial',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'No convencional',
                'id_dominio_padre' => $lista_transportes->id_dominio,
                'descripcion' => 'No convencional',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación dominios hijos transportes */

        /* Creación dominios hijos accesos */
            TblDominio::create([
                'nombre' => 'Difícil acceso',
                'id_dominio_padre' => $lista_accesos->id_dominio,
                'descripcion' => 'Difícil acceso',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Fácil acceso',
                'id_dominio_padre' => $lista_accesos->id_dominio,
                'descripcion' => 'Fácil acceso',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación dominios hijos accesos */

        /* Creación dominios hijos tipos de items */
            $manoObra = TblDominio::create([
                'nombre' => 'Mano de obra',
                'id_dominio_padre' => $lista_tipo_items->id_dominio,
                'descripcion' => 'Item tipo mano de obra',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $materiales = TblDominio::create([
                'nombre' => 'Materiales',
                'id_dominio_padre' => $lista_tipo_items->id_dominio,
                'descripcion' => 'Item tipo materiales',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $transporte = TblDominio::create([
                'nombre' => 'Transporte',
                'id_dominio_padre' => $lista_tipo_items->id_dominio,
                'descripcion' => 'Item tipo transporte',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación dominios hijos tipo de items */

        /* Creación dominios hijos tipos de trabajo */
            TblDominio::create([
                'nombre' => 'Preventivo',
                'id_dominio_padre' => $lista_tipo_trabajo->id_dominio,
                'descripcion' => 'Tipo de trabajo preventivo',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Correctivo',
                'id_dominio_padre' => $lista_tipo_trabajo->id_dominio,
                'descripcion' => 'Tipo de trabajo correctivo',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Emergencia',
                'id_dominio_padre' => $lista_tipo_trabajo->id_dominio,
                'descripcion' => 'Tipo de trabajo emergencia',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación dominios hijos tipo de trabajo */

        /* Creación dominios hijos tipos de prioridad */
            TblDominio::create([
                'nombre' => 'Urgente',
                'id_dominio_padre' => $lista_prioridad->id_dominio,
                'descripcion' => 'Tipo de prioridad urgente',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Alta',
                'id_dominio_padre' => $lista_prioridad->id_dominio,
                'descripcion' => 'Tipo de prioridad alta',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Media',
                'id_dominio_padre' => $lista_prioridad->id_dominio,
                'descripcion' => 'Tipo de prioridad media',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Baja',
                'id_dominio_padre' => $lista_prioridad->id_dominio,
                'descripcion' => 'Tipo de prioridad baja',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación dominios hijos tipo de prioridad */

        /* Creación dominios hijos tipos de procesos */
            $cotizacion_creada = TblDominio::create([
                'nombre' => 'Cotización creada',
                'id_dominio_padre' => $lista_procesos->id_dominio,
                'descripcion' => 'Cotización creada',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $cotizacion_devuelta = TblDominio::create([
                'nombre' => 'Cotización devuelta',
                'id_dominio_padre' => $lista_procesos->id_dominio,
                'descripcion' => 'Cotización devuelta',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $cotizacion_revisada = TblDominio::create([
                'nombre' => 'Cotización revisada',
                'id_dominio_padre' => $lista_procesos->id_dominio,
                'descripcion' => 'Cotización revisada',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $cotizacion_enviada = TblDominio::create([
                'nombre' => 'Cotización enviada',
                'id_dominio_padre' => $lista_procesos->id_dominio,
                'descripcion' => 'Cotización enviada',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $cotizacion_pendiente_aprobacion = TblDominio::create([
                'nombre' => 'Cotización pendiente aprobación',
                'id_dominio_padre' => $lista_procesos->id_dominio,
                'descripcion' => 'Cotización pendiente aprobación',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $cotizacion_rechazada = TblDominio::create([
                'nombre' => 'Cotización rechazada',
                'id_dominio_padre' => $lista_procesos->id_dominio,
                'descripcion' => 'Cotización rechazada',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $cotizacion_cancelada = TblDominio::create([
                'nombre' => 'Cotización cancelada',
                'id_dominio_padre' => $lista_procesos->id_dominio,
                'descripcion' => 'Cotización cancelada',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $cotizacion_aprobada = TblDominio::create([
                'nombre' => 'Cotización aprobada',
                'id_dominio_padre' => $lista_procesos->id_dominio,
                'descripcion' => 'Cotización aprobada',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación dominios hijos tipo de prioridad */

        /* Creación dominios hijos tipos de subsistemas */
            TblDominio::create([
                'nombre' => 'Motogenerador',
                'id_dominio_padre' => $lista_subsistemas->id_dominio,
                'descripcion' => 'Motogenerador',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Aires acondicionados',
                'id_dominio_padre' => $lista_subsistemas->id_dominio,
                'descripcion' => 'Aires acondicionados',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Sistema puesta tierra',
                'id_dominio_padre' => $lista_subsistemas->id_dominio,
                'descripcion' => 'Sistema puesta tierra',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Baja tensión',
                'id_dominio_padre' => $lista_subsistemas->id_dominio,
                'descripcion' => 'Baja Tensión',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Media tensión',
                'id_dominio_padre' => $lista_subsistemas->id_dominio,
                'descripcion' => 'Media tensión',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Obra civil',
                'id_dominio_padre' => $lista_subsistemas->id_dominio,
                'descripcion' => 'Obra civil',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Sistema regulado',
                'id_dominio_padre' => $lista_subsistemas->id_dominio,
                'descripcion' => 'Sistema regulado',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'ATS',
                'id_dominio_padre' => $lista_subsistemas->id_dominio,
                'descripcion' => 'ATS',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'SPT/MT/BT',
                'id_dominio_padre' => $lista_subsistemas->id_dominio,
                'descripcion' => 'Sistema puesta atierra, media tensión y baja tensión',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            TblDominio::create([
                'nombre' => 'Power',
                'id_dominio_padre' => $lista_subsistemas->id_dominio,
                'descripcion' => 'Sistema de respaldo power',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación dominios hijos tipos de subsistemas */

        /* Creación dominios hijos estados actividad */
            $actividad_programado = TblDominio::create([
                'nombre' => 'Programado',
                'id_dominio_padre' => $lista_estados_actividad->id_dominio,
                'descripcion' => 'Programado',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $actividad_comprando = TblDominio::create([
                'nombre' => 'Comprando',
                'id_dominio_padre' => $lista_estados_actividad->id_dominio,
                'descripcion' => 'Comprando',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $actividad_reprogramado = TblDominio::create([
                'nombre' => 'Reprogramado',
                'id_dominio_padre' => $lista_estados_actividad->id_dominio,
                'descripcion' => 'Reprogramado',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $actividad_ejecutado = TblDominio::create([
                'nombre' => 'Ejecutado',
                'id_dominio_padre' => $lista_estados_actividad->id_dominio,
                'descripcion' => 'Ejecutado',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $actividad_pausada = TblDominio::create([
                'nombre' => 'Pausada',
                'id_dominio_padre' => $lista_estados_actividad->id_dominio,
                'descripcion' => 'Pausada',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $actividad_liquidado = TblDominio::create([
                'nombre' => 'Liquidado',
                'id_dominio_padre' => $lista_estados_actividad->id_dominio,
                'descripcion' => 'Liquidado',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $actividad_conciliado = TblDominio::create([
                'nombre' => 'Conciliado',
                'id_dominio_padre' => $lista_estados_actividad->id_dominio,
                'descripcion' => 'Conciliado',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación dominios hijos estados actividad */

        /* Creación parametros */
            // Creación parametro tipo documentos
            TblParametro::create([
                'llave' => 'id_dominio_tipo_documento',
                'valor' => $tipo_documentos->id_dominio,
                'descripcion' => 'Lista con los tipos de documentos',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creación parametro tipo terceros
            TblParametro::create([
                'llave' => 'id_dominio_tipo_tercero',
                'valor' => $tipo_terceros->id_dominio,
                'descripcion' => 'Lista con los tipos de usuarios',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creación parametro usuario super administrador
            TblParametro::create([
                'llave' => 'id_dominio_super_administrador',
                'valor' => $superAdministrador->id_dominio,
                'descripcion' => 'id_dominio_super_administrador',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creación parametro usuario administrador
            TblParametro::create([
                'llave' => 'id_dominio_administrador',
                'valor' => $administrador->id_dominio,
                'descripcion' => 'id_dominio_administrador',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creación parametro usuario proveedor
            TblParametro::create([
                'llave' => 'id_dominio_proveedor',
                'valor' => $proveedor->id_dominio,
                'descripcion' => 'id_dominio_proveedor',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creación parametro usuario cliente
            TblParametro::create([
                'llave' => 'id_dominio_cliente',
                'valor' => $cliente->id_dominio,
                'descripcion' => 'id_dominio_cliente',
                'id_usuareg' => $user->id_usuario,
            ]);
            TblParametro::create([
                'llave' => 'id_dominio_representante_cliente',
                'valor' => $representante_cliente->id_dominio,
                'descripcion' => 'id_dominio_representante_cliente',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creación parametro usuario representante cliente
            // Creación parametro usuario contratista
            TblParametro::create([
                'llave' => 'id_dominio_contratista',
                'valor' => $contratista->id_dominio,
                'descripcion' => 'id_dominio_contratista',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creación parametro usuario coordinador
            TblParametro::create([
                'llave' => 'id_dominio_coordinador',
                'valor' => $coordinador->id_dominio,
                'descripcion' => 'id_dominio_coordinador',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creación parametro usuario contratista
            TblParametro::create([
                'llave' => 'id_dominio_analista',
                'valor' => $analista->id_dominio,
                'descripcion' => 'id_dominio_analista',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacion parametros tipos plantillas correo
            TblParametro::create([
                'llave' => 'id_dominio_plantilla_correo',
                'valor' => $tipo_plantilla_correo->id_dominio,
                'descripcion' => 'id_dominio_plantilla_correo',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacion parametros tipos de impuestos
            TblParametro::create([
                'llave' => 'id_dominio_impuestos',
                'valor' => $tipos_impuestos->id_dominio,
                'descripcion' => 'id_dominio_impuestos',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creación parametro plantilla correo
            TblParametro::create([
                'llave' => 'id_dominio_plantilla_correo_default',
                'valor' => $correo->id_dominio,
                'descripcion' => 'id_dominio_plantilla_correo_default',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro lista de las zonas
            TblParametro::create([
                'llave' => 'id_dominio_zonas',
                'valor' => $lista_zonas->id_dominio,
                'descripcion' => 'id_dominio_zonas',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro lista de transportes
            TblParametro::create([
                'llave' => 'id_dominio_transportes',
                'valor' => $lista_transportes->id_dominio,
                'descripcion' => 'id_dominio_transportes',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro lista de accesos
            TblParametro::create([
                'llave' => 'id_dominio_accesos',
                'valor' => $lista_accesos->id_dominio,
                'descripcion' => 'id_dominio_accesos',
                'id_usuareg' => $user->id_usuario,
            ]);
            TblParametro::create([
                'llave' => 'id_dominio_tipo_items',
                'valor' => $lista_tipo_items->id_dominio,
                'descripcion' => 'id_dominio_tipo_items',
                'id_usuareg' => $user->id_usuario
            ]);
            // Creacón parametro mano de obra
            TblParametro::create([
                'llave' => 'id_dominio_mano_obra',
                'valor' => $manoObra->id_dominio,
                'descripcion' => 'id_dominio_mano_obra',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro materiales
            TblParametro::create([
                'llave' => 'id_dominio_materiales',
                'valor' => $materiales->id_dominio,
                'descripcion' => 'id_dominio_materiales',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro materiales
            TblParametro::create([
                'llave' => 'id_dominio_transporte',
                'valor' => $transporte->id_dominio,
                'descripcion' => 'id_dominio_transporte',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro tipos de trabajo
            TblParametro::create([
                'llave' => 'id_dominio_tipos_trabajo',
                'valor' => $lista_tipo_trabajo->id_dominio,
                'descripcion' => 'id_dominio_tipos_trabajo',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro tipos de proceso
            TblParametro::create([
                'llave' => 'id_dominio_tipos_proceso',
                'valor' => $lista_procesos->id_dominio,
                'descripcion' => 'id_dominio_tipos_proceso',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro tipos de prioridad
            TblParametro::create([
                'llave' => 'id_dominio_tipos_prioridad',
                'valor' => $lista_prioridad->id_dominio,
                'descripcion' => 'id_dominio_tipos_prioridad',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro cotización creada
            TblParametro::create([
                'llave' => 'id_dominio_cotizacion_creada',
                'valor' => $cotizacion_creada->id_dominio,
                'descripcion' => 'id_dominio_cotizacion_creada',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro cotización devuelta
            TblParametro::create([
                'llave' => 'id_dominio_cotizacion_devuelta',
                'valor' => $cotizacion_devuelta->id_dominio,
                'descripcion' => 'id_dominio_cotizacion_devuelta',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro cotización revisada
            TblParametro::create([
                'llave' => 'id_dominio_cotizacion_revisada',
                'valor' => $cotizacion_revisada->id_dominio,
                'descripcion' => 'id_dominio_cotizacion_revisada',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro cotización enviada
            TblParametro::create([
                'llave' => 'id_dominio_cotizacion_enviada',
                'valor' => $cotizacion_enviada->id_dominio,
                'descripcion' => 'id_dominio_cotizacion_enviada',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro cotización pendiente aprobación
            TblParametro::create([
                'llave' => 'id_dominio_cotizacion_pendiente_aprobacion',
                'valor' => $cotizacion_pendiente_aprobacion->id_dominio,
                'descripcion' => 'id_dominio_cotizacion_pendiente_aprobacion',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro cotización rechazada
            TblParametro::create([
                'llave' => 'id_dominio_cotizacion_rechazada',
                'valor' => $cotizacion_rechazada->id_dominio,
                'descripcion' => 'id_dominio_cotizacion_rechazada',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro cotización cancelada
            TblParametro::create([
                'llave' => 'id_dominio_cotizacion_cancelada',
                'valor' => $cotizacion_cancelada->id_dominio,
                'descripcion' => 'id_dominio_cotizacion_cancelada',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creacón parametro cotización cancelada
            TblParametro::create([
                'llave' => 'id_dominio_cotizacion_aprobada',
                'valor' => $cotizacion_aprobada->id_dominio,
                'descripcion' => 'id_dominio_cotizacion_aprobada',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creación parametro cédula
            TblParametro::create([
                'llave' => 'id_dominio_cedula',
                'valor' => $cedula->id_dominio,
                'descripcion' => 'id_dominio_cedula',
                'id_usuareg' => $user->id_usuario
            ]);
            // Creación parametro cédula extrangeria
            TblParametro::create([
                'llave' => 'id_dominio_cedula_extrangeria',
                'valor' => $cedula_extranjeria->id_dominio,
                'descripcion' => 'id_dominio_cedula_extrangeria',
                'id_usuareg' => $user->id_usuario
            ]);
            // Creación parametro pasaporte
            TblParametro::create([
                'llave' => 'id_dominio_pasaporte',
                'valor' => $pasaporte->id_dominio,
                'descripcion' => 'id_dominio_pasaporte',
                'id_usuareg' => $user->id_usuario
            ]);
            // Creación parametro nit
            TblParametro::create([
                'llave' => 'id_dominio_nit',
                'valor' => $nit->id_dominio,
                'descripcion' => 'id_dominio_nit',
                'id_usuareg' => $user->id_usuario
            ]);
            // Creación parametro lista subsistemas
            TblParametro::create([
                'llave' => 'id_dominio_subsistemas',
                'valor' => $lista_subsistemas->id_dominio,
                'descripcion' => 'id_dominio_subsistemas',
                'id_usuareg' => $user->id_usuario
            ]);
            // Creacón parametro estados de la actividad
            TblParametro::create([
                'llave' => 'id_dominio_estados_actividad',
                'valor' => $lista_estados_actividad->id_dominio,
                'descripcion' => 'id_dominio_estados_actividad',
                'id_usuareg' => $user->id_usuario,
            ]);
            // Creación parametro actividad programado
            TblParametro::create([
                'llave' => 'id_dominio_actividad_programado',
                'valor' => $actividad_programado->id_dominio,
                'descripcion' => 'id_dominio_actividad_programado',
                'id_usuareg' => $user->id_usuario
            ]);
            // Creación parametro actividad comprando
            TblParametro::create([
                'llave' => 'id_dominio_actividad_comprando',
                'valor' => $actividad_comprando->id_dominio,
                'descripcion' => 'id_dominio_actividad_comprando',
                'id_usuareg' => $user->id_usuario
            ]);
            // Creación parametro actividad reprogramado
            TblParametro::create([
                'llave' => 'id_dominio_actividad_reprogramado',
                'valor' => $actividad_reprogramado->id_dominio,
                'descripcion' => 'id_dominio_actividad_reprogramado',
                'id_usuareg' => $user->id_usuario
            ]);
            // Creación parametro actividad ejecutado
            TblParametro::create([
                'llave' => 'id_dominio_actividad_ejecutado',
                'valor' => $actividad_ejecutado->id_dominio,
                'descripcion' => 'id_dominio_actividad_ejecutado',
                'id_usuareg' => $user->id_usuario
            ]);
            // Creación parametro actividad pausada
            TblParametro::create([
                'llave' => 'id_dominio_actividad_pausada',
                'valor' => $actividad_pausada->id_dominio,
                'descripcion' => 'id_dominio_actividad_pausada',
                'id_usuareg' => $user->id_usuario
            ]);
            // Creación parametro actividad liquidado
            TblParametro::create([
                'llave' => 'id_dominio_actividad_liquidado',
                'valor' => $actividad_liquidado->id_dominio,
                'descripcion' => 'id_dominio_actividad_liquidado',
                'id_usuareg' => $user->id_usuario
            ]);
            // Creación parametro actividad conciliado
            TblParametro::create([
                'llave' => 'id_dominio_actividad_conciliado',
                'valor' => $actividad_conciliado->id_dominio,
                'descripcion' => 'id_dominio_actividad_conciliado',
                'id_usuareg' => $user->id_usuario
            ]);
        /* Fin creación parametros */

        /* Se crean terceros del sistema */
            $administrador_pagina = TblTercero::create([
                'id_dominio_tipo_documento' => $cedula->id_dominio,
                'documento' => '1083870826',
                'dv' => '',
                'razon_social' => '',
                'nombres' => 'Cristian Andrés',
                'apellidos' => 'Ijaji',
                'ciudad' => 'Bogotá',
                'direccion' => 'Calle 180 # 54 - 57',
                'correo' => 'candres651@gmail.com',
                'telefono' => '3165163721',
                'id_dominio_tipo_tercero' => $superAdministrador->id_dominio,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* FIn creación terceros del sistema */

        /* Se crean los menús */
            $menuTerceros = TblMenu::create([
                'url' => 'clients.index',
                'icon' => 'fa-solid fa-address-book nav_icon',
                'nombre' => 'Terceros',
                'orden' => 1,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $menuSitios = TblMenu::create([
                'url' => 'sites.index',
                'icon' => 'fa-solid fa-tower-cell nav_icon',
                'nombre' => 'Puntos interés',
                'orden' => 2,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $menuPrecios = TblMenu::create([
                'url' => 'priceList.index',
                'icon' => 'fa-solid fa-list-ol nav_icon',
                'nombre' => 'Lista precios',
                'orden' => 3,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $menuCotizaciones = TblMenu::create([
                'url' => 'quotes.index',
                'icon' => 'fa-solid fa-clipboard-list nav_icon',
                'nombre' => 'Cotizaciones',
                'orden' => 4,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $menuActividades = TblMenu::create([
                'url' => 'activities.index',
                'icon' => 'fa-solid fa-person-digging nav_icon',
                'nombre' => 'Actividades',
                'orden' => 5,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario
            ]);
            $menuConsolidados = TblMenu::create([
                'url' => 'deals.index',
                'icon' => 'fa-solid fa-handshake nav_icon',
                'nombre' => 'Consolidados',
                'orden' => 6,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario
            ]);
            $menuUsuarios = TblMenu::create([
                'url' => 'users.index',
                'icon' => 'fa-solid fa-chalkboard-user nav_icon',
                'nombre' => 'Usuarios',
                'orden' => 7,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $menuPerfiles = TblMenu::create([
                'url' => 'profiles.index',
                'icon' => 'fa-solid fa-list-check nav_icon',
                'nombre' => 'Perfiles',
                'orden' => 8,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $menuDominios = TblMenu::create([
                'url' => 'domains.index',
                'icon' => 'fa-solid fa-screwdriver nav_icon',
                'nombre' => 'Dominios',
                'orden' => 9,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            $menuParametros = TblMenu::create([
                'url' => 'params.index',
                'icon' => 'fa-solid fa-sliders nav_icon',
                'nombre' => 'Parámetros',
                'orden' => 10,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* Fin creación de los menús */

        /* Se crean los menús del perfil super administrador */
            TblMenuTipoTercero::create([
                'id_menu' => $menuTerceros->id_menu,
                'id_tipo_tercero' => $administrador_pagina->id_dominio_tipo_tercero,
                'crear' => true,
                'editar' => true,
                'ver' => true,
                'importar' => true,
                'exportar' => true,
            ]);
            TblMenuTipoTercero::create([
                'id_menu' => $menuSitios->id_menu,
                'id_tipo_tercero' => $administrador_pagina->id_dominio_tipo_tercero,
                'crear' => true,
                'editar' => true,
                'ver' => true,
                'importar' => true,
                'exportar' => true,
            ]);
            TblMenuTipoTercero::create([
                'id_menu' => $menuPrecios->id_menu,
                'id_tipo_tercero' => $administrador_pagina->id_dominio_tipo_tercero,
                'crear' => true,
                'editar' => true,
                'ver' => true,
                'importar' => true,
                'exportar' => true,
            ]);
            TblMenuTipoTercero::create([
                'id_menu' => $menuCotizaciones->id_menu,
                'id_tipo_tercero' => $administrador_pagina->id_dominio_tipo_tercero,
                'crear' => true,
                'editar' => true,
                'ver' => true,
                'importar' => true,
                'exportar' => true,
            ]);
            TblMenuTipoTercero::create([
                'id_menu' => $menuActividades->id_menu,
                'id_tipo_tercero' => $administrador_pagina->id_dominio_tipo_tercero,
                'crear' => true,
                'editar' => true,
                'ver' => true,
                'importar' => true,
                'exportar' => true
            ]);
            TblMenuTipoTercero::create([
                'id_menu' => $menuConsolidados->id_menu,
                'id_tipo_tercero' => $administrador_pagina->id_dominio_tipo_tercero,
                'crear' => true,
                'editar' => true,
                'ver' => true,
                'importar' => true,
                'exportar' => true
            ]);
            TblMenuTipoTercero::create([
                'id_menu' => $menuUsuarios->id_menu,
                'id_tipo_tercero' => $administrador_pagina->id_dominio_tipo_tercero,
                'crear' => true,
                'editar' => true,
                'ver' => true,
                'importar' => true,
                'exportar' => true,
            ]);
            TblMenuTipoTercero::create([
                'id_menu' => $menuPerfiles->id_menu,
                'id_tipo_tercero' => $administrador_pagina->id_dominio_tipo_tercero,
                'crear' => true,
                'editar' => true,
                'ver' => true,
                'importar' => true,
                'exportar' => true,
            ]);
            TblMenuTipoTercero::create([
                'id_menu' => $menuDominios->id_menu,
                'id_tipo_tercero' => $administrador_pagina->id_dominio_tipo_tercero,
                'crear' => true,
                'editar' => true,
                'ver' => true,
                'importar' => true,
                'exportar' => true,
            ]);
            TblMenuTipoTercero::create([
                'id_menu' => $menuParametros->id_menu,
                'id_tipo_tercero' => $administrador_pagina->id_dominio_tipo_tercero,
                'crear' => true,
                'editar' => true,
                'ver' => true,
                'importar' => true,
                'exportar' => true,
            ]);
        /* */

        $user->id_tercero = $administrador_pagina->id_tercero;
        $user->save();
    }
}
