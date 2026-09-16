<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Video;
use Illuminate\Http\Request;
use App\Repositories\Dashboard\Contracts\VideoRepositoryInterface;

class VideoRepository implements VideoRepositoryInterface
{
    public function index(Request $request)
    {
        $user = auth('admin')->user();

        $query = Video::with('teacher:id,name')
            ->orderBy('created_at', 'desc');

        // الطالب يشوف فيديوهات الصف بتاعه بس
        if ($user && $user->type === 'student') {
            // لو الطالب ملوش صف محدد → where grade null = مفيش نتائج (مش كل المحتوى)

            $query->where('grade', $user->grade);
        }

        // المدرس يشوف المحتوى بتاعه فقط (منع التضارب بين المدرسين)
        if ($user && $user->type === 'teacher') {
            $query->where('teacher_id', $user->id);
        }

        // فلتر اختياري بالصف
        if ($request->filled('grade') && $request->grade !== 'all') {
            $query->where('grade', $request->grade);
        }

        // فلتر المدرس (للأدمن) والمادة — ديناميكية تعدد المدرسين
        if ($request->filled('teacher') && $request->teacher !== 'all') {
            $query->where('teacher_id', $request->teacher);
        }

        if ($request->filled('subject') && $request->subject !== 'all') {
            $query->where('subject', $request->subject);
        }

        // دعم Ajax DataTable بنفس ستايل getModelData لو مطلوب
        if ($request->ajax() && $request->has('draw')) {
            return getModelData(
                new Video(),
                relations: ['teacher:id,name'],
                andsFilters: $user && $user->type === 'student' && $user->grade
                    ? [['grade', '=', $user->grade]]
                    : []
            );
        }

        return $query->paginate(12);
    }

    public function store(array $data)
    {
        return Video::create($data);
    }

    public function update(array $data, $video)
    {
        $video->update($data);
        return $video;
    }

    public function destroy($video)
    {
        // مسح الثمبنيل المرفوع لو موجود
        if (!empty($video->thumbnail_url) && !str_starts_with($video->thumbnail_url, 'http')) {
            deleteImageFromDirectory(basename($video->thumbnail_url), 'Video');
        }

        return $video->delete();
    }

    public function show($video)
    {
        return $video->load('teacher:id,name');
    }

    public function find($id)
    {
        return Video::find($id);
    }

    public function incrementViews($video)
    {
        $video->increment('views_count');
        return $video;
    }
}
