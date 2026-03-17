<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Tareas por Asesor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            position: relative; /* Necesario para el footer fijo */
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            height: 80px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 24px;
            color: rgb(3, 16, 159);
            margin: 0;
        }
        .header p {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: rgb(3, 16, 159);
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .footer {
            position: fixed; /* Fija el footer en la parte inferior */
            bottom: 0; /* Lo coloca en la parte inferior */
            left: 0; /* Alineado a la izquierda */
            width: 100%; /* Ocupa todo el ancho */
            text-align: center;
            font-size: 12px;
            color: #777;
            background-color: white; /* Fondo blanco para que el texto sea legible */
            padding: 10px 0; /* Espaciado interno */
            border-top: 1px solid #ddd; /* Línea superior para separar el contenido */
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .asesor-title {
            font-size: 18px;
            color: rgb(3, 16, 159);
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        /* Ajustes para evitar que el contenido se solape con el footer */
        .content {
            margin-bottom: 60px; /* Espacio adicional para el footer */
        }
    </style>
</head>
<body>
    <!-- Encabezado con logo -->
    <div class="header">
        <img src="{{ asset('imagenes/logo.png') }}" alt="Logo ACT Consultores">
        <h1>Reporte de Tareas por Asesor</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Contenido del reporte -->
    <div class="content">
        @foreach ($tareasAgrupadas as $asesor => $tareas)
            <div class="asesor-title">{{ $asesor }}</div>
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Descripción</th>
                        <th>Fecha Facturada</th>
                        <th>Fecha Cumplida</th>
                        <th>Estado</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0; // Inicializar la variable para sumar los totales
                    @endphp
                    @foreach ($tareas as $tarea)
                        <tr>
                            <td>{{ $tarea->cliente->nombre_cliente ?? 'Sin cliente' }}</td>
                            <td>{{ $tarea->nombre }}</td>
                            <td>@formatoFecha($tarea->fecha_facturada)</td>
                            <td>                
                                @if($tarea->fecha_cumplida)
                                    @formatoFecha($tarea->fecha_cumplida)
                                @endif
                            </td>
                            <td>{{ $tarea->estado }}</td>
                            <td>${{ number_format($tarea->total, 2) }}</td>
                        </tr>
                        @php
                            $total += $tarea->total; // Sumar el valor de cada tarea al total
                        @endphp
                    @endforeach
                    <!-- Fila del total por asesor -->
                    <tr class="total-row">
                        <td colspan="5" style="text-align: right;"><strong>Total {{ $asesor }}:</strong></td>
                        <td><strong>${{ number_format($total, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <p>ACT Consultores - Todos los derechos reservados</p>
    </div>
</body>
</html>