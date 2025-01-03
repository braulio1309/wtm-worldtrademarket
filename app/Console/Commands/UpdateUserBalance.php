<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Core\Auth\User;
use App\Models\Core\Setting\Setting;
use App\Models\Transaction;

class UpdateUserBalance extends Command
{
    protected $signature = 'balance:update';
    protected $description = 'Actualiza el balance de los usuarios diariamente de lunes a viernes';

    public function calcularTasaDiaria(float $porcentaje, int $dias)
    {
        $tasa = pow(1 + ($porcentaje / 100), 1 / $dias) - 1;
        return $tasa;
    }

    public function handle()
    {
        $porcentaje = Setting::where('name', '=', 'comisiones')->select('value')->first(); // 3% total
        $differentComission = Setting::where('name', '=', 'DifferentComision')->select('value')->first();
        $dias = 22; // 22 días hábiles

        $dailyRate = $this->calcularTasaDiaria((float)$porcentaje->value, $dias);

        $users = User::whereHas('account') // Filtrar usuarios con cuenta asociada
            ->with('account')
            ->get();

        foreach ($users as $user) {
            $users = Setting::where('name', '=', 'users')->select('value')->first();
            $ids = explode(',', $users->value);
            if (in_array($user->id, $ids)) {
                $dailyRate = $this->calcularTasaDiaria((float)$differentComission->value, $dias);
            } else {
                $dailyRate = $this->calcularTasaDiaria((float)$porcentaje->value, $dias);
            }

            $account = $user->account;
            $currentBalance = $account->balance;
            $increment = $currentBalance * $dailyRate;
            $account->balance += $increment;
            $account->save();

            Transaction::create([
                'accountId' => $account->id,
                'type' => 'bonus',
                'amount' => $increment,
                'status' => 'approved',
                //'description' => 'Referral bonus for user ID ' . $account->userId,
                'folio' => $this->generateUniqueIdentifier()
            ]);

            $this->info("Balance actualizado para el usuario {$user->id}. Incremento: {$increment}. A razón de: {$dailyRate}");
        }

        return Command::SUCCESS;
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
}
