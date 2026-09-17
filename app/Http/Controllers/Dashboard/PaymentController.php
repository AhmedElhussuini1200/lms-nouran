<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dashboard\PaymentService;

class PaymentController extends Controller
{
    protected $service;

    public function __construct(PaymentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_payments');
        return $this->service->index($request);
    }

    public function create()
    {
        $this->authorize('create_payments');
        return $this->service->create();
    }

    public function store(Request $request)
    {
        $this->authorize('create_payments');
        return $this->service->store($request);
    }

    public function edit(Payment $payment)
    {
        $this->authorize('update_payments');
        return $this->service->edit($payment);
    }

    public function update(Request $request, Payment $payment)
    {
        $this->authorize('update_payments');
        return $this->service->update($request, $payment);
    }

    public function destroy(Request $request, Payment $payment)
    {
        $this->authorize('delete_payments');
        return $this->service->destroy($request, $payment);
    }

    public function monthlyPdf(Request $request)
    {
        $this->authorize('view_payments');
        return $this->service->monthlyPdf($request);
    }

    public function generate(Request $request)
    {
        $this->authorize('create_payments');
        $request->validate(['month' => ['nullable', 'regex:/^\d{4}-\d{2}$/']]);
        \Artisan::call('lms:invoices', ['month' => $request->month ?: date('Y-m')]);

        return redirect()->route('admin.payments.index', ['month' => $request->month ?: date('Y-m')])
            ->with('success', trim(\Artisan::output()));
    }
}
