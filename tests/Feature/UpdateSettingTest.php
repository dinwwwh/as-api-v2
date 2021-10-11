<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Str;
use Tests\TestCase;

class UpdateSettingTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['update_setting']);
        $setting = Setting::factory()
            ->state([
                'value' => false,
                'rules' => ['boolean']
            ])
            ->create();
        $router = route('settings.update', ['setting' => $setting]);
        $data = [
            'value' => true,
            'description' => Str::random(),
        ];

        $this->actingAs($user)
            ->json('put', $router, $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('settings', [
            'key' => $setting->getKey(),
            'description' => $data['description']
        ]);
        $setting->refresh()->value == $data['value'];
    }

    public function test_request_invalid_value()
    {
        $user = $this->factoryUser(['update_setting']);
        $setting = Setting::factory()
            ->state([
                'value' => false,
                'rules' => ['boolean']
            ])
            ->create();
        $router = route('settings.update', ['setting' => $setting]);
        $data = [
            'value' => 'true',
            'description' => 'This is description :))'
        ];

        $this->actingAs($user)
            ->json('put', $router, $data)
            ->assertStatus(422);
    }

    public function test_middleware_lack_update_setting_permission()
    {
        $user = $this->factoryUser(['update_setting'], true);
        $setting = Setting::factory()
            ->state([
                'value' => false,
                'rules' => ['boolean']
            ])
            ->create();
        $router = route('settings.update', ['setting' => $setting]);
        $data = [
            'value' => true,
            'description' => 'This is description :))'
        ];

        $this->actingAs($user)
            ->json('put', $router, $data)
            ->assertStatus(403);
    }
}
