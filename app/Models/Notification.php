<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'admin_id',
        'title',
        'message',
        'type',
        'is_read',
        'link',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    // للتوافق مع الكود القديم
    public function user()
    {
        return $this->admin();
    }

    // Accessor للتوافق مع Laravel Notifications API (data attribute)
    public function getDataAttribute()
    {
        // نرجع array متوافق مع الـ view اللي بيفترض data['title_ar'], data['icon'], إلخ
        return [
            'title_ar' => $this->title,
            'title_en' => $this->title,
            'description_ar' => $this->message,
            'description_en' => $this->message,
            'color' => $this->type ?? 'primary', // info, success, warning, error -> primary, success, warning, danger
            'icon' => $this->getIconByType($this->type),
        ];
    }

    // Helper method لتحديد الأيقونة حسب النوع
    protected function getIconByType($type)
    {
        $icons = [
            'info' => '<i class="ki-outline ki-information-5 fs-2"></i>',
            'success' => '<i class="ki-outline ki-check-circle fs-2"></i>',
            'warning' => '<i class="ki-outline ki-information fs-2"></i>',
            'error' => '<i class="ki-outline ki-cross-circle fs-2"></i>',
        ];

        return $icons[$type] ?? $icons['info'];
    }
}
