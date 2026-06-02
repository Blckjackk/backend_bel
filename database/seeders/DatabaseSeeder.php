<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\City;
use App\Models\Feature;
use App\Models\Office;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::create([
            'name' => 'Admin OfficeHub',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('admin1'),
            'role' => 'admin',
        ]);

        $provider = User::create([
            'name' => 'Office Provider',
            'email' => 'provider@gmail.com',
            'password' => Hash::make('provider'),
            'role' => 'provider',
        ]);

        $customer = User::create([
            'name' => 'Belva Risma',
            'email' => 'belva@gmail.com',
            'password' => Hash::make('belva'),
            'role' => 'customer',
        ]);

        // 2. Seed Cities
        $jakarta = City::create([
            'name' => 'Jakarta Pusat',
            'slug' => 'jakarta-pusat',
            'image' => '/assets/images/thumbnails/thumbnails-1.png',
        ]);

        $bandung = City::create([
            'name' => 'Bandung',
            'slug' => 'bandung',
            'image' => '/assets/images/thumbnails/thumbnails-2.png',
        ]);

        $surabaya = City::create([
            'name' => 'Surabaya',
            'slug' => 'surabaya',
            'image' => '/assets/images/thumbnails/thumbnails-3.png',
        ]);

        $yogyakarta = City::create([
            'name' => 'Yogyakarta',
            'slug' => 'yogyakarta',
            'image' => '/assets/images/thumbnails/thumbnails-4.png',
        ]);

        // 3. Seed Features
        $wifi = Feature::create(['name' => 'High Speed Wifi', 'icon' => 'wifi.svg']);
        $privacy = Feature::create(['name' => '100% Privacy', 'icon' => 'security.svg']);
        $freeMove = Feature::create(['name' => 'Free Move', 'icon' => 'user.svg']);
        $sustainability = Feature::create(['name' => 'Sustainability', 'icon' => 'verify.svg']);
        $parking = Feature::create(['name' => 'Parking Space', 'icon' => 'location.svg']);
        $compact = Feature::create(['name' => 'Compact', 'icon' => 'clock.svg']);

        // 4. Seed Offices
        // Office 1
        $office1 = Office::create([
            'city_id' => $jakarta->id,
            'provider_id' => $provider->id,
            'name' => 'Angga Park Central Master Silicon Valley Star Class',
            'slug' => Str::slug('Angga Park Central Master Silicon Valley Star Class'),
            'thumbnail' => '/assets/images/thumbnails/thumbnails-1.png',
            'about' => "Nikmati kemudahan sewa ruang kantor berkelas di jantung kota Jakarta Pusat. Didesain dengan tata kelola ramah lingkungan dan fasilitas berteknologi modern untuk mendukung inovasi bisnis skala global Anda.",
            'address' => 'Gedung BWA HQ Lantai 21, Jl. Sudirman No. 210406, Jakarta Pusat',
            'price' => 28560000,
            'duration_type' => '20 days',
            'is_open' => true,
            'is_full_booked' => false,
            'rating' => 4.5,
            'sales_contacts' => [
                [
                    'name' => 'Budi Santoso',
                    'role' => 'Sales Manager',
                    'photo' => '/assets/images/photos/photo-1.png',
                    'email' => 'budi.santoso@example.com',
                    'phone' => '123-456-7890'
                ],
                [
                    'name' => 'Siti Nurhaliza',
                    'role' => 'Sales Representative',
                    'photo' => '/assets/images/photos/photo-2.png',
                    'email' => 'siti.nurhaliza@example.com',
                    'phone' => '098-765-4321'
                ]
            ]
        ]);

        $office1->features()->sync([$wifi->id, $privacy->id, $freeMove->id, $sustainability->id, $parking->id, $compact->id]);
        $office1->images()->createMany([
            ['image' => '/assets/images/thumbnails/thumbnails-1.png'],
            ['image' => '/assets/images/thumbnails/thumbnail-details-2.png']
        ]);

        // Office 2
        $office2 = Office::create([
            'city_id' => $surabaya->id,
            'provider_id' => $provider->id,
            'name' => 'Pondok Pekerja Remote Surabaya',
            'slug' => Str::slug('Pondok Pekerja Remote Surabaya'),
            'thumbnail' => '/assets/images/thumbnails/thumbnails-3.png',
            'about' => "Ruang kerja komunal berkonsep asri dan tenang di Surabaya. Dirancang khusus bagi para digital nomad dan remote developer yang mendambakan produktivitas tinggi dengan sentuhan kopi terbaik.",
            'address' => 'Gedung BWA HQ Surabaya Timur No. 10214, Surabaya',
            'price' => 12000000,
            'duration_type' => '15 days',
            'is_open' => true,
            'is_full_booked' => false,
            'rating' => 4.8,
            'sales_contacts' => [
                [
                    'name' => 'Ahmad Wijaya',
                    'role' => 'Sales Manager',
                    'photo' => '/assets/images/photos/photo-1.png',
                    'email' => 'ahmad.wijaya@example.com',
                    'phone' => '121-472-7890'
                ],
                [
                    'name' => 'Dewi Lestari',
                    'role' => 'Sales Representative',
                    'photo' => '/assets/images/photos/photo-2.png',
                    'email' => 'dewi.lestari@example.com',
                    'phone' => '098-765-2104'
                ]
            ]
        ]);

        $office2->features()->sync([$wifi->id, $privacy->id, $compact->id]);
        $office2->images()->createMany([
            ['image' => '/assets/images/thumbnails/thumbnails-3.png'],
            ['image' => '/assets/images/thumbnails/thumbnail-details-2.png']
        ]);
    }
}
