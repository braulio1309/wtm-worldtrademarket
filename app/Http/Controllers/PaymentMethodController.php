<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos enviados
        $validated = $request->validate([
            'wallet' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'interbank_key' => 'nullable|string|max:255',
            'active_method' => 'required|in:usdt,bank_account',
        ]);

        // Obtener el usuario autenticado
        $user = auth()->user();

        $lastActiveMethod = PaymentMethod::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('updated_at') // o 'created_at', según tu lógica
            ->first();

        if ($lastActiveMethod) {
            $lastActiveMethod->status = 'inactive';
            $lastActiveMethod->save();
        }
        
        // Crear o actualizar el método de pago
        $paymentMethod = PaymentMethod::Create(
            [
                'status' => 'active',
                'wallet' => $validated['wallet'] ?? null,
                'bank' => $validated['bank'] ?? null,
                'interbank_key' => $validated['interbank_key'] ?? null,
                'user_id' => $user->id,
                'type' => $validated['active_method']
            ]
        );

        

        return created_responses('PaymentMethod');
    }
}
