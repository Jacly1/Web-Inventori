<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
/**
* Run the database seeds.
*/
    public function run(): void
    {
        $permissions = [
        'create-role',
        'edit-role',
        'delete-role',
        'create-user',
        'edit-user',
        'delete-user',
        'create-stok',
        'edit-stok',
        'show-stok',
        'delete-stok',
        'create-masuk',
        'edit-masuk',
        'show-masuk',
        'delete-masuk',
        'create-keluar',
        'edit-keluar',
        'show-keluar',
        'delete-keluar',
        'show-notifikasi',
        'show-dashboard',
        'show-stok'
        ];
// Looping and Inserting Array's Permissions into PermissionTable
    // foreach ($permissions as $permission) {
    //     Permission::create(['name' => $permission]);
    // }

    foreach ($permissions as $permission) {
        // Cek apakah permission sudah ada sebelum membuatnya
        if (!Permission::where('name', $permission)->where('guard_name', 'web')->exists()) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
}