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

        table{
            border: 0.5px solid #ddd;
        }

        th, td {
            border: 0.5px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        th {
            background-color: #e5e5e5;
            color: #000;
        }

        th:first-child {
            border-left: none;
        }

        th:last-child {
            border-right: none;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

<div class="header">
    <img src="{{ asset('images/log.jpg') }}" style="position: absolute; top: 10px; right: 10px; width: 180px; height: auto; z-index: 9999;" />
    <p class="header-title" style="margin-right: 200px;">{{$tipo}}</p>
</div>

<table>
    <thead>
        <tr>
            <th scope="col" width=3%>N°</th>
            <th>Número de Expediente</th>
            <th>Fecha de Inicio</th>
            <th>Demandante</th>
            <th>Demandado</th>
            <th>Delito</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @php
            $indice = 1;
        @endphp

        @for ($i = 0; $i < 5; $i++)
            @php
                switch ($i) {
                    case 0:
                        $currentData = $data1;
                        break;
                    case 1:
                        $currentData = $data2;
                        break;
                    case 2:
                        $currentData = $data3;
                        break;
                    case 3:
                        $currentData = $data4;
                        break;
                    case 4:
                        $currentData = $data5;
                        break;
                }
            @endphp

            @if(empty($data1))
                <tr>
                    <td colspan="7">No hay datos disponibles</td>
                </tr>
                @php
                $data1="vacio";
                @endphp
            @else
                @foreach($currentData as $expediente)
                    <tr>
                        <td>{{$indice++}}</td>
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
                                        {{ $expediente['procesal'][0]['razon_social'] ?? ''}}
                                    @endif
                                @else
                                    @foreach($expediente['procesal'] as $proc)
                                        
                                            @if($proc['tipo_persona'] === 'NATURAL')
                                                *{{ ucwords(strtolower($proc['nombres'] ?? '')) . ' 
                                                    ' . ucwords(strtolower($proc['apellido_paterno'] ?? '')) . ' 
                                                    ' . ucwords(strtolower($proc['apellido_materno'] ?? '')) }}
                                            @else
                                               *{{ $proc['razon_social'] ?? ''}}
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
                                        {{ $expediente['procesal'][0]['razon_social'] ?? '' }}
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
                        <td>{{ ucwords(strtolower($expediente['pretencion'] ?? ''))}}</td>
                        <td>
                            {{ ucwords(strtolower($expediente['estado_proceso']  ?? ''))}}
                        </td>
                    </tr>
                @endforeach
            @endif
        @endfor
    </tbody>
</table>

</body>

</html>
