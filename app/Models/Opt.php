<?php

namespace App\Models;

use App\Models\Detection;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opt extends Model
{
    protected $table = 'opt';
    protected $primaryKey = 'id_opt';

    protected $fillable = [
        'jenis_opt',
        'nama_opt',
        'deskripsi',
        'penyebab',
        'gejala',
        'pencegahan',
        'penanganan',
        'foto_opt',
    ];

    public function produk(): BelongsToMany
    {
        return $this->belongsToMany(Produk::class, 'opt_produk', 'id_opt', 'id_produk');
    }

    public function detections(): HasMany
    {
        return $this->hasMany(Detection::class, 'id_opt', 'id_opt');
    }
}
