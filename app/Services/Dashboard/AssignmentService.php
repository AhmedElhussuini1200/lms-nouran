<?php

namespace App\Services\Dashboard;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\StoreAssignmentRequest;
use App\Http\Requests\Dashboard\UpdateAssignmentRequest;
use App\Repositories\Dashboard\Contracts\AssignmentRepositoryInterface;
use App\Services\WhatsappService;

class AssignmentService
{
    protected $assignmentRepository;
    protected $whatsapp;

    public function __construct(AssignmentRepositoryInterface $assignmentRepository, WhatsappService $whatsapp)
    {
        $this->assignmentRepository = $assignmentRepository;
        $this->whatsapp = $whatsapp;
    }

    public function index(Request $request)
    {
        $assignments = $this->assignmentRepository->index($request);
        $grades = ['' => __('الكل'), '1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        $teachers = auth('admin')->user()->type === 'admin'
            ? \App\Models\Admin::where('type', 'teacher')->orderBy('name')->get(['id', 'name', 'subject'])
            : collect();

        return view('dashboard.assignments.index', compact('assignments', 'grades', 'teachers'));
    }

    public function create()
    {
        $grades = ['1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        return view('dashboard.assignments.create', compact('grades'));
    }

    public function store(StoreAssignmentRequest $request)
    {
        $data = $request->validated();
        $data['teacher_id'] = auth('admin')->id();

        if ($request->hasFile('file')) {
            $data['file_path'] = 'storage/' . $request->file('file')->store('assignments', 'public');
            unset($data['file']);
        }

        $assignment = $this->assignmentRepository->store($data);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم إضافة الواجب بنجاح'), 'url' => route('admin.assignments.show', $assignment->id)]);
        }

        return redirect()->route('admin.assignments.show', $assignment->id)->with('success', __('تم إضافة الواجب بنجاح'));
    }

    public function show(Assignment $assignment)
    {
        $this->authorizeView($assignment);
        $assignment = $this->assignmentRepository->show($assignment);
        $user = auth('admin')->user();
        $mySubmission = null;

        if ($user->type === 'student') {
            $mySubmission = $this->assignmentRepository->mySubmission($assignment, $user->id);
        }

        return view('dashboard.assignments.show', compact('assignment', 'mySubmission'));
    }

    public function edit(Assignment $assignment)
    {
        $this->authorizeOwner($assignment);
        $grades = ['1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        return view('dashboard.assignments.edit', compact('assignment', 'grades'));
    }

    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        $this->authorizeOwner($assignment);
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file_path'] = 'storage/' . $request->file('file')->store('assignments', 'public');
            unset($data['file']);
        }

        $this->assignmentRepository->update($data, $assignment);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم تحديث الواجب بنجاح'), 'url' => route('admin.assignments.show', $assignment->id)]);
        }

