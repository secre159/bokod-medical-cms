<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowData extends Command
{
    protected $signature = 'show:data {table?}';
    protected $description = 'Show data from database tables';

    public function handle()
    {
        $table = $this->argument('table');
        
        if ($table) {
            $this->showTableData($table);
        } else {
            $this->showAllTablesSummary();
        }
    }
    
    private function showAllTablesSummary()
    {
        $this->info('Database Tables Summary:');
        $this->info('========================');
        
        $tables = ['users', 'patients', 'appointments', 'messages', 'conversations', 'medicines'];
        
        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->count();
                $this->line("$table: $count records");
            } catch (\Exception $e) {
                $this->line("$table: Table not found");
            }
        }
        
        $this->info("\nUse 'php artisan show:data tablename' to see specific table data");
    }
    
    private function showTableData($table)
    {
        try {
            $data = DB::table($table)->limit(5)->get();
            
            $this->info("First 5 records from $table:");
            $this->info('================================');
            
            if ($data->isEmpty()) {
                $this->warn("No data found in $table");
                return;
            }
            
            // Show as table format
            $headers = array_keys((array) $data->first());
            $rows = $data->map(function ($item) {
                return array_values((array) $item);
            })->toArray();
            
            $this->table($headers, $rows);
            
        } catch (\Exception $e) {
            $this->error("Error accessing table $table: " . $e->getMessage());
        }
    }
}