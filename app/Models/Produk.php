<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'id_jenis',
        'nama_produk',
        'deskripsi_produk',
        'foto_produk',
        'manfaat',
        'keunggulan',
        'dosis_penggunaan',
    ];

    public function jenisProduk(): BelongsTo
    {
        return $this->belongsTo(JenisProduk::class, 'id_jenis', 'id_jenis');
    }

    public function opt(): BelongsToMany
    {
        return $this->belongsToMany(Opt::class, 'opt_produk', 'id_produk', 'id_opt');
    }
}
