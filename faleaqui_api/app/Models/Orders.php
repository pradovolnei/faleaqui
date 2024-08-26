<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'title', 'obs', 'user_id', 'suport_id', 'latitude', 'altitude', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function photos()
    {
        return $this->hasMany(Photo::class, 'order_id');
    }
}
