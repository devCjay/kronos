<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menu_items')) {
            $menuIds = DB::table('menu_items')
                ->where(function ($query) {
                    $query->where('label', 'like', '%Copy Trading%')
                        ->orWhere('label', 'like', '%Copy Trade%')
                        ->orWhere('route_name', 'like', '%copy-trading%')
                        ->orWhere('route_name', 'like', '%copy_trading%')
                        ->orWhere('route_wildcard', 'like', '%copy-trading%')
                        ->orWhere('route_wildcard', 'like', '%copy_trading%')
                        ->orWhere('url', 'like', '%copy-trading%')
                        ->orWhere('url', 'like', '%copy_trading%');
                })
                ->pluck('id');

            if ($menuIds->isNotEmpty()) {
                DB::table('menu_items')->whereIn('parent_id', $menuIds)->delete();
                DB::table('menu_items')->whereIn('id', $menuIds)->delete();
            }
        }

        if (Schema::hasTable('cron_jobs')) {
            DB::table('cron_jobs')
                ->where('command', 'like', '%copy_trading%')
                ->orWhere('command', 'like', '%copy-trading%')
                ->delete();
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

        Schema::dropIfExists('copy_trading_histories');
        Schema::dropIfExists('copy_tradings');

        cache()->forget('admin_menu_items');
        cache()->forget('user_menu_items');
    }

    public function down(): void
    {
        // Removed trading history cannot be reconstructed safely.
    }
};
