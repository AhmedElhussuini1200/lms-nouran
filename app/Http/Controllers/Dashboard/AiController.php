<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\AiTutorService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function index()
    {
        return view('dashboard.ai.index');
    }

    public function ask(Request $request, AiTutorService $ai)
    {
        $request->validate(['question' => ['required', 'string', 'max:1000']]);
        $result = $ai->ask(auth('admin')->user(), $request->question);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return view('dashboard.ai.index', ['result' => $result, 'question' => $request->question]);
    }

    public function quiz(Request $request, AiTutorService $ai)
    {
        $request->validate(['lesson' => ['required', 'string', 'max:5000'], 'count' => ['nullable', 'integer', 'min:1', 'max:10']]);
        $quiz = $ai->generateQuiz(auth('admin')->user(), $request->lesson, (int) ($request->count ?? 5));

        return response()->json(['quiz' => $quiz]);
    }
}
