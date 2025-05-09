<?php

namespace App\Http\Controllers\App\Users;

use App\Exceptions\GeneralException;
use App\Filters\Core\UserFilter;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Core\Auth\User;
use App\Models\Transaction;
use App\Notifications\Core\User\UserNotification;
use App\Services\Core\Auth\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(UserService $service, UserFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        
        return $this->service->with('status:id,name,type')
            ->filters($this->filter)
            ->latest()
            ->paginate(request()->get('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->service->save();
        return created_responses('crud');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        return $this->service->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, $id)
    {
        $user = $this->service->find($id);
        
        //Crear transaccion
        //cambiar saldo 
        //editar cuenta
        if ($user->status_id != 3 && $user->id != auth()->id()) {

            $this->service->where('id', $id)->update(\request()->only('status_id'));

            notify()
                ->on('user_updated')
                ->with($user)
                ->send(UserNotification::class);

            return updated_responses('user');
        } else {
            throw new GeneralException(trans('default.status_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        if ($user->delete()) {
            return deleted_responses('user');
        }
        return failed_responses();
    }

    public function getUsers()
    {
        return $this->service->with('status:id,name,type')
            ->filters($this->filter)
            ->latest()
            ->get();
    }

    public function updateUserName(Request $request, $id)
    {
        $user = $this->service->find($id);
        $accountId = $request->all()['account']['id'];
        $accountOld = Account::where('id', $accountId)->first();
        if($accountOld->balance > $request->all()['account']['balance']){
            $total = $accountOld->balance - $request->all()['account']['balance'];
            Transaction::create([
                'accountId' => $accountId,
                'amount' => $total,
                'status' => 'approved',
                'type' => 'withdrawal',
                'description' => 'Ajuste administrador'
            ]);

            $accountOld->balance = $request->all()['account']['balance'];
            $accountOld->save();
        }elseif ($accountOld->balance < $request->all()['account']['balance']){
            $total = $request->all()['account']['balance'] - $accountOld->balance;
            Transaction::create([
                'accountId' => $accountId,
                'amount' => $total,
                'status' => 'approved',
                'type' => 'deposit',
                'description' => 'Ajuste administrador'
            ]);
            $accountOld->balance = $request->all()['account']['balance'];
            $accountOld->save();
        }
        $this->service->where('id', $id)->update(\request()->only('first_name', 'last_name'));

        notify()
            ->on('user_updated')
            ->with($user)
            ->send(UserNotification::class);

        return updated_responses('user');
    }

    public function updateValidateDocument($id){
        $user = User::where('id', $id)->get();
        $user = $user[0];
        $user->document_verified = 1;
        $user->save();
        return updated_responses('user');
    }
}
