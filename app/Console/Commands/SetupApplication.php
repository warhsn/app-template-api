<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;

class SetupApplication extends Command
{
    protected $signature = 'app:setup {--fresh : Run fresh migrations before setup}';

    protected $description = 'Sets up the application by running all populate commands in the correct order';

    public function handle()
    {
        $this->info('🚀 Starting application setup...');
        $this->newLine();

        $this->call('passport:install');

        // Option to run fresh migrations
        if ($this->option('fresh')) {
            $this->info('🔄 Running fresh migrations...');
            $this->call('migrate:fresh');
            $this->newLine();
        }

        // Create password grant client
        $this->info('🔐 Creating password grant client...');
        $this->call('passport:client', [
            '--password' => true,
            '--name' => config('app.name').' Password Grant Client',
            '--provider' => 'users',
            '--no-interaction' => true,
        ]);
        $this->line('   ✅ Password grant client created successfully');
        $this->newLine();

        // Array of populate commands in logical order
        $populateCommands = [];

        $totalCommands = count($populateCommands);
        $currentCommand = 0;

        foreach ($populateCommands as $commandInfo) {
            $currentCommand++;

            $this->info("📦 [{$currentCommand}/{$totalCommands}] {$commandInfo['description']}...");

            try {
                $this->call($commandInfo['command']);
                $this->line("   ✅ {$commandInfo['description']} completed successfully");
            } catch (Exception $e) {
                $this->error("   ❌ Failed to run {$commandInfo['command']}: {$e->getMessage()}");

                if ($this->confirm('Do you want to continue with the remaining commands?', true)) {
                    continue;
                } else {
                    $this->error('Setup aborted.');

                    return 1;
                }
            }

            $this->newLine();
        }

        $this->newLine();
        $this->info('🎉 Application setup completed successfully!');
        $this->newLine();

        $this->table(['Command', 'Description'], [
            ['app:setup', 'Run this command to set up the application'],
            ['app:setup --fresh', 'Run with fresh migrations before setup'],
        ]);

        $this->info('💡 Your application is now ready to use!');

        return 0;
    }
}
