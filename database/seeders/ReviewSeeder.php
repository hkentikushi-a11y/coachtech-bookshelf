<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        $reviews = [
            ['user' => 0, 'book' => 0,  'rating' => 5, 'comment' => '猫の一人称視点が面白く、夏目漱石の風刺がとても好きです。'],
            ['user' => 0, 'book' => 1,  'rating' => 5, 'comment' => '大人も子どもも楽しめる哲学的な物語。何度読んでも感動します。'],
            ['user' => 0, 'book' => 2,  'rating' => 4, 'comment' => '具体的なコード例が多く、実務にすぐ役立つ内容でした。'],
            ['user' => 0, 'book' => 5,  'rating' => 5, 'comment' => 'データに基づいて世界を見る大切さを改めて感じました。'],
            ['user' => 0, 'book' => 9,  'rating' => 4, 'comment' => 'アイデアの発想法や整理法が丁寧に解説されていて勉強になりました。'],
            ['user' => 1, 'book' => 0,  'rating' => 4, 'comment' => '明治時代の雰囲気が生き生きと描かれていてとても楽しめました。'],
            ['user' => 1, 'book' => 2,  'rating' => 5, 'comment' => 'コードレビューの観点が変わりました。チームでも輪読しています。'],
            ['user' => 1, 'book' => 3,  'rating' => 5, 'comment' => '人間関係に悩んでいる人に強くおすすめできる一冊です。'],
            ['user' => 1, 'book' => 6,  'rating' => 4, 'comment' => 'Clean Codeの原則を実践することでコードの品質が上がりました。'],
            ['user' => 1, 'book' => 7,  'rating' => 4, 'comment' => '歴史の大きな流れをわかりやすく捉えられる視点を得ました。'],
            ['user' => 2, 'book' => 1,  'rating' => 4, 'comment' => '短い言葉に深い意味が込められていて何度も読み返しています。'],
            ['user' => 2, 'book' => 3,  'rating' => 4, 'comment' => '古典的な名著ですが今でも十分に通用する内容です。'],
            ['user' => 2, 'book' => 4,  'rating' => 3, 'comment' => 'アドラー心理学の入門として読みやすいですが、やや繰り返しが多い。'],
            ['user' => 2, 'book' => 5,  'rating' => 5, 'comment' => '統計データを用いた世界の見方が目からウロコでした。'],
            ['user' => 2, 'book' => 8,  'rating' => 4, 'comment' => 'ディストピア描写のリアルさが圧巻で、現代社会と重ねて読みました。'],
            ['user' => 2, 'book' => 10, 'rating' => 3, 'comment' => '97章それぞれが短くまとまっていて読みやすいです。'],
            ['user' => 3, 'book' => 0,  'rating' => 3, 'comment' => '文体が独特で最初は慣れませんでしたが、読み進めると面白かったです。'],
            ['user' => 3, 'book' => 2,  'rating' => 5, 'comment' => '命名の重要性を改めて実感しました。全エンジニア必読だと思います。'],
            ['user' => 3, 'book' => 6,  'rating' => 5, 'comment' => 'Clean Codeのリファクタリング事例がとても参考になりました。'],
            ['user' => 3, 'book' => 7,  'rating' => 5, 'comment' => '人類の歴史をここまで俯瞰できる本は他にないと感じました。'],
            ['user' => 3, 'book' => 9,  'rating' => 5, 'comment' => 'FACTFULNESSと合わせて読むと世界の見方が大きく変わります。'],
            ['user' => 3, 'book' => 10, 'rating' => 4, 'comment' => 'ベテランエンジニアの知見が凝縮されていて参考になります。'],
            ['user' => 4, 'book' => 1,  'rating' => 5, 'comment' => '子どものころに読んで大人になってから読み返すと感動が深まります。'],
            ['user' => 4, 'book' => 3,  'rating' => 5, 'comment' => '人間関係の本質を突いた言葉が多く、何度でも読み返したい一冊です。'],
            ['user' => 4, 'book' => 4,  'rating' => 4, 'comment' => '哲学的な対話形式で読みやすく、自己肯定感を高めるヒントが得られました。'],
            ['user' => 4, 'book' => 5,  'rating' => 4, 'comment' => 'グローバルな視点でものごとを考える習慣が身につきました。'],
            ['user' => 4, 'book' => 6,  'rating' => 5, 'comment' => 'Clean Codeは読んで終わりではなく実践してこそ価値がある本です。'],
            ['user' => 4, 'book' => 8,  'rating' => 5, 'comment' => '監視社会や全体主義への警鐘として今読むべき小説だと思います。'],
            ['user' => 4, 'book' => 9,  'rating' => 3, 'comment' => '少し難しい表現もありますが、知的好奇心が刺激される内容です。'],
            ['user' => 0, 'book' => 4,  'rating' => 4, 'comment' => '嫌われる勇気は自分の生き方を見直すきっかけになった本です。'],
            ['user' => 1, 'book' => 8,  'rating' => 5, 'comment' => '1984年の世界観はSFでありながらリアリティがあって引き込まれます。'],
            ['user' => 2, 'book' => 6,  'rating' => 5, 'comment' => 'コードの可読性についてこれほど丁寧に説明した本はないと思います。'],
        ];

        foreach ($reviews as $data) {
            if (isset($users[$data['user']]) && isset($books[$data['book']])) {
                Review::firstOrCreate(
                    [
                        'user_id' => $users[$data['user']]->id,
                        'book_id' => $books[$data['book']]->id,
                    ],
                    [
                        'rating' => $data['rating'],
                        'comment' => $data['comment'],
                    ]
                );
            }
        }
    }
}
