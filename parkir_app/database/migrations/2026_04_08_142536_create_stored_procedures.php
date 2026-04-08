<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Procedure untuk Log Aktivitas
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_log_aktivitas");
        DB::unprepared("
            CREATE PROCEDURE sp_log_aktivitas(
                IN p_id_user INT, 
                IN p_aktivitas VARCHAR(255), 
                IN p_tabel VARCHAR(50), 
                IN p_id_record INT, 
                IN p_detail TEXT, 
                IN p_ip VARCHAR(45)
            )
            BEGIN
                INSERT INTO tb_log_aktivitas (id_user, aktivitas, tabel_terkait, id_record_terkait, detail, ip_address, waktu_aktivitas)
                VALUES (p_id_user, p_aktivitas, p_tabel, p_id_record, p_detail, p_ip, NOW());
            END
        ");

        // 2. Procedure/Function untuk Cek Kapasitas Area
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_area_tersedia");
        DB::unprepared("
            CREATE PROCEDURE sp_get_area_tersedia()
            BEGIN
                SELECT *, (kapasitas - terisi) AS sisa_slot 
                FROM tb_area_parkir 
                WHERE status_area = 'aktif' AND terisi < kapasitas;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_log_aktivitas");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_area_tersedia");
    }
};
