<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menu_items')) {
            $copyTradingMenuIds = DB::table('menu_items')
                ->where('label', 'like', '%Copy Trading%')
                ->orWhere('route_name', 'like', '%copy-trading%')
                ->orWhere('route_wildcard', 'like', '%copy-trading%')
                ->pluck('id');

            if ($copyTradingMenuIds->isNotEmpty()) {
                DB::table('menu_items')->whereIn('parent_id', $copyTradingMenuIds)->delete();
                DB::table('menu_items')->whereIn('id', $copyTradingMenuIds)->delete();
            }

            DB::table('menu_items')
                ->where('route_name', 'like', '%copy-trading%')
                ->orWhere('route_wildcard', 'like', '%copy-trading%')
                ->delete();
        }

        if (Schema::hasTable('cron_jobs')) {
            DB::table('cron_jobs')->where('command', 'copy_trading_sync')->delete();
        }

        if (Schema::hasTable('settings')) {
            $setting = DB::table('settings')->where('key', 'modules')->first();
            if ($setting) {
                $modules = json_decode($setting->value, true);
                if (is_array($modules) && array_key_exists('copy_trading_module', $modules)) {
                    unset($modules['copy_trading_module']);
                    DB::table('settings')->where('key', 'modules')->update([
                        'value' => json_encode($modules, JSON_UNESCAPED_SLASHES),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Intentionally not restored: Copy Trading has been removed from the product.
    }
};
