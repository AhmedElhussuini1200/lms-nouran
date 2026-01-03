<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VideoService;
use App\Services\AuthService;

class VideoController extends Controller
{
    protected $videoService;
    protected $authService;

    public function __construct(VideoService $videoService, AuthService $authService)
    {
        $this->middleware('auth');
        $this->videoService = $videoService;
        $this->authService = $authService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videos = $this->videoService->getAllVideos();
        return view('videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = $this->authService->getUser();
        if (!$user || !$user->isTeacher()) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }
        return view('videos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $this->authService->getUser();
        if (!$user || !$user->isTeacher()) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade' => 'required|in:1_secondary,2_secondary,3_secondary',
            'video_url' => 'required|url',
            'thumbnail_url' => 'nullable|url',
            'duration_seconds' => 'nullable|integer|min:0',
        ]);

        $this->videoService->createVideo($data);
        return redirect()->route('videos.index')->with('success', 'تم إضافة الفيديو بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->videoService->incrementViews($id);
        $video = $this->videoService->getVideoById($id);
        return view('videos.show', compact('video'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = $this->authService->getUser();
        if (!$user || !$user->isTeacher()) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }
        $video = $this->videoService->getVideoById($id);
        return view('videos.edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = $this->authService->getUser();
        if (!$user || !$user->isTeacher()) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade' => 'required|in:1_secondary,2_secondary,3_secondary',
            'video_url' => 'required|url',
            'thumbnail_url' => 'nullable|url',
            'duration_seconds' => 'nullable|integer|min:0',
        ]);

        $this->videoService->updateVideo($id, $data);
        return redirect()->route('videos.index')->with('success', 'تم تحديث الفيديو بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = $this->authService->getUser();
        if (!$user || !$user->isTeacher()) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }
        $this->videoService->deleteVideo($id);
        return redirect()->route('videos.index')->with('success', 'تم حذف الفيديو بنجاح');
    }
}
