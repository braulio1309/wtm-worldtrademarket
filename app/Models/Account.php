<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Auth\User;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['userId', 'balance'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'accountId');
    }
}
