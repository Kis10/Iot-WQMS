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
            
            // Services Details
            [
                'key' => 'service1_num', 'value' => '01', 'type' => 'text'
            ],
            [
                'key' => 'service1_title', 'value' => 'Automated Data Collection', 'type' => 'text'
            ],
            [
                'key' => 'service1_desc', 'value' => 'Continuous background data harvesting from a pond, simultaneously without manual intervention.', 'type' => 'textarea'
            ],
            [
                'key' => 'service2_num', 'value' => '02', 'type' => 'text'
            ],
            [
                'key' => 'service2_title', 'value' => 'Smart Alert Notifications', 'type' => 'text'
            ],
            [
                'key' => 'service2_desc', 'value' => 'Instant Alert notifications when water parameters exceed safe threshold limits for your specific fish species.', 'type' => 'textarea'
            ],
            [
                'key' => 'service3_num', 'value' => '03', 'type' => 'text'
            ],
            [
                'key' => 'service3_title', 'value' => 'AI Condition Analysis', 'type' => 'text'
            ],
            [
                'key' => 'service3_desc', 'value' => 'Advanced algorithms that analyze patterns to predict water quality health and recommend corrective actions.', 'type' => 'textarea'
            ],

            // Team Members
            [
                'key' => 'about_title', 'value' => 'Meet the Team', 'type' => 'text'
            ],
            [
                'key' => 'about_subtitle', 'value' => 'The dedicated minds behind AquaSense, working together to revolutionize aquaculture monitoring.', 'type' => 'text'
            ],
            [
                'key' => 'team1_name', 'value' => 'Kirstine A. Sanchez', 'type' => 'text'
            ],
            [
                'key' => 'team1_role', 'value' => 'Web/Arduino Developer', 'type' => 'text'
            ],
            [
                'key' => 'team1_desc', 'value' => 'Spearheads the hardware integration and full-stack web development.', 'type' => 'textarea'
            ],
            [
                'key' => 'team1_img', 'value' => null, 'image' => null, 'type' => 'image'
            ],
            [
                'key' => 'team2_name', 'value' => 'Dannica J. Besinio', 'type' => 'text'
            ],
            [
                'key' => 'team2_role', 'value' => 'Documenter', 'type' => 'text'
            ],
            [
                'key' => 'team2_desc', 'value' => 'Ensures comprehensive documentation of system processes and user guides.', 'type' => 'textarea'
            ],
            [
                'key' => 'team2_img', 'value' => null, 'image' => null, 'type' => 'image'
            ],
            [
                'key' => 'team3_name', 'value' => 'Joy Mae A. Samra', 'type' => 'text'
            ],
            [
                'key' => 'team3_role', 'value' => 'Documenter', 'type' => 'text'
            ],
            [
                'key' => 'team3_desc', 'value' => 'Focuses on research, technical writing, and system validation.', 'type' => 'textarea'
            ],
            [
                'key' => 'team3_img', 'value' => null, 'image' => null, 'type' => 'image'
            ],
            [
                'key' => 'team4_name', 'value' => 'Jonas D. Parraño', 'type' => 'text'
            ],
            [
                'key' => 'team4_role', 'value' => 'System Analyst / Capstone Adviser', 'type' => 'text'
            ],
            [
                'key' => 'team4_desc', 'value' => 'Provides expert guidance on system architecture and project direction.', 'type' => 'textarea'
            ],
            [
                'key' => 'team4_img', 'value' => null, 'image' => null, 'type' => 'image'
            ],

            // Footer Additional
            [
                'key' => 'footer_brand', 'value' => 'IoT-Based AquaSense', 'type' => 'text'
            ],
            [
                'key' => 'footer_copyright', 'value' => '© 2026 IoT-Based Water Quality Monitoring System. All rights reserved.', 'type' => 'text'
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
