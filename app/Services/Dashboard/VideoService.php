<?php

namespace App\Services\Dashboard;

use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\StoreVideoRequest;
use App\Http\Requests\Dashboard\UpdateVideoRequest;
use App\Repositories\Dashboard\Contracts\VideoRepositoryInterface;

class VideoService
{
    protected $videoRepository;

    public function __construct(VideoRepositoryInterface $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    public function index(Request $request)
    {
        $videos = $this->videoRepository->index($request);

        if ($request->ajax() && $request->has('draw')) {
            return response()->json($videos);
        }

        $grades = [
            '' => __('All'),
            '1_secondary' => __('الأول الثانوي'),
            '2_secondary' => __('الثاني الثانوي'),
            '3_secondary' => __('الثالث الثانوي'),
        ];

        $teachers = auth('admin')->user()->type === 'admin'
            ? \App\Models\Admin::where('type', 'teacher')->orderBy('name')->get(['id', 'name', 'subject'])
            : collect();

        return view('dashboard.videos.index', compact('videos', 'grades', 'teachers'));
    }

    public function create()
    {
        $grades = [
            '1_secondary' => __('الأول الثانوي'),
            '2_secondary' => __('الثاني الثانوي'),
            '3_secondary' => __('الثالث الثانوي'),
        ];

        return view('dashboard.videos.create', compact('grades'));
    }

    public function store(StoreVideoRequest $request)
    {
        $data = $request->validated();
        $data['teacher_id'] = auth('admin')->id();

        // تحويل رابط يوتيوب العادي لرابط embed
        if (!empty($data['video_url'])) {
            $data['video_url'] = $this->normalizeVideoUrl($data['video_url']);
        }

        // رفع الثمبنيل لو مرفوع كملف
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_url'] = 'storage/Images/Videos/' . uploadImageToDirectory($request->file('thumbnail'), 'Video');
            unset($data['thumbnail']);
        }

        $video = $this->videoRepository->store($data);

        // إشعار طلاب نفس الصف + أولياء أمورهم بفيديو جديد (ميزة تنافسية: تنبيه لحظي)
        notifyStudentsByGrade($video->grade, __('فيديو جديد'), $video->title, 'info', route('admin.videos.show', $video->id));

        if ($request->ajax()) {
            return response()->json([
                'message' => __('تم إضافة الفيديو بنجاح'),
                'url' => route('admin.videos.show', $video->id),
            ]);
        }

        return redirect()->route('admin.videos.show', $video->id)
            ->with('success', __('تم إضافة الفيديو بنجاح'));
    }

    public function show(Video $video)
    {
        $this->authorizeView($video);
        $video = $this->videoRepository->show($video);
        $this->videoRepository->incrementViews($video);

        $related = Video::where('grade', $video->grade)
            ->where('id', '!=', $video->id)
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard.videos.show', compact('video', 'related'));
    }

    public function edit(Video $video)
    {
        $this->authorizeOwner($video);

        $grades = [
            '1_secondary' => __('الأول الثانوي'),
            '2_secondary' => __('الثاني الثانوي'),
            '3_secondary' => __('الثالث الثانوي'),
        ];

        return view('dashboard.videos.edit', compact('video', 'grades'));
    }

    public function update(UpdateVideoRequest $request, Video $video)
    {
        $this->authorizeOwner($video);

        $data = $request->validated();

        if (!empty($data['video_url'])) {
            $data['video_url'] = $this->normalizeVideoUrl($data['video_url']);
        }

        if ($request->hasFile('thumbnail')) {
            if (!empty($video->thumbnail_url) && !str_starts_with($video->thumbnail_url, 'http')) {
                deleteImageFromDirectory(basename($video->thumbnail_url), 'Video');
            }
            $data['thumbnail_url'] = 'storage/Images/Videos/' . uploadImageToDirectory($request->file('thumbnail'), 'Video');
            unset($data['thumbnail']);
        }

        $this->videoRepository->update($data, $video);

        if ($request->ajax()) {
            return response()->json([
                'message' => __('تم تحديث الفيديو بنجاح'),
                'url' => route('admin.videos.show', $video->id),
            ]);
        }

        return redirect()->route('admin.videos.show', $video->id)
            ->with('success', __('تم تحديث الفيديو بنجاح'));
    }

    public function destroy(Request $request, Video $video)
    {
        $this->authorizeOwner($video);

        $this->videoRepository->destroy($video);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم حذف الفيديو بنجاح'), 'url' => route('admin.videos.index')]);
        }

        return redirect()->route('admin.videos.index')
            ->with('success', __('تم حذف الفيديو بنجاح'));
    }

    protected function authorizeOwner(Video $video): void
    {
        $user = auth('admin')->user();

        if ($user->type === 'admin') {
            return;
        }

        abort_if($video->teacher_id !== $user->id, 403, __('غير مصرح لك بتعديل هذا الفيديو'));
    }

    protected function authorizeView(Video $video): void
    {
        $user = auth('admin')->user();

        if ($user->type === 'admin' || $user->type === 'parent') {
            return;
        }

        if ($user->type === 'teacher') {
            abort_if($video->teacher_id !== $user->id, 403, __('غير مصرح لك'));
        }

        if ($user->type === 'student') {
            abort_if($video->grade !== $user->grade, 403, __('غير مصرح لك'));
        }
    }

    protected function normalizeVideoUrl(string $url): string
    {
        // youtube watch -> embed
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0';
        }

        // youtu.be short -> embed
        if (preg_match('/youtu\.be\/([^?&]+)/', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0';
        }

        return $url;
    }
}
