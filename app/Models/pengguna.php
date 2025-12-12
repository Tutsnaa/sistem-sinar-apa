<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    protected $table = 'pengguna';

    protected $fillable = [
        'foto',
        'nama_lengkap',
        'email',
        'no_telepon',
        'nama_pengguna',
        'kata_sandi',
        'role'
    ];
}