<?php

namespace App\Http\Controllers;

use App\Filters\Common\Auth\TransactionFilter as AppUserFilter;
use App\Filters\Core\TransactionFilter;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Referral;

use App\Models\Transaction;
use App\Services\Core\Auth\TransactionService;

use Illuminate\Http\Request;
use App\Mail\WithdrawalMail;
use App\Mail\DepositoMail;
use App\Mail\DepositoProcesadoMail;
use App\Mail\RetiroProcesadoMail;
use App\Mail\RetiroMail;

use Illuminate\Support\Facades\Mail;
use App\Models\Core\Auth\User;

use Carbon\Carbon;


/**
 * Class TransactionController.
 */
class TransactionController extends Controller
{

    /**
     * TransactionController constructor.
     *
     * @param TransactionService $Transaction
     * @param TransactionFilter $filter
     */

    public function __construct(TransactionService $Transaction, TransactionFilter $filter)
    {
        $this->service = $Transaction;
        $this->filter = $filter;
    }


    /**
     *
     * @return mixed
     */
    public function index($type = 'deposit')
    {
        $user = Auth()->user();
        return (new AppUserFilter(
            $this->service
                ->filters($this->filter)
                ->with('account.user')
                //->where('type', '!=', 'withdrawal')
                ->where('accountId', $user->account->id)
                ->latest()
        ))->filter()
            ->paginate(request()->get('per_page', 10));
    }

    /**
     *
     * @return mixed
     */
    public function withdrawal()
    {
        $user = Auth()->user();
        return (new AppUserFilter(
            $this->service
                ->filters($this->filter)
                ->with('account.user')
                ->where('type', '=', 'withdrawal')
                ->where('accountId', $user->account->id)
                ->latest()
        ))->filter()
            ->paginate(request()->get('per_page', 10));
    }

    public function comisiones($id)
    {
        $user = User::where('id', $id)->with('account')->first();

        $account = $user->account;
        return (new AppUserFilter(
            $this->service
                ->filters($this->filter)
                ->with('account.user')
                ->where('type', '=', 'bonus')
                ->where('accountId', $account->id)
                ->latest()
        ))->filter()
            ->paginate(request()->get('per_page', 10));
    }


