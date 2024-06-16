<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Montos de Ejecucion</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            border-bottom: 0.5px solid #000000;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            font-size: 13px;
        }

        .horizontal {
            white-space: nowrap;
        }

        .title {
            font-weight: bold;
        }

        .subtitle {
            font-weight: 500;
        }

        .date {
            font-size: 12px;
            text-align: right !important;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: right;
            padding: 10px;
        }
    </style>
    </style>
</head>

<body>
    <div class="header">
        <p class="date">Fecha de impresión:
            {{ \Carbon\Carbon::now()->setTimezone('America/Lima')->format('d/m/Y H:i:s') }}</p>
        <img src="{{ asset('images/log.jpg') }}" style="width: 180px; height: auto;" />
        <h2 class="subtitle">Reporte de Montos en Ejecucion</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Número</th>
                <th class="horizontal">Fecha de Inicio</th>
                <th>Materia</th>
                <th>Demandante</th>
                <th>Demandado</th>
                <th>Monto total fijado en sentencia</th>
                <th>Saldo total por pagar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($proceedings as $index => $proceeding)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="horizontal">{{ $proceeding->exp_numero }}</td>
                    <td class="horizontal">{{ $proceeding->exp_fecha_inicio }}</td>
                    <td>{{ $proceeding->materia->mat_nombre }}</td>
                    <td>
                        @php
                            $demandante = 'UNPRG';
                            if ($proceeding->multiple === '0') {
                                foreach ($proceeding->procesal as $value) {
                                    if ($value->tipo_procesal === 'DEMANDANTE') {
                                        $demandante =
                                            $value->tipo_persona === 'NATURAL'
                                                ? ucwords(strtolower($value->persona->nat_nombres)) .
                                                    ' ' .
                                                    ucwords(strtolower($value->persona->nat_apellido_paterno)) .
                                                    ' ' .
                                                    ucwords(strtolower($value->persona->nat_apellido_materno))
                                                : $value->persona->jur_razon_social;
                                        break;
                                    }
                                }
                            } else {
                                $demandante = 'Demanda Colectiva';
                            }
                        @endphp
                        {{ $demandante }}
                    </td>
                    <td>
                        @php
                            $demandado = 'UNPRG';
                            if ($proceeding->multiple === '0') {
                                foreach ($proceeding->procesal as $value) {
                                    if ($value->tipo_procesal === 'DEMANDADO') {
                                        $demandado =
                                            $value->tipo_persona === 'NATURAL'
                                                ? ucwords(strtolower($value->persona->nat_nombres)) .
                                                    ' ' .
                                                    ucwords(strtolower($value->persona->nat_apellido_paterno)) .
                                                    ' ' .
                                                    ucwords(strtolower($value->persona->nat_apellido_materno))
                                                : $value->persona->jur_razon_social;
                                        break;
                                    }
                                }
                            } else {
                                $demandado = 'Demanda Colectiva';
                            }
                        @endphp
                        {{ $demandado }}
                    </td>
                    <td>
                        @if ($proceeding->montos)
                            {{ $proceeding->montos->total_amount_sentence ?? '0.00' }}
                        @else
                            0.00
                        @endif
                    </td>
                    <td>
                        @if ($proceeding->montos)
                            {{ $proceeding->montos->total_balance_payable ?? '0.00' }}
                        @else
                            0.00
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        @if ($proceedings->isEmpty() || $index === $proceedings->count() - 1)
            <p>MONTOS TOTALES</p>
            <p>Monto total fijado en sentencia =
                @if ($amounts['total_amount_sentence'] != 0)
                    {{ $amounts['total_amount_sentence'] }}
                @else
                    0.00
                @endif
            </p>
            <p>Saldo total por pagar =
                @if ($amounts['total_balance_payable'] != 0)
                    {{ $amounts['total_balance_payable'] }}
                @else
                    0.00
                @endif
        @endif
    </div>
</body>

</html>
