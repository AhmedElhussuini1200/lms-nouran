<?php

namespace App\Services\Dashboard;

use App\Models\Role;
use App\Models\Admin;
use App\Http\Requests\Dashboard\StoreAdminRequest;
use App\Repositories\Dashboard\Contracts\AdminRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


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
        $students = Admin::where('type', 'student')->orderBy('name')->get(['id', 'name', 'grade']);

        return view('dashboard.admin.admins.create', compact('roles', 'types', 'grades', 'students'));
    }

    public function store(StoreAdminRequest $request)
    {
        $data = $request->validated();
        $admin = DB::transaction(function () use ($data) {
            $admin = $this->adminRepository->store(collect($data)->except(['children', 'parent_name', 'parent_email', 'parent_phone'])->toArray());

            // ولي الأمر يتربط بأبنائه إجباري
            if ($admin->type === 'parent') {
                $admin->students()->sync($data['children'] ?? []);
            }

            // ولي الأمر جوه فورم الطالب: موجود؟ اربط — مش موجود؟ أنشئ واربط
            if ($admin->type === 'student' && (! empty($data['parent_name']) || ! empty($data['parent_email']) || ! empty($data['parent_phone']))) {
                $parent = null;
                if (! empty($data['parent_email'])) {
                    $parent = Admin::where('email', $data['parent_email'])->first();
                }
                if (! $parent && ! empty($data['parent_phone'])) {
                    $parent = Admin::where('phone', $data['parent_phone'])->first();
                }
                if (! $parent) {
                    $parent = Admin::create([
                        'name' => $data['parent_name'] ?: __('ولي أمر') . ' ' . $admin->name,
                        'email' => $data['parent_email'] ?: 'parent-' . $admin->id . '@lms.local',
                        'phone' => $data['parent_phone'] ?? null,
                        'type' => 'parent',
                        'password' => Hash::make('12345678'),
                    ]);
                }
                $parent->students()->syncWithoutDetaching([$admin->id]);
            }

            return $admin;
        });

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
        $teachers = $admin->type === 'student' ? Admin::where('type', 'teacher')->orderBy('name')->get(['id', 'name', 'subject']) : collect();

        return view('dashboard.admin.admins.edit', compact('admin', 'roles', 'types', 'grades', 'students', 'teachers'));
    }

    // صفحة رفع شيت الطلبة
    public function importForm()
    {
        return view('dashboard.admin.admins.import');
    }

    // تنفيذ رفع الشيت (إنشاء + تحديث الموجود + تسجيل مع مدرسين)
    public function importStore(\Illuminate\Http\Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120']]);
        $import = new \App\Imports\StudentsImport();
        \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
        $s = $import->stats();

        return redirect()->route('admin.admins.index', ['type' => 'student'])
            ->with('success', __('تم الاستيراد') . ": {$s['created']} " . __('جديد') . " • {$s['updated']} " . __('تحديث') . ($s['failed'] ? " • {$s['failed']} " . __('صف مرفوض') : ''));
    }

    // قالب الشيت
    public function importTemplate()
    {
        $rows = collect([['name' => 'اسم الطالب', 'email' => 'student@mail.com', 'phone' => '01xxxxxxxxx', 'grade' => '1', 'password' => '12345678', 'teacher_emails' => 'teacher@lms.com', 'parent_name' => 'ولي الأمر', 'parent_email' => 'parent@mail.com', 'parent_phone' => '01xxxxxxxxx']]);

        return \Maatwebsite\Excel\Facades\Excel::download(new class($rows) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function __construct(public $rows) {}
            public function collection()
            {
                return $this->rows;
            }
            public function headings(): array
            {
                return ['name', 'email', 'phone', 'grade', 'password', 'teacher_emails', 'parent_name', 'parent_email', 'parent_phone'];
            }
        }, 'students-template.xlsx');
    }

    // إضافة سريعة repeater: صفحة
    public function quickForm()
    {
        $grades = ['1_secondary' => __('الأول الثانوي'), '2_secondary' => __('الثاني الثانوي'), '3_secondary' => __('الثالث الثانوي')];
        $teachers = Admin::where('type', 'teacher')->orderBy('name')->get(['id', 'name', 'subject']);

        return view('dashboard.admin.admins.quick', compact('grades', 'teachers'));
    }

    // إضافة سريعة repeater: حفظ صفوف متعددة
    public function quickStore(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'rows' => ['required', 'array', 'min:1', 'max:50'],
            'rows.*.name' => ['required', 'string', 'max:255'],
            'rows.*.email' => ['required', 'email', 'max:255', 'distinct'],
            'rows.*.phone' => ['nullable', 'string', 'max:20'],
            'rows.*.grade' => ['required', 'in:1_secondary,2_secondary,3_secondary'],
            'rows.*.teacher_id' => ['nullable', 'exists:admins,id'],
        ]);
        $created = 0;
        $skipped = [];
        foreach ($request->rows as $i => $row) {
            if (Admin::where('email', $row['email'])->exists()) {
                $skipped[] = $i + 1;
                continue;
            }
            $s = Admin::create([
                'name' => $row['name'], 'email' => $row['email'], 'phone' => $row['phone'] ?? null,
                'type' => 'student', 'grade' => $row['grade'],
                'password' => \Illuminate\Support\FacadesHash::make('12345678'),
            ]);
            if (! empty($row['teacher_id'])) {
                $s->enrolledTeachers()->attach($row['teacher_id']);
            }
            $created++;
        }
        $msg = __('تم إنشاء') . " $created " . __('طالب') . ' (' . __('كلمة المرور الافتراضية') . ': 12345678)';
        if ($skipped) {
            $msg .= ' • ' . __('تخطي صفوف مكررة') . ': ' . implode('، ', $skipped);
        }

        return redirect()->route('admin.admins.index', ['type' => 'student'])->with('success', $msg);
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

        // تسجيل الطالب مع المدرسين (المواد)
        if ($admin->type === 'student' && array_key_exists('teachers', $data)) {
            $admin->enrolledTeachers()->sync($data['teachers'] ?? []);
            unset($data['teachers']);
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
        DB::transaction(function () use ($data, $admin) {
            $this->adminRepository->destroy($data, $admin);

            // طالب اتمسح → أولياء أموره اللي مبقاش لهم أبناء عايشين يتمسحوا معاه (soft)
            if ($admin->type === 'student') {
                foreach ($admin->parents()->withTrashed()->get() as $parent) {
                    $hasLiving = $parent->students()->where('admins.id', '!=', $admin->id)->exists();
                    if (! $hasLiving && ! $parent->trashed()) {
                        $parent->delete();
                    }
                }
            }
        });

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
