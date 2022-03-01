<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission as Model;

class PermissionsSeeder extends Seeder
{
    /*
     * Permission name must be uppercase
     * Permission name suffix is guard name followed by model or feature name
     */
    const MODELS = [
        [
            'name' => 'API_USER',
            'guard_name' => 'api',
            'display_name' => 'User'
        ],
        [
            'name' => 'API_DASHBOARD',
            'guard_name' => 'api',
            'display_name' => 'Dashboard'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::MODELS as $model) {
            if (!DB::table('permissions')->where($model)->exists()) {
                Model::create($model);
            }
        }
    }
}
