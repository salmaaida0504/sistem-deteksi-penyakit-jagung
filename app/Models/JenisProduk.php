<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisProduk extends Model
{
    protected $table = 'jenis_produk';
    protected $primaryKey = 'id_jenis';

    protected $fillable = [
        'jenis_produk',
    ];

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'id_jenis', 'id_jenis');
    }
}
