<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\ExamService;
use App\Http\Requests\Dashboard\StoreExamRequest;
use App\Http\Requests\Dashboard\UpdateExamRequest;
use App\Http\Requests\Dashboard\StoreQuestionRequest;

class ExamController extends Controller
{
    protected $service;

    public function __construct(ExamService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_exams');
        return $this->service->index($request);
    }

    public function create()
    {
        $this->authorize('create_exams');
        return $this->service->create();
    }

    public function store(StoreExamRequest $request)
    {
        $this->authorize('create_exams');
        return $this->service->store($request);
    }

    public function show(Exam $exam)
    {
        $this->authorize('view_exams');
        return $this->service->show($exam);
    }

    public function edit(Exam $exam)
    {
        $this->authorize('update_exams');
        return $this->service->edit($exam);
    }

    public function update(UpdateExamRequest $request, Exam $exam)
    {
        $this->authorize('update_exams');
        return $this->service->update($request, $exam);
    }

    public function destroy(Request $request, Exam $exam)
    {
        $this->authorize('delete_exams');
        return $this->service->destroy($request, $exam);
    }

    public function submit(Request $request, Exam $exam)
    {
        return $this->service->submit($request, $exam);
    }

    public function startAttempt(Request $request, Exam $exam)
    {
        return $this->service->startAttempt($request, $exam);
    }

    public function grade(Request $request, ExamResult $result)
    {
        return $this->service->grade($request, $result);
    }

    public function addQuestion(StoreQuestionRequest $request, Exam $exam)
    {
        return $this->service->addQuestion($request, $exam);
    }

    public function deleteQuestion(Request $request, Question $question)
    {
        return $this->service->deleteQuestion($request, $question);
    }
}
