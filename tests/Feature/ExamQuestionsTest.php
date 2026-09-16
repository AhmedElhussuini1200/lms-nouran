<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Course;
use App\Models\Question;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;

class ExamQuestionsTest extends LmsTestCase
{
    public function test_teacher_can_add_mcq_question(): void
    {
        $exam = $this->makeExam();

        $this->loginAs($this->teacher)->post("/dashboard/exams/{$exam->id}/questions", [
            'type' => 'mcq',
            'question' => 'عاصمة مصر؟',
            'options' => ['القاهرة', 'الجيزة'],
            'correct_answer' => 'القاهرة',
            'marks' => 5,
        ])->assertRedirect();

        $this->assertDatabaseHas('questions', ['exam_id' => $exam->id, 'question' => 'عاصمة مصر؟']);
    }

    public function test_mcq_auto_graded_and_essay_goes_to_review(): void
    {
        $exam = $this->makeExam();
        $mcq = Question::create([
            'exam_id' => $exam->id, 'type' => 'mcq', 'question' => 'Q1',
            'options' => ['a', 'b'], 'correct_answer' => 'a', 'marks' => 5,
        ]);
        $essay = Question::create([
            'exam_id' => $exam->id, 'type' => 'essay', 'question' => 'Q2', 'marks' => 10,
        ]);

        // إجابة صحيحة للاختيارات + مقالي → درجة تلقائية 5 وحالة قيد التصحيح
        $this->loginAs($this->student)->post("/dashboard/exams/{$exam->id}/submit", [
            'answers' => [$mcq->id => 'a', $essay->id => 'نص الإجابة'],
        ])->assertRedirect();

        $result = $exam->results()->where('student_id', $this->student->id)->first();
        $this->assertEquals(5, (float) $result->marks_obtained);
        $this->assertEquals('under_review', $result->status->slug);
    }

    public function test_pure_mcq_exam_graded_instantly(): void
    {
        $exam = $this->makeExam();
        $mcq = Question::create([
            'exam_id' => $exam->id, 'type' => 'mcq', 'question' => 'Q1',
            'options' => ['a', 'b'], 'correct_answer' => 'b', 'marks' => 4,
        ]);

        $this->loginAs($this->student)->post("/dashboard/exams/{$exam->id}/submit", [
            'answers' => [$mcq->id => 'a'], // إجابة خاطئة
        ])->assertRedirect();

        $result = $exam->results()->where('student_id', $this->student->id)->first();
        $this->assertEquals(0, (float) $result->marks_obtained);
        $this->assertEquals('graded', $result->status->slug);
    }

    public function test_other_teacher_cannot_add_question(): void
    {
        $exam = $this->makeExam();

        $this->loginAs($this->teacher2)->post("/dashboard/exams/{$exam->id}/questions", [
            'type' => 'essay', 'question' => 'X',
        ])->assertForbidden();
    }

    public function test_assignment_submit_notifies_teacher_and_parent(): void
    {
        $assignment = Assignment::create([
            'title' => 'W', 'grade' => '1_secondary',
            'teacher_id' => $this->teacher->id, 'total_marks' => 100,
        ]);

        $this->loginAs($this->student)->post(
            "/dashboard/assignments/{$assignment->id}/submit",
            ['submission_text' => 'حلي'],
            $this->ajaxHeaders()
        )->assertOk();

        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id, 'student_id' => $this->student->id,
        ]);
        $this->assertDatabaseHas('notifications', ['admin_id' => $this->teacher->id]);
        $this->assertDatabaseHas('notifications', ['admin_id' => $this->parent->id]);
    }

    public function test_course_title_unique_per_teacher_grade(): void
    {
        Course::create([
            'title' => 'حصة مكررة', 'grade' => '1_secondary', 'teacher_id' => $this->teacher->id,
        ]);

        $this->loginAs($this->teacher)->postJson('/dashboard/courses', [
            'title' => 'حصة مكررة', 'grade' => '1_secondary',
        ], $this->ajaxHeaders())->assertStatus(422);
    }

    protected function makeExam(): Exam
    {
        return Exam::create([
            'title' => 'امتحان', 'grade' => '1_secondary',
            'teacher_id' => $this->teacher->id, 'total_marks' => 100,
            'exam_date' => now()->addDay(), 'duration_minutes' => 60,
        ]);
    }
}
