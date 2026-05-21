<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            MerchantSeeder::class,
            AssistanceProgramSeeder::class,
            AssistanceRequestSeeder::class,
            SettlementSeeder::class,
            BlockchainSeeder::class,
        ]);

        $this->command?->newLine();
        $this->command?->info('EduNexUs demo database is ready.');
        $this->command?->line('Admin: admin@edunexus.test / password');
        $this->command?->line('Auditor: auditor@edunexus.test / password');
        $this->command?->line('Member: ana.reyes@edunexus.test / password');
        $this->command?->line('Member: roberto.cruz@edunexus.test / password');
        $this->command?->line('Merchant: lipa.supplies@edunexus.test / password');
        $this->command?->line('Merchant: educare.bookstore@edunexus.test / password');
        $this->command?->newLine();
        $this->command?->line('Demo QR claim reference: EDU-DEMO-QR-001');
        $this->command?->line('Pending settlement reference: EDU-DEMO-CLAIM-001');
        $this->command?->line('Partial payout reference: EDU-DEMO-PARTIAL-001');
        $this->command?->line('Released payout reference: EDU-DEMO-RELEASED-001');
    }
}
