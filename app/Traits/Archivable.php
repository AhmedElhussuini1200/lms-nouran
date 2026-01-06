<?php

namespace App\Traits;

use App\Models\Archive;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait Archivable
{
    public function archive()
    {
        DB::transaction(function () {

            Archive::create([
                'table_name'  => $this->getTable(),
                'record_id'   => $this->getKey(),
                'data'        => $this->toArray(),
                'archived_by' => Auth::id(),
            ]);

            $this->delete(); // حذف من الجدول الأصلي
        });
    }
}
?>