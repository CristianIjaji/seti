<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class CotizacionExport implements FromView, WithEvents, WithDrawings, WithTitle
{
    use Exportable;

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function title(): string
    {
        return $this->model->tblEstacion->nombre;
    }

    public function view(): View
    {
        return view('partials.formato-cotizacion', [
            'quote' => $this->model,
            'row' => "<tr style='height: 5px;'>
                <td></td>
                <td style='border-left: 2pt solid black;'></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style='border-right: 2pt solid black;'></td>
            </tr>",
            'items_material' => 15 + count($this->model->getmaterialescotizacion($this->model->id_cotizacion)) - 1,
            'items_mano_obra' => count($this->model->getmanoobracotizacion($this->model->id_cotizacion)) - 1,
            'items_transporte' => count($this->model->gettransportecotizacion($this->model->id_cotizacion)) - 1,
            'bordernone' => 'border: none;',
            'borderleft' => 'border-left: 2pt solid black;',
            'bordertop' => 'border-top: 2pt solid black;',
            'borderright' => 'border-right: 2pt solid black;',
            'borderbottom' => 'border-bottom: 2pt solid black;',
            'nowrap' => "white-space: nowrap;",
            'black' => "color: black;",
            'red' => "color: red;",
            'bgblue' => "background: #366092;",
            'bold' => "font-weight: bold;",
            'textleft' => "text-align: left;",
            'textcenter' => "text-align: center;",
            'textright' => "text-align: right;",
        ]);
    }

    public function properties(): array
    {
        return [
            'creator'        => 'SETI LTDA',
            'lastModifiedBy' => 'SETI LTDA',
            'title'          => 'Reporte',
            'description'    => 'Reporte seti',
            'subject'        => 'Reporte',
            'keywords'       => 'Reportes,export,spreadsheet',
            'category'       => 'Reportes',
            'manager'        => 'SETI LTDA',
            'company'        => 'SETI LTDA',
        ];
    }

    public function drawings()
    {
        $seti = "/images/seti.png";
        $logo_cliente = (isset($this->model->tblCliente->tblterceroresponsable)
            ? ($this->model->tblCliente->tblterceroresponsable->logo != '' ? "/storage/".$this->model->tblCliente->tblterceroresponsable->logo : $seti)
            : ($this->model->tblCliente->logo != '' ? "/storage/".$this->model->tblCliente->logo : $seti)
        );
        $logo_contratista = (isset($this->model->tblContratista->tblterceroresponsable)
            ? ($this->model->tblContratista->tblterceroresponsable->logo != '' ? "/storage/".$this->model->tblContratista->tblterceroresponsable->logo : $seti)
            : ($this->model->tblContratista->logo != '' ? "/storage/".$this->model->tblContratista->logo : $seti)
        );

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo cliente');
        $drawing->setDescription('Logo cliente');
        $drawing->setPath(public_path($logo_cliente));
        $drawing->setHeight(52);
        $drawing->setOffsetX(20);
        $drawing->setOffsetY(2);
        $drawing->setCoordinates('B2');
        
        $drawing2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing2->setName('Logo contratista');
        $drawing2->setDescription('Logo contratista');
        $drawing2->setPath(public_path($logo_contratista));
        $drawing2->setHeight(52);
        $drawing2->setOffsetX(20);
        $drawing2->setOffsetY(2);
        $drawing2->setCoordinates('H2');
        
        return [$drawing, $drawing2];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $stylesArray = [
                    'font' => [
                        'name' => 'Arial',
                        'size' => '10'
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'columns' => [
                        'alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'font' => [
                            'size' => '12px'
                        ]
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        ],
                    ],
                    'formato_numero' => [
                        'numberFormat' => [
                            'formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
                        ]
                    ]
                ];

                $event->sheet->getDelegate()->getStyleByColumnAndRow(2, 2, 9, 4)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(2, 5, 9, 11)->applyFromArray($stylesArray);
                
                $event->sheet->getDelegate()->getStyleByColumnAndRow(2, 12, 2, 12)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(3, 12, 5, 12)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(6, 12, 6, 12)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(7, 12, 7, 12)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(8, 12, 8, 12)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(9, 12, 9, 12)->applyFromArray($stylesArray);

                // Formato celdas: ??tem, Descripci??n, Un., Cant., VR UNIT, VR TOTAL
                $event->sheet->getDelegate()->getStyleByColumnAndRow(2, 12, 9, 12)->applyFromArray($stylesArray);

                $row = 12 + count($this->model->tblcotizaciondetalle);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(2, 13, 9, $row)->applyFromArray($stylesArray);
                $row += 3;
                $event->sheet->getDelegate()->getStyleByColumnAndRow(8, 13, 9, $row)->applyFromArray($stylesArray['formato_numero']);

                $event->sheet->getDelegate()->getStyleByColumnAndRow(3, ($row - 2), 8, ($row - 2))->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(9, ($row - 2), 9, ($row - 2))->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(9, ($row - 2), 9, ($row - 2))->applyFromArray($stylesArray['formato_numero']);

                $event->sheet->getDelegate()->getStyleByColumnAndRow(3, ($row - 1), 8, ($row - 1))->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(9, ($row - 1), 9, ($row - 1))->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(9, ($row - 1), 9, ($row - 1))->applyFromArray($stylesArray['formato_numero']);

                $event->sheet->getDelegate()->getStyleByColumnAndRow(3, $row, 8, $row)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(9, $row, 9, $row)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(9, $row, 9, $row)->applyFromArray($stylesArray['formato_numero']);

                $event->sheet->getDelegate()->getColumnDimension('H')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('I')->setAutoSize(true);

                $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
                $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);
                $event->sheet->getDelegate()->getPageSetup()->setPrintArea("B2:I$row");
                $event->sheet->getDelegate()->getStyleByColumnAndRow(2, 2, 9, $row)->applyFromArray($stylesArray);
            },
        ];
    }
}
