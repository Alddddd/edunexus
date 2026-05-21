<?php

namespace Database\Seeders;

use App\Models\AssistanceProgram;
use App\Models\MerchantCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssistanceProgramSeeder extends Seeder
{
    use DemoSeederSupport;

    public function run(): void
    {
        $admin = $this->demoMember('admin@edunexus.test');

        $programs = [
            [
                'program_name' => 'Education Assistance',
                'description' => 'Classroom supplies, books, learning kits, and teaching materials for cooperative member-teachers.',
                'merchant_category' => 'School Supplies',
                'maximum_amount' => 5000,
                'expiration_days' => 30,
            ],
            [
                'program_name' => 'Emergency Assistance',
                'description' => 'Short-term cooperative support for urgent household or school-related emergency needs.',
                'merchant_category' => 'Community Relief',
                'maximum_amount' => 7000,
                'expiration_days' => 14,
            ],
            [
                'program_name' => 'Medical Assistance',
                'description' => 'Medicine and basic healthcare reimbursement assistance through accredited pharmacy partners.',
                'merchant_category' => 'Pharmacy',
                'maximum_amount' => 8000,
                'expiration_days' => 21,
            ],
        ];

        foreach ($programs as $program) {
            $category = MerchantCategory::firstOrCreate(
                ['name' => $program['merchant_category']],
                [
                    'slug' => Str::slug($program['merchant_category']),
                    'status' => 'Active',
                ]
            );

            AssistanceProgram::updateOrCreate(
                ['program_name' => $program['program_name']],
                $program + [
                    'merchant_category_id' => $category->id,
                    'status' => 'Active',
                    'created_by' => $admin->id,
                ]
            );
        }
    }
}
