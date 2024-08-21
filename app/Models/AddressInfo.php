<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'address_1', 'address_2', 'country', 'state_province', 'city', 'zipcode',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
