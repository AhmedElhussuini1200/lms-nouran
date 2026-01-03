<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\CourseRepositoryInterface;
use App\Interfaces\AssignmentRepositoryInterface;
use App\Interfaces\ExamRepositoryInterface;
use App\Interfaces\VideoRepositoryInterface;
use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\CourseRepository;
use App\Repositories\AssignmentRepository;
use App\Repositories\ExamRepository;
use App\Repositories\VideoRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Exam;
use App\Models\Video;
use App\Models\Notification;
use App\Models\User;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Interfaces to Repositories
        $this->app->bind(CourseRepositoryInterface::class, function ($app) {
            return new CourseRepository(new Course());
        });

        $this->app->bind(AssignmentRepositoryInterface::class, function ($app) {
            return new AssignmentRepository(new Assignment());
        });

        $this->app->bind(ExamRepositoryInterface::class, function ($app) {
            return new ExamRepository(new Exam());
        });

        $this->app->bind(VideoRepositoryInterface::class, function ($app) {
            return new VideoRepository(new Video());
        });

        $this->app->bind(NotificationRepositoryInterface::class, function ($app) {
            return new NotificationRepository(new Notification());
        });

        $this->app->bind(UserRepositoryInterface::class, function ($app) {
            return new UserRepository(new User());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
