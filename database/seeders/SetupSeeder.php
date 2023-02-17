<?php

namespace Database\Seeders;

use App\Models\TblActividad;
use App\Models\TblConsolidado;
use App\Models\TblCotizacion;
use App\Models\TblConsolidadoDetalle;
use App\Models\TblCotizacionDetalle;
use App\Models\TblDominio;
use App\Models\TblEstado;
use App\Models\TblFactura;
use App\Models\TblHallazgo;
use App\Models\TblInformeActivdad;
use App\Models\TblInventario;
use App\Models\TblKardex;
use App\Models\TblLiquidacion;
use App\Models\TblLiquidacionDetalle;
use App\Models\TblListaPrecio;
use App\Models\TblMenu;
use App\Models\TblMenuTipoTercero;
use App\Models\TblMovimiento;
use App\Models\TblMovimientoDetalle;
use App\Models\TblOrdenCompra;
use App\Models\TblOrdenCompraDetalle;
use App\Models\TblParametro;
use App\Models\TblPuntosInteres;
use App\Models\TblTercero;
use App\Models\TblUsuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetupSeeder extends Seeder
{
    private function createRegister($model, $params) {
        return $model::create($params);
    }

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

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
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

        /* Inicio limpieza de tablas */
            TblInformeActivdad::truncate();
            TblKardex::truncate();
            TblMovimientoDetalle::truncate();
            TblMovimiento::truncate();
            TblInventario::truncate();
            TblConsolidadoDetalle::truncate();
            TblConsolidado::truncate();
            TblEstado::truncate();
            TblHallazgo::truncate();
            TblActividad::truncate();
            TblFactura::truncate();
            TblInformeActivdad::truncate();
            TblOrdenCompraDetalle::truncate();
            TblOrdenCompra::truncate();
            TblLiquidacionDetalle::truncate();
            TblLiquidacion::truncate();            
            TblMenuTipoTercero::truncate();
            TblMenu::truncate();
            TblCotizacionDetalle::truncate();
            TblCotizacion::truncate();
            TblListaPrecio::truncate();
            TblPuntosInteres::truncate();
            TblTercero::truncate();
            TblParametro::truncate();
            TblDominio::truncate();
            TblUsuario::truncate();
        /* Fin limpieza de tablas */

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Throwable $th) {
            //throw $th;
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
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

        $modelDominio = new TblDominio;
        $modelParametro = new TblParametro;
        $modelMenu = new TblMenu;
        $modelMenuTercero = new TblMenuTipoTercero;

        $dominios_padres = [
            'Tipo documentos' => [
                'id_dominio' => null,
                'key' => 'id_dominio_tipo_documento', 
                'childs' => [
                    'Cédula' => ['id_dominio' => null, 'key' => 'id_dominio_cedula'],
                    'Cédula extranjeria' => ['id_dominio' => null, 'key' => 'id_dominio_cedula_extrangeria'],
                    'Pasaporte' => ['id_dominio' => null, 'key' => 'id_dominio_pasaporte'],
                    'NIT' => ['id_dominio' => null, 'key' => 'id_dominio_nit'],
                    'Almacén' => ['id_dominio' => null, 'key' => 'id_dominio_documento_almacen']
                ]
            ],
            'Tipo terceros' => [
                'id_dominio' => null,
                'key' => 'id_dominio_tipo_tercero',
                'childs' => [
                    'Super Administrador' => ['id_dominio' => null, 'key' => 'id_dominio_super_administrador'],
                    'Administrador' => ['id_dominio' => null, 'key' => 'id_dominio_administrador'],
                    'Proveedor' => ['id_dominio' => null, 'key' => 'id_dominio_proveedor'],
                    'Cliente' => ['id_dominio' => null, 'key' => 'id_dominio_cliente'],
                    'Representante cliente' => ['id_dominio' => null, 'key' => 'id_dominio_representante_cliente'],
                    'Contratista' => ['id_dominio' => null, 'key' => 'id_dominio_contratista'],
                    'Coordinador' => ['id_dominio' => null, 'key' => 'id_dominio_coordinador'],
                    'Analista' => ['id_dominio' => null, 'key' => 'id_dominio_analista'],
                    'Almacén' => ['id_dominio' => null, 'key' => 'id_dominio_almacen'],
                ]
            ],
            'Plantillas correo' => [
                'id_dominio' => null,
                'key' => 'id_dominio_plantilla_correo',
                'childs' => [
                    'Correo' => ['id_dominio' => null, 'key' => 'id_dominio_plantilla_correo_default', 'descripcion' => $plantilla_correo]
                ]
            ],
            'Impuestos' => [
                'id_dominio' => null,
                'key' => 'id_dominio_impuestos',
                'childs' => [
                    'IVA 19%' => ['id_dominio' => null, 'key' => '', 'descripcion' => '19%']
                ]
            ],
            'Zonas estaciones' => [
                'id_dominio' => null,
                'key' => 'id_dominio_zonas',
                'childs' => [
                    'Centro' => ['id_dominio' => null, 'key' => ''],
                    'Centro Oriente' => ['id_dominio' => null, 'key' => ''],
                    'Costa' => ['id_dominio' => null, 'key' => ''],
                    'Norte' => ['id_dominio' => null, 'key' => ''],
                    'Noroccidente' => ['id_dominio' => null, 'key' => ''],
                    'Occidente' => ['id_dominio' => null, 'key' => ''],
                    'Oriente' => ['id_dominio' => null, 'key' => ''],
                    'Suroccidente' => ['id_dominio' => null, 'key' => ''],
                ]
            ],
            'Tipo de transportes de las estaciones' => [
                'id_dominio' => null,
                'key' => 'id_dominio_transportes',
                'childs' => [
                    'Mular' => ['id_dominio' => null, 'key' => ''],
                    'Aéreo' => ['id_dominio' => null, 'key' => ''],
                    'Marítimo' => ['id_dominio' => null, 'key' => ''],
                    'Fluvial' => ['id_dominio' => null, 'key' => ''],
                    'No convencional' => ['id_dominio' => null, 'key' => ''],
                ]
            ],
            'Accesos de las estaciones' => [
                'id_dominio' => null,
                'key' => 'id_dominio_accesos',
                'childs' => [
                    'Difícil acceso' => ['id_dominio' => null, 'key' => ''],
                    'Fácil acceso' => ['id_dominio' => null, 'key' => ''],
                ]
            ],
            'Tipos de trabajo' => [
                'id_dominio' => null,
                'key' => 'id_dominio_tipos_trabajo',
                'childs' => [
                    'Preventivo' => ['id_dominio' => null, 'key' => ''],
                    'Correctivo' => ['id_dominio' => null, 'key' => ''],
                    'Emergencia' => ['id_dominio' => null, 'key' => ''],
                ]
            ],
            'Prioridades' => [
                'id_dominio' => null,
                'key' => 'id_dominio_tipos_prioridad',
                'childs' => [
                    'Urgente' => ['id_dominio' => null, 'key' => ''],
                    'Alta' => ['id_dominio' => null, 'key' => ''],
                    'Media' => ['id_dominio' => null, 'key' => ''],
                    'Baja' => ['id_dominio' => null, 'key' => ''],
                ]
            ],
            'Estados de la cotización' => [
                'id_dominio' => null,
                'key' => 'id_dominio_tipos_proceso',
                'childs' => [
                    'Creada' => ['id_dominio' => null, 'key' => 'id_dominio_cotizacion_creada'],
                    'Devuelta' => ['id_dominio' => null, 'key' => 'id_dominio_cotizacion_devuelta'],
                    'Revisada' => ['id_dominio' => null, 'key' => 'id_dominio_cotizacion_revisada'],
                    'Enviada cliente' => ['id_dominio' => null, 'key' => 'id_dominio_cotizacion_enviada'],
                    'Pendiente aprobación cliente' => ['id_dominio' => null, 'key' => 'id_dominio_cotizacion_pendiente_aprobacion'],
                    'Rechazada cliente' => ['id_dominio' => null, 'key' => 'id_dominio_cotizacion_rechazada'],
                    'Cancelada' => ['id_dominio' => null, 'key' => 'id_dominio_cotizacion_cancelada'],
                    'Aprobada cliente' => ['id_dominio' => null, 'key' => 'id_dominio_cotizacion_aprobada'],
                ]
            ],
            'Tipos de items LPU' => [
                'id_dominio' => null,
                'key' => 'id_dominio_tipo_items',
                'childs' => [
                    'Mano de obra' => ['id_dominio' => null, 'key' => 'id_dominio_mano_obra'],
                    'Materiales' => ['id_dominio' => null, 'key' => 'id_dominio_materiales'],
                    'Transporte' => ['id_dominio' => null, 'key' => 'id_dominio_transporte'],
                ]
            ],
            'Subsistemas' => [
                'id_dominio' => null,
                'key' => 'id_dominio_subsistemas',
                'childs' => [
                    'Motogenerador' => ['id_dominio' => null, 'key' => ''],
                    'Aires acondicionados' => ['id_dominio' => null, 'key' => ''],
                    'Sistema puesta tierra' => ['id_dominio' => null, 'key' => ''],
                    'Baja tensión' => ['id_dominio' => null, 'key' => ''],
                    'Media tensión' => ['id_dominio' => null, 'key' => ''],
                    'Obra civil' => ['id_dominio' => null, 'key' => ''],
                    'Sistema regulado' => ['id_dominio' => null, 'key' => ''],
                    'ATS' => ['id_dominio' => null, 'key' => ''],
                    'SPT/MT/BT' => ['id_dominio' => null, 'key' => ''],
                    'Power' => ['id_dominio' => null, 'key' => ''],
                ]
            ],
            'Estados de la actividad' => [
                'id_dominio' => null,
                'key' => 'id_dominio_estados_actividad',
                'childs' => [
                    'Programada' => ['id_dominio' => null, 'key' => 'id_dominio_actividad_programado'],
                    'Comprando' => ['id_dominio' => null, 'key' => 'id_dominio_actividad_comprando'],
                    'Reprogramada' => ['id_dominio' => null, 'key' => 'id_dominio_actividad_reprogramado'],
                    'Ejecutada' => ['id_dominio' => null, 'key' => 'id_dominio_actividad_ejecutado'],
                    'Informe cargado' => ['id_dominio' => null, 'key' => 'id_dominio_actividad_informe_cargado'],
                    'Pausada' => ['id_dominio' => null, 'key' => 'id_dominio_actividad_pausada'],
                    'Liquidada' => ['id_dominio' => null, 'key' => 'id_dominio_actividad_liquidado'],
                    'Conciliada' => ['id_dominio' => null, 'key' => 'id_dominio_actividad_conciliado'],
                ]
            ],
            'Estados del consolidado' => [
                'id_dominio' => null,
                'key' => 'id_dominio_estados_consolidado',
                'childs' => [
                    'Creado' => ['id_dominio' => null, 'key' => 'id_dominio_consolidado_creado'],
                    'Conciliado' => ['id_dominio' => null, 'key' => 'id_dominio_consolidado_conciliado'],
                    'Cancelado' => ['id_dominio' => null, 'key' => 'id_dominio_consolidado_cancelado'],
                ]
            ],
            'Listado de los medios de pago orden' => [
                'id_dominio' => null,
                'key' => 'id_dominio_medio_pago_orden_compra',
                'childs' => [
                    'Crédito' => ['id_dominio' => null, 'key' => 'id_dominio_medio_pago_credito'],
                    'Contado' => ['id_dominio' => null, 'key' => 'id_dominio_megio_pago_contado'],
                ]
            ],
            'Tipos de orden de compra' => [
                'id_dominio' => null,
                'key' => 'id_dominio_tipo_orden_compra',
                'childs' => [
                    'Compra' => ['id_dominio' => null, 'key' => 'id_dominio_orden_compra'],
                ]
            ],
            'Estados de orden de compra' => [
                'id_dominio' => null,
                'key' => 'id_dominio_estados_orden',
                'childs' => [
                    'Abierta' => ['id_dominio' => null, 'key' => 'id_dominio_orden_abierta'],
                    'Cerrada' => ['id_dominio' => null, 'key' => 'id_dominio_orden_cerrada'],
                    'Parcial' => ['id_dominio' => null, 'key' => 'id_dominio_orden_parcial'],
                    'Cancelada' => ['id_dominio' => null, 'key' => 'id_dominio_orden_cancelada'],
                ]
            ],
            'Tipos de movimientos de inventario' => [
                'id_dominio' => null,
                'key' => 'id_dominio_tipo_movimiento',
                'childs' => [
                    'Entrada' => [
                        'id_dominio' => null,
                        'key' => 'id_dominio_entrada',
                        'childs' => [
                            'Ajuste inventario' => ['id_dominio' => null, 'key' => 'id_dominio_movimiento_entrada_ajuste'],
                            'Devolución material' => ['id_dominio' => null, 'key' => 'id_dominio_movimiento_entrada_devolucion'],
                            'Inventario inicial' => ['id_dominio' => null, 'key' => 'id_dominio_movimiento_entrada_inicial'],
                            'Orden compra' => ['id_dominio' => null, 'key' => 'id_dominio_movimiento_entrada_orden'],
                            'traslado' => ['id_dominio' => null, 'key' => 'id_dominio_movimiento_entrada_traslado'],
                        ]
                    ],
                    'Salida' => [
                        'id_dominio' => null,
                        'key' => 'id_dominio_salida',
                        'childs' => [
                            'Actividad' => ['id_dominio' => null, 'key' => 'id_dominio_movimiento_salida_actividad'],
                            'Ajuste inventario' => ['id_dominio' => null, 'key' => 'id_dominio_movimiento_salida_ajuste'],
                            'traslado' => ['id_dominio' => null, 'key' => 'id_dominio_movimiento_salida_traslado']
                        ]
                    ]
                ]
            ],
            'Estados de movimiento' => [
                'id_dominio' => null,
                'key' => 'id_dominio_estados_movimiento',
                'childs' => [
                    'Pendiente' => ['id_dominio' => null, 'key' => 'id_dominio_movimiento_pendiente'],
                    'Cancelado' => ['id_dominio' => null, 'key' => 'id_dominio_movimiento_cancelado'],
                    'Completado' => ['id_dominio' => null, 'key' => 'id_dominio_movimiento_completado'],
                    'En proceso' => ['id_dominio' => null, 'key' => 'id_dominio_movimiento_proceso'],
                ]
            ],
            'Clasificación inventario' => [
                'id_dominio' => null,
                'key' => 'id_dominio_clasificacion_inventario',
                'childs' => [
                    'Material' => ['id_dominio' => null, 'key' => ''],
                    'Herramienta' => ['id_dominio' => null, 'key' => ''],
                ]
            ],
            'Estados de la liquidacion' => [
                'id_dominio' => null,
                'key' => 'id_dominio_estados_liquidacion',
                'childs' => [
                    'Creada' => ['id_dominio' => null, 'key' => 'id_dominio_liquidacion_creada'],
                    'Cancelada' => ['id_dominio' => null, 'key' => 'id_dominio_liquidacion_cancelada'],
                    'Aprobada' => ['id_dominio' => null, 'key' => 'id_dominio_liquidacion_aprobada'],
                ]
            ]
        ];

        foreach ($dominios_padres as $dominio_padre => $dominios_hijos) {
            $params = [
                'nombre' => $dominio_padre,
                'descripcion' => $dominio_padre,
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ];

            $dominios_padres[$dominio_padre]['id_dominio'] = $this->createRegister($modelDominio, $params)->id_dominio;

            $this->createRegister($modelParametro, [
                'llave' => $dominios_padres[$dominio_padre]['key'],
                'valor' => $dominios_padres[$dominio_padre]['id_dominio'],
                'descripcion' => $dominios_padres[$dominio_padre]['key'],
                'id_usuareg' => $user->id_usuario
            ]);

            foreach ($dominios_hijos['childs'] as $nombre_dominio => $dominio) {
                $params = [
                    'id_dominio_padre' => $dominios_padres[$dominio_padre]['id_dominio'],
                    'nombre' => $nombre_dominio,
                    'descripcion' => isset($dominio['descripcion']) ? $dominio['descripcion'] : $nombre_dominio,
                    'estado' => 1,
                    'id_usuareg' => $user->id_usuario,
                ];

                $dominios_padres[$dominio_padre]['childs'][$nombre_dominio]['id_dominio'] = $this->createRegister($modelDominio, $params)->id_dominio;

                if(!empty($dominio['key'])) {
                    $this->createRegister($modelParametro, [
                        'llave' => $dominio['key'],
                        'valor' => $dominios_padres[$dominio_padre]['childs'][$nombre_dominio]['id_dominio'],
                        'descripcion' => $dominio['key'],
                        'id_usuareg' => $user->id_usuario
                    ]);
                }

                if(isset($dominio['childs'])) {
                    foreach ($dominio['childs'] as $_nombre_dominio => $_dominio) {
                        $params = [
                            'id_dominio_padre' => $dominios_padres[$dominio_padre]['childs'][$nombre_dominio]['id_dominio'],
                            'nombre' => $_nombre_dominio,
                            'descripcion' => isset($_dominio['descripcion']) ? $_dominio['descripcion'] : $_nombre_dominio,
                            'estado' => 1,
                            'id_usuareg' => $user->id_usuario,
                        ];

                        $dominios_padres[$dominio_padre]['childs'][$nombre_dominio]['childs'][$_nombre_dominio]['id_dominio'] = $this->createRegister($modelDominio, $params)->id_dominio;

                        if(!empty($_dominio['key'])) {
                            $this->createRegister($modelParametro, [
                                'llave' => $_dominio['key'],
                                'valor' => $dominios_padres[$dominio_padre]['childs'][$nombre_dominio]['childs'][$_nombre_dominio]['id_dominio'],
                                'descripcion' => $_dominio['key'],
                                'id_usuareg' => $user->id_usuario
                            ]);
                        }
                    }
                }
            }
        }

        /* Se crea administrador de la página */
            $administrador_pagina = TblTercero::create([
                'id_dominio_tipo_documento' => $dominios_padres['Tipo documentos']['childs']['Cédula']['id_dominio'],
                'documento' => '1083870826',
                'dv' => '',
                'razon_social' => '',
                'nombres' => 'Cristian Andrés',
                'apellidos' => 'Ijaji',
                'ciudad' => 'Bogotá',
                'direccion' => 'Calle 180 # 54 - 57',
                'correo' => 'candres651@gmail.com',
                'telefono' => '3165163721',
                'id_dominio_tipo_tercero' => $dominios_padres['Tipo terceros']['childs']['Super Administrador']['id_dominio'],
                'estado' => 1,
                'id_usuareg' => $user->id_usuario,
            ]);
        /* FIn creación terceros del sistema */

        $menus = [
            'Terceros' => [
                'id_menu' => null,
                'menu' => [
                    'url' => 'clients.index',
                    'icon' => 'fa-solid fa-address-book nav_icon',
                    'nombre' => 'Terceros',
                    'orden' => 1,
                    'estado' => 1,
                    'id_usuareg' => $user->id_usuario,
                ]
            ],
            'Almacén' => [
                'id_menu' => null,
                'menu' => [
                    'icon' => 'fa-solid fa-store nav_icon',
                    'nombre' => 'Almacén',
                    'orden' => 2,
                    'estado' => 1,
                    'id_usuareg' => $user->id_usuario
                ],
                'submenu' => [
                    0 => [
                        'id_menu' => null,
                        'menu' => [
                            'id_menu_padre' => null,
                            'url' => 'stores.index',
                            'icon' => 'fa-solid fa-warehouse nav_icon',
                            'nombre' => 'Inventario',
                            'orden' => 1,
                            'estado' => 1,
                            'id_usuareg' => $user->id_usuario
                        ]
                    ],
                    1 => [
                        'id_menu' => null,
                        'menu' => [
                            'id_menu_padre' => null,
                            'url' => 'moves.index',
                            'icon' => 'fa-solid fa-cart-flatbed nav_icon',
                            'nombre' => 'Movimientos',
                            'orden' => 3,
                            'estado' => 1,
                            'id_usuareg' => $user->id_usuario
                        ]
                    ],
                    2 => [
                        'id_menu' => null,
                        'menu' => [
                            'id_menu_padre' => null,
                            'url' => 'kardex.index',
                            'icon' => 'fa-solid fa-chart-column nav_icon',
                            'nombre' => 'Kardex',
                            'orden' => 3,
                            'estado' => 1,
                            'id_usuareg' => $user->id_usuario
                        ]
                    ],
                    3 => [
                        'id_menu' => null,
                        'menu' => [
                            'id_menu_padre' => null,
                            'url' => 'purchases.index',
                            'icon' => 'fa-solid fa-cart-shopping nav_icon',
                            'nombre' => 'Orden compra',
                            'orden' => 4,
                            'estado' => 1,
                            'id_usuareg' => $user->id_usuario,
                        ]
                    ]
                ]
            ],
            'Puntos interés' => [
                'id_menu' => null,
                'menu' => [
                    'url' => 'sites.index',
                    'icon' => 'fa-solid fa-tower-cell nav_icon',
                    'nombre' => 'Puntos interés',
                    'orden' => 3,
                    'estado' => 1,
                    'id_usuareg' => $user->id_usuario,
                ]
            ],
            'Lista precios' => [
                'id_menu' => null,
                'menu' => [
                    'url' => 'priceList.index',
                    'icon' => 'fa-solid fa-list-ol nav_icon',
                    'nombre' => 'Lista precios',
                    'orden' => 4,
                    'estado' => 1,
                    'id_usuareg' => $user->id_usuario,
                ]
            ],
            'Cotizaciones' => [
                'id_menu' => null,
                'menu' => [
                    'url' => 'quotes.index',
                    'icon' => 'fa-solid fa-clipboard-list nav_icon',
                    'nombre' => 'Cotizaciones',
                    'orden' => 5,
                    'estado' => 1,
                    'id_usuareg' => $user->id_usuario,
                ]
            ],
            'Actividades' => [
                'id_menu' => null,
                'menu' => [
                    'url' => 'activities.index',
                    'icon' => 'fa-solid fa-person-digging nav_icon',
                    'nombre' => 'Actividades',
                    'orden' => 6,
                    'estado' => 1,
                    'id_usuareg' => $user->id_usuario
                ]
            ],
            'Consolidados' => [
                'id_menu' => null,
                'menu' => [
                    'url' => 'deals.index',
                    'icon' => 'fa-solid fa-handshake nav_icon',
                    'nombre' => 'Consolidados',
                    'orden' => 7,
                    'estado' => 1,
                    'id_usuareg' => $user->id_usuario
                ]
            ],
            'Configuración' => [
                'id_dominio' => null,
                'menu' => [
                    'icon' => 'fa-solid fa-gears nav_icon',
                    'nombre' => 'Configuración',
                    'orden' => 8,
                    'estado' => 1,
                    'id_usuareg' => $user->id_usuario
                ],
                'submenu' => [
                    0 => [
                        'id_menu' => null,
                        'menu' => [
                            'url' => 'users.index',
                            'icon' => 'fa-solid fa-chalkboard-user nav_icon',
                            'nombre' => 'Usuarios',
                            'orden' => 1,
                            'estado' => 1,
                            'id_usuareg' => $user->id_usuario,
                        ]
                    ],
                    1 => [
                        'id_menu' => null,
                        'menu' => [
                            'url' => 'profiles.index',
                            'icon' => 'fa-solid fa-id-card-clip nav_icon',
                            'nombre' => 'Perfiles',
                            'orden' => 2,
                            'estado' => 1,
                            'id_usuareg' => $user->id_usuario,
                        ]
                    ],
                    2 => [
                        'id_menu' => null,
                        'menu' => [
                            'url' => 'domains.index',
                            'icon' => 'fa-solid fa-screwdriver nav_icon',
                            'nombre' => 'Dominios',
                            'orden' => 3,
                            'estado' => 1,
                            'id_usuareg' => $user->id_usuario,
                        ]
                    ],
                    3 => [
                        'id_menu' => null,
                        'menu' => [
                            'url' => 'params.index',
                            'icon' => 'fa-solid fa-sliders nav_icon',
                            'nombre' => 'Parámetros',
                            'orden' => 4,
                            'estado' => 1,
                            'id_usuareg' => $user->id_usuario,
                        ]
                    ]
                ]
            ]
        ];

        foreach ($menus as $nombre => $menu) {
            $menus[$nombre]['id_menu'] = $this->createRegister($modelMenu, $menu['menu'])->id_menu;

            $this->createRegister($modelMenuTercero, [
                'id_menu' => $menus[$nombre]['id_menu'],
                'id_dominio_tipo_tercero' => $administrador_pagina->id_dominio_tipo_tercero,
                'crear' => isset($menu['submenu']) ? false : true,
                'editar' => isset($menu['submenu']) ? false : true,
                'ver' => true,
                'importar' => isset($menu['submenu']) ? false : true,
                'exportar' => isset($menu['submenu']) ? false : true,
            ]);

            if(isset($menu['submenu'])) {
                foreach ($menu['submenu'] as $key => $submenu) {
                    $submenu['menu']['id_menu_padre'] = $menus[$nombre]['id_menu'];
                    $menus[$nombre]['submenu'][$key]['id_menu'] = $this->createRegister($modelMenu, $submenu['menu'])->id_menu;

                    $this->createRegister($modelMenuTercero, [
                        'id_menu' => $menus[$nombre]['submenu'][$key]['id_menu'],
                        'id_dominio_tipo_tercero' => $administrador_pagina->id_dominio_tipo_tercero,
                        'crear' => true,
                        'editar' => true,
                        'ver' => true,
                        'importar' => true,
                        'exportar' => true,
                    ]);
                }
            }
        }

        $user->id_tercero = $administrador_pagina->id_tercero;
        $user->save();
    }
}