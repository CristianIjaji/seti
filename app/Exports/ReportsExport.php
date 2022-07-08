<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportsExport implements FromCollection, ShouldAutoSize, WithStrictNullComparison, WithHeadings, WithProperties, WithEvents, Responsable
{
    use Exportable;

    protected $headers;
    protected $model;

    public function __construct($headers, $model)
    {
        $this->headers = $headers;
        $this->model = $model;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->model;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function properties(): array
    {
        return [
            'creator'        => 'Customer Connection',
            'lastModifiedBy' => 'Customer Connection',
            'title'          => 'Reporte',
            'description'    => 'Reporte almacén Castañeda',
            'subject'        => 'Reporte',
            'keywords'       => 'Reportes,export,spreadsheet',
            'category'       => 'Reportes',
            'manager'        => 'Customer Connection',
            'company'        => 'Customer Connection',
        ];
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $stylesArray = [
                    'headers' => [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'font' => [
                            'bold' => true,
                            'size' => '14x',
                            'color' => ['argb' => 'FFFFFFFF']
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '0578a4',]
                        ]
                    ],
                    'columns' => [
                        'alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'font' => [
                            'size' => '12px'
                        ]
                    ]
                ];

                $event->sheet->getDelegate()->freezePaneByColumnAndRow(1, 2);
                // $event->sheet->getDelegate()->getRowDimension()
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(40);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(1, 1, count($this->headers), 1)->applyFromArray($stylesArray['headers']);
                $event->sheet->getDelegate()->getStyleByColumnAndRow(1, 2, count($this->headers), count($this->model) + 1)->applyFromArray($stylesArray['columns']);
            },
        ];
    }
}
