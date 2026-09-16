<?php

namespace App\Models;

use App\Models\Scopes\SortingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Scopes\WithoutDefaultRole;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;

    protected $guarded = ['abilities'];
    protected $appends = ['name'];
    protected $casts = [
        'created_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d'
    ];

    public static $modules = [
        'admins',
        'dashboard',
        'roles',
        'videos',
        'courses',
        'assignments',
        'exams',
        'payments',
        'statuses',
        'branding',
        'settings',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new WithoutDefaultRole());
    }

    public function getNameAttribute()
    {
        return $this->attributes['name_' . app()->getLocale()];
    }

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_role')
            ->withoutGlobalScope(SortingScope::class)
            ->whereNot('id', 1);
    }

    public function abilities()
    {
        return $this->belongsToMany(Ability::class);
    }
}
