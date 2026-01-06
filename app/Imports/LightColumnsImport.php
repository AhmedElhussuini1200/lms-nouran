<?php

namespace App\Imports;

use App\Models\LightColumn;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithChunkReading;

// الاحتفاظ بالعناوين كما هي
// HeadingRowFormatter::default('none');

class LightColumnsImport implements ToModel, WithHeadingRow, WithChunkReading
{
    public function chunkSize(): int
    {
        return 500; // قراءة 500 صف في كل مرة لتقليل استهلاك الذاكرة
    }

    public function model(array $row)
    {
        return new LightColumn([
            'objectid' => $row['OBJECTID *'] ?? null,
            'shape' => $row['Shape *'] ?? null,
            'column_number' => $row['رقم العمود'] ?? null,
            'cable_number' => $row['رقم كيبل التغذية'] ?? null,
            'plate_number' => $row['رقم اللوحة'] ?? null,
            'column_height' => $row['ارتفاع العمود'] ?? null,
            'arm_length' => $row['طول الزراع'] ?? null,
            'lights_count' => $row['عدد الكشافات'] ?? null,
            'neighborhood' => $row['الحي'] ?? null,
            'street' => $row['الشارع'] ?? null,
            'notes' => $row['ملاحظات'] ?? null,
            'lamp_type' => $row['نوع الكشاف'] ?? null,
            'x' => $row['X'] ?? null,
            'y' => $row['Y'] ?? null,
            'efficiency' => $row['الكفائة الكلية'] ?? null,
'last_maintenance' => isset($row['تاريخ اخر صيانة'])
    && $row['تاريخ اخر صيانة'] !== '<Null>'
    ? $row['تاريخ اخر صيانة']
    : null,
            'manufacturer' => $row['الشركة المصنعة'] ?? null,
            'model_number' => $row['رقم الموديل'] ?? null,
            'door_number' => $row['باب العمود'] ?? null,
            'fuse_type' => $row['نوع الفيوز'] ?? null,
            'concrete_base' => $row['القاعدة الخرصانية'] ?? null,
            'column_paint' => $row['دهان العمود'] ?? null,
            'earthing' => $row['التأريض'] ?? null,
            'implementer' => $row['الجهة المنفذة'] ?? null,
            'municipality' => $row['البلدية'] ?? null,
        ]);
    }
}
