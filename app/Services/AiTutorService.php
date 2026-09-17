<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AiLog;
use App\Models\Course;
use App\Models\Video;
use Illuminate\Support\Facades\Http;

/**
 * مساعد ذكي للمذاكرة: يعمل محلياً (بحث في محتوى المنصة + توليد أسئلة)
 * ولو مفتاح OPENAI_API_KEY موجود يستخدمه للإجابات المتقدمة.
 */
class AiTutorService
{
    // سؤال من طالب: ابحث في العناوين والأوصاف ثم لخّص
    public function ask(?Admin $user, string $question): array
    {
        $q = mb_strtolower($question);
        $keywords = array_filter(preg_split('/\s+/u', $q), fn ($w) => mb_strlen($w) > 2);

        $courses = Course::query()
            ->when($user?->grade, fn ($qq) => $qq->where('grade', $user->grade))
            ->where(function ($qq) use ($keywords, $question) {
                foreach (array_slice($keywords, 0, 5) as $k) {
                    $qq->orWhere('title', 'like', "%{$k}%")->orWhere('description', 'like', "%{$k}%");
                }
            })->limit(3)->get(['id', 'title', 'description']);

        $videos = Video::query()
            ->when($user?->grade, fn ($qq) => $qq->where('grade', $user->grade))
            ->where(function ($qq) use ($keywords) {
                foreach (array_slice($keywords, 0, 5) as $k) {
                    $qq->orWhere('title', 'like', "%{$k}%")->orWhere('description', 'like', "%{$k}%");
                }
            })->limit(3)->get(['id', 'title', 'description']);

        // لو مفتاح OpenAI موجود → إجابة LLM مع سياق الدروس
        if (config('services.openai.key')) {
            $answer = $this->llmAnswer($question, $courses, $videos);
        } else {
            $answer = $this->localAnswer($question, $courses, $videos);
        }

        AiLog::create(['admin_id' => $user?->id, 'kind' => 'ask', 'prompt' => $question, 'response' => mb_substr($answer, 0, 4000)]);

        return ['answer' => $answer, 'courses' => $courses, 'videos' => $videos];
    }

    // توليد أسئلة MCQ من نص درس
    public function generateQuiz(?Admin $user, string $lessonText, int $count = 5): array
    {
        $count = max(1, min(10, $count));
        $sentences = array_values(array_filter(array_map('trim', preg_split('/[.!\n؟?]+/u', $lessonText)), fn ($s) => mb_strlen($s) > 20));

        if (config('services.openai.key') && count($sentences) >= 2) {
            $quiz = $this->llmQuiz($lessonText, $count);
            if ($quiz) {
                AiLog::create(['admin_id' => $user?->id, 'kind' => 'generate_quiz', 'prompt' => mb_substr($lessonText, 0, 2000), 'response' => json_encode($quiz, JSON_UNESCAPED_UNICODE)]);
                return $quiz;
            }
        }

        // fallback محلي: كل جملة → سؤال أكمل
        $quiz = [];
        foreach (array_slice($sentences, 0, $count) as $i => $s) {
            $words = preg_split('/\s+/u', $s);
            $hideIdx = (int) (count($words) / 2);
            $answer = $words[$hideIdx] ?? '';
            $words[$hideIdx] = '........';
            $quiz[] = [
                'question' => 'أكمل: ' . implode(' ', $words),
                'options' => array_values(array_unique([$answer, 'لا شيء مما سبق', 'كل ما سبق', 'غير ذلك'])),
                'correct_answer' => $answer,
            ];
        }

        AiLog::create(['admin_id' => $user?->id, 'kind' => 'generate_quiz', 'prompt' => mb_substr($lessonText, 0, 2000), 'response' => json_encode($quiz, JSON_UNESCAPED_UNICODE)]);

        return $quiz;
    }

    // تصحيح مقالي: تداخل كلمات مفتاحية + طول الإجابة (0-100%)
    public function gradeEssay(string $modelAnswer, string $studentAnswer, float $maxMarks): array
    {
        $norm = fn ($t) => array_filter(preg_split('/\s+/u', mb_strtolower($t)), fn ($w) => mb_strlen($w) > 2);
        $model = array_unique($norm($modelAnswer));
        $student = $norm($studentAnswer);
        $hits = count(array_intersect($model, $student));
        $coverage = count($model) ? $hits / count($model) : 0;
        $lengthFactor = min(1, count($student) / max(10, count($model)));
        $scoreRatio = round(0.7 * $coverage + 0.3 * $lengthFactor, 2);
        $marks = round($scoreRatio * $maxMarks, 1);

        return ['ratio' => $scoreRatio, 'marks' => $marks, 'hits' => $hits, 'total_keywords' => count($model)];
    }

    protected function localAnswer(string $question, $courses, $videos): string
    {
        if ($courses->isEmpty() && $videos->isEmpty()) {
            return 'لم أجد درساً مطابقاً في منهجك. جرّب صياغة أخرى أو اسأل مدرسك. (فعّل مفتاح OPENAI_API_KEY لإجابات أعمق)';
        }
        $out = 'وجدت لك هذه الدروس المرتبطة بسؤالك:' . "\n";
        foreach ($courses as $c) {
            $out .= '📚 ' . $c->title . ' — ' . mb_substr($c->description ?? '', 0, 120) . "\n";
        }
        foreach ($videos as $v) {
            $out .= '🎬 ' . $v->title . ' — ' . mb_substr($v->description ?? '', 0, 120) . "\n";
        }

        return $out;
    }

    protected function llmAnswer(string $question, $courses, $videos): string
    {
        try {
            $context = $courses->map(fn ($c) => $c->title . ': ' . $c->description)->implode("\n");
            $res = Http::withToken(config('services.openai.key'))->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'أنت مساعد مذاكرة لطالب ثانوية عامة مصرية. أجب بالعربية من السياق التالي:' . "\n" . $context],
                    ['role' => 'user', 'content' => $question],
                ],
            ]);

            return $res->json('choices.0.message.content') ?? $this->localAnswer($question, $courses, $videos);
        } catch (\Throwable) {
            return $this->localAnswer($question, $courses, $videos);
        }
    }

    protected function llmQuiz(string $lesson, int $count): ?array
    {
        try {
            $res = Http::withToken(config('services.openai.key'))->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'ولّد ' . $count . ' أسئلة اختيار من متعدد بصيغة JSON: [{"question":..,"options":[..4..],"correct_answer":..}]'],
                    ['role' => 'user', 'content' => $lesson],
                ],
            ]);
            $text = $res->json('choices.0.message.content') ?? '';
            $quiz = json_decode(preg_replace('/```json|```/', '', $text), true);

            return is_array($quiz) ? $quiz : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
