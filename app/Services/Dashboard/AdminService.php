<?php

namespace App\Services\Dashboard;

use App\Models\Role;
use App\Models\Admin;
use App\Http\Requests\Dashboard\StoreAdminRequest;
use App\Repositories\Dashboard\Contracts\AdminRepositoryInterface;

class AdminService
{
    protected $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function index($request)
    {
        abort_unless(auth('admin')->user()->type === 'admin', 403);
        if ($request->ajax() && $request->has('draw')) {
            return response()->json($this->adminRepository->index($request));
        }

        $admins = $this->adminRepository->index($request);
        $types = ['admin' => __('إدمن'), 'teacher' => __('مدرس'), 'student' => __('طالب'), 'parent' => __('ولي أمر')];
        $grades = ['' => __('الكل'), '1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        return view('dashboard.admin.admins.index', compact('admins', 'types', 'grades'));
    }

    public function show($admin)
    {
        $admin = $this->adminRepository->show($admin);

        $profile = null;
        if ($admin->type === 'student') {
            $submissions = \App\Models\AssignmentSubmission::with(['assignment:id,title,total_marks', 'status'])
                ->where('student_id', $admin->id)->latest()->get();
            $results = \App\Models\ExamResult::with(['exam:id,title,total_marks', 'status'])
                ->where('student_id', $admin->id)->latest()->get();
            $absences = \App\Models\Attendance::with('course:id,title')
                ->where('student_id', $admin->id)->where('status', 'absent')
                ->orderByDesc('date')->limit(10)->get();
            $absenceCount = \App\Models\Attendance::where('student_id', $admin->id)->where('status', 'absent')->count();
            $presentCount = \App\Models\Attendance::where('student_id', $admin->id)->where('status', 'present')->count();
            $payments = \App\Models\Payment::with('status')->where('student_id', $admin->id)->orderByDesc('month')->get();
            $assignedIds = $submissions->pluck('assignment_id');
            $missing = \App\Models\Assignment::where('grade', $admin->grade)
                ->whereNotIn('id', $assignedIds)->orderBy('due_date')->limit(5)->get();

            $profile = [
                'submissions' => $submissions,
                'results' => $results,
                'absences' => $absences,
                'absenceCount' => $absenceCount,
                'presentCount' => $presentCount,
                'payments' => $payments,
                'missing' => $missing,
                'avg' => $results->avg('marks_obtained') ? round($results->avg('marks_obtained'), 1) : null,
                'points' => $submissions->count() * 10 + $results->count() * 20,
                'dueTotal' => $payments->sum('amount') - $payments->sum('paid_amount'),
            ];
        }

        return view('dashboard.admin.admins.show', compact('admin', 'profile'));
    }

    public function create()
    {
        abort_unless(auth('admin')->user()->type === 'admin', 403);
        $roles = Role::all();
        $types = ['admin' => __('إدمن'), 'teacher' => __('مدرس'), 'student' => __('طالب'), 'parent' => __('ولي أمر')];
        $grades = ['1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];

        return view('dashboard.admin.admins.create', compact('roles', 'types', 'grades'));
    }

    public function store(StoreAdminRequest $request)
    {
        $data = $request->validated();
        $admin = $this->adminRepository->store($data);

        if ($request->ajax()) {
            return response()->json(['message' => __('تمت الإضافة بنجاح'), 'url' => route('admin.admins.show', $admin->id)]);
        }

        return redirect()->route('admin.admins.show', $admin->id)->with('success', __('تمت الإضافة بنجاح'));
    }

    public function edit($admin)
    {
        $roles = Role::all();
        $types = ['admin' => __('إدمن'), 'teacher' => __('مدرس'), 'student' => __('طالب'), 'parent' => __('ولي أمر')];
        $grades = ['1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];
        $students = $admin->type === 'parent' ? Admin::where('type', 'student')->orderBy('name')->get(['id', 'name', 'grade']) : collect();

        return view('dashboard.admin.admins.edit', compact('admin', 'roles', 'types', 'grades', 'students'));
    }

    public function update($data, $admin)
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // ربط الأبناء بولي الأمر
        if ($admin->type === 'parent' && array_key_exists('children', $data)) {
            $admin->students()->sync($data['children'] ?? []);
            unset($data['children']);
        }

        $this->adminRepository->update($data, $admin);

        if (request()->ajax()) {
            return response()->json(['message' => __('تم التحديث بنجاح'), 'url' => route('admin.admins.show', $admin->id)]);
        }

        return redirect()->route('admin.admins.show', $admin->id)->with('success', __('تم التحديث بنجاح'));
    }

    public function destroy($data, $admin)
    {
        if ($admin->id === auth('admin')->id()) {
            abort(400, __('لا يمكنك حذف حسابك'));
        }

        $isAjax = $data instanceof \Illuminate\Http\Request ? $data->ajax() : request()->ajax();
        $this->adminRepository->destroy($data, $admin);

        if ($isAjax) {
            return response()->json(['message' => __('تم الحذف بنجاح'), 'url' => route('admin.admins.index')]);
        }

        return redirect()->route('admin.admins.index')->with('success', __('تم الحذف بنجاح'));
    }

    public function deleteSelected($data)
    {
        $ids = collect($data['selected_items_ids'] ?? [])->reject(fn ($id) => (int) $id === (int) auth('admin')->id())->values()->toArray();

        return $this->adminRepository->deleteSelected($ids);
    }

    public function restoreSelected($data)
    {
        return $this->adminRepository->restoreSelected($data);
    }

    public function restore($data, $admin)
    {
        return $this->adminRepository->restore($data, $admin);
    }

    public function status($admin)
    {
        return $this->adminRepository->status($admin);
    }

    public function isValid($request, $admin)
    {
        return $this->adminRepository->isValid($request, $admin);
    }
}
