<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ExcelTemplateExport implements FromCollection
{
    public $unitsData;

    public function __construct($unitsData)
    {
        $this->unitsData = $unitsData;
    }

    /**
     * @param $data
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->unitsData;
    }
}
