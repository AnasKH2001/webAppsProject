<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;




    use App\Models\GovernmentEntity;



class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    // /**
    //  * Seed the application's database.
    //  */
    // public function run(): void
    // {
    //     // User::factory(10)->create();

    //     User::factory()->create([
    //         'name' => 'Test User',
    //         'email' => 'test@example.com',
    //     ]);
    // }







public function run(): void
{
    $entity = GovernmentEntity::create([
        'name' => 'Ministry of Health',
        'code' => 'MOH',
    ]);

    User::create([
        'name' => 'John Employee',
        'email' => 'john@example.com',
        'password' => bcrypt('secret'),
        'role' => 'employee',
        'entity_id' => $entity->id,
    ]);
}









}


