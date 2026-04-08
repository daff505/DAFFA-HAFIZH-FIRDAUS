<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bagikan data pengaturan sistem ke seluruh view secara global
        try {
            if (Schema::hasTable('tb_pengaturan')) {
                $settings = DB::table('tb_pengaturan')->pluck('nilai', 'kunci')->toArray();
                View::share('sys_settings', $settings);
            }
        } catch (\Exception $e) {
            // Jika database belum siap, abaikan agar tidak error saat migrasi/setup
        }

        Gate::define('admin', fn($user) => $user->role === 'admin');
        Gate::define('petugas', fn($user) => $user->role === 'petugas');
        Gate::define('owner', fn($user) => $user->role === 'owner');
        
        // Rekap Transaksi Sesuai Waktu (Hanya Owner)
        Gate::define('view-reports', fn($user) => $user->role === 'owner');
        
        // Transaksi & Cetak Struk (Hanya Petugas)
        Gate::define('manage-transaksi', fn($user) => $user->role === 'petugas');
        
        // Manajemen Kendaraan (Hanya Admin)
        Gate::define('manage-kendaraan', fn($user) => $user->role === 'admin');
    }
}
