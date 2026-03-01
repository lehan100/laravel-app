<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $user = User::create([
                    'name' => 'Admin',
                    'email' => 'admin@gmail.com',
                    'username' => 'admin',
                    'status' => 1,
                    'password' => bcrypt('123456')
        ]);

        $role = Role::firstOrCreate(['name' => 'Aministrators']);

        $permissions = Permission::pluck('id', 'id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }

}
