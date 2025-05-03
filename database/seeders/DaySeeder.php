<?php

namespace Database\Seeders;

use App\Models\Day;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Day::create(
            [
                'name' => "الجمعة"
            ]
        );
        Day::create(
            [
                'name' => "السبت"
            ]
        );
        Day::create(
            [
                'name' => "الأحد"
            ]
        );
        Day::create(
            [
                'name' => "الاثنين"
            ]
        );
        Day::create(
            [
                'name' => "الثلاثاء"
            ]
        );
        Day::create(
            [
                'name' => "الأربعاء"
            ]
        );
        Day::create(
            [
                'name' => "الخميس"
            ]
        );
    }
}
