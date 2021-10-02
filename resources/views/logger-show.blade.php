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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>

    @empty($c_file)

        <title>Logger - empty</title>

    @else

        <title>Logger - File: {{ $c_file }}</title>

    @endempty

    <style>
        .modal-body .mt-2 label {
            font-size: 18px;
            text-transform: uppercase;
            color: #424242;
        }

    </style>

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
            <a class="btn btn-danger" href="{{ route('logger.delete', $c_file) }}" onclick="return confirm('Yakin!!')">
                Remove File: {{ $c_file }}
            </a>
        </div>

    @endempty


    <table id="logger" class="table is-striped" style="width:100%">
        <thead>
            <tr>
                <th>id</th>
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
                    <?php

                        try {

                            $log_id     =   array_key_exists('id',$log) ? $log['id'] : 'empty';
                            $class      =   array_key_exists('class',$log) ? $log['class'] : 'empty';
                            $file_name  =   array_key_exists('file_name',$log) ? $log['file_name'] : 'empty';
                            $file_line  =   array_key_exists('file_line',$log) ? $log['file_line'] : 'empty';
                            $user_ip    =   array_key_exists('user_ip',$log) ? $log['user_ip'] : 'empty';
                            $user_id    =   array_key_exists('user_id',$log) ? $log['user_id'] : 'empty';
                            $created_at =   array_key_exists('created_at',$log) ? $log['created_at'] : 'empty';
                            $code       =   array_key_exists('code',$log) ? $log['code'] : 'empty';
                            $message    =   array_key_exists('message',$log) ? $log['message'] : 'empty';

                            // ...
                        } catch (\Throwable $th) {

                        }
                    ?>

                    <td>{{ str_limit($log_id, 5, '...') }}</td>
                    <td>{{ str_limit($class, 25, '...') }}</td>
                    <td>{{ str_limit($message, 25, '...') }}</td>
                    <td>{{ $user_ip }}</td>
                    <td>{{ $user_id }}</td>
                    <td>{{ str_limit($file_name, 25, '...') . ':' . $file_line }}</td>
                    <td>{{ date('Y-M-d s:i', (int) $created_at) }}</td>
                    <td>

                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#m{{ $loop->count }}"
                            onclick='fetchTrace( "{{ $c_file }}", "{{ $log_id }}", "modal-stacktrace-{{ $loop->count }}" )'>
                            detail
                        </button>

                        {{-- Modal --}}
                        <div class="modal fade" id="m{{ $loop->count }}" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabel">
                                            {{ $class . ':' . $code }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="input-group">
                                            <span class="input-group-text " id="id">id</span>
                                            <input type="text" class="form-control disabled" disabled
                                                placeholder="id" aria-describedby="id" value="{{ $log['id'] }}">
                                        </div>
                                        <div class="mt-2">
                                            <label for="_message" class="form-label"><b>Message</b></label>
                                            <input type="text" id="_message" class="form-control" disabled
                                                value="{{ $message }}">
                                        </div>
                                        <div class="mt-2">
                                            <label for="_file" class="form-label"><b>File</b></label>
                                            <input type="text" id="_file" class="form-control" disabled
                                                value="{{ $file_name . ':' . $file_line }}">
                                        </div>
                                        <div class="mt-2">
                                            <label for="_user_id" class="form-label"><b>User Id</b></label>
                                            <input type="text" id="_user_id" class="form-control" disabled
                                                value="{{ $user_id }}">
                                        </div>
                                        <div class="mt-2">
                                            <label for="_user_ip" class="form-label"><b>IP address</b></label>
                                            <input type="text" id="_user_ip" class="form-control" disabled
                                                value="{{ $user_ip }}">
                                        </div>
                                        <div class=" mt-2" id="modal-stacktrace-{{ $loop->count }}">
                                            Loading...
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End Modal --}}

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

<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#logger').DataTable();
    });

    function fetchTrace(filename, id, elId) {
        $.ajax({
            url: `\\logger\\ajax\\trace\\${filename}\\${id}`,
            type: 'GET',
            success: function(data) {
                document.getElementById(elId).innerHTML = data;
            },
            error: function(error) {
                console.error(error);
            }
        });
    }
</script>

</body>

</html>
