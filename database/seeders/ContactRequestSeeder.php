<?php

namespace Database\Seeders;

use App\Models\ContactRequest;
use Illuminate\Database\Seeder;

class ContactRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactRequest::factory(5)->create();
    }
}
