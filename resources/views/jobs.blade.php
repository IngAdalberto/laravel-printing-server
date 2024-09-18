<!DOCTYPE html>
<html lang="en">
<head>
  <title>Lista de trabajos de impresión</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  <h2>
    Lista de trabajos de impresión
    @if( app('request')->input('status') != null )
      <small>Estado: {{ app('request')->input('status') }}</small>
    @endif
  </h2>
  <p>
    Filtrar: 
    <a class="btn btn-warning" href="{{ url('/') . '/jobs?status=Pendiente' }}">Pendientes</a>
    <a class="btn btn-default" href="{{ url('/') . '/jobs?status=Cancelado' }}">Cancelados</a>
    <a class="btn btn-success" href="{{ url('/') . '/jobs?status=Completado' }}">Completados</a>
  </p>
  <table class="table">
    <thead>
      <tr>
        <th>Fecha creación</th>
        <th>IP Impresora</th>
        <th>Estado</th>
        <th>Detalles</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($jobs as $job)
            <?php 
                $tr_class = 'warning';
                if ($job->status == 'Completado') {
                    $tr_class = 'success';
                }
                if ($job->status == 'Eliminado') {
                    $tr_class = 'default';
                }
            ?>
            
            <tr class="{{$tr_class}}">
                <td>{{ $job->created_at }}</td>
                <td>{{ $job->printer_ip }}</td>
                <td>{{ $job->status }}</td>
                <td>{{ $job->log }}</td>
                <td>
                    @if($job->status != 'Eliminado')
                      <a class="btn btn-sn btn-primary" href="{{ url('/') . '/reprint_job/' . $job->id }}" title="Reimprimir"> <i class="glyphicon glyphicon-print"></i> </a>

                      @if($job->status != 'Completado')
                        <a class="btn btn-sn btn-danger" href="{{ url('/') . '/cancel_job/' . $job->id }}" title="Cancelar"> <i class="glyphicon glyphicon-trash"></i> </a>
                      @endif

                    @endif
                </td>
            </tr>
            
        @endforeach
    </tbody>
  </table>
</div>

</body>
</html>

