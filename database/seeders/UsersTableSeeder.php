<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Hashファサードをインポート

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存データをクリアしてから新しいデータを投入する場合（開発環境向け）
        // DB::table('users')->truncate();

        DB::table('users')->insert([
            // 管理者ユーザー
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'), // パスワードは「password」
                'email_verified_at' => now(),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'is_admin' => true, // 管理者フラグ
            ],
            // 一般ユーザー1
            [
                'name' => 'General User 1',
                'email' => 'user1@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'is_admin' => false, // 一般ユーザー
            ],
            // 一般ユーザー2
            [
                'name' => 'General User 2',
                'email' => 'user2@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'is_admin' => false, // 一般ユーザー
            ],
        ]);
    }
}