<?php

namespace App\Imports;

use App\Models\Station;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

// الاحتفاظ بالعناوين كما هي
HeadingRowFormatter::default('none');

class StationsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Station([
            'name_ar'          => ($row['Shape *'] ?? '')." ".($row['OBJECTID *'] ?? null),
            'description'   => $row['ملاحظات'] ?? null,
            'plate_number'  => $row['رقم اللوحة'] ?? null,
            'address'       => $row['الطريق'] ?? null,
            'neighborhood_id' => $this->getNeighborhoodId($row['الحي'] ?? null),
            'municipality_id' => $this->getMunicipalityId($row['البلدية'] ?? null),
            'status_id'     => $this->getStatusId($row['حالة المحول'] ?? 'جيد'),
            'last_maintenance' => isset($row['تاريخ اخر صيانة']) && $row['تاريخ اخر صيانة'] !== '<Null>'
                ? $row['تاريخ اخر صيانة']
                : null,
            'x'             => $row['X'] ?? null,
            'y'             => $row['Y'] ?? null,
            'is_active'     => 1,
            'city_id'     => 559,

        ]);
    }

    private function getNeighborhoodId($name)
    {
        return DB::table('neighborhood')
                 ->where('name_ar', $name)
                 ->orWhere('name_en', $name)
                 ->value('id');
    }

    private function getMunicipalityId($name)
    {
        return DB::table('municipalities')->where('name_ar', $name)
                 ->orWhere('name_en', $name)->value('id');
    }

    private function getStatusId($name)
    {
        return DB::table('status')->where('name_ar', $name)
                 ->orWhere('name_en', $name)->value('id') ?? 1;
    }
}
