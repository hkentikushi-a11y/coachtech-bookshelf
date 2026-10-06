<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReadingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        if ($users->isEmpty() || $books->isEmpty()) {
            return;
        }

        $today = now();

        // ユーザー 1: 読んでいる（各タイミングのリマインダーをテストできるデータ）
        $user1 = $users->first();
        $bookIds = $books->pluck('id')->shuffle()->take(6)->values();

        $plans1 = [
            ['status' => 'reading', 'target_date' => $today->copy()->addDays(7)->toDateString(),  'started_at' => $today->copy()->subDays(7)->toDateString()],
            ['status' => 'reading', 'target_date' => $today->copy()->addDays(3)->toDateString(),  'started_at' => $today->copy()->subDays(3)->toDateString()],
            ['status' => 'reading', 'target_date' => $today->copy()->addDays(1)->toDateString(),  'started_at' => $today->copy()->subDays(1)->toDateString()],
            ['status' => 'reading', 'target_date' => $today->copy()->subDays(2)->toDateString(),  'started_at' => $today->copy()->subDays(10)->toDateString()],
            ['status' => 'want',    'target_date' => $today->copy()->addDays(30)->toDateString(), 'started_at' => null],
            ['status' => 'done',    'target_date' => null, 'started_at' => $today->copy()->subDays(20)->toDateString(), 'finished_at' => $today->copy()->subDays(5)->toDateString()],
        ];

        foreach (array_slice($plans1, 0, count($bookIds)) as $i => $data) {
            ReadingPlan::firstOrCreate(
                ['user_id' => $user1->id, 'book_id' => $bookIds[$i]],
                $data
            );
        }

        // ユーザー 2: シンプルなデータ
        if ($users->count() >= 2) {
            $user2 = $users->get(1);
            $bookIds2 = $books->pluck('id')->diff($bookIds)->take(3)->values();

            foreach ($bookIds2 as $j => $bookId) {
                $statuses = ['want', 'reading', 'done'];
                ReadingPlan::firstOrCreate(
                    ['user_id' => $user2->id, 'book_id' => $bookId],
                    [
                        'status' => $statuses[$j] ?? 'want',
                        'target_date' => $j === 1 ? $today->copy()->addDays(14)->toDateString() : null,
                        'started_at' => $j >= 1 ? $today->copy()->subDays(5)->toDateString() : null,
                    ]
                );
            }
        }
    }
}
