<?php

namespace App\Repositories\Dashboard\Contracts;

use Illuminate\Http\Request;

interface PaymentRepositoryInterface
{
    public function index(Request $request);
    public function store(array $data);
    public function update(array $data, $payment);
    public function destroy($payment);
    public function monthlySummary(?string $year = null): array;
}
