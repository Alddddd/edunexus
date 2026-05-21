<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    use DemoSeederSupport;

    public function run(): void
    {
        $this->demoUser('admin@edunexus.test', 'Clarissa Mendoza', 'admin');
        $this->demoUser('auditor@edunexus.test', 'Ramon Villanueva', 'auditor');

        $this->demoUser('ana.reyes@edunexus.test', 'Ana Reyes', 'member');
        $this->demoUser('roberto.cruz@edunexus.test', 'Roberto Cruz', 'member');
        $this->demoUser('lorna.garcia@edunexus.test', 'Lorna Garcia', 'member');
        $this->demoUser('maria.santos@edunexus.test', 'Maria Santos', 'member');
    }
}
