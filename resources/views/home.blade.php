@extends('layouts.app')

@section('title', 'Home | Laravel WebSocket')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">{{ __('Chat Room') }}</div>

                    <div class="card-body">

                        <div id="chat">
                            @foreach ($chats as $chat)
                                @if ($chat->user_id == auth()->id())
                                    <div class="row flex justify-content-end">
                                        <div class="col-md-12 flex justify-content-end">
                                            <div>{{ $chat->message }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="row flex justify-content-start">
                                        <div class="col-md-12 flex justify-content-start">
                                            <div>{{ $chat->message }}</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>



                        <form method="POST" action="chat_room" class="mt-5">
                            @csrf

                            <div class="form-group">
                                <label>Message</label>
                                <input type="text" class="form-control" id="message" placeholder="Type a message">
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitButton">Submit</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script>
        $(document).ready(function() {
            var ws = new WebSocket('ws://localhost:8080');

            ws.onopen = function(e) {
                ws.send("Connection established!");
            };

            ws.onmessage = function(e){
                var data = JSON.parse(e.data);
            };

            $('form').submit(function(e) {
                e.preventDefault();
                var messageText = $('#message').val();
                var data = {
                    userId: "{{ auth()->id() }}",
                    from: "{{ auth()->user()->name }}",
                    msg: messageText,
                };

                ws.send(JSON.stringify(data));

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: {
                        _token: "{{ csrf_token() }}",
                        values: data,
                    },
                    success: function() {
                        $('#chat').append(`<div class="row flex justify-content-end">
                                                            <div class="col-md-12 flex justify-content-end"><div>${ messageText }</div>
                                                            </div></div>`);
                        $('#message').val('');
                    },
                    error: function(err) {
                        console.log(err);
                    },
                });

            });
        });

    </script>

@endpush
