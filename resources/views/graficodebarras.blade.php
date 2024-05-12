
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Demandas Por año</title>
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
    <img src="{{ asset('images/log.jpg') }}" style="position: absolute; top: 10px; right: 10px; width: 180px; height: auto; z-index: 9999;" />
    <p class="header-title" style="margin-right: 200px;">Demandas Realizadas Por Año Civil/Laboral</p>
</div>
<div>
<!-- <img src="https://quickchart.io/chart?v=2.9.4&c={type:'bar',data:{labels:[{!! $data->pluck('year')->implode(',')!!}],datasets:[{label:'Demandas',backgroundColor:'rgba(54, 162, 235, 0.5)',borderColor:'rgb(54, 162, 235)',borderWidth:1,data:[{!! $data->pluck('cantidad')->implode(',') !!}]}]}}" style="width: 90%;"> -->
<!-- <img src="https://quickchart.io/chart?sandbox={
  type:'bar',
  data:{
    labels:[{!! $data->pluck('year')->implode(',') !!}],
    datasets:[{
      label:'Demandas',
      backgroundColor:'rgba(54, 162, 235, 0.5)',
      borderColor:'rgb(54, 162, 235)',
      borderWidth:1,
      data:[{!! $data->pluck('cantidad')->implode(',') !!}]
    }]
  },
}" style="width: 90%;"> -->
<img src="https://quickchart.io/chart?c={
  type:'bar',
  data:{
    labels:[{!! $data->pluck('year')->implode(',') !!}],
    datasets:[{
      label:'Demandas',
      backgroundColor:'rgba(54, 162, 235, 0.5)',
      borderColor:'rgb(54, 162, 235)', 
      borderWidth:1,
      data:[{!! $data->pluck('cantidad')->implode(',') !!}]
    }]
  },
  options:{
    plugins:{
      datalabels:{
        display:true,
        color:'grey',
        font:{
          weight:'normal'
        },
        anchors:'center',
        align:'center'
      }
    }
  }
}" style="width: 90%;">

</div>
</body>
</html>
