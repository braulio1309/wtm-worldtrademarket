<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retiro</title>
</head>
<body>
    <h1>Hola, Braulio</h1>
    <p>Han solicitado un retiro:</p>
    <ul>
        <li>Email: {{ $data['email'] }}</li>
        <li>Folio: {{ $data['folio'] }}</li>
        <li>Monto: {{ $data['amount'] }}</li>
        <li>Metodo de retiro: {{ $data['paymentMethod'] }}</li>
        @if($data['paymentMethod'] == 'usdt')
            <li>Wallet: {{ $data['wallet'] }}</li>
        @else
            <li>Banco: {{ $data['bank'] }}</li>
            <li>Clave interbancaria: {{ $data['interbank_key'] }}</li>
        @endif
    </ul>
</body>
</html>
