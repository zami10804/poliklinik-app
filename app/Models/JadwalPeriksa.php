<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPeriksa extends Model
{
    use HasFactory;

    protected $table = 'jadwal_periksa'; 

    protected $fillable = [
        'id_dokter',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    // Ubah relasi ini mengarah ke model User
    public function dokter()
    {
        return $this->belongsTo(User::class, 'id_dokter');
    }
}