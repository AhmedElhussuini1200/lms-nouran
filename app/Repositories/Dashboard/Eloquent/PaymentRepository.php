<?php

namespace App\Repositories\Dashboard\Eloquent;

use App\Models\Payment;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Repositories\Dashboard\Contracts\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function index(Request $request)
    {
        $user = auth('admin')->user();
        $query = Payment::with(['student:id,name,grade', 'status'])->orderBy('month', 'desc');

        if ($user && $user->type === 'student') {
            $query->where('student_id', $user->id);
        }

        if ($user && $user->type === 'parent') {
            $query->whereIn('student_id', $user->students()->pluck('admins.id'));
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->whereHas('status', fn ($q) => $q->where('slug', $request->status));
        }

        return $query->paginate(15);
    }

    public function store(array $data)
    {
        $data['status_id'] = $this->resolveStatus($data);
        return Payment::create($data);
    }

    public function update(array $data, $payment)
    {
        $merged = array_merge($payment->toArray(), $data);
        $data['status_id'] = $this->resolveStatus($merged);
        $payment->update($data);
        return $payment;
    }

    public function destroy($payment)
    {
        return $payment->delete();
    }

    public function monthlySummary(?string $year = null): array
    {
        $year = $year ?? date('Y');
        $rows = Payment::selectRaw('month, SUM(amount) as total, SUM(paid_amount) as paid')
            ->where('month', 'like', $year . '-%')
            ->groupBy('month')->orderBy('month')->get();

        return [
            'year' => $year,
            'rows' => $rows,
            'total' => $rows->sum('total'),
            'paid' => $rows->sum('paid'),
            'remaining' => $rows->sum('total') - $rows->sum('paid'),
        ];
    }

    protected function resolveStatus(array $data): ?int
    {
        $amount = (float) ($data['amount'] ?? 0);
        $paid = (float) ($data['paid_amount'] ?? 0);

        $slug = Status::PENDING;
        if ($amount > 0 && $paid >= $amount) {
            $slug = Status::PAID;
        } elseif ($paid > 0) {
            $slug = Status::PARTIAL;
        }

        return Status::idFor($slug);
    }
}
