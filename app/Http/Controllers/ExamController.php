<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExamService;
use App\Services\AuthService;
use App\Models\ExamResult;

class ExamController extends Controller
{
    protected $examService;
    protected $authService;

    public function __construct(ExamService $examService, AuthService $authService)
    {
        $this->middleware('auth');
        $this->examService = $examService;
        $this->authService = $authService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = $this->examService->getAllExams();
        return view('exams.index', compact('exams'));
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
        return view('exams.create');
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
            'exam_date' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
        ]);

        $this->examService->createExam($data);
        return redirect()->route('exams.index')->with('success', 'تم إضافة الامتحان بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $exam = $this->examService->getExamById($id);
        $result = null;
        
        $user = $this->authService->getUser();
        if ($user->isStudent()) {
            $result = ExamResult::where('exam_id', $id)
                ->where('student_id', $user->id)
                ->first();
        }
        
        return view('exams.show', compact('exam', 'result'));
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
        $exam = $this->examService->getExamById($id);
        return view('exams.edit', compact('exam'));
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
            'exam_date' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
        ]);

        $this->examService->updateExam($id, $data);
        return redirect()->route('exams.index')->with('success', 'تم تحديث الامتحان بنجاح');
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
        $this->examService->deleteExam($id);
        return redirect()->route('exams.index')->with('success', 'تم حذف الامتحان بنجاح');
    }
}
