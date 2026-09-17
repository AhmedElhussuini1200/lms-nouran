<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title',
        'description',
        'grade',
        'teacher_id',
        'subject',
        'video_url',
        'thumbnail_url',
        'duration_seconds',
        'views_count',
    ];

    public function teacher()
    {
        return $this->belongsTo(Admin::class, 'teacher_id');
    }

    public function progresses()
    {
        return $this->hasMany(VideoProgress::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getYoutubeIdAttribute(): ?string
    {
        return youtubeId($this->video_url);
    }

    public function getEmbedUrlAttribute(): ?string
    {
        return youtubeEmbed($this->video_url);
    }
}
