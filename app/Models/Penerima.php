<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerima extends Model
{
    protected $table = 'penerima';
    protected $guarded = ['id'];
    protected $fillable = [
        'nama',
        'alamat',
        'no_hp',
        'usia',
        'jumlah_anak',
        'kelayakan_rumah',
        'pendapatan_perbulan',
    ];
}
