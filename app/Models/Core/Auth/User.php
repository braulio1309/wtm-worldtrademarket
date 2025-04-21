<?php

namespace App\Models\Core\Auth;

use Altek\Eventually\Eventually;
use App\Models\Core\Auth\Traits\Attribute\UserAttribute;
use App\Models\Core\Auth\Traits\Boot\UserBootTrait;
use App\Models\Core\Auth\Traits\Method\HasRoles;
use App\Models\Core\Auth\Traits\Method\UserMethod;
use App\Models\Core\Auth\Traits\Method\UserStatus;
use App\Models\Core\Auth\Traits\Relationship\UserRelationship;
use App\Models\Core\Auth\Traits\Rules\UserRules;
use App\Models\Core\Auth\Traits\Scope\UserScope;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Account;
use App\Models\Referral;
use App\Models\PaymentMethod;
use App\Models\Transaction;

class User extends BaseUser implements HasLocalePreference
{
    protected static $logAttributes = [
        'first_name',
        'last_name',
        'email',
    ];

    use UserAttribute,
        UserMethod,
        UserRelationship,
        UserScope,
        HasRoles,
        UserRules,
        UserBootTrait,
        LogsActivity,
        Eventually,
        Notifiable,
        CausesActivity,
        UserStatus,
        HasFactory;

    public function preferredLocale(): ?string
    {
        return app()->getLocale() ?? 'en';
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $class_alias_array = explode('\\', get_called_class());
        $class_name = strtolower(end($class_alias_array));

        return trans('default.log_description_message', [
            'model' => trans('default.' . $class_name),
            'event' => trans('default.' . $eventName)
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'userId');
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrerId');
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class)->orderBy('created_at', 'desc');
    }

    public function transactions()
    {
        return $this->hasManyThrough(
            \App\Models\Transaction::class, // modelo final
            \App\Models\Account::class,     // modelo intermedio
            'userId',                      // Foreign key en accounts que referencia a users
            'accountId',                   // Foreign key en transactions que referencia a accounts
            'id',                           // Local key en users
            'id'                            // Local key en accounts
        );
    }
}
