<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'tb_pengaturan';
    protected $primaryKey = 'id_pengaturan';
    
    protected $fillable = [
        'kunci',
        'nilai',
        'keterangan'
    ];

    public $timestamps = true;
}
