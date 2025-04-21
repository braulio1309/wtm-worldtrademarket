<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Correo</title>
    <style>
        body {
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            background-color: #1e293b; /* Azul oscuro */
            color: #e2e8f0; /* Texto gris claro */
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #334155;
        }

        .content {
            margin-top: 20px;
            line-height: 1.6;
            font-size: 16px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 30px;
            border-top: 1px solid #334155;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>World Trade Market</h2>

        </div>

        <div class="content">
            <!-- Contenido aquí -->
            Hola {{ $data['name'] }}, tu Deposito procesado con éxito, los fondos están reflejados en su balance.
            <ul>
                <li>Folio: {{ $data['folio'] }}</li>
                <li>Monto: {{ $data['amount'] }}</li>
                <li>Fecha: {{ $data['fecha'] }}</li>
            </ul>
            
        </div>

        <div class="footer">
            Este mensaje fue enviado automáticamente desde WTM.
        </div>
    </div>
</body>
</html>
