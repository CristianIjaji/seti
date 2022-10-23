<?php

namespace App\Exports;

use App\Models\TblCotizacion;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ConsolidadoExport implements WithMultipleSheets
{
    use Exportable;

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
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

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new DetalleConsolidadoExport($this->model);

        foreach ($this->model[0]->tblconsolidadodetalle as $detalle) {
            $cotizacion = TblCotizacion::find($detalle->tblactividad->tblcotizacion->id_cotizacion)->get();
            $sheets[] = new CotizacionExport($cotizacion);
        }

        return $sheets;
    }
}
