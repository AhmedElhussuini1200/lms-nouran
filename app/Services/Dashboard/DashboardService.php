<?php

namespace App\Services\Dashboard;

use App\Models\Abstracte;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContarctItem;
use App\Models\District;
use App\Models\Extinguisher;
use App\Models\Mission;
use App\Models\Station;
use App\Models\Neighborhood;
use App\Models\Status;
use App\Models\Warehouse;
use Carbon\Carbon;
use App\Repositories\Dashboard\Contracts\DashboardRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    protected $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }


    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();

        if (! $user) {
            return redirect()->route('admin.login-form');
        }

        // توجيه حسب نوع المستخدم إلى الـ views اللي عندك في resources/views/dashboard
        switch ($user->type) {
            case 'admin':
                return view('dashboard.admin.index');
            case 'teacher':
                return view('dashboard.teacher', [
                    // TODO: هنا تحط الـ data الفعلية من الكورسات/الواجبات...الخ
                    'stats' => [
                        'courses' => 0,
                        'assignments' => 0,
                        'exams' => 0,
                        'videos' => 0,
                    ],
                    'upcomingCourses' => collect(),
                    'pendingAssignments' => collect(),
                    'recentVideos' => collect(),
                ]);
            case 'student':
                return view('dashboard.student', [
                    'stats' => [
                        'courses' => 0,
                        'assignments' => 0,
                        'exams' => 0,
                        'videos' => 0,
                    ],
                    'upcomingCourses' => collect(),
                    'pendingAssignments' => collect(),
                    'recentVideos' => collect(),
                ]);
            case 'parent':
                return view('dashboard.parent');
            default:
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login-form');
        }
    }

    //     // فلاتر التاريخ من الواجهة (YYYY-MM-DD)
    //     $startDate = $request->input('start_date');
    //     $endDate   = $request->input('end_date');

    //     // KPIs
    //     $totalCompanies = Company::count();
    //     $totalEmployees = Admin::where('type', 'admin')->count();
    //     $totalConsultants = Admin::where('type', 'consultant')->count();
    //     $totalContractors = Admin::where('type', 'contractor')->count();

    //     // المهام
    //     // فلترة المهام حسب التاريخ (إن وُجد)
    //     $missionsBaseQuery = Mission::with(['station', 'approvedBy', 'rejectedBy']);
    //     if ($startDate && $endDate) {
    //         $start = Carbon::parse($startDate)->startOfDay();
    //         $end   = Carbon::parse($endDate)->endOfDay();

    //         $missionsBaseQuery->whereBetween('created_at', [$start, $end]);
    //     }

    //     $totalMissions = (clone $missionsBaseQuery)->count();
    //     // استخدام Status IDs مباشرة
    //     $newId = 1; // new
    //     $pendingId = 2; // pending
    //     $reviewingId = 3; // reviewing
    //     $inProgressId = 4; // in-progress
    //     $completedId = 5; // completed
    //     $rejectedId = 6; // rejected

    //     // المهام الجديدة: حالة "new" ولم يتم اعتمادها بعد
    //     $missionsNew = (clone $missionsBaseQuery)
    //         ->where('statue_id', $newId)
    //         ->whereNull('approved_by')
    //         ->count();
    //     $missionsPending = (clone $missionsBaseQuery)
    //         ->where('statue_id', $pendingId)
    //         ->count();
    //     $missionsReviewing = (clone $missionsBaseQuery)
    //         ->where('statue_id', $reviewingId)
    //         ->count();
    //     $missionsInProgress = (clone $missionsBaseQuery)
    //         ->where('statue_id', $inProgressId)
    //         ->count();
    //     $missionsCompleted = (clone $missionsBaseQuery)
    //         ->where('is_completed', 1)
    //         ->count();
    //     $missionsRejected = (clone $missionsBaseQuery)
    //         ->where('statue_id', $rejectedId)
    //         ->count();

    //     // قوائم المهام حسب حالة الاعتماد / الرفض لاستخدامها في جدول المهام في الداشبورد
    //     $missionsAllList = (clone $missionsBaseQuery)->limit(3)->get();
    //     $missionsPendingList = $missionsAllList->filter(function ($mission) {
    //         return is_null($mission->approved_by) && is_null($mission->rejected_by);
    //     });
    //     $missionsApprovedList = $missionsAllList->filter(function ($mission) {
    //         return !is_null($mission->approved_by);
    //     });
    //     $missionsRejectedList = $missionsAllList->filter(function ($mission) {
    //         return !is_null($mission->rejected_by);
    //     });

    //     // المستخلصات (Abstractes) في حالة Pending مع فلترة التاريخ
    //     $abstractesBaseQuery = Abstracte::with(['contract', 'createdBy'])
    //         ->where('status_id', $pendingId);

    //     if ($startDate && $endDate) {
    //         $start = Carbon::parse($startDate)->startOfDay();
    //         $end   = Carbon::parse($endDate)->endOfDay();

    //         $abstractesBaseQuery->whereBetween('created_at', [$start, $end]);
    //     }

    //     $pendingAbstractsCount = (clone $abstractesBaseQuery)->count();
    //     $pendingAbstracts = (clone $abstractesBaseQuery)->limit(5)->get();

    //     // Contract Items في حالة Pending مع فلترة التاريخ
    //     $contractItemsBaseQuery = ContarctItem::with(['contract', 'status', 'createdBy'])
    //         ->where('status_id', $pendingId);

    //     if ($startDate && $endDate) {
    //         $start = Carbon::parse($startDate)->startOfDay();
    //         $end   = Carbon::parse($endDate)->endOfDay();

    //         $contractItemsBaseQuery->whereBetween('created_at', [$start, $end]);
    //     }

    //     $pendingContractItemsCount = (clone $contractItemsBaseQuery)->count();
    //     $pendingContractItems = (clone $contractItemsBaseQuery)->limit(5)->get();

    //     // الإطفاءات الجديدة التي لم يُسجل عليها أي إجراء (لا توجد Logs)
    //     $newExtinguishersBaseQuery = Extinguisher::with(['city', 'district', 'neighborhood', 'station'])
    //         ->whereDoesntHave('logs');

    //     if ($startDate && $endDate) {
    //         $start = Carbon::parse($startDate)->startOfDay();
    //         $end   = Carbon::parse($endDate)->endOfDay();

    //         $newExtinguishersBaseQuery->whereBetween('created_at', [$start, $end]);
    //     }

    //     $pendingExtinguishersStatus1Count = (clone $newExtinguishersBaseQuery)->count();
    //     $newExtinguishersWithoutAction = (clone $newExtinguishersBaseQuery)->limit(5)->get();

    //     // عدد الموظفين في كل حي (منطقة) لاستخدامه في ويدجت "Top Selling" بدلاً من الداتا الوهمية
    //     $employeesByNeighborhood = District::withCount('admins')
    //         ->having('admins_count', '>', 0)
    //         ->orderByDesc('admins_count')
    //         ->limit(7)
    //         ->get();

    //     // نقاط المحطات على الخريطة (تتطلب وجود حقول x / y في جدول المحطات)
    //     // نعيد رقم اللوحة، الاسم، وحالة التفعيل لاستخدامها في الخريطة
    //     $missionsForMap = Station::select('id', 'x', 'y', 'plate_number', 'name_ar', 'is_active')
    //         ->whereNotNull('x')
    //         ->whereNotNull('y')
    //         ->get();

    //     // حساب عدد المحطات حسب حالة التفعيل
    //     $stationsActiveCount = Station::where('is_active', 1)
    //         ->whereNotNull('x')
    //         ->whereNotNull('y')
    //         ->count();
    //     $stationsInactiveCount = Station::where('is_active', 0)
    //         ->whereNotNull('x')
    //         ->whereNotNull('y')
    //         ->count();
    //     // جلب الـ Status IDs مرة واحدة
    //     $extStatusIds = Status::whereIn('name_en', ['pending', 'in_progress', 'completed'])
    //         ->pluck('id', 'name_en');

    //     $totalExtinguishers = Extinguisher::count();
    //     $extinguishersPending = Extinguisher::where('status_id', $extStatusIds['pending'] ?? 0)->count();
    //     $extinguishersInProgress = Extinguisher::where('status_id', $extStatusIds['in_progress'] ?? 0)->count();
    //     $extinguishersCompleted = Extinguisher::where('status_id', $extStatusIds['completed'] ?? 0)->count();

    //     $extinguishersPercentage = [
    //         'pending' => $totalExtinguishers ? round(($extinguishersPending / $totalExtinguishers) * 100, 1) : 0,
    //         'in_progress' => $totalExtinguishers ? round(($extinguishersInProgress / $totalExtinguishers) * 100, 1) : 0,
    //         'completed' => $totalExtinguishers ? round(($extinguishersCompleted / $totalExtinguishers) * 100, 1) : 0,
    //     ];

    //     // التحليل حسب المقاول / الشركة
    //     $extinguishersByCompany = Extinguisher::select('company_id')
    //         ->selectRaw('count(*) as total')
    //         ->groupBy('company_id')
    //         ->with('company') // علاقة في الموديل
    //         ->get();
    //     $contractors_count = Contract::count();
    //     $missionsStatus = [
    //         'labels' => ['Pending', 'In Progress', 'Completed'],
    //         'series' => [
    //             $missionsPending,
    //             $missionsInProgress,
    //             $missionsCompleted,
    //         ],
    //         'colors' => ['#f59e0b', '#0ea5e9', '#16a34a'], // برتقالي، أزرق، أخضر
    //     ];
    //     $district_count = District::count();
    //     // $mission_count = Mission::count();

    //     // جلب أول 3 مناطق مع عدد الشركات والأشخاص والإطفاءات
    //     $districts = District::withCount([ 'extinguishers','missions','people', 'companies'])->get();

    //     $calDistricts = $districts->map(function ($d) {
    //         $d->total =  $d->extinguisher_count;
    //         return $d;
    //     });




    //     $topDistricts = $calDistricts->sortByDesc(function ($d) {
    //         return $d->total; // إجمالي الأشخاص + الشركات + المطافئ
    //     })->take(3);



    //     // حساب نسبة النمو بناءً على بيانات الشهر السابق لكل منطقة
    //     $colors = ['#f59e0b', 'success', 'warning'];
    //     $chartData = [
    //         'labels' => $districts->pluck('name'),
    //         // 'people' => $districts->pluck('people_count'),
    //         // 'companies' => $districts->pluck('companies_count'),
    //         'extinguishers' => $districts->pluck('extinguisher_count'),
    //         // 'missions' => $districts->pluck('mission_count'),
    //         'colors' => $colors

    //     ];
    //     // dd($chartData);

    //     // Count totals for dashboard widgets
    //     $totalWarehouses = Warehouse::count();
    //     $totalContractItems = ContarctItem::count();
    //     $totalAbstracts = Abstracte::count();
    //     $totalStations = Station::count();
    //     $stationsActiveCount = Station::where('is_active', 1)->count();
    //     $stationsInactiveCount = Station::where('is_active', 0)->count();
    //     $data = compact(
    //         'contractors_count',
    //         'totalCompanies',
    //         'missionsStatus',
    //         'totalEmployees',
    //         'totalConsultants',
    //         'totalContractors',
    //         'totalMissions',
    //         'missionsNew',
    //         'missionsPending',
    //         'missionsReviewing',
    //         'missionsInProgress',
    //         'missionsCompleted',
    //         'missionsRejected',
    //         'extStatusIds',
    //         'totalExtinguishers',
    //         'extinguishersPending',
    //         'extinguishersInProgress',
    //         'extinguishersCompleted',
    //         'extinguishersPercentage',
    //         'extinguishersByCompany',
    //         'missionsForMap',
    //         'stationsActiveCount',
    //         'stationsInactiveCount',
    //         'startDate',
    //         'endDate',
    //         'missionsAllList',
    //         'missionsPendingList',
    //         'missionsApprovedList',
    //         'missionsRejectedList',
    //         'district_count',
    //         'districts',
    //         'chartData',
    //         'topDistricts',
    //         'employeesByNeighborhood',
    //         'pendingAbstracts',
    //         'pendingAbstractsCount',
    //         'pendingContractItems',
    //         'pendingContractItemsCount',
    //         'newExtinguishersWithoutAction',
    //         'pendingExtinguishersStatus1Count',
    //         'totalWarehouses',
    //         'totalContractItems',
    //         'totalAbstracts',
    //         'totalStations',
    //         'stationsActiveCount',
    //         'stationsInactiveCount',
    //     );


    //     if ($AdminType === 'admin') {
    //         return view('dashboard.admin.dashboard', $data);
    //     } elseif ($AdminType === 'consultant') {
    //         return view('dashboard.consultant.dashboard', $data);
    //     } elseif ($AdminType === 'contractor') {
    //         return view('dashboard.contractor.dashboard', $data);
    //     }

    //     return view('welcome');
    // }
}
