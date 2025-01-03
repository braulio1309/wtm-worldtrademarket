<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Auth\User;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = ['referrerId', 'referredId'];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrerId');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referredId');
    }
}
