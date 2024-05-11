<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Abogados</title>
    <style>
        /* body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }

        h1 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        } */

        /* td.correo {
            font-size: 10px; 
        } */

        body {
            font-family: Georgia, Times, "Times New Roman", serif;
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
        }

        td {
            border: 0.5px solid #ddd;
            padding: 8px;
            text-align: center;
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

        /* @media print {
            body {
                margin: 0;
                padding: 0;
            }

            h1 {
                page-break-before: always;
            }

            table {
                page-break-inside: auto;
            }

            th,
            td {
                padding: 6px;

            }
        } */
    </style>
</head>

<body>

    <div class="header">
        <p class="header-title">Reporte de Abogados</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Dni</th>
                <th>Nombres y Apellidos</th>
                <th>Telefono</th>
                <th>Email</th>
                <!-- <th>CARGA LABORAL</th> -->
                <!-- <th>DISPONIBILIDAD</th> -->
            </tr>
        </thead>
        <tbody>
        <tbody>
            @foreach ($data as $item)
            @php
            $nombres = ucwords(strtolower($item['persona']['nat_nombres'] ?? '')) . ' ' . ($item['persona']['nat_apellido_paterno'] ? ucwords(strtolower($item['persona']['nat_apellido_paterno'])) : '') . ' ' . ($item['persona']['nat_apellido_materno'] ? ucwords(strtolower($item['persona']['nat_apellido_materno'])) : '');
            $correo = strtolower($item['persona']['nat_correo'] ?? '');
            @endphp

            <tr>
                <td>{{ ucwords(strtolower($item['persona']['nat_dni'] ?? '')) }}</td>
                <td>{{ $nombres }}</td>
                <td>{{ $item['persona']['nat_telefono'] ?? '' }}</td>
                <td class="correo">{{ $correo }}</td>
            </tr>
            @endforeach
        </tbody>

        </tbody>
    </table>

</body>

</html>