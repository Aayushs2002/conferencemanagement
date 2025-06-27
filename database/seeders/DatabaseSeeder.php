<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user =  User::factory()->create([
            'f_name' => 'super',
            'l_name' => 'admin',
            'email' => 'admin@admin.com',
            'type' => 1
        ]);


        $this->call([
            CountrySeeder::class,
            NamePrefixSeeder::class,
            ScientificSessionCategorySeeder::class,
            PermissionSeeder::class,
        ]);
        $role = Role::create([
            'name' => 'SuperAdmin',
            'guard_name' => 'web',
        ]);
        $role->givePermissionTo(Permission::all());
        $user->assignRole($role);
        // $permissions = Permission::all();
        // foreach ($permissions as $permission) {
        //     $user->conferencePermissions()->syncWithoutDetaching([
        //         $permission->id => ['conference_id' => 1],
        //     ]);
        // }
    }
}
