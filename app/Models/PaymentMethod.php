<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Auth\User;

class PaymentMethod extends Model
{
    use HasFactory;


    protected $table = 'payment_methods_wtm';

    protected $fillable = [
        'user_id',
        'type',
        'wallet',
        'bank',
        'interbank_key',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

