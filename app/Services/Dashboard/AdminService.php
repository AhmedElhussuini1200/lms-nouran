<?php

namespace App\Services\Dashboard;


use App\Models\City;
use App\Models\Role;
use App\Models\Admin;
use App\Models\Sector;
use App\Models\Company;
use App\Models\Contract;
use App\Models\District;
use Illuminate\Support\Arr;
use App\Models\ContractType;
use App\Models\Municipality;
use App\Models\Neighborhood;
use Illuminate\Support\Facades\Auth;
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
        $user = auth()->user();

        // تحضير المتغيرات الأساسية للـ View
        $cities = City::with('districts.neighborhoods.sectors')->get();
        $isBlockeds = [
            ''  => __('All'),
            0   => __('Active'),
            1   => __('Blocked'),
        ];
        $employees = [];

        // 🔹 الحالة الخاصة بطلب Ajax
        if ($request->ajax()) {
            $query = $this->adminRepository->index($request);
            return response()->json($query);
        }

        // 🔹 لو المستخدم مدير، هات الموظفين اللي تحته وفي نفس المنطقة
        if ($user->type === 'admin') {
            $employees = Admin::where('reporting_to_id', $user->id)
                ->get();
        }
        $roles = Role::where('company_id', null)->get();


        // مفيش داعي نجيب كل الإداريين لو هنفلترهم بعدين
        $admins = Admin::with('company')->get();



        // $admins = $admins->get();

        $types = [
            'admin' => __("Amana"),
            'consultant' => __("consultant"),
            'contractor' => __("contractor"),
        ];



        return view('dashboard.admin.admins.index', compact(
            'cities',
            'admins',
            'roles',
            'types',
            'isBlockeds',
            'employees'
        ));
    }


    public function show($admin)
    {
        // جلب البيانات من الـ repository
        $admin = $this->adminRepository->show($admin);

        $roles = Role::all();

        $cities = City::with('districts.neighborhoods.sectors')->get();

        $types = [
            'admin'      => __('Amana'),
            'consultant' => __('consultant'),
            'contractor' => __('contractor'),
        ];

        return view('dashboard.admin.admins.show', compact('admin', 'cities', 'roles', 'types'));
    }



    public function create()
    {
        $cities = City::with('districts.neighborhoods.sectors')->get();
        $companies = Company::select('id', 'name_ar', 'email', 'phone', 'type')
            ->whereDoesntHave('superadmin')
            ->get();
        $roles = Role::where('company_id', '=', null)
            ->get();

        $contractTypes = ContractType::all();
        $contracts = Contract::all();

        $municipalities = Municipality::all();

        $managers = Admin::all();
        // dd($companies);


        return view('dashboard.admin.admins.create', compact(
            'cities',
            'companies',
            'contractTypes',
            'managers',
            'roles',
            'contracts',
            'municipalities'
        ));
    }

    // Service
    public function store(StoreAdminRequest $request)
    {
        // في Laravel Controller
        $data = $request->validated();
        if ($request->hasFile('stamp')) {
            $data['stamp'] = uploadImageToDirectory($request->file('stamp'), 'Images/Admins/stamps'); //path to directory in storage public folder Images/Images/Admins/stamps
        }

        if ($request->hasFile('signature')) {
            $data['signature'] = uploadImageToDirectory($request->file('signature'), 'Images/Admins/signatures'); //path to directory in storage public folder Images/Images/Admins/signatures
        }
        return $this->adminRepository->store($data);
    }


    public function edit($admin)
    {

        $cities = City::with('districts.neighborhoods.sectors')->get();

        $companies = Company::all();
        $contractTypes = ContractType::all();
        $municipalities = Municipality::all();

        $managers = Admin::where('id', '!=', $admin->id)->get();


        // dd($managers);
        $contracts = Contract::all();
        $roles = Role::all();
        $types = [
            'admin'      => __('Amana'),
            'consultant' => __('consultant'),
            'contractor' => __('contractor'),
        ];


        return view('dashboard.admin.admins.edit', compact(
            'admin',
            'cities',
            'municipalities',
            'companies',
            'contractTypes',
            'contracts',
            'managers',
            'roles',
            'types'
        ));
    }

    public function update($data, $admin)
    {
        $updateData = $data;

        // ✅ معالجة كلمة المرور
        if (empty($updateData['password'])) {
            unset($updateData['password']);
        }

        // معالجة الختم (Stamp)
        if (!empty($data['stamp']) && $data['stamp'] instanceof \Illuminate\Http\UploadedFile) {
            if (!empty($admin->stamp) && file_exists(storage_path('app/public/Images/Admins/stamps/' . $admin->stamp))) {
                unlink(storage_path('app/public/Images/Admins/stamps/' . $admin->stamp));
            }
            $updateData['stamp'] = uploadImageToDirectory($data['stamp'], 'Images/Admins/stamps');
        } else {
            unset($updateData['stamp']);
        }

        // معالجة الإمضاء (Signature)
        if (!empty($data['signature']) && $data['signature'] instanceof \Illuminate\Http\UploadedFile) {
            if (!empty($admin->signature) && file_exists(storage_path('app/public/Images/Admins/signatures/' . $admin->signature))) {
                unlink(storage_path('app/public/Images/Admins/signatures/' . $admin->signature));
            }
            $updateData['signature'] = uploadImageToDirectory($data['signature'], 'Images/Admins/signatures');
        } else {
            unset($updateData['signature']);
        }

        return $this->adminRepository->update($updateData, $admin);
    }



    public function destroy($data, $admin)
    {
        if ($admin->id === auth()->id()) {
            abort(400, __('You cannot delete your own account'));
        }

        abort_if(
            $admin->employees()->exists(),
            400,
            __('This admin cannot be deleted because it is linked to existing tasks or employees')
        );

        if ($data->ajax()) {
            return $this->adminRepository->destroy($data, $admin);
        }
    }
    //   public function deleteSelected($data)
    //     {
    //         return $this->adminRepository->deleteSelected($data);
    //     }

    public function deleteSelected($data)
    {
        $adminIds = $data->input('selected_items_ids', []);

        // ✅ التحقق من أن كل مستخدم صالح للحذف
        foreach ($adminIds as $id) {
            $admin = $this->adminRepository->find($id);

            if (!$admin) {
                abort(404, __('admin not found'));
            }

            abort_if(
                $admin->employees()->exists(),
                400,
                __('This admin cannot be deleted because it is linked to existing tasks or employees')
            );
        }

        // ✅ استبعاد المستخدم الحالي مباشرة من قاعدة البيانات
        $ids = Admin::whereIn('id', $adminIds)
            ->where('id', '!=', auth()->id())
            ->pluck('id')     // ناخد IDs فقط
            ->toArray();      // نحولها لمصفوفة عادية

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

    private function checkContractExpiration(array $updateData): array
    {
        if (!empty($updateData['end_contract_date']) && now()->greaterThan($updateData['end_contract_date'])) {
            $updateData['is_blocked'] = true;
        }

        return $updateData;
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
