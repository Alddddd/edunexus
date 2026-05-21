<?php

namespace Database\Seeders;

use App\Models\MerchantProfile;
use App\Models\MerchantCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MerchantSeeder extends Seeder
{
    use DemoSeederSupport;

    public function run(): void
    {
        $this->merchant(
            'lipa.supplies@edunexus.test',
            'Lipa School Supplies Center Operator',
            'Lipa School Supplies Center',
            'School Supplies',
            '09171234567',
            'CM Recto Avenue, Lipa City, Batangas',
            'Lipa School Supplies Center',
            '09171234567',
            'Please release to the registered GCash merchant account after settlement review.'
        );

        $this->merchant(
            'educare.bookstore@edunexus.test',
            'EduCare Bookstore Cashier',
            'EduCare Bookstore',
            'School Supplies',
            '09179876543',
            'P. Torres Street, Batangas City',
            'EduCare Bookstore',
            '09179876543',
            'Use branch cashier account for cooperative reimbursement releases.'
        );

        $this->merchant(
            'allied.learning@edunexus.test',
            'Allied Learning Hub Desk',
            'Allied Learning Hub',
            'Community Relief',
            '09175661234',
            'JP Laurel Highway, Tanauan City',
            null,
            null,
            null
        );

        $this->merchant(
            'health.pharmacy@edunexus.test',
            'Batangas Health Pharmacy Teller',
            'Batangas Health Pharmacy',
            'Pharmacy',
            '09174567890',
            'Mabini Avenue, Batangas City',
            'Batangas Health Pharmacy',
            '09174567890',
            'Demo-safe payout destination for medical assistance settlements.'
        );
    }

    private function merchant(
        string $email,
        string $name,
        string $businessName,
        string $category,
        string $contactNumber,
        string $address,
        ?string $payoutName,
        ?string $payoutNumber,
        ?string $notes
    ): void {
        $user = $this->demoUser($email, $name, 'merchant');
        $categoryRecord = MerchantCategory::firstOrCreate(
            ['name' => $category],
            [
                'slug' => Str::slug($category),
                'status' => 'Active',
            ]
        );

        MerchantProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'business_name' => $businessName,
                'merchant_category_id' => $categoryRecord->id,
                'merchant_category' => $category,
                'contact_number' => $contactNumber,
                'address' => $address,
                'payout_account_name' => $payoutName,
                'payout_account_number' => $payoutNumber,
                'payout_qr' => $payoutNumber ? 'demo/payout-qr/' . str($businessName)->slug() . '.webp' : null,
                'payout_notes' => $notes,
                'status' => 'Active',
            ]
        );
    }
}
