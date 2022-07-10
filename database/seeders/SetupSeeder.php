<?php

namespace Database\Seeders;

use App\Models\TblConfiguracion;
use App\Models\TblDominio;
use App\Models\TblOrden;
use App\Models\TblParametro;
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

        TblOrden::truncate();
        TblTercero::truncate();
        TblParametro::truncate();
        TblDominio::truncate();
        TblUsuario::truncate();
        TblConfiguracion::truncate();

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
        sleep(1);
        $tipo_terceros = TblDominio::create([
            'nombre' => 'Tipo terceros',
            'descripcion' => 'Lista con los tipos de usuarios',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario,
        ]);
        sleep(1);
        $tipo_orden = TblDominio::create([
            'nombre' => 'Tipo orden',
            'descripcion' => 'Lista con los tipos de orden',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario,
        ]);
        sleep(1);
        $tipo_plantilla_correo = TblDominio::create([
            'nombre' => 'Tipo plantillas correo',
            'descripcion' => 'Lista con los tipos de plantillas de correo',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario,
        ]);
        sleep(1);
        $tipo_plantilla_recibo = TblDominio::create([
            'nombre' => 'Tipo plantillas recibo',
            'descripcion' => 'Lista con los tipos de plantillas de recibos',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario,
        ]);
        sleep(1);
        $tipo_tiempos_domicilio = TblDominio::create([
            'nombre' => 'Tipo tiempos domicilio',
            'descripcion' => 'Lista con los tipos de tiempos del domiciliario',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario,
        ]);
        sleep(1);
        $estados_orden = TblDominio::create([
            'nombre' => 'Estados de la orden',
            'descripcion' => 'Lista con los estados de la orden',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        sleep(1);
        $lista_zonas = TblDominio::create([
            'nombre' => 'Listado de zonas de las estaciones',
            'descripcion' => 'Listado de zonas de las estaciones',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        sleep(1);
        $lista_transportes = TblDominio::create([
            'nombre' => 'Listado de transportes de las estaciones',
            'descripcion' => 'Listado de transportes de las estaciones',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        sleep(1);
        $lista_accesos = TblDominio::create([
            'nombre' => 'Listado de accesos de las estaciones',
            'descripcion' => 'Listado de accesos de las estaciones',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
        sleep(1);

        /* Creacioón dominios hijos tipo documentos */
            $cedula = TblDominio::create([
                'nombre' => 'Cédula',
                'id_dominio_padre' => $tipo_documentos->id_dominio,
                'descripcion' => 'Tipo documento cédula',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $cedula_extranjeria = TblDominio::create([
                'nombre' => 'Cédula extranjeria',
                'id_dominio_padre' => $tipo_documentos->id_dominio,
                'descripcion' => 'Tipo documento cédula extranjeria',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $pasaporte = TblDominio::create([
                'nombre' => 'Pasaporte',
                'id_dominio_padre' => $tipo_documentos->id_dominio,
                'descripcion' => 'Tipo documento cédula',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $nit = TblDominio::create([
                'nombre' => 'NIT',
                'id_dominio_padre' => $tipo_documentos->id_dominio,
                'descripcion' => 'Tipo documento NIT',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
        /* Fin creación dominios hijo tipo documentos */

        /* Creación dominios hijos tipo terceros */
            $superAdministrador = TblDominio::create([
                'nombre' => 'Super Administrador',
                'id_dominio_padre' => $tipo_terceros->id_dominio,
                'descripcion' => 'Tipo usuario super administrador',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $administrador = TblDominio::create([
                'nombre' => 'Administrador',
                'id_dominio_padre' => $tipo_terceros->id_dominio,
                'descripcion' => 'Tipo usuario administrador',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $agente = TblDominio::create([
                'nombre' => 'Agente',
                'id_dominio_padre' => $tipo_terceros->id_dominio,
                'descripcion' => 'Tipo usuario agente',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $asociado = TblDominio::create([
                'nombre' => 'Asociado',
                'id_dominio_padre' => $tipo_terceros->id_dominio,
                'descripcion' => 'Tipo usuario asociado',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
        /* Fin creación dominios hijos tipo terceros */

        /* Creación dominios hijos tipo orden*/
            $domicilio = TblDominio::create([
                'nombre' => 'Domicilio',
                'id_dominio_padre' => $tipo_orden->id_dominio,
                'descripcion' => 'Tipo orden domicilio',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $reserva_hotel = TblDominio::create([
                'nombre' => 'Reserva hotel',
                'id_dominio_padre' => $tipo_orden->id_dominio,
                'descripcion' => 'Tipo orden reserva hotel',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $reserva_restaurante = TblDominio::create([
                'nombre' => 'Reserva restaurante',
                'id_dominio_padre' => $tipo_orden->id_dominio,
                'descripcion' => 'Tipo orden reserva restaurante',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
        /* Fin creacion dominios hijos tipo orden*/

        /* Creación dominios hijos tipo plantllas*/
            $correo = TblDominio::create([
                'nombre' => 'Correo',
                'id_dominio_padre' => $tipo_plantilla_correo->id_dominio,
                'descripcion' => $plantilla_correo,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $recibo = TblDominio::create([
                'nombre' => 'Recibo',
                'id_dominio_padre' => $tipo_plantilla_recibo->id_dominio,
                'descripcion' =>
                '{
                    "ticket": [
                      {"action": "cut"},
                      {"action": "emphasize", "value": 1},
                      {"action": "align", "value": "center"},
                      {"text": "$empresa"},
                      {"text": "Recibo de compra"},
                      {"text": "$direccion_ TEL $telefono_"},
                      {"action": "feed", "value": 1},
                      {"action": "align", "value": "left"},
                      {"text": "Fecha: $fecha              Hora: $hora"},
                      {"text": "$line"},
                      $descripcion,
                      {"text": "$line"},
                      {"action": "align", "value": "right"},
                      {"text": "TOTAL"},
                      {"text": "$ $total"},
                      {"action": "feed", "value": 1},
                      {"action": "align", "value": "left"},
                      {"text": "Cliente: $nombre"},
                      {"text": "Dirección: $direccion"},
                      {"text": "Teléfono: $telefono"},
                      {"action": "feed", "value": 1},
                      {"text": "Gracias por elegirnos!, disfrute su orden"},
                      {"action": "cut"}
                    ]
                }',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
        /* Fin creación dominios hijos tipo plantllas*/

        /* Creación dominios hijos tipo tiempos domiciliario */
            TblDominio::create([
                'nombre' => '5 minutos',
                'id_dominio_padre' => $tipo_tiempos_domicilio->id_dominio,
                'descripcion' => 'Tiempo llegada 5 minutos',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            TblDominio::create([
                'nombre' => '10 minutos',
                'id_dominio_padre' => $tipo_tiempos_domicilio->id_dominio,
                'descripcion' => 'Tiempo llegada 10 minutos',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            TblDominio::create([
                'nombre' => '15 minutos',
                'id_dominio_padre' => $tipo_tiempos_domicilio->id_dominio,
                'descripcion' => 'Tiempo llegada 15 minutos',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            TblDominio::create([
                'nombre' => '20 minutos',
                'id_dominio_padre' => $tipo_tiempos_domicilio->id_dominio,
                'descripcion' => 'Tiempo llegada 20 minutos',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            TblDominio::create([
                'nombre' => '25 minutos',
                'id_dominio_padre' => $tipo_tiempos_domicilio->id_dominio,
                'descripcion' => 'Tiempo llegada 25 minutos',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            TblDominio::create([
                'nombre' => '30 minutos',
                'id_dominio_padre' => $tipo_tiempos_domicilio->id_dominio,
                'descripcion' => 'Tiempo llegada 30 minutos',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
        /* Fin creación dominios hijos tipo tiempos domiciliario */

        /* Creación dominios hijos tipo estados de la orden */
            $orden_rechazada = TblDominio::create([
                'nombre' => 'Orden rechazada',
                'id_dominio_padre' => $estados_orden->id_dominio,
                'descripcion' => 'Orden rechazada',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $orden_cola = TblDominio::create([
                'nombre' => 'Orden en cola',
                'id_dominio_padre' => $estados_orden->id_dominio,
                'descripcion' => 'Orden en cola',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $orden_aceptada = TblDominio::create([
                'nombre' => 'Orden aceptada',
                'id_dominio_padre' => $estados_orden->id_dominio,
                'descripcion' => 'Orden aceptada',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $orden_camino = TblDominio::create([
                'nombre' => 'Orden en camino',
                'id_dominio_padre' => $estados_orden->id_dominio,
                'descripcion' => 'Orden en camino',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $orden_entregada = TblDominio::create([
                'nombre' => 'Orden entregada',
                'id_dominio_padre' => $estados_orden->id_dominio,
                'descripcion' => 'Orden entregada',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $orden_devuelta = TblDominio::create([
                'nombre' => 'Orden devuelta',
                'id_dominio_padre' => $estados_orden->id_dominio,
                'descripcion' => 'Orden devuelta',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $orden_aceptada_domiciliario = TblDominio::create([
                'nombre' => 'Orden aceptada domiciliario',
                'id_dominio_padre' => $estados_orden->id_dominio,
                'descripcion' => 'Orden aceptada domiciliario',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            $orden_completada = TblDominio::create([
                'nombre' => 'Orden completada',
                'id_dominio_padre' => $estados_orden->id_dominio,
                'descripcion' => 'Orden completada',
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
        /* Fin creación dominios hijos tipo tiempos domiciliario */

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

        /* Creación parametros */
            // Creación parametro tipo documentos
            TblParametro::create([
                'llave' => 'id_dominio_tipo_documento',
                'valor' => $tipo_documentos->id_dominio,
                'descripcion' => 'Lista con los tipos de documentos',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creación parametro tipo terceros
            TblParametro::create([
                'llave' => 'id_dominio_tipo_tercero',
                'valor' => $tipo_terceros->id_dominio,
                'descripcion' => 'Lista con los tipos de usuarios',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creación parametro tipo orden
            TblParametro::create([
                'llave' => 'id_dominio_tipo_orden',
                'valor' => $tipo_orden->id_dominio,
                'descripcion' => 'Lista con los tipos de orden',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creación parametro usuario super administrador
            TblParametro::create([
                'llave' => 'id_dominio_super_administrador',
                'valor' => $superAdministrador->id_dominio,
                'descripcion' => 'id_dominio_super_administrador',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creación parametro usuario administrador
            TblParametro::create([
                'llave' => 'id_dominio_administrador',
                'valor' => $administrador->id_dominio,
                'descripcion' => 'id_dominio_administrador',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creación parametro usuario agente
            TblParametro::create([
                'llave' => 'id_dominio_agente',
                'valor' => $agente->id_dominio,
                'descripcion' => 'id_dominio_agente',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creación parametro usuario asociado
            TblParametro::create([
                'llave' => 'id_dominio_asociado',
                'valor' => $asociado->id_dominio,
                'descripcion' => 'id_dominio_asociado',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacion parametros tipos plantillas correo
            TblParametro::create([
                'llave' => 'id_dominio_plantilla_correo',
                'valor' => $tipo_plantilla_correo->id_dominio,
                'descripcion' => 'id_dominio_plantilla_correo',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacion parametros tipos plantillas correo
            TblParametro::create([
                'llave' => 'id_dominio_plantilla_recibo',
                'valor' => $tipo_plantilla_recibo->id_dominio,
                'descripcion' => 'id_dominio_plantilla_recibo',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creación parametro plantilla correo
            TblParametro::create([
                'llave' => 'id_dominio_plantilla_correo_default',
                'valor' => $correo->id_dominio,
                'descripcion' => 'id_dominio_plantilla_correo_default',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro plantilla recibo
            TblParametro::create([
                'llave' => 'id_dominio_plantilla_recibo_default',
                'valor' => $recibo->id_dominio,
                'descripcion' => 'id_dominio_plantilla_recibo_default',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro tiempos domiciliario
            TblParametro::create([
                'llave' => 'id_dominio_tiempos_domicilio',
                'valor' => $tipo_tiempos_domicilio->id_dominio,
                'descripcion' => 'id_dominio_tiempos_domicilio',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro estados orden
            TblParametro::create([
                'llave' => 'id_dominio_estados_orden',
                'valor' => $estados_orden->id_dominio,
                'descripcion' => 'id_dominio_estados_orden',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro orden rechazada
            TblParametro::create([
                'llave' => 'id_dominio_orden_rechazada',
                'valor' => $orden_rechazada->id_dominio,
                'descripcion' => 'id_dominio_orden_rechazada',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro orden cola
            TblParametro::create([
                'llave' => 'id_dominio_orden_cola',
                'valor' => $orden_cola->id_dominio,
                'descripcion' => 'id_dominio_orden_cola',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro orden aceptada
            TblParametro::create([
                'llave' => 'id_dominio_orden_aceptada',
                'valor' => $orden_aceptada->id_dominio,
                'descripcion' => 'id_dominio_orden_aceptada',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro orden camino
            TblParametro::create([
                'llave' => 'id_dominio_orden_camino',
                'valor' => $orden_camino->id_dominio,
                'descripcion' => 'id_dominio_orden_camino',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro orden entregada
            TblParametro::create([
                'llave' => 'id_dominio_orden_entregada',
                'valor' => $orden_entregada->id_dominio,
                'descripcion' => 'id_dominio_orden_entregada',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro orden devuelta
            TblParametro::create([
                'llave' => 'id_dominio_orden_devuelta',
                'valor' => $orden_devuelta->id_dominio,
                'descripcion' => 'id_dominio_orden_devuelta',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro orden aceptada domiciliario
            TblParametro::create([
                'llave' => 'id_dominio_orden_aceptada_domiciliario',
                'valor' => $orden_aceptada_domiciliario->id_dominio,
                'descripcion' => 'id_dominio_orden_aceptada_domiciliario',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro orden aceptada domiciliario
            TblParametro::create([
                'llave' => 'id_dominio_orden_completada',
                'valor' => $orden_completada->id_dominio,
                'descripcion' => 'id_dominio_orden_completada',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);

            // Creacón parametro tipo orden domicilio
            TblParametro::create([
                'llave' => 'id_tipo_orden_domicilio',
                'valor' => $domicilio->id_dominio,
                'descripcion' => 'id_tipo_orden_domicilio',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro tipo orden reserva hotel
            TblParametro::create([
                'llave' => 'id_tipo_orden_reserva_hotel',
                'valor' => $reserva_hotel->id_dominio,
                'descripcion' => 'id_tipo_orden_reserva_hotel',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro tipo orden reserva restaurante
            TblParametro::create([
                'llave' => 'id_tipo_orden_reserva_restaurante',
                'valor' => $reserva_restaurante->id_dominio,
                'descripcion' => 'id_tipo_orden_reserva_restaurante',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro lista de las zonas
            TblParametro::create([
                'llave' => 'id_dominio_zonas',
                'valor' => $lista_zonas->id_dominio,
                'descripcion' => 'id_dominio_zonas',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro lista de transportes
            TblParametro::create([
                'llave' => 'id_dominio_transportes',
                'valor' => $lista_transportes->id_dominio,
                'descripcion' => 'id_dominio_transportes',
                'id_usuareg' => $user->id_usuario,
            ]);
            sleep(1);
            // Creacón parametro lista de accesos
            TblParametro::create([
                'llave' => 'id_dominio_accesos',
                'valor' => $lista_accesos->id_dominio,
                'descripcion' => 'id_dominio_accesos',
                'id_usuareg' => $user->id_usuario,
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
            sleep(1);
        /* FIn creación terceros del sistema */

        $user->id_tercero = $administrador_pagina->id_tercero;
        $user->save();

        TblConfiguracion::create([
            'id_tercero_cliente' => $user->id_tercero,
            'impresora' => '',
            'id_dominio_recibo' => $recibo->id_dominio,
            'logo' => '',
            'estado' => 1,
            'id_usuareg' => $user->id_usuario
        ]);
    }
}
