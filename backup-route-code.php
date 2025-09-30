<?php
// Add this to your web.php routes file

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::get('/backup-data/{table?}', function($table = null) {
    try {
        if ($table) {
            // Backup specific table
            if (!Schema::hasTable($table)) {
                return response()->json(['error' => 'Table not found'], 404);
            }
            
            $data = DB::table($table)->get();
            return response()->json([
                'table' => $table,
                'rows' => $data->count(),
                'data' => $data
            ]);
        } else {
            // List all tables
            $tables = DB::select("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_type = 'BASE TABLE'
                ORDER BY table_name
            ");
            
            return response()->json([
                'tables' => array_map(function($t) { return $t->table_name; }, $tables)
            ]);
        }
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
?>