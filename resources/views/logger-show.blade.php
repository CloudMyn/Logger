<?php

use function CloudMyn\Logger\Helpers\str_limit;

?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.1/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bulma.min.css">

    @empty($c_file)

        <title>Logger - empty</title>

    @else

        <title>Logger - File: {{ $c_file }}</title>

    @endempty

</head>

<body>

    <div style="position: relative;;width: 100%;" class="p-5">

        <ul class="nav nav-pills mb-2 w-100">
            @forelse ($files as $file)
                <li class="nav-item">
                    <a class="nav-link @if ($file === $c_file) active @endif" aria-current="page"
                        href="{{ route('logger.show', $file) }}">{{ $file }}</a>
                </li>
            @empty
                <li class="nav-item">
                    <a class="nav-link bg-secondary text-light disabled" disabled aria-current="page"
                        href="javascript: void(0)">Log file not found!</a>
                </li>
            @endforelse

        </ul>

    @empty($c_file)

    @else

        <div class="d-flex mb-4">
            <a class="btn btn-danger" href="{{ route('logger.delete', $c_file) }}">
                Remove File: {{ $c_file }}
            </a>
        </div>

    @endempty


    <table id="logger" class="table is-striped" style="width:100%">
        <thead>
            <tr>
                <th>class</th>
                <th>mesage</th>
                <th>ip address</th>
                <th>user id</th>
                <th>file</th>
                <th>created at</th>
                <th>action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ str_limit($log['class'], 25, '...') }}</td>
                    <td>{{ str_limit($log['message'], 25, '...') }}</td>
                    <td>{{ $log['user_ip'] }}</td>
                    <td>{{ $log['user_id'] }}</td>
                    <td>{{ str_limit($log['file_name'], 25, '...') . ':' . $log['file_line'] }}</td>
                    <td>{{ date('Y-M-d s:i', (int) $log['create_at']) }}</td>
                    <td>
                        <a href="#" class="badge bg-primary">detail</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>class</th>
                <th>mesage</th>
                <th>ip address</th>
                <th>user id</th>
                <th>file</th>
                <th>created at</th>
                <th>action</th>
            </tr>
        </tfoot>
    </table>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bulma.min.js"></script>

<script>
    $(document).ready(function() {
        $('#logger').DataTable();
    });
</script>

</body>

</html>
