<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\AssignmentService;
use App\Http\Requests\Dashboard\StoreAssignmentRequest;
use App\Http\Requests\Dashboard\UpdateAssignmentRequest;

class AssignmentController extends Controller
{
    protected $service;

    public function __construct(AssignmentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_assignments');
        return $this->service->index($request);
    }

    public function create()
    {
        $this->authorize('create_assignments');
        return $this->service->create();
    }

    public function store(StoreAssignmentRequest $request)
    {
        $this->authorize('create_assignments');
        return $this->service->store($request);
    }

    public function show(Assignment $assignment)
    {
        $this->authorize('view_assignments');
        return $this->service->show($assignment);
    }

    public function edit(Assignment $assignment)
    {
        $this->authorize('update_assignments');
        return $this->service->edit($assignment);
    }

    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        $this->authorize('update_assignments');
        return $this->service->update($request, $assignment);
    }

    public function destroy(Request $request, Assignment $assignment)
    {
        $this->authorize('delete_assignments');
        return $this->service->destroy($request, $assignment);
    }

    public function submit(Request $request, Assignment $assignment)
    {
        return $this->service->submit($request, $assignment);
    }

    public function grade(Request $request, AssignmentSubmission $submission)
    {
        return $this->service->grade($request, $submission);
    }

    public function startReview(Request $request, AssignmentSubmission $submission)
    {
        return $this->service->startReview($request, $submission);
    }
}
