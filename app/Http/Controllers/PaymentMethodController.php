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

        // Crear o actualizar el método de pago
        $paymentMethod = PaymentMethod::updateOrCreate(
            ['user_id' => $user->id, 'type' => $validated['active_method']],
            [
                'status' => 'active',
                'wallet' => $validated['wallet'] ?? null,
                'bank' => $validated['bank'] ?? null,
                'interbank_key' => $validated['interbank_key'] ?? null,
            ]
        );

        return created_responses('PaymentMethod');
    }
}
