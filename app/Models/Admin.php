<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'grade',
        'subject',
        'phone',
        'is_blocked',
        'whatsapp_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'teacher_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'teacher_id');
    }

    public function videos()
    {
        return $this->hasMany(Video::class, 'teacher_id');
    }

    // علاقة الإشعارات المخصصة (من جدول notifications اللي عندك)
    public function customNotifications()
    {
        return $this->hasMany(Notification::class, 'admin_id');
    }

    // Helper methods للتوافق مع Laravel Notifications API
    public function notifications()
    {
        // نرجع query builder عشان نستخدمه زي Laravel Notifications
        return $this->customNotifications();
    }

    public function unreadNotifications()
    {
        // نرجع الإشعارات اللي مش مقروءة (is_read = false)
        return $this->customNotifications()->where('is_read', false);
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class, 'student_id');
    }

    public function students()
    {
        return $this->belongsToMany(Admin::class, 'student_parent', 'parent_id', 'student_id');
    }

    public function parents()
    {
        return $this->belongsToMany(Admin::class, 'student_parent', 'student_id', 'parent_id');
    }

    // Helper methods
    public function isTeacher()
    {
        return $this->type === 'teacher';
    }

    public function isStudent()
    {
        return $this->type === 'student';
    }

    public function isParent()
    {
        return $this->type === 'parent';
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_role');
    }

    public function abilities()
    {
        // abilities() هنا بترجع Collection كاملة من موديل Ability
        // عشان الكود القديم اللي بيعتمد على relations يفضل شغال
        if (! $this->relationLoaded('roles')) {
            $this->load('roles.abilities');
        }

        return $this->roles
            ->flatMap(function ($role) {
                return $role->abilities;
            })
            ->unique('id')
            ->values();
    }

    public function hasAbility($ability)
    {
        // التوافق مع الكود اللي بيستخدم اسم القدرة كنص
        return $this->abilities()
            ->pluck('name')
            ->contains($ability);
    }
}
