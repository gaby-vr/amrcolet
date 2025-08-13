<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivrareCancelRequest extends Model
{
    protected $fillable = [
        'livrare_id',
        'type',   
    ];

    protected $table = 'livrari_cancel_requests';

    public function livrare()
    {
        return $this->belongsTo(Livrare::class);
    }
}
