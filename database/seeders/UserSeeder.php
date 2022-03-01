<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $model = [
            'name' => 'Administrator',
            'email' => 'admin@local.test',
            'password' => 'Overpower_123'
        ];

        if (!User::where(['name' => $model['name']])->exists()) {
            $user = new User;

            if ($user->saveModel($model)) {
                $permission = DB::table('permissions')->where('name', 'API_USER')->first();

                if (!is_null($permission)) {
                    $user->assignPermission([$permission->id]);
                }
            }
        }
    }
}
