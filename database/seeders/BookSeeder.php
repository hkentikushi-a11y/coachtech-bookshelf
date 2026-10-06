<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $books = [
            [
                'title' => '吾輩は猫である', 'author' => '夏目漱石',
                'isbn' => '9784101010014', 'published_date' => '1905-01-01',
                'description' => '猫の視点から人間社会を風刺した夏目漱石の代表作。',
                'genres' => ['小説'],
            ],
            [
                'title' => '星の王子さま', 'author' => 'サン=テグジュペリ',
                'isbn' => '9784422100524', 'published_date' => '1953-03-01',
                'description' => '砂漠に不時着したパイロットと星の王子さまとの出会いを描いた名作。',
                'genres' => ['小説', '哲学'],
            ],
            [
                'title' => 'リーダブルコード', 'author' => 'Dustin Boswell',
                'isbn' => '9784873115658', 'published_date' => '2012-06-23',
                'description' => 'より良いコードを書くための実践的なテクニックを解説した技術書。',
                'genres' => ['技術'],
            ],
            [
                'title' => '人を動かす', 'author' => 'D・カーネギー',
                'isbn' => '9784422100428', 'published_date' => '1936-11-01',
                'description' => '人間関係の原則を学ぶ、世界で最も読まれたビジネス書の一つ。',
                'genres' => ['ビジネス', '自己啓発'],
            ],
            [
                'title' => '嫌われる勇気', 'author' => '岸見一郎',
                'isbn' => '9784478025819', 'published_date' => '2013-12-13',
                'description' => 'アドラー心理学をわかりやすく解説した哲学対話。',
                'genres' => ['哲学', '自己啓発'],
            ],
            [
                'title' => 'FACTFULNESS', 'author' => 'ハンス・ロスリング',
                'isbn' => '9784822289605', 'published_date' => '2019-01-11',
                'description' => '10の思い込みを乗り越え、データを基に世界を正しく見る習慣。',
                'genres' => ['ビジネス', '科学'],
            ],
            [
                'title' => 'Clean Code', 'author' => 'Robert C. Martin',
                'isbn' => '9784048930598', 'published_date' => '2017-12-18',
                'description' => 'アジャイルソフトウェアの技法として、クリーンなコードの書き方を学ぶ。',
                'genres' => ['技術'],
            ],
            [
                'title' => 'サピエンス全史（上）', 'author' => 'ユヴァル・ノア・ハラリ',
                'isbn' => '9784309226712', 'published_date' => '2016-09-08',
                'description' => '文明・帝国・宗教の世界史をホモ・サピエンスの視点で描く。',
                'genres' => ['歴史', '科学'],
            ],
            [
                'title' => '1984年', 'author' => 'ジョージ・オーウェル',
                'isbn' => '9784003231516', 'published_date' => '1972-07-18',
                'description' => '全体主義社会を描いたディストピア小説の傑作。',
                'genres' => ['小説'],
            ],
            [
                'title' => '思考の整理学', 'author' => '外山滋比古',
                'isbn' => '9784480020475', 'published_date' => '1983-04-24',
                'description' => '知的生産の技術を説いた、長年にわたりベストセラーを続ける名著。',
                'genres' => ['自己啓発'],
            ],
            [
                'title' => 'プログラマが知るべき97のこと', 'author' => 'Kevlin Henney',
                'isbn' => '9784873114798', 'published_date' => '2010-12-18',
                'description' => 'プログラマとしての心得を97のエッセイで綴った実践的な一冊。',
                'genres' => ['技術'],
            ],
        ];

        foreach ($books as $data) {
            $book = Book::firstOrCreate(
                ['isbn' => $data['isbn']],
                [
                    'user_id' => $user->id,
                    'title' => $data['title'],
                    'author' => $data['author'],
                    'published_date' => $data['published_date'],
                    'description' => $data['description'],
                    'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text='.urlencode($data['title']),
                ]
            );

            $genreIds = Genre::whereIn('name', $data['genres'])->pluck('id');
            $book->genres()->sync($genreIds);
        }
    }
}
