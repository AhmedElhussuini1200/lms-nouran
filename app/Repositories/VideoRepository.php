<?php

namespace App\Repositories;

use App\Interfaces\VideoRepositoryInterface;
use App\Models\Video;

class VideoRepository extends BaseRepository implements VideoRepositoryInterface
{
    public function __construct(Video $model)
    {
        parent::__construct($model);
    }

    public function getByTeacher($teacherId)
    {
        return $this->model->where('teacher_id', $teacherId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByGrade($grade)
    {
        return $this->model->where('grade', $grade)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRecent($limit = 5)
    {
        return $this->model->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function incrementViews($id)
    {
        $video = $this->find($id);
        $video->increment('views_count');
        return $video;
    }
}

