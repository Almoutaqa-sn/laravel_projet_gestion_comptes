<?php



namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Compte;
use App\Models\Transaction;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        Admin::factory(3)->create()->each(function ($admin) {
            Client::factory(5)->create()->each(function ($client) use ($admin) {
                Compte::factory(2)->forClient($client->id)->forAdmin($admin->id)->create()->each(function ($compte) use ($admin) {
                    Transaction::factory(3)->forCompte($compte->id)->forAdmin($admin->id)->create();
                });
            });
        });
    }
}
