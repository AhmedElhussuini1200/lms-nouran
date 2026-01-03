<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AssignmentService;
use App\Services\AuthService;
use App\Models\AssignmentSubmission;

class AssignmentController extends Controller
{
    protected $assignmentService;
    protected $authService;

    public function __construct(AssignmentService $assignmentService, AuthService $authService)
    {
        $this->middleware('auth');
        $this->assignmentService = $assignmentService;
        $this->authService = $authService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = $this->assignmentService->getAllAssignments();
        return view('assignments.index', compact('assignments'));
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
        return view('assignments.create');
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
            'description' => 'required|string',
            'grade' => 'required|in:1_secondary,2_secondary,3_secondary',
            'due_date' => 'required|date',
            'total_marks' => 'required|integer|min:1',
            'file' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('assignments', 'public');
        }

        $this->assignmentService->createAssignment($data);
        return redirect()->route('assignments.index')->with('success', 'تم إضافة الواجب بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $assignment = $this->assignmentService->getAssignmentById($id);
        $submission = null;
        
        $user = $this->authService->getUser();
        if ($user->isStudent()) {
            $submission = AssignmentSubmission::where('assignment_id', $id)
                ->where('student_id', $user->id)
                ->first();
        }
        
        return view('assignments.show', compact('assignment', 'submission'));
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
        $assignment = $this->assignmentService->getAssignmentById($id);
        return view('assignments.edit', compact('assignment'));
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
            'description' => 'required|string',
            'grade' => 'required|in:1_secondary,2_secondary,3_secondary',
            'due_date' => 'required|date',
            'total_marks' => 'required|integer|min:1',
            'file' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('assignments', 'public');
        }

        $this->assignmentService->updateAssignment($id, $data);
        return redirect()->route('assignments.index')->with('success', 'تم تحديث الواجب بنجاح');
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
        $this->assignmentService->deleteAssignment($id);
        return redirect()->route('assignments.index')->with('success', 'تم حذف الواجب بنجاح');
    }
}
