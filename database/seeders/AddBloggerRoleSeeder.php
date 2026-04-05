<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AddBloggerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add blogger role to specific users
        $bloggerEmails = [
            'javerianigar40@gmail.com',
            'official.onedollarmeme@gmail.com',
            'kinzasaeed688@gmail.com',
        ];

        foreach ($bloggerEmails as $email) {
            User::where('email', $email)->update(['role' => 'blogger']);
        }

        $this->command->info('Blogger role added to ' . count($bloggerEmails) . ' users.');
    }
}
