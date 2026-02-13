<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            // Hero
            [
                'key' => 'hero_title',
                'value' => 'IoT-Based Water Quality <br> <span class="gradient-text">Monitoring System</span>',
                'type' => 'html'
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'Ensuring a sustainable aquaculture environment through high-precision IoT sensors and real-time data analytics.',
                'type' => 'textarea'
            ],
            [
                'key' => 'hero_bg',
                'value' => null, // default bg
                'image' => null,
                'type' => 'image'
            ],

            // Mission
            [
                'key' => 'mission_badge',
                'value' => 'OUR MISSION',
                'type' => 'text'
            ],
            [
                'key' => 'mission_title',
                'value' => 'The Future of Aquaculture Management',
                'type' => 'text'
            ],
            [
                'key' => 'mission_text',
                'value' => 'Our system is designed to provide farmers with a robust, reliable, and user-friendly platform for monitoring vital aquatic conditions. By leveraging the power of IoT, we help eliminate the guesswork, reduce risks, and maximize productivity in aquaculture operations.',
                'type' => 'textarea'
            ],

            // Features (Sensors) - Just Header for now
            [
                'key' => 'sensors_title',
                'value' => 'Integrated Sensor Technology',
                'type' => 'text'
            ],
            [
                'key' => 'sensors_subtitle',
                'value' => 'Our system utilizes five high-precision sensors to capture every critical metric.',
                'type' => 'text'
            ],
            
            // Services
            [
                 'key' => 'services_title',
                 'value' => 'Our Services',
                 'type' => 'text'
            ],
            [
                 'key' => 'services_subtitle',
                 'value' => 'We provide end-to-end solutions for aquaculture technology integration.',
                 'type' => 'text'
            ],

            // Contact
            [
                 'key' => 'contact_title',
                 'value' => 'Contact Us',
                 'type' => 'text'
            ],
            [
                 'key' => 'contact_subtitle',
                 'value' => 'Have questions? We\'re here to help you optimize your aquaculture operations.',
                 'type' => 'text'
            ],

            // Footer
            [
                'key' => 'footer_devs',
                'value' => 'Developed by: Kirstine A. Sanchez, Dannica J. Besinio and Joy Mae A. Samra',
                'type' => 'text'
            ]
        ];

        foreach ($defaults as $item) {
            \App\Models\LandingContent::updateOrCreate(
                ['key' => $item['key']],
                $item
            );
        }
    }
}
