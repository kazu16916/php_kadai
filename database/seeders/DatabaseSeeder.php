<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Theme;
use App\Models\Option;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // カテゴリの作成
        $categories = [
            'グルメ',
            'スポーツ',
            'エンターテイメント',
            '教育',
            '技術',
            '旅行',
            'ライフスタイル'
        ];

        foreach ($categories as $categoryName) {
            Category::create(['name' => $categoryName]);
        }

        // テストユーザーの作成
        $user1 = User::create([
            'username' => 'psyduck',
            'password' => Hash::make('password123'),
        ]);

        $user2 = User::create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
        ]);

        // サンプルテーマの作成
        $theme1 = Theme::create([
            'title' => '好きな教科',
            'description' => '学校で好きだった教科について教えてください',
            'creator_id' => $user1->id,
            'category_id' => Category::where('name', '教育')->first()->id,
        ]);

        // 好きな教科の選択肢
        $subjects = ['数学', '国語', '英語', '理科', '社会', '体育', '音楽', '美術'];
        foreach ($subjects as $subject) {
            Option::create([
                'theme_id' => $theme1->id,
                'name' => $subject,
            ]);
        }

        // 追加のサンプルテーマ
        $theme2 = Theme::create([
            'title' => '好きなプログラミング言語',
            'description' => '現在使用している、または学習したいプログラミング言語は？',
            'creator_id' => $user2->id,
            'category_id' => Category::where('name', '技術')->first()->id,
        ]);

        $languages = ['PHP', 'JavaScript', 'Python', 'Java', 'C++', 'Go', 'Rust', 'TypeScript'];
        foreach ($languages as $language) {
            Option::create([
                'theme_id' => $theme2->id,
                'name' => $language,
            ]);
        }

        $theme3 = Theme::create([
            'title' => '好きな季節',
            'description' => 'あなたが一番好きな季節はどれですか？',
            'creator_id' => $user1->id,
            'category_id' => Category::where('name', 'ライフスタイル')->first()->id,
        ]);

        $seasons = ['春', '夏', '秋', '冬'];
        foreach ($seasons as $season) {
            Option::create([
                'theme_id' => $theme3->id,
                'name' => $season,
            ]);
        }
    }
}