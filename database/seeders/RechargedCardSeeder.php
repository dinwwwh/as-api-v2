<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class RechargedCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::firstOrCreate(['key' => 'open_recharging_card_thesieure'], [
            'description' => 'Quyết định liệu có mở cổng nạp thẻ qua thesieure hay không.',
            'assigned_config_key' => 'thesieure.open_recharging_card',
            'value' => true,
            'structure_description' => 'true/false',
            'public' => true,
            'rules' => ['boolean'],
        ]);

        Setting::firstOrCreate(['key' => 'open_recharging_card'], [
            'description' => 'Quyết định liệu có mở cổng nạp thẻ thủ công hay không.',
            'value' => true,
            'structure_description' => 'true/false',
            'public' => true,
            'rules' => ['boolean'],
        ]);

        Setting::firstOrCreate(['key' => 'recharged_card_telcos'], [
            'description' => 'Chứa thông tin về các mệnh giá, chiết khấu của các loại thẻ hỗ trợ nạp thủ công.',
            'value' => [
                [
                    'name' => 'viettel',
                    'faceValues' => [
                        [
                            'value' => 10000,
                            'tax' => 20,
                            'taxForInvalidFaceValue' => 100,
                        ],
                        [
                            'value' => 20000,
                            'tax' => 30,
                            'taxForInvalidFaceValue' => 90,
                        ],
                        [
                            'value' => 50000,
                            'tax' => 40,
                            'taxForInvalidFaceValue' => 80,
                        ],
                        [
                            'value' => 100000,
                            'tax' => 50,
                            'taxForInvalidFaceValue' => 70,
                        ]
                    ],
                ]
            ],
            'structure_description' => '[
                    [
                        "name" => "tên nhà mạng",
                        "faceValues": [
                            [
                                "value": "mệnh giá",
                                "tax": "phí thu theo % khi nạp thẻ thành công",
                                "taxForInvalidFaceValue": "phí thu theo % khi nạp thẻ sai mệnh giá"
                            ]
                        ]
                    ]
                ]',
            'public' => true,
            'rules' => [
                'array',
                '*.name' => ['required', 'string'],
                '*.faceValues' => ['required', 'array'],
                '*.faceValues.*.value' => ['required', 'integer', 'min:0'],
                '*.faceValues.*.tax' => ['required', 'integer', 'min:0', 'max:100'],
                '*.faceValues.*.taxForInvalidFaceValue' => ['required', 'integer', 'min:0', 'max:100'],
            ],
        ]);

        Permission::firstOrCreate(['key' => 'approve_recharged_card'], [
            'name' => 'phê duyệt thẻ cào nạp thủ công',
            'description' => 'Quyết định xem người dùng có thể phê duyệt các thẻ nạp bằng hình thức thủ công.',
        ]);

        Permission::firstOrCreate(['key' => 'manage_recharged_card'], [
            'name' => 'quản lý thẻ cào nạp thủ công',
            'description' => 'Quyết định xem người dùng có thể xem thông tin nhạy cảm của tất cả các thẻ, phê duyệt các thẻ mà người khác đang phê duyệt (nếu có quyền phê duyệt).',
        ]);

        User::firstOrCreate(
            ['login' => 'thesieure'],
            [
                'name' => 'Thesieure',
                'gender' => 'male',
                'avatar_path' => "https://avatars.dicebear.com/api/male/thesieure.svg",
                'email' => 'invalid email',
                'password' => 'invalid bcrypt password', // password
                'email_verified_at' => now(),
            ]
        );
    }
}
