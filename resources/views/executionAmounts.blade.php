<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $tipo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        th:first-child,
        td:first-child {
            border-left: none;
        }

        th:last-child,
        td:last-child {
            border-right: none;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #f2f2f2;
            text-align: center;
            padding: 10px 0;
        }

    </style>
</head>

<body>

    <div class="header">
        <img src="{{ asset('images/log.jpg') }}"
            style="position: absolute; top: 10px; right: 10px; width: 180px; height: auto; z-index: 9999;" />

        <p>Reporte de Montos en Ejecucion</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Número</th>
                <th>Fecha de Inicio</th>
                <th>Materia</th>
                <th>Demandante</th>
                <th>Demandado</th>
                <th>Monto total fijado en sentencia</th>
                <th>Saldo total por pagar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($proceedings as $proceeding)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $proceeding->exp_numero }}</td>
                    <td>{{ $proceeding->exp_fecha_inicio }}</td>
                    <td>{{ $proceeding->materia->mat_nombre }}</td>
                    <td>
                        @foreach ($proceeding->procesal as $item)
                            @if ($item->tipo_procesal === 'DEMANDANTE')
                                @php
                                    $demandante =
                                        $item->tipo_persona === 'NATURAL' ? $item->persona->nat_nombres : 'UNPRG';
                                    if ($item->tipo_persona === 'NATURAL') {
                                       
                                    }
                                @endphp
                                {{ $demandante }}
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach ($proceeding->procesal as $item)
                            @if ($item->tipo_procesal === 'DEMANDADO')
                                @php
                                    $demandado =
                                        $item->tipo_persona === 'NATURAL' ? $item->persona->nat_nombres : 'UNPRG';
                                @endphp
                                {{ $demandado }}
                            @endif
                        @endforeach
                    </td>
                    <td>{{ $proceeding->montos->total_amount_sentence }}</td>
                    <td>{{ $proceeding->montos->total_balance_payable }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Pie de página - Inserta aquí lo que quieras que aparezca al final de la página</p>
    </div>

</body>

</html>
