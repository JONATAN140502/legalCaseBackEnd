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
    <img src="{{ asset('images/log.jpg') }}" style="position: absolute; 
        top: 10px; right: 10px; width: 180px; height: auto; z-index: 9999;" />

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
                <th>Monto de Ejecución 1</th>
                <th>Monto de Ejecución 2</th>
                <th>Interés 1</th>
                <th>Interés 2</th>
                <th>Costos</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sumaEjecucion1 = 0;
                $sumaEjecucion2 = 0;
                $sumaInteres1 = 0;
                $sumaInteres2 = 0;
                $sumaCostos = 0;
                $sumaTotal = 0;
            @endphp

            @foreach($formattedData as $expediente)
                <tr>
                <td>{{$loop->iteration}}</td>
                    <td>{{ $expediente['numero'] ?? '' }}</td>
                    <td>{{ $expediente['fecha_inicio'] ?? ''}}</td>
                    <td>
                    @if($expediente['procesal'][0]['tipo_procesal'] === 'DEMANDANTE')
                            @if($expediente['multiple']=== '0')
                                    @if($expediente['procesal'][0]['tipo_persona'] === 'NATURAL')
                                        {{ ucwords(strtolower($expediente['procesal'][0]['nombres'] ?? '')) . ' 
                                            ' . ucwords(strtolower($expediente['procesal'][0]['apellido_paterno'] ?? '')) . ' 
                                            ' . ucwords(strtolower($expediente['procesal'][0]['apellido_materno'] ?? '')) }}
                                    @else
                                        {{ ucwords(strtolower($expediente['procesal'][0]['razon_social'] ?? '')) }}
                                    @endif
                            @else
                            @foreach($expediente['procesal'] as $proc)
                                        
                                        @if($proc['tipo_persona'] === 'NATURAL')
                                            *{{ucwords(strtolower($proc['nombres'] ?? '')) . ' 
                                                ' . ucwords(strtolower($proc['apellido_paterno'] ?? '')) . ' 
                                                ' . ucwords(strtolower($proc['apellido_materno'] ?? '')) }}
                                        @else
                                            *{{$proc['razon_social'] ?? '' }}
                                        @endif
                                    
                                @endforeach 
                        @endif
                        
                        @else
                            UNPRG
                        @endif
                    </td>
                    <td>
                        @if($expediente['procesal'][0]['tipo_procesal'] !== 'DEMANDANTE')
                            @if($expediente['multiple']=== '0')
                                    @if($expediente['procesal'][0]['tipo_persona'] === 'NATURAL')
                                        {{ ucwords(strtolower($expediente['procesal'][0]['nombres'] ?? '')) . ' 
                                            ' . ucwords(strtolower($expediente['procesal'][0]['apellido_paterno'] ?? '')) . ' 
                                            ' . ucwords(strtolower($expediente['procesal'][0]['apellido_materno'] ?? '')) }}
                                    @else
                                        {{ ucwords(strtolower($expediente['procesal'][0]['razon_social'] ?? '')) }}
                                    @endif
                            @else
                            @foreach($expediente['procesal'] as $proc)
                                        
                                        @if($proc['tipo_persona'] === 'NATURAL')
                                            *{{ucwords(strtolower($proc['nombres'] ?? '')) . ' 
                                                ' . ucwords(strtolower($proc['apellido_paterno'] ?? '')) . ' 
                                                ' . ucwords(strtolower($proc['apellido_materno'] ?? '')) }}
                                        @else
                                            *{{$proc['razon_social'] ?? '' }}
                                        @endif
                                    
                                @endforeach 
                        @endif
                        
                        @else
                            UNPRG
                        @endif
                    </td>
                    <td> 
                        {{ !empty($expediente['monto_ejecucion1']) ? $expediente['monto_ejecucion1'] : '0.00' }}
                    </td>
                    <td>
                        {{ !empty($expediente['monto_ejecucion2']) ? $expediente['monto_ejecucion2'] : '0.00' }}
                    </td>
                    <td>
                        {{ !empty($expediente['interes1']) ? $expediente['interes1'] : '0.00' }}
                    </td>
                    <td>
                        {{ !empty($expediente['interes2']) ? $expediente['interes2'] : '0.00' }}
                    </td>
                    <td>
                        {{ !empty($expediente['costos']) ? $expediente['costos'] : '0.00' }}
                    </td>


                    <td>
                        @php
                            $ejecucion1 = floatval($expediente['monto_ejecucion1'] ?? '0.00');
                            $ejecucion2 = floatval($expediente['monto_ejecucion2'] ?? '0.00');
                            $interes1 = floatval($expediente['interes1'] ?? '0.00');
                            $interes2 = floatval($expediente['interes2'] ?? '0.00');
                            $costos = floatval($expediente['costos'] ?? '0.00');
                            $total = $ejecucion1 + $ejecucion2 + $interes1 + $interes2 + $costos;
                            $sumaEjecucion1 += $ejecucion1;
                            $sumaEjecucion2 += $ejecucion2;
                            $sumaInteres1 += $interes1;
                            $sumaInteres2 += $interes2;
                            $sumaCostos += $costos;
                            $sumaTotal += $total;
                        @endphp
                        {{ number_format($total, 2)  }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

</body>

</html>