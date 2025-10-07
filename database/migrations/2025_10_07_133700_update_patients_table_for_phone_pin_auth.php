<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, fix duplicate mobile phone numbers by adding a suffix
        $duplicates = \DB::table('patients')
            ->select('mobile_phone', \DB::raw('COUNT(*) as count'))
            ->groupBy('mobile_phone')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            $patients = \DB::table('patients')
                ->where('mobile_phone', $duplicate->mobile_phone)
                ->orderBy('id')
                ->get();

            foreach ($patients->skip(1) as $index => $patient) {
                $newPhone = $duplicate->mobile_phone . '_' . ($index + 1);
                \DB::table('patients')
                    ->where('id', $patient->id)
                    ->update(['mobile_phone' => $newPhone]);
            }
        }

        Schema::table('patients', function (Blueprint $table) {
            // Check if password column exists and drop it
            if (Schema::hasColumn('patients', 'password')) {
                $table->dropColumn('password');
            }
            
            // Add PIN column (4 digits) if it doesn't exist
            if (!Schema::hasColumn('patients', 'pin')) {
                $table->string('pin', 4)->after('mobile_phone');
            }
            
            // Make mobile_phone unique for authentication if not already unique
            if (!$this->hasUniqueIndex('patients', 'mobile_phone')) {
                $table->unique('mobile_phone');
            }
        });
    }

    /**
     * Check if a unique index exists on a column
     */
    private function hasUniqueIndex($table, $column)
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table} WHERE Column_name = '{$column}' AND Non_unique = 0");
        return count($indexes) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Remove PIN column and unique constraint on mobile_phone
            $table->dropUnique(['mobile_phone']);
            $table->dropColumn('pin');
            
            // Add back password column
            $table->string('password')->after('email');
        });
    }
};