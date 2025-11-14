<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default user
        User::create([
            'name' => 'Miura',
            'username' => 'miura',
            'email' => 'miura@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create sample schools
        $schools = [
            [
                'name' => 'さくら国際言語学院',
                'contact_person' => '八代 寿介',
                'position' => '事務局長',
                'phone' => '083-235-7389',
            ],
            [
                'name' => '専門学校 さくら国際言語教育学院',
                'phone' => '0838-21-7289',
            ],
            [
                'name' => '折尾愛真短期大学',
                'contact_person' => '増田 真',
                'position' => '短期大学事務',
                'phone' => '093-602-2105',
            ],
            [
                'name' => '北九州YMCA学院',
                'contact_person' => '安東 宣子',
                'phone' => '093-531-1587',
            ],
        ];

        foreach ($schools as $schoolData) {
            School::create($schoolData);
        }

        // Create sample students
        $students = [
            [
                'name_english' => 'リン',
                'name_kana' => 'リン',
                'nationality' => 'ミャンマー',
                'gender' => '女',
                'age' => 22,
                'email' => 'rin@example.com',
                'jlpt_level' => 'N3',
                'school_id' => 1,
                'enrollment_year' => 2026,
                'status' => '試験待ち',
                'applied' => true,
            ],
            [
                'name_english' => 'アルビン',
                'name_kana' => 'アルビン',
                'nationality' => 'イラン|イラン・イスラム共和国',
                'gender' => '男',
                'age' => 20,
                'email' => 'alvin@example.com',
                'jlpt_level' => 'N2',
                'school_id' => 4,
                'enrollment_year' => 2026,
                'status' => '試験待ち',
                'applied' => true,
            ],
        ];

        foreach ($students as $studentData) {
            Student::create($studentData);
        }
    }
}

