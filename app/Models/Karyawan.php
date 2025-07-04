<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Karyawan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = "karyawan";
    protected $primaryKey = "user_id";
    // protected $guard = "karyawan";

    protected $fillable = [
        'user_id',
        'departemen_id',
        'nama_lengkap',
        'jabatan',
        'telepon',
        'email',
        'password',
        'foto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }
}
