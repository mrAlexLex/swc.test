<?php

namespace Database\Seeders;

use App\Models\Task\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user with known credentials
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create tasks for test user
        Task::factory()
            ->count(10)
            ->forUser($testUser)
            ->create();

        // Create 9 additional users
        $users = User::factory()
            ->count(9)
            ->create();

        // Create 40 more tasks distributed among all users
        $allUsers = $users->push($testUser);

        Task::factory()
            ->count(40)
            ->recycle($allUsers)
            ->create();
    }
}
