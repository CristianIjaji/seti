<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class OrdenExport implements FromView, WithEvents, WithDrawings, WithTitle
{
    use Exportable;

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function title(): string
    {
        return 'OC_'.str_pad($this->model->id_orden_compra, 4, '0', STR_PAD_LEFT);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('partials.formato-orden', [
            'orden' => $this->model,
            'row' => "<tr style='height: 5px;'>
                <td></td>
                <td style='border-left: 10pt solid black;'></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style='border-right: 10pt solid black;'></td>
            </tr>",

            'bordernone' => 'border: none;',
            'borderleft' => 'border-left: 10pt solid black;',
            'bordertop' => 'border-top: 10pt solid black;',
            'borderright' => 'border-right: 10pt solid black;',
            'borderbottom' => 'border-bottom: 10pt solid black;',
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
        $header = "/images/header-orden.png";
        $footer = "/images/footer-orden.png";

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Header orden');
        $drawing->setDescription('Header orden');
        $drawing->setPath(public_path($header));
        $drawing->setHeight(135);
        $drawing->setCoordinates('B2');

        $row = 29 + count($this->model->tbldetalleorden);

        $drawing2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing2->setName('Footer orden');
        $drawing2->setDescription('Footer orden');
        $drawing2->setPath(public_path($footer));
        $drawing2->setHeight(119);
        $drawing2->setOffsetY(36);
        $drawing2->setCoordinates('B'.$row);

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
                    'aling_vertical' => [
                        'alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
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
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'FFF'
                        ],
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

                $event->sheet->getDelegate()->getStyleByColumnAndRow(2, 2, 9, 2)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(2, 3, 9, 8)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(2, 9, 9, 9)->applyFromArray($stylesArray);

                $event->sheet->getDelegate()->getStyleByColumnAndRow(3, 15)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(4, 15, 6, 15)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(7, 15)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(8, 15)->applyFromArray($stylesArray);

                $row = 15 + count($this->model->tbldetalleorden);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(3, 12, 9, $row)->applyFromArray($stylesArray['aling_vertical']);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(3, 15, 8, $row)->applyFromArray($stylesArray);

                $event->sheet->getDelegate()->getStyleByColumnAndRow(7, 16, 8, $row)->applyFromArray($stylesArray['formato_numero']);

                $row = $row + 1;
                $event->sheet->getDelegate()->getStyleByColumnAndRow(8, $row)->applyFromArray($stylesArray);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(8, $row)->applyFromArray($stylesArray['formato_numero']);

                $row = $row + 2;
                $event->sheet->getDelegate()->getStyleByColumnAndRow(4, $row, 8, ($row + 2))->applyFromArray($stylesArray);

                $event->sheet->getDelegate()->getStyleByColumnAndRow(8, $row, 8, ($row + 2))->applyFromArray($stylesArray['formato_numero']);

                $row = $row + 5;
                $event->sheet->getDelegate()->getStyleByColumnAndRow(3, $row, 8, ($row + 3))->applyFromArray($stylesArray['aling_vertical']);

                $row = $row + 5;
                $event->sheet->getDelegate()->getStyleByColumnAndRow(3, $row)->applyFromArray($stylesArray['aling_vertical']);

                $row = $row + 1;
                $event->sheet->getDelegate()->getStyleByColumnAndRow(2, $row, 9, ($row + 2))->applyFromArray($stylesArray);

                $row = $row + 2;
                $event->sheet->getDelegate()->getPageSetup()->setFitToWidth(1);
                $event->sheet->getDelegate()->getPageSetup()->setFitToHeight(0);
                $event->sheet->getDelegate()->getPageSetup()->setPrintArea("B2:I$row");
                $event->sheet->getDelegate()->getStyleByColumnAndRow(2, 2, 9, $row)->applyFromArray($stylesArray);
            }
        ];
    }
}
