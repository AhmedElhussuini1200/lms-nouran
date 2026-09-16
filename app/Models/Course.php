<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'grade',
        'teacher_id',
        'subject',
        'scheduled_at',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(Admin::class, 'teacher_id');
    }
}
