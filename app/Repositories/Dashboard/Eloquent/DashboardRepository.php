<?php

namespace App\Repositories\Dashboard\Eloquent;

use Carbon\Carbon;
use App\Models\City;
use App\Models\User;
use App\Models\Field;
use App\Models\Status;
use App\Models\Mission;
use App\Models\Transaction;
use App\Repositories\Dashboard\Contracts\DashboardRepositoryInterface;

class DashboardRepository implements DashboardRepositoryInterface
{
    // implement methods

 public function index($request)
{
    $locale = app()->getLocale(); // 'en' or 'ar' or others

    // Top cities based on user count for the current month
    $column = "cities.name_{$locale}";
    $startOfCurrentMonth = Carbon::now()->startOfMonth();
    $endOfCurrentMonth = Carbon::now()->endOfMonth();
    $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
    $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

    $currentMonthcountUsers = User::whereBetween('users.created_at', [$startOfCurrentMonth, $endOfCurrentMonth])->count() ?? 0;
    $lastMonthcountUsers = User::whereBetween('users.created_at', [$startOfLastMonth, $endOfLastMonth])->count() ?? 0;

    // [$calculatePercentage, $trend] = $this->getChangePercentage($currentMonthcountUsers, $lastMonthcountUsers) ?? [0, 'neutral'];

    // $topCitiesBasedUser = \DB::table('users')
    //     ->join('cities', 'users.city_id', '=', 'cities.id')
    //     ->select("$column as city", \DB::raw('COUNT(users.id) as user_count'))
    //     ->whereBetween('users.created_at', [$startOfCurrentMonth, $endOfCurrentMonth])
    //     ->whereNotNull('users.city_id')
    //     ->groupBy($column)
    //     ->orderByDesc('user_count')
    //     ->limit(5)
    //     ->get() ?? collect([]);

    // Monthly activities by field

    // // In-progress missions this month
    // $result = $this->getInProgressMissionsThisMonth($startOfCurrentMonth, $endOfCurrentMonth, $startOfLastMonth, $endOfLastMonth) ?? [];
    // $deliveredResult = $this->getDeliveredMissionsThisMonth($startOfCurrentMonth, $endOfCurrentMonth, $startOfLastMonth, $endOfLastMonth) ?? [];

    // // Total commissions
    // $totalOwnerCommission = $this->totalOwnerCommission($startOfCurrentMonth, $endOfCurrentMonth) ?? ['totalOwnerCommission' => 0, 'uniqueUsers' => 0, 'remainingCount' => 0];
    // $totalProviderCommission = $this->totalProviderCommission($startOfCurrentMonth, $endOfCurrentMonth) ?? ['totalProviderCommission' => 0, 'uniqueUsers' => 0, 'remainingCount' => 0];

    // // users verification status
    // $userVerificationStatus = $this->userVerificationStatus($startOfCurrentMonth, $endOfCurrentMonth, $startOfLastMonth, $endOfLastMonth) ?? [
    //     'calculatePercentageStatus' => 0,
    //     'usersTrendStatus' => 'neutral',
    //     'totalUsers' => 0,
    //     'totalUsersAproved' => 0,
    //     'totalUsersNotAproved' => 0,
    //     'approvedPercent' => 0,
    //     'notApprovedPercent' => 0,
    // ];

    // // Top providers
    // $topProviders = $this->topProvidersBasedOffer($startOfCurrentMonth, $endOfCurrentMonth) ?? collect([]);

    // // Most active fields
    // [$mostActiveFields, $avgCompletion] = $this->mostActiveFields($startOfCurrentMonth, $endOfCurrentMonth) ?? [collect([]), 0];

    // // Active Missions
    // $activeMissions = $this->activeMissions() ?? ['activeMissions' => 0, 'pendingMissions' => 0, 'pendingPercentage' => 0];

    return [
        number_format($currentMonthcountUsers ?? 0, 0, '.', ','),
        $topCitiesBasedUser ?? [],
        $calculatePercentage ?? 0,
        $trend ?? 'neutral',
        $topFields ?? [],
        $countFields ?? 0,
        $result['chartData'] ?? [],
        $result['totalMissions'] ?? 0,
        $result['calculateMissionDeliveriesPercentage'] ?? 0,
        $result['trendMissionDeliveries'] ?? 'neutral',
        $deliveredResult['chartData'] ?? [],
        $deliveredResult['totalMissions'] ?? 0,
        $deliveredResult['calculateMissionDeliveriesPercentage'] ?? 0,
        $deliveredResult['trendMissionDeliveries'] ?? 'neutral',
        $totalOwnerCommission['totalOwnerCommission'] ?? 0,
        $totalOwnerCommission['uniqueUsers'] ?? 0,
        $totalOwnerCommission['remainingCount'] ?? 0,
        $totalProviderCommission['totalProviderCommission'] ?? 0,
        $totalProviderCommission['uniqueUsers'] ?? 0,
        $totalProviderCommission['remainingCount'] ?? 0,
        $userVerificationStatus['calculatePercentageStatus'] ?? 0,
        $userVerificationStatus['usersTrendStatus'] ?? 'neutral',
        $userVerificationStatus['totalUsers'] ?? 0,
        $userVerificationStatus['totalUsersAproved'] ?? 0,
        $userVerificationStatus['totalUsersNotAproved'] ?? 0,
        $userVerificationStatus['approvedPercent'] ?? 0,
        $userVerificationStatus['notApprovedPercent'] ?? 0,
        $topProviders ?? [],
        $mostActiveFields ?? [],
        $avgCompletion ?? 0,
        $activeMissions ?? [],
        $activeMissions['activeMissions'] ?? 0,
        $activeMissions['pendingMissions'] ?? 0,
        $activeMissions['pendingPercentage'] ?? 0,
    ];
}



}
