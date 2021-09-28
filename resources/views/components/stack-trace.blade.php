<label for="stack_trace" class="form-label"><b>Stack
        Trace</b></label>
<div>

    @forelse ($traces as $trace)
        <div class="alert alert-warning">

            @if (property_exists($trace, 'class') && property_exists($trace, 'function'))
                <h5>{{ $trace->class . ':' . $trace->function }}</h5>
            @elseif(property_exists($trace, 'function'))
                <h5>{{ $trace->function }}</h5>
            @endif

            <p class="m-0">
                @if (property_exists($trace, 'file') && property_exists($trace, 'line'))
                    {{ $trace->file . ':' . $trace->line }}
                @else
                    '-'
                @endif
            </p>

        </div>
    @empty
        <div class="alert alert-info">No stack traces available!</div>
    @endforelse

</div>
