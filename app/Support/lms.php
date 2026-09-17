<?php

// LMS: الهوية + الصفوف + المدرس الحالي + يوتيوب (كانت في dashboard_functions.php)

if (!function_exists('brand')) {
    // الهوية ديناميك: المدرس → "منصة + اسمه" (أو brand_name لو مظبطه)
    // الطالب → هوية مدرسه المختار — ولي الأمر → هوية مدرس أبنائه لو واحد — غير كده العامة
    function brand($key, $default = null)
    {
        static $cache = null;
        if ($cache === null) {
            try {
                $cache = \App\Models\Setting::pluck('value', 'key')->toArray();
            } catch (\Throwable $e) {
                $cache = [];
            }
        }
        try {
            $me = auth('admin')->user();
            $teacher = null;
            if ($me && $me->type === 'teacher') {
                $teacher = $me;
            } elseif ($me && $me->type === 'student' && function_exists('currentTeacher')) {
                $teacher = currentTeacher($me);
            } elseif ($me && $me->type === 'parent') {
                $tids = collect();
                foreach ($me->students ?? [] as $child) {
                    $tids = $tids->merge($child->enrolledTeachers()->pluck('admins.id'));
                }
                if ($tids->unique()->count() === 1) {
                    $teacher = \App\Models\Admin::find($tids->first());
                }
            }
            if ($teacher) {
                if ($key === 'site_name') {
                    return $teacher->brand_name ?: __('منصة') . ' ' . $teacher->name;
                }
                $mine = [
                    'primary_color' => $teacher->brand_primary,
                    'secondary_color' => $teacher->brand_secondary,
                    'logo' => $teacher->brand_logo,
                ];
                if (! empty($mine[$key])) {
                    return $mine[$key];
                }
            }
        } catch (\Throwable $e) {
        }
        $defaults = [
            'site_name' => 'منصة نوران التعليمية',
            'primary_color' => '#1b84ff',
            'secondary_color' => '#17c653',
            'logo' => 'assets/logo/lms-logo-letter-design-initials-linked-circle-uppercase-monogram-typography-technology-business-real-estate-brand-393870301.webp',
        ];
        return $cache[$key] ?? $defaults[$key] ?? $default;
    }
}

if (!function_exists('allowedGrades')) {
    // الصفوف المسموح للمستخدم رؤيتها: الطالب صفه فقط، ولي الأمر صفوف أبنائه، والباقي الكل (null)
    function allowedGrades($user = null): ?array
    {
        $user = $user ?: auth('admin')->user();
        if (! $user) {
            return [];
        }
        if ($user->type === 'student') {
            return $user->grade ? [$user->grade] : [];
        }
        if ($user->type === 'parent') {
            return $user->students()->pluck('admins.grade')->filter()->unique()->values()->all();
        }

        return null;
    }
}

if (!function_exists('youtubeId')) {
    // يستخرج ID اليوتيوب (11 حرف) من أي صيغة رابط — أو null لو الرابط غير صالح
    function youtubeId(?string $url): ?string
    {
        if (! $url) {
            return null;
        }
        $patterns = [
            '/youtube\.com\/watch\?.*v=([A-Za-z0-9_-]{11})/',
            '/youtu\.be\/([A-Za-z0-9_-]{11})/',
            '/youtube(?:-nocookie)?\.com\/embed\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/live\/([A-Za-z0-9_-]{11})/',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $url, $m)) {
                return $m[1];
            }
        }

        return null;
    }
}

if (!function_exists('youtubeEmbed')) {
    function youtubeEmbed(?string $url): ?string
    {
        $id = youtubeId($url);

        return $id ? 'https://www.youtube-nocookie.com/embed/' . $id . '?rel=0' : null;
    }
}

if (!function_exists('currentTeacher')) {
    // المدرس المختار حالياً للطالب (من كارت الاختيار) — أو الوحيد تلقائياً — أو null
    function currentTeacher($user = null)
    {
        $user = $user ?: auth('admin')->user();
        if (! $user || $user->type !== 'student') {
            return null;
        }
        if (session()->has('current_teacher_id')) {
            $t = \App\Models\Admin::where('type', 'teacher')->find(session('current_teacher_id'));
            if ($t && $user->enrolledTeachers()->where('admins.id', $t->id)->exists()) {
                return $t;
            }
            session()->forget('current_teacher_id');
        }
        $teachers = $user->enrolledTeachers()->get();
        if ($teachers->count() === 1) {
            session(['current_teacher_id' => $teachers->first()->id]);
            return $teachers->first();
        }

        return null;
    }
}

if (!function_exists('gradeLockLabel')) {
    // نص شارة الصف المقفول للطالب/ولي الأمر
    function gradeLockLabel($user = null): string
    {
        $user = $user ?: auth('admin')->user();
        if (! $user) {
            return '';
        }
        if ($user->type === 'student') {
            return $user->grade ? __($user->grade) : __('بدون صف');
        }
        if ($user->type === 'parent') {
            $g = $user->students()->pluck('admins.grade')->filter()->unique()->map(fn ($x) => __($x))->implode('، ');
            return $g ?: __('بدون صف');
        }

        return '';
    }
}