        return redirect()->route('admin.assignments.show', $assignment->id)->with('success', __('تم تحديث الواجب بنجاح'));
    }

    public function destroy(Request $request, Assignment $assignment)
    {
        $this->authorizeOwner($assignment);
        $this->assignmentRepository->destroy($assignment);

        if ($request->ajax()) {
            return response()->json(['message' => __('تم حذف الواجب بنجاح'), 'url' => route('admin.assignments.index')]);
        }

        return redirect()->route('admin.assignments.index')->with('success', __('تم حذف الواجب بنجاح'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        abort_unless(auth('admin')->user()->type === 'student', 403);
        $this->authorizeView($assignment);
        $request->validate([
            'submission_text' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,png,webp', 'max:20480'],
        ]);

        abort_if(!$request->submission_text && !$request->hasFile('file'), 422, __('أدخل نصاً أو أرفق ملفاً'));

        $data = ['student_id' => auth('admin')->id(), 'submission_text' => $request->submission_text];

        if ($request->hasFile('file')) {
            $data['file_path'] = 'storage/' . $request->file('file')->store('submissions', 'public');
        }

        $submission = $this->assignmentRepository->submit($assignment, $data);

        // إشعار المدرس + أولياء أمور الطالب
        $student = auth('admin')->user();
        notifyAdmin($assignment->teacher_id, __('تسليم واجب جديد'), $student->name . ' - ' . $assignment->title, 'info', route('admin.assignments.show', $assignment->id));

        if ($request->ajax()) {
            return response()->json(['message' => __('تم تسليم الواجب بنجاح'), 'url' => route('admin.assignments.show', $assignment->id)]);
        }

        return redirect()->route('admin.assignments.show', $assignment->id)->with('success', __('تم تسليم الواجب بنجاح'));
    }

    public function grade(Request $request, AssignmentSubmission $submission)
    {
        $request->validate([
            'marks' => ['nullable', 'numeric', 'min:0'],
            'teacher_feedback' => ['nullable', 'string'],
            'status' => ['required', 'in:under_review,graded,returned'],
        ]);

        $this->authorizeOwner($submission->assignment);
        $data = $request->only(['marks', 'teacher_feedback']);
        $data['status_id'] = \App\Models\Status::idFor($request->status);

        $this->assignmentRepository->grade($submission, $data);

        // إشعار الطالب + أولياء أموره بالنتيجة والحالة
        $statusName = \App\Models\Status::where('slug', $request->status)->value('name_ar') ?? $request->status;
        $link = route('admin.assignments.show', $submission->assignment_id);
        notifyAdmin($submission->student_id, __('تحديث تصحيح الواجب') . ': ' . $statusName, __('درجتك') . ': ' . $request->marks . ' - ' . $request->teacher_feedback, $request->status === 'graded' ? 'success' : 'warning', $link);
        foreach ($submission->student->parents ?? [] as $parent) {
            notifyAdmin($parent->id, __('متابعة واجب') . ': ' . $statusName, $submission->student->name . ' - ' . $request->marks, $request->status === 'graded' ? 'success' : 'warning', $link);
            // واتساب ولي الأمر عند رصد درجة الواجب
            $this->whatsapp->send(
                $parent->phone ?? '',
                __('متابعة واجب') . ': ' . $submission->student->name . ' - ' . $submission->assignment->title . ' - ' . $statusName . ' - ' . __('درجته') . ': ' . $request->marks,
                $parent->whatsapp_key ?? '',
                $parent->id
            );
        }

        if ($request->ajax()) {
            return response()->json(['message' => __('تم رصد الدرجة بنجاح'), 'url' => $link]);
        }

        return redirect()->back()->with('success', __('تم رصد الدرجة بنجاح'));
    }

    public function startReview(Request $request, AssignmentSubmission $submission)
    {
        $this->authorizeOwner($submission->assignment);
        $this->assignmentRepository->grade($submission, ['status_id' => \App\Models\Status::idFor(\App\Models\Status::UNDER_REVIEW)]);
        notifyAdmin($submission->student_id, __('بدء تصحيح واجبك'), $submission->assignment->title, 'warning', route('admin.assignments.show', $submission->assignment_id));

        if ($request->ajax()) {
            return response()->json(['message' => __('تم بدء التصحيح'), 'url' => route('admin.assignments.show', $submission->assignment_id)]);
        }

        return redirect()->back()->with('success', __('تم بدء التصحيح'));
    }

    protected function authorizeOwner(Assignment $assignment): void
    {
        $user = auth('admin')->user();
        if ($user->type === 'admin') {
            return;
        }
        abort_if($assignment->teacher_id !== $user->id, 403, __('غير مصرح لك'));
    }

    protected function authorizeView(Assignment $assignment): void
    {
        $user = auth('admin')->user();
        if ($user->type === 'admin' || $user->type === 'parent') {
            return;
        }
        if ($user->type === 'teacher') {
            abort_if($assignment->teacher_id !== $user->id, 403, __('غير مصرح لك'));
        }
        if ($user->type === 'student') {
            abort_if($assignment->grade !== $user->grade, 403, __('غير مصرح لك'));
        }
    }
}
