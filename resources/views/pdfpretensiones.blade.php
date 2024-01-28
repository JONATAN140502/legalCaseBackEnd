<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{$tipo}}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            /* background-color: #007bff; */
            /* padding: 20px 0; */
        }

        .header-title {
            color: #000;
            font-size: 24px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table {
            border: 0.5px solid #ddd;
        }

        th {
            border: 0.5px solid #fff;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        td {
            border: 0.5px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        th {
            /* background-color: #007bff; */
            background-color: #e5e5e5;
            color: #000;
        }

        th:first-child {
            border-left: none;
            /* Elimina el borde izquierdo en la primera columna del encabezado */
        }

        th:last-child {
            border-right: none;
            /* Elimina el borde derecho en la última columna del encabezado */
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

<div class="header">
    <img src="{{ asset('images/log.jpg') }}" style="position: absolute; top: 10px; right: 10px; width: 100px; height: auto; z-index: 9999;" />
    <p class="header-title" style="margin-right: 250px;">{{$tipo}}</p>
</div>
@if(empty($formattedData))
    <p>No hay datos disponibles</p>
@else
    <table>
        <thead>
            <tr>
                <th scope="col" width=3%>N°</th>
                <th>Número</th>
                <th>Fecha de Inicio</th>
                <th>Demandante</th>
                <th>Demandado</th>
                <th>Estado</th>
                <th>Pretensión</th>
                <th>Monto de Pretensión</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sumaPretensionDemandante = $sumaPretensionDemandado = $sumaPretensionTotal = 0;
            @endphp

            @foreach($formattedData as $expediente)
                @php
                    $montoPretencion = floatval($expediente['monto_pretencion']);
                    $tipoProcesal = $expediente['procesal'][0]['tipo_procesal'];
                    $multiple = $expediente['multiple'] === '0';
                    $tipoPersona = $expediente['procesal'][0]['tipo_persona'];
                    $nombres = ucwords(strtolower($expediente['procesal'][0]['nombres'] ?? ''));
                    $apellidoPaterno = ucwords(strtolower($expediente['procesal'][0]['apellido_paterno'] ?? ''));
                    $apellidoMaterno = ucwords(strtolower($expediente['procesal'][0]['apellido_materno'] ?? ''));
                    $razonSocial = ucwords(strtolower($expediente['procesal'][0]['razon_social'] ?? ''));
                @endphp

                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{ $expediente['numero'] ?? '' }}</td>
                    <td>{{ $expediente['fecha_inicio'] ?? ''}}</td>
                    <td>
                        @if($tipoProcesal === 'DEMANDANTE')
                            @if($multiple)
                                Múltiples
                            @else
                                @if($tipoPersona === 'NATURAL')
                                    {{ $nombres . ' ' . $apellidoPaterno . ' ' . $apellidoMaterno }}
                                @else
                                    {{ $razonSocial }}
                                @endif
                            @endif
                        @else
                            UNPRG
                        @endif
                    </td>
                    <td>
                        @if($tipoProcesal !== 'DEMANDANTE')
                            @if($multiple)
                                Múltiples
                            @else
                                @if($tipoPersona === 'NATURAL')
                                    {{ $nombres . ' ' . $apellidoPaterno . ' ' . $apellidoMaterno }}
                                @else
                                    {{ $razonSocial }}
                                @endif
                            @endif
                        @else
                            UNPRG
                        @endif
                    </td>
                    <td>{{$expediente['estado_proceso'] ?? ''}}</td>
                    <td>{{ $expediente['pretencion'] ?? '' }}</td>
                    <td>{{ $expediente['monto_pretencion'] ?? '' }}</td>
                </tr>

                @php
                    if ($tipoProcesal === 'DEMANDANTE') {
                        $sumaPretensionDemandante += $montoPretencion;
                    } else {
                        $sumaPretensionDemandado += $montoPretencion;
                    }
                    $sumaPretensionTotal += $montoPretencion;
                @endphp
            @endforeach
        </tbody>
    </table>
    <p>Suma de Pretensiones de Demandantes: {{ number_format($sumaPretensionDemandante, 2) }}</p>
    <p>Suma de Pretensiones de Demandados: {{ number_format($sumaPretensionDemandado, 2) }}</p>
    <p>Pretensión Total: {{ number_format($sumaPretensionTotal, 2) }}</p>
@endif
</body>
</html>