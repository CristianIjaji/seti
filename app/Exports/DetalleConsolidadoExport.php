<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DetalleConsolidadoExport implements FromView, WithEvents, WithTitle, WithColumnFormatting
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function title(): string
    {
        return 'ACTIVIDADES SETI '.mb_strtoupper($this->model[0]->nombre_mes);
    }

    public function view(): View
    {
        return view('partials.formato-consolidado', [
            'deal' => $this->model,
            'total' => 0,
            'item' => 1
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

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $font_10 = [
                    'font' => [
                        'name' => 'Calibri',
                        'size' => '10',
                    ]
                ];
                $font_12 = [
                    'font' => [
                        'name' => 'Calibri',
                        'size' => '12',
                    ],
                ];
                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ]
                ];
                $vertical_center = [
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => false,
                    ]
                ];
                $fill = [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'b4c6e7',]
                    ],
                ];
                $columns = [
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => false,
                    ],
                    'font' => [
                        'size' => '12px'
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ]
                ];

                $rows = count($this->model[0]->tblconsolidadodetalle) + 4;

                $event->sheet->getDelegate()->getStyle('A1')->applyFromArray(array_merge($font_12, $vertical_center));

                $event->sheet->getDelegate()->getStyle('B2')->applyFromArray(array_merge($border, $font_10, $vertical_center));
                $event->sheet->getDelegate()->getStyleByColumnAndRow(3, 2, 6, 2)->applyFromArray(array_merge($border, $font_10, $vertical_center, $fill), true);

                // Campos con los nombres de las columnas
                $event->sheet->getDelegate()->getStyleByColumnAndRow(1, 4, 9, 4)->applyFromArray(array_merge($border, $vertical_center, $fill), true);

                $event->sheet->getDelegate()->getStyleByColumnAndRow(1, 4, 9, $rows)->applyFromArray($columns);
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('E')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('F')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('G')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('H')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('I')->setAutoSize(true);

                $event->sheet->getDelegate()->getStyleByColumnAndRow(6, $rows + 1)->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        ],
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        ],
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        ],
                    ]
                ]);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(7, $rows + 1)->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        ],
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        ],
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        ],
                    ]
                ]);
            },
        ];
    }
}
