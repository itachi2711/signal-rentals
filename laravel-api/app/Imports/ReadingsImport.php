<?php

namespace App\Imports;

use App\Models\Reading;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;

class ReadingsImport implements ToCollection, WithEvents
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $heading = $rows->shift();

        $utilityID = $heading[0];
        $propertyID = $heading[1];
        $propertyCode = $heading[2];

        foreach ($rows as $row)
        {
            $unit = Unit::where('unit_name', $row[0])->first();
            $readingDate = $row[1];
            $currentReading = $row[2];

            if (isset($unit) && $currentReading >= 0) {
               Reading::create([
                    'property_id'       => $propertyID,
                    'utility_id'        => $utilityID,
                    'unit_id'           => $unit->id,
                    'reading_date'      => $readingDate,
                    'current_reading'   => $currentReading
                ]);
            }
        }
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();

                if (!empty($totalRows)) {
                    echo $totalRows['Worksheet'];
                }
            }
        ];
    }
}
