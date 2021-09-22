<?php

namespace Database\Seeders;

use App\Models\Rule;
use Illuminate\Database\Seeder;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rule::firstOrCreate(['key' => 'required'], [
            'name' => 'bắt buộc',
            'description' => 'Xác định xem trường này có bắt buộc không.',
        ]);

        Rule::firstOrCreate(['key' => 'email'], [
            'name' => 'email',
            'description' => 'Trường này phải là một email hợp lệ.',
        ]);

        Rule::firstOrCreate(['key' => 'string'], [
            'name' => 'Chuỗi',
            'description' => 'Trường này phải là chuỗi ký tự.',
        ]);

        Rule::firstOrCreate(['key' => 'integer'], [
            'name' => 'Số nguyên',
            'description' => 'Trường này phải là số nguyên.',
        ]);

        Rule::firstOrCreate(['key' => 'min:0'], [
            'name' => 'Tối thiểu 0',
            'description' => 'Trường này phải lớn hơn hoặc bằng không.',
        ]);
    }
}
