<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\VideoService;
use App\Http\Requests\Dashboard\StoreVideoRequest;
use App\Http\Requests\Dashboard\UpdateVideoRequest;

class VideoController extends Controller
{
    protected $service;

    public function __construct(VideoService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_videos');
        return $this->service->index($request);
    }

    public function create()
    {
        $this->authorize('create_videos');
        return $this->service->create();
    }

    public function store(StoreVideoRequest $request)
    {
        $this->authorize('create_videos');
        return $this->service->store($request);
    }

    public function show(Video $video)
    {
        $this->authorize('view_videos');
        return $this->service->show($video);
    }

    public function edit(Video $video)
    {
        $this->authorize('update_videos');
        return $this->service->edit($video);
    }

    public function update(UpdateVideoRequest $request, Video $video)
    {
        $this->authorize('update_videos');
        return $this->service->update($request, $video);
    }

    public function destroy(Request $request, Video $video)
    {
        $this->authorize('delete_videos');
        return $this->service->destroy($request, $video);
    }
}
