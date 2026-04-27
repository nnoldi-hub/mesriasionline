<?php

namespace Database\Seeders;

use App\Services\EmailTemplateService;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templateService = app(EmailTemplateService::class);
        $templateService->seedDefaultTemplates();
        
        $this->command->info('Template-urile email default au fost create.');
    }
}
