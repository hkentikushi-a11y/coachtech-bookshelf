<?php

namespace App\Http\Controllers;

use App\Enums\ReadingStatus;
use App\Http\Requests\ReadingPlanStoreRequest;
use App\Http\Requests\ReadingPlanUpdateRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReadingPlanController extends Controller
{
    public function index(): View
    {
        $plans = auth()->user()
            ->readingPlans()
            ->with('book')
            ->orderByRaw("CASE status WHEN 'reading' THEN 0 WHEN 'want' THEN 1 WHEN 'done' THEN 2 ELSE 3 END")
            ->orderBy('target_date')
            ->paginate(15);

        return view('reading-plans.index', compact('plans'));
    }

    public function create(): View
    {
        $this->authorize('create', ReadingPlan::class);

        $registeredBookIds = auth()->user()->readingPlans()->pluck('book_id')->toArray();
        $books = Book::whereNotIn('id', $registeredBookIds)->orderBy('title')->get();

        return view('reading-plans.create', compact('books'));
    }

    public function store(ReadingPlanStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', ReadingPlan::class);

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if (($data['status'] ?? 'want') === 'reading' && empty($data['started_at'])) {
            $data['started_at'] = today()->toDateString();
        }

        ReadingPlan::create($data);

        return redirect()->route('reading-plans.index')
            ->with('success', '読書計画を登録しました。');
    }

    public function edit(ReadingPlan $readingPlan): View
    {
        $this->authorize('update', $readingPlan);

        return view('reading-plans.edit', compact('readingPlan'));
    }

    public function update(ReadingPlanUpdateRequest $request, ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('update', $readingPlan);

        $data = $request->validated();

        if ($data['status'] === 'reading' && ! $readingPlan->started_at) {
            $data['started_at'] = today()->toDateString();
        }

        if ($data['status'] === 'done' && ! $readingPlan->finished_at) {
            $data['finished_at'] = today()->toDateString();
        }

        $readingPlan->update($data);

        return redirect()->route('reading-plans.index')
            ->with('success', '読書計画を更新しました。');
    }

    public function destroy(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('delete', $readingPlan);
        $readingPlan->delete();

        return redirect()->route('reading-plans.index')
            ->with('success', '読書計画を削除しました。');
    }

    public function done(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('markDone', $readingPlan);

        $readingPlan->update([
            'status' => ReadingStatus::Done,
            'finished_at' => today()->toDateString(),
        ]);

        return redirect()->route('reading-plans.index')
            ->with('success', '「'.$readingPlan->book->title.'」を読了にしました！');
    }
}