    public function createTransaction(Request $request)
    {

        if ($request->input('type') == 'withdrawal') {
            $firstTransaction = auth()->user()->transactions()->orderBy('created_at', 'asc')->first();
            $oneYearPassed = $firstTransaction && Carbon::parse($firstTransaction->created_at)->addYear()->lte(now());
            $capitalInversion = $firstTransaction?->amount ?? 0;
            $disponible = auth()->user()->wallet;
            $retiro = $request->input('amount');

            $retiroValido = $retiro <= ($disponible - $capitalInversion);
            if (false) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto a retirar excede el disponible después de restar el capital de inversión.'
                ], 403);
            }
        }

        $identifier = $this->generateUniqueIdentifier();

        Transaction::create([
            'accountId' => Auth()->user()->account->id,
            'amount' => $request->input('amount'),
            //'description' => $request->input('description'),
            'type' => $request->input('type'),
            'folio' => $identifier
        ]);



        if ($request->input('type') == 'withdrawal') {

            $payment = Auth()->user()->paymentMethods[0];

            $data = [
                'name' => Auth()->user()->first_name,
                'email' => Auth()->user()->email,
                //'message' => $request->input('description'),
                'amount' => $request->input('amount'),
                'folio' => $identifier,
                'paymentMethod' => $payment->type,
                'wallet' => $payment->wallet,
                'bank' => $payment->bank,
                'interbank_key' => $payment->interbank_key,
                'fecha' => now(),
            ];

            Mail::to('braulioza@gmail.com')
                ->send(new WithdrawalMail($data));
            Mail::to(Auth()->user()->email)
                ->send(new DepositoMail($data));
        } else {
            $data = [
                'name' => Auth()->user()->first_name,
                'email' => Auth()->user()->email,
                //'message' => $request->input('description'),
                'amount' => $request->input('amount'),
                'folio' => $identifier,
                'fecha' => now(),
                
            ];
            Mail::to(Auth()->user()->email)
                ->send(new DepositoMail($data));
        }



        return created_responses('Transaction');
    }

    public function editTransaction(Request $request, $id)
    {

        $transaction = Transaction::where('id', $id)->first();
        $amount = $transaction->amount;
        $amount = ($transaction->type == 'deposit') ? $amount : $amount * -1;

        $data = [
            'name' => Auth()->user()->first_name,
            'email' => Auth()->user()->email,
            'amount' => $request->input('amount'),
            'folio' => $transaction->folio,
            'fecha' => $transaction->created_at
        ];

        if ($request->input('status') == 'approved') {
            $account = Account::where('id', $transaction->accountId)->first();
            $account->balance += $amount;
            $transaction->status = $request->input('status');
            $transaction->save();
            $account->save();

            $referral = Referral::where('referredId', $account->userId)->first();
            if ($referral) {
                $referrerAccount = $referral->referrer->account;
                if ($referrerAccount) {
                    $bonus = $amount * 0.05; // 5% bonus
                    $referrerAccount->balance += $bonus;
                    $referrerAccount->save();

                    // Create transaction for the bonus
                    Transaction::create([
                        'accountId' => $referrerAccount->id,
                        'type' => 'bonus',
                        'amount' => $bonus,
                        'status' => 'approved',
                        //'description' => 'Referral bonus for user ID ' . $account->userId,
                        'folio' => $this->generateUniqueIdentifier()
                    ]);
                }
            }
        }

        if ($transaction->type == 'deposit') {

            Mail::to(Auth()->user()->email)
                ->send(new DepositoProcesadoMail($data));
        } else {
            Mail::to(Auth()->user()->email)
                ->send(new RetiroProcesadoMail($data));
        }

        return created_responses('Transaction');
    }

    public function editTransaction2(Request $request, $id)
    {

        $transaction = Transaction::where('id', $id)->first();
        $amount = $transaction->amount;
        $amount = ($transaction->type != 'withdrawal') ? $amount : $amount * -1;

        $data = [
            'name' => Auth()->user()->first_name,
            'email' => Auth()->user()->email,
            'amount' => $request->input('amount'),
            'folio' => $transaction->folio,
            'fecha' => $transaction->created_at
        ];
        if ($request->input('status') == 'approved') {
            $account = Account::where('id', $transaction->accountId)->first();
            $account->balance += $amount;
            $transaction->status = $request->input('status');
            $transaction->save();
            $account->save();

            $referral = Referral::where('referredId', $account->userId)->first();
            if ($referral) {
                $referrerAccount = $referral->referrer->account;
                if ($referrerAccount) {
                    $bonus = $amount * 0.05; // 5% bonus
                    $referrerAccount->balance += $bonus;
                    $referrerAccount->save();

                    // Create transaction for the bonus
                    Transaction::create([
                        'accountId' => $referrerAccount->id,
                        'type' => 'bonus',
                        'amount' => $bonus,
                        'status' => 'approved',
                        //'description' => 'Referral bonus for user ID ' . $account->userId,
                        'folio' => $this->generateUniqueIdentifier()
                    ]);
                }
            }
        }

        if ($transaction->type == 'deposit') {

            Mail::to(Auth()->user()->email)
                ->send(new DepositoProcesadoMail($data));
        } else {
            Mail::to(Auth()->user()->email)
                ->send(new RetiroProcesadoMail($data));
        }

        return created_responses('Transaction');
    }

    public static function generateUniqueIdentifier()
    {
        // Obtener el último identificador
        $lastTransaction = Transaction::latest('id')->first();

        // Generar el siguiente número
        $lastIdentifier = $lastTransaction ? intval($lastTransaction->folio) : 0;
        $newIdentifier = $lastIdentifier + 1;

        // Asegurar que sea de 5 dígitos, rellenando con ceros si es necesario
        return str_pad($newIdentifier, 5, '0', STR_PAD_LEFT);
    }

    /**
     *
     * @return mixed
     */
    public function status()
    {
        return (new AppUserFilter(
            $this->service
                ->filters($this->filter)
                ->with([
                    'account.user.paymentMethods' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }
                ])
                ->where('status', '=', 'pending')
                ->where('type', '!=', 'withdrawal')
                ->latest()
        ))->filter()
            ->paginate(request()->get('per_page', 10));
    }

    public function statusWithdrawal()
    {
        return (new AppUserFilter(
            $this->service
                ->filters($this->filter)
                ->with([
                    'account.user.paymentMethods' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }
                ])
                ->where('status', '=', 'pending')
                ->where('type', '=', 'withdrawal')
                ->latest()
        ))->filter()
            ->paginate(request()->get('per_page', 10));
    }


    /**
     * @param TransactionRequest $request
     * @return array
     */
    public function store($request)
    {
        $this->service
            ->create()
            ->when($request->get('roles'), function (TransactionService $service) use ($request) {
                $service->assignRole($request->get('roles'));
            })->notify('Transaction_created');

        return created_responses('Transaction');
    }


    /**
     * @param Transaction $Transaction
     * @return Transaction
     */
    public function show(Transaction $Transaction)
    {
        return $Transaction;
    }

    public function referrals()
    {
        $user = Auth()->user();

        return (new AppUserFilter(
            Auth::user()->referrals()
                ->with('account') // Incluye las cuentas de los usuarios referidos
                ->where('status', 'active') // Filtra por usuarios activos
                ->latest()
        ))->filter()
            ->paginate(request()->get('per_page', 10));
    }

    public function getEncryptedUserId()
    {
        $user = Auth()->user();
        $encryptedId = encrypt($user->id);

        return response()->json([
            'status' => true,
            'encrypted_id' => $encryptedId,
        ]);
    }
}
