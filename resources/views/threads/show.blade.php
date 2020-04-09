@extends('layouts.app')

@section('header')
    <link rel="stylesheet" href="{{ asset('css/vendor/jquery.atwho.css') }}">
@endsection

@section('content')

    <thread-view :initial-replies-count="{{ $thread->replies_count }}" inline-template>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex">
                            <div class="align-self-start">
                                <img class="mr-2" src="{{ $thread->creator->avatar_path }}" alt="" width="50" height="auto">
                                <a href="{{ route('profiles', $thread->creator) }}">{{ $thread->creator->name }}</a> posted:
                                {{$thread->title}}
                            </div>
                            @can ('update', $thread)
                                <div class="align-self-end ml-auto">
                                    <form action="{{ $thread->path() }}" method="POST">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button class="btn btn-link" type="submit">Delete Thread</button>
                                    </form>
                                </div>
                            @endcan
                        </div>

                        <div class="panel-body p-4">
                            {{$thread->body}}
                        </div>
                    </div>
'
                    <replies @removed="repliesCount--" @added="repliesCount++"></replies>

                </div><!-- col-md-8 -->
                <div class="col-md-4">
                    <div class="card p-2">
                        <p>
                            This thread was published {{ $thread->created_at->diffForHumans() }} by
                            <a href="#">{{ $thread->creator->name }}</a>, and currently has <span v-text="repliesCount"></span> {{ Str::plural('comment', $thread->replies_count) }}
                        </p>

                        <p>
                            <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}"></subscribe-button>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </thread-view>
@endsection
