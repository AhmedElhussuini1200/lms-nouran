<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreAdminRequest;
use App\Http\Requests\Dashboard\UpdateAdminRequest;
use App\Models\Company;
use App\Models\Municipality;
use App\Models\Role;
use App\Services\Dashboard\AdminService;

class AdminController extends Controller
{
    protected $service;

    public function __construct(AdminService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request)
    {
        $this->authorize('view_admins');
        return $this->service->index($request);
    }

    public function create()
    {
        $this->authorize('create_admins');
        return $this->service->create();
    }

    public function show(Admin $admin)
    {
        $this->authorize('view_admins');
        return $this->service->show($admin);
    }

    // Controller
    public function store(StoreAdminRequest $request)
    {
        $this->authorize('create_admins');
        return $this->service->store($request);
    }

    public function edit(Admin $admin)
    {

        $this->authorize('update_admins');


        return $this->service->edit($admin);
    }


    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $this->authorize('update_admins');
        return $this->service->update($request->validated(), $admin);
    }
    public function destroy(Request $request, Admin $admin)
    {
        $this->authorize('delete_admins');
        return $this->service->destroy($request, $admin);
    }
    public function deleteSelected(Request $request)
    {
        $this->authorize('delete_admins');
        return $this->service->deleteSelected($request->all());
    }
    public function restoreSelected(Request $request)
    {
        $this->authorize('delete_admins');
        return $this->service->restoreSelected($request);
    }

    public function restore(Request $request, Admin $admin)
    {
        $this->authorize('delete_admins');
        return $this->service->restore($request, $admin);
    }

    public function status(Request $request, Admin $admin)
    {
        $this->authorize('update_admins');
        return response($this->service->status($admin));
    }

    public function isValid(Request $request, Admin $admin)
    {
        $this->authorize('is_valid_admins');
        $this->service->isValid($request, $admin);
        return response(['successful']);
    }

    public function getCompaniesByType($type)
    {
        $types = ['consultant', 'contractor'];

        if (!in_array($type, $types)) {
            return response()->json([]);
        }

        $companies = Company::where('type', $type)->get();

        return response()->json($companies);
    }

    public function getAdminsByCompany($company_id)
    {
        // هات الإداريين جوه نفس الشركة
        $admins = Admin::where('company_id', $company_id)->get();

        return response()->json($admins);
    }


    public function getAdminsWithoutCompany()
    {
        return Admin::whereNull('company_id')
                     ->get(['id', 'name']);


    }


    public function getRolesByCompany(Company $company)
    {
        // نفترض أن كل Role مرتبط بشركة عبر علاقة company_roles
        // أو إذا عندك حقل company_id في roles
        $roles = $company->roles()->select('id', 'name_ar', 'name_en')->get();

        return response()->json($roles);
    }

    public function getAdminRolesNoCompany()
    {
        $roles = Role::whereNull('company_id')
                     ->select('id', 'name_ar')
                     ->get();

        return response()->json($roles);
    }

    public function getMunicipalities($cityId)
    {
        $municipalities = Municipality::where('city_id', $cityId)
            ->select('id', 'name_ar', 'name_en')
            ->get();

        return response()->json($municipalities);
    }

}
