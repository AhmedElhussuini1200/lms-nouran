<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionBank;
use Illuminate\Http\Request;

// بنك الأسئلة: إضافة + تجميع امتحان متوازن (سهل/متوسط/صعب)
class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);
        $q = QuestionBank::with('teacher:id,name')->latest();
        if (auth('admin')->user()->type === 'teacher') {
            $q->where('teacher_id', auth('admin')->id());
        }
        if ($request->grade) {
            $q->where('grade', $request->grade);
        }
        if ($request->difficulty) {
            $q->where('difficulty', $request->difficulty);
        }
        if ($request->topic) {
            $q->where('topic', 'like', "%{$request->topic}%");
        }
        $items = $q->paginate(20);

        return view('dashboard.bank.index', compact('items'));
    }

    public function store(Request $request)
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);
        $data = $request->validate([
            'grade' => ['required', 'in:1_secondary,2_secondary,3_secondary'],
            'type' => ['required', 'in:mcq,true_false,essay'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'topic' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'question' => ['required', 'string'],
            'options' => ['nullable', 'array'],
            'correct_answer' => ['nullable', 'string'],
            'marks' => ['nullable', 'numeric', 'min:0'],
        ]);
        $data['teacher_id'] = auth('admin')->id();
        QuestionBank::create($data);

        return back()->with('success', __('تمت الإضافة لبنك الأسئلة'));
    }

    // تجميع امتحان من البنك: POST {exam_id, counts: {easy, medium, hard}, topic?}
    public function assemble(Request $request, Exam $exam)
    {
        abort_unless(in_array(auth('admin')->user()->type, ['admin', 'teacher']), 403);
        $request->validate([
            'easy' => ['nullable', 'integer', 'min:0', 'max:50'],
            'medium' => ['nullable', 'integer', 'min:0', 'max:50'],
            'hard' => ['nullable', 'integer', 'min:0', 'max:50'],
            'topic' => ['nullable', 'string'],
        ]);

        $sort = (int) ($exam->questions()->max('sort') ?? 0);
        $added = 0;
        foreach (['easy' => 1, 'medium' => 2, 'hard' => 3] as $level => $ord) {
            $n = (int) ($request->input($level, 0));
            if ($n <= 0) {
                continue;
            }
            $pool = QuestionBank::where('grade', $exam->grade)
                ->where('difficulty', $level)
                ->when($request->topic, fn ($qq) => $qq->where('topic', 'like', "%{$request->topic}%"))
                ->inRandomOrder()->limit($n)->get();
            foreach ($pool as $b) {
                Question::create([
                    'exam_id' => $exam->id, 'type' => $b->type, 'question' => $b->question,
                    'options' => $b->options, 'correct_answer' => $b->correct_answer,
                    'marks' => $b->marks, 'sort' => ++$sort,
                ]);
                $added++;
            }
        }

        return redirect()->route('admin.exams.show', $exam->id)->with('success', __('تمت إضافة أسئلة') . ': ' . $added);
    }
}
