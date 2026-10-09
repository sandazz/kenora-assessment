<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RegistrationStatus;
use App\Enums\Role;
use App\Enums\WorkshopStatus;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create fixed accounts
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => Role::Admin,
            'is_active' => true,
        ]);

        $manager = User::create([
            'name' => 'Programme Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => Role::Manager,
            'is_active' => true,
        ]);

        $staff = User::create([
            'name' => 'Front Desk Staff',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => Role::Staff,
            'is_active' => true,
        ]);

        // Extra staff member for variety
        $staff2 = User::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@example.com',
            'password' => Hash::make('password'),
            'role' => Role::Staff,
            'is_active' => true,
        ]);

        // Create 8 sample workshops across 3 locations
        $now = now();

        // 1. Upcoming this week — Downtown Centre (pottery)
        $pottery1 = Workshop::create([
            'code' => 'POT-101',
            'title' => 'Introduction to Pottery',
            'instructor' => 'Maria Chen',
            'location' => 'Downtown Centre',
            'description' => 'Learn the basics of hand-building and wheel-throwing in this beginner-friendly pottery class.',
            'starts_at' => $now->copy()->addDays(2)->setTime(10, 0),
            'ends_at' => $now->copy()->addDays(2)->setTime(12, 0),
            'capacity' => 8,
            'status' => WorkshopStatus::Scheduled,
            'created_by' => $manager->id,
            'updated_by' => null,
        ]);

        // 2. Upcoming this week — Westside Branch (coding)
        $coding1 = Workshop::create([
            'code' => 'COD-201',
            'title' => 'Python for Beginners',
            'instructor' => 'James O\'Brien',
            'location' => 'Westside Branch',
            'description' => 'A gentle introduction to programming with Python. No prior experience needed.',
            'starts_at' => $now->copy()->addDays(3)->setTime(14, 0),
            'ends_at' => $now->copy()->addDays(3)->setTime(16, 0),
            'capacity' => 12,
            'status' => WorkshopStatus::Scheduled,
            'created_by' => $manager->id,
            'updated_by' => null,
        ]);

        // 3. Upcoming this week — Northgate Hub (fitness)
        $fitness1 = Workshop::create([
            'code' => 'FIT-301',
            'title' => 'Morning Yoga Flow',
            'instructor' => 'Anika Patel',
            'location' => 'Northgate Hub',
            'description' => 'Start your week right with a guided yoga session suitable for all levels.',
            'starts_at' => $now->copy()->addDays(1)->setTime(8, 0),
            'ends_at' => $now->copy()->addDays(1)->setTime(9, 0),
            'capacity' => 15,
            'status' => WorkshopStatus::Scheduled,
            'created_by' => $manager->id,
            'updated_by' => null,
        ]);

        // 4. Next week — Downtown Centre (painting)
        $painting1 = Workshop::create([
            'code' => 'ART-102',
            'title' => 'Watercolour Painting Basics',
            'instructor' => 'Lena Novak',
            'location' => 'Downtown Centre',
            'description' => 'Explore watercolour techniques and create your first masterpiece.',
            'starts_at' => $now->copy()->addDays(8)->setTime(10, 0),
            'ends_at' => $now->copy()->addDays(8)->setTime(12, 30),
            'capacity' => 10,
            'status' => WorkshopStatus::Scheduled,
            'created_by' => $manager->id,
            'updated_by' => null,
        ]);

        // 5. Next week — Westside Branch (cooking)
        $cooking1 = Workshop::create([
            'code' => 'CUL-401',
            'title' => 'Healthy Meal Prep',
            'instructor' => 'Carlos Ruiz',
            'location' => 'Westside Branch',
            'description' => 'Learn to prepare a week of healthy, balanced meals in just a few hours.',
            'starts_at' => $now->copy()->addDays(9)->setTime(13, 0),
            'ends_at' => $now->copy()->addDays(9)->setTime(15, 0),
            'capacity' => 10,
            'status' => WorkshopStatus::Scheduled,
            'created_by' => $manager->id,
            'updated_by' => null,
        ]);

        // 6. Fully booked — Northgate Hub (fitness)
        $bookedWorkshop = Workshop::create([
            'code' => 'FIT-302',
            'title' => 'HIIT Bootcamp',
            'instructor' => 'Tom Bradley',
            'location' => 'Northgate Hub',
            'description' => 'High-intensity interval training for intermediate and advanced participants.',
            'starts_at' => $now->copy()->addDays(4)->setTime(7, 0),
            'ends_at' => $now->copy()->addDays(4)->setTime(8, 0),
            'capacity' => 3,
            'status' => WorkshopStatus::Scheduled,
            'created_by' => $manager->id,
            'updated_by' => null,
        ]);

        // Fill the booked workshop
        Registration::create([
            'workshop_id' => $bookedWorkshop->id,
            'attendee_name' => 'Alice Thompson',
            'attendee_email' => 'alice.thompson@email.com',
            'status' => RegistrationStatus::Active,
            'registered_by' => $staff->id,
            'registered_at' => now()->subDays(2),
            'active_key' => 1,
        ]);
        Registration::create([
            'workshop_id' => $bookedWorkshop->id,
            'attendee_name' => 'Bob Martinez',
            'attendee_email' => 'bob.martinez@email.com',
            'status' => RegistrationStatus::Active,
            'registered_by' => $staff->id,
            'registered_at' => now()->subDays(1),
            'active_key' => 1,
        ]);
        Registration::create([
            'workshop_id' => $bookedWorkshop->id,
            'attendee_name' => 'Carol White',
            'attendee_email' => 'carol.white@email.com',
            'status' => RegistrationStatus::Active,
            'registered_by' => $manager->id,
            'registered_at' => now()->subHours(6),
            'active_key' => 1,
        ]);

        // 7. Cancelled workshop — Downtown Centre
        $cancelled = Workshop::create([
            'code' => 'POT-102',
            'title' => 'Advanced Pottery Glazing',
            'instructor' => 'Maria Chen',
            'location' => 'Downtown Centre',
            'description' => 'Advanced glazing techniques for experienced potters.',
            'starts_at' => $now->copy()->addDays(5)->setTime(14, 0),
            'ends_at' => $now->copy()->addDays(5)->setTime(16, 0),
            'capacity' => 6,
            'status' => WorkshopStatus::Cancelled,
            'created_by' => $manager->id,
            'updated_by' => $manager->id,
        ]);

        // A registration on cancelled workshop (history data)
        Registration::create([
            'workshop_id' => $cancelled->id,
            'attendee_name' => 'David Lee',
            'attendee_email' => 'david.lee@email.com',
            'status' => RegistrationStatus::Active,
            'registered_by' => $staff->id,
            'registered_at' => now()->subDays(3),
            'active_key' => 1,
        ]);

        // 8. Completed workshop — Westside Branch
        $completed = Workshop::create([
            'code' => 'COD-101',
            'title' => 'Spreadsheet Mastery',
            'instructor' => 'Petra Hofer',
            'location' => 'Westside Branch',
            'description' => 'Master Excel and Google Sheets for everyday tasks.',
            'starts_at' => $now->copy()->subDays(7)->setTime(10, 0),
            'ends_at' => $now->copy()->subDays(7)->setTime(12, 0),
            'capacity' => 10,
            'status' => WorkshopStatus::Completed,
            'created_by' => $manager->id,
            'updated_by' => $manager->id,
        ]);

        // Mix of active and cancelled registrations for completed workshop
        Registration::create([
            'workshop_id' => $completed->id,
            'attendee_name' => 'Emma Wilson',
            'attendee_email' => 'emma.wilson@email.com',
            'status' => RegistrationStatus::Active,
            'registered_by' => $staff->id,
            'registered_at' => $now->copy()->subDays(14),
            'active_key' => 1,
        ]);
        Registration::create([
            'workshop_id' => $completed->id,
            'attendee_name' => 'Frank Brown',
            'attendee_email' => 'frank.brown@email.com',
            'status' => RegistrationStatus::Active,
            'registered_by' => $manager->id,
            'registered_at' => $now->copy()->subDays(13),
            'active_key' => 1,
        ]);
        // A cancelled registration for history
        Registration::create([
            'workshop_id' => $completed->id,
            'attendee_name' => 'Grace Kim',
            'attendee_email' => 'grace.kim@email.com',
            'status' => RegistrationStatus::Cancelled,
            'registered_by' => $staff->id,
            'registered_at' => $now->copy()->subDays(12),
            'cancelled_by' => $staff2->id,
            'cancelled_at' => $now->copy()->subDays(8),
            'cancel_reason' => 'Schedule conflict',
            'active_key' => null,
        ]);

        // Add a few registrations to the pottery workshop (with a cancelled one too)
        Registration::create([
            'workshop_id' => $pottery1->id,
            'attendee_name' => 'Henry Adams',
            'attendee_email' => 'henry.adams@email.com',
            'status' => RegistrationStatus::Active,
            'registered_by' => $staff->id,
            'registered_at' => now()->subDays(1),
            'active_key' => 1,
        ]);
        // Henry previously cancelled — demonstrates re-registration works
        Registration::create([
            'workshop_id' => $pottery1->id,
            'attendee_name' => 'Irene Davis',
            'attendee_email' => 'irene.davis@email.com',
            'status' => RegistrationStatus::Cancelled,
            'registered_by' => $staff2->id,
            'registered_at' => now()->subDays(3),
            'cancelled_by' => $staff->id,
            'cancelled_at' => now()->subDays(2),
            'cancel_reason' => 'No longer able to attend',
            'active_key' => null,
        ]);
    }
}
