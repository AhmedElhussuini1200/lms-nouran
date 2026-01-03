<?php

namespace App\Services;

use App\Interfaces\VideoRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class VideoService
{
    protected $videoRepository;

    public function __construct(VideoRepositoryInterface $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    public function getAllVideos($paginate = 15)
    {
        $user = Auth::user();
        
        if ($user->isTeacher()) {
            $videos = $this->videoRepository->getByTeacher($user->id);
        } else {
            $videos = $this->videoRepository->getByGrade($user->grade);
        }
        
        // Convert collection to paginated results
        $page = request()->get('page', 1);
        $perPage = $paginate;
        $items = $videos->forPage($page, $perPage);
        $total = $videos->count();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getVideoById($id)
    {
        return $this->videoRepository->find($id);
    }

    public function createVideo(array $data)
    {
        $data['teacher_id'] = Auth::id();
        return $this->videoRepository->create($data);
    }

    public function updateVideo($id, array $data)
    {
        return $this->videoRepository->update($id, $data);
    }

    public function deleteVideo($id)
    {
        return $this->videoRepository->delete($id);
    }

    public function incrementViews($id)
    {
        return $this->videoRepository->incrementViews($id);
    }

    public function getRecentVideos($limit = 5)
    {
        $user = Auth::user();
        
        if ($user->isTeacher()) {
            return $this->videoRepository->getByTeacher($user->id)
                ->sortByDesc('created_at')
                ->take($limit);
        }
        
        return $this->videoRepository->getByGrade($user->grade)
            ->sortByDesc('created_at')
            ->take($limit);
    }
}

