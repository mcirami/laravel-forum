<div class="card">
    <div class="card-header">
        <div class="level d-flex">
            <div class="align-self-start">

                {{ $heading }}

            </div>
            <div class="align-self-end ml-auto">
                {{-- {{ $thread->created_at->diffForHumans() }}--}}
            </div>
        </div>
    </div>

    <div class="panel-body p-4">

        {!! $body !!}

    </div>
</div>
