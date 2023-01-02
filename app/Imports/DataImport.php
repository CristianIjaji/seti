<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DataImport implements ToModel, WithValidation, WithStartRow, WithChunkReading
{
    use Importable;

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return $this->model::createRow($row);
    }

    public function startRow(): int
    {
        return 2;
    }

    public function rules(): array
    {
        return $this->model::getRules();
    }

    public function customValidationAttributes() {
        return $this->model::getProperties();
    }

    // public function batchSize(): int
    // {
    //     return 1000;
    // }

    public function chunkSize(): int
    {
        return 1000;
    }
}
