<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Demandantes</title>
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
            border: 0.5px solid #ddd;
        }

        th {
            padding: 8px;
            text-align: center;
            font-size: 12px;
            background-color: #e5e5e5;
            color: #000;
        }

        th:first-child {
            border-left: none;
        }

        th:last-child {
            border-right: none;
        }

        td {
            border: 0.5px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="header">
        <p class="header-title">Demandantes</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Documento</th>
                <th>Nombres / Razón Social</th>
                <th>Dirección</th>
                <th>Correo</th>
            </tr>
        </thead>
        <tbody>
            @php
            $personasMostradasNaturales = [];
            $personasMostradasJuridicas = [];
            @endphp

            @foreach ($data as $expediente)
            @php
            $tipoPersona = $expediente['type'];
            $personaData = $expediente['person_data'];
            $direccion = $expediente['person']['address']['dir_calle_av'];
            @endphp

            @if ($tipoPersona == 'natural' && !in_array($personaData['nat_id'], $personasMostradasNaturales))
            @php
            $personasMostradasNaturales[] = $personaData['nat_id'];
            $nombresRazonSocial = ucwords(strtolower($personaData['nat_apellido_paterno'] . ' ' . $personaData['nat_apellido_materno'] . ', ' . $personaData['nat_nombres']));
            $direccion = ucwords(strtolower($direccion));
            $correo = strtolower($personaData['nat_correo']);
            @endphp
            <tr>
                <td>{{ $personaData['nat_dni'] }}</td>
                <td>{{ $nombresRazonSocial }}</td>
                <td>{{ $direccion }}</td>
                <td>{{ $correo }}</td>
            </tr>
            @elseif ($tipoPersona == 'juridica' && !in_array($personaData['jur_id'], $personasMostradasJuridicas))
            @php
            $personasMostradasJuridicas[] = $personaData['jur_id'];
            $nombresRazonSocial = ucwords(strtolower($personaData['jur_razon_social']));
            $direccion = ucwords(strtolower($direccion));
            $correo = strtolower($personaData['jur_correo']);
            @endphp
            <tr>
                <td>{{ $personaData['jur_ruc'] }}</td>
                <td>{{ $nombresRazonSocial }}</td>
                <td>{{ $direccion }}</td>
                <td>{{ $correo }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
