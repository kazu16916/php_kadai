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
            'description' => '好きな教科',
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
    }
}