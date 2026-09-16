<?php

namespace Tests\Feature;

use App\Models\Payment;

class PaymentTest extends LmsTestCase
{
    public function test_admin_can_create_invoice(): void
    {
        $response = $this->loginAs($this->admin)->postJson('/dashboard/payments', [
            'student_id' => $this->student->id,
            'month' => '2026-10',
            'amount' => 400,
            'paid_amount' => 0,
            'method' => 'cash',
        ], $this->ajaxHeaders());

        $response->assertOk()->assertJsonStructure(['message', 'url']);
        $this->assertDatabaseHas('payments', [
            'student_id' => $this->student->id, 'month' => '2026-10',
        ]);
    }

    public function test_duplicate_invoice_returns_friendly_error_not_sql(): void
    {
        Payment::create([
            'student_id' => $this->student->id, 'month' => '2026-10',
            'amount' => 400, 'paid_amount' => 0, 'created_by' => $this->admin->id,
        ]);

        $response = $this->loginAs($this->admin)->postJson('/dashboard/payments', [
            'student_id' => $this->student->id, 'month' => '2026-10', 'amount' => 400,
        ], $this->ajaxHeaders());

        $response->assertStatus(422)->assertJsonValidationErrors('month');
        $this->assertStringNotContainsString('SQLSTATE', $response->getContent());
    }

    public function test_partial_payment_status_auto(): void
    {
        $payment = Payment::create([
            'student_id' => $this->student->id, 'month' => '2026-11',
            'amount' => 400, 'paid_amount' => 0, 'created_by' => $this->admin->id,
        ]);

        $this->loginAs($this->admin)->putJson("/dashboard/payments/{$payment->id}", [
            'amount' => 400, 'paid_amount' => 150,
        ], $this->ajaxHeaders())->assertOk();

        $this->assertEquals('partial', $payment->fresh()->status->slug);
    }

    public function test_full_payment_status_paid(): void
    {
        $payment = Payment::create([
            'student_id' => $this->student->id, 'month' => '2026-12',
            'amount' => 400, 'paid_amount' => 0, 'created_by' => $this->admin->id,
        ]);

        $this->loginAs($this->admin)->putJson("/dashboard/payments/{$payment->id}", [
            'amount' => 400, 'paid_amount' => 400,
        ], $this->ajaxHeaders())->assertOk();

        $this->assertEquals('paid', $payment->fresh()->status->slug);
    }

    public function test_teacher_cannot_create_invoice(): void
    {
        $this->loginAs($this->teacher)->postJson('/dashboard/payments', [
            'student_id' => $this->student->id, 'month' => '2026-10', 'amount' => 100,
        ], $this->ajaxHeaders())->assertStatus(403);
    }

    public function test_student_sees_own_invoices_only(): void
    {
        $other = $this->makeUser('s9@lms.test', 'student', '2_secondary');
        Payment::create([
            'student_id' => $other->id, 'month' => '2026-10',
            'amount' => 100, 'paid_amount' => 0, 'created_by' => $this->admin->id,
        ]);

        $response = $this->loginAs($this->student)->get('/dashboard/payments');
        $response->assertOk()->assertDontSee('s9@lms.test', false);
    }

    public function test_monthly_pdf_downloads(): void
    {
        Payment::create([
            'student_id' => $this->student->id, 'month' => '2026-10',
            'amount' => 400, 'paid_amount' => 100, 'created_by' => $this->admin->id,
        ]);

        $response = $this->loginAs($this->admin)->get('/dashboard/payments/monthly-pdf?month=2026-10');
        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }
}
