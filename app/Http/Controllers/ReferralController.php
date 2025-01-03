<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Core\Auth\ReferralService;
use App\Filters\Common\Auth\ReferralFilter as AppUserFilter;
use App\Filters\Core\ReferralFilter;

class ReferralController extends Controller
{

    public function __construct(ReferralService $Referral, ReferralFilter $filter)
    {
        $this->service = $Referral;
        $this->filter = $filter;
    }

    public function referrals()
    {

        return (new AppUserFilter(
            $this->service
            ->select(
                'users.id',
                'referrals.referrerId',
                'referrals.referredId',
                'users.first_name as referred_first_name',
                'users.last_name as referred_last_name',
                'users.email as referred_email',
                'accounts.balance as referred_balance'
            )
            ->join('users', 'referrals.referredId', '=', 'users.id') // Join con la tabla de usuarios
            ->leftJoin('accounts', 'users.id', '=', 'accounts.userId') // Join con la tabla de cuentas
            ->where('referrals.referrerId', '=', Auth()->user()->id)
            ->latest('referrals.created_at')
        ))->filter()
            ->paginate(request()->get('per_page', 10));
    }
}
