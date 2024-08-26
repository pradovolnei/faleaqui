<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'photo'];

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }

}
