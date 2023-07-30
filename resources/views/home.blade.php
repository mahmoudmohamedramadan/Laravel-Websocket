@extends('layouts.app')

@section('title', 'Laravel WebSocket | Ratchet')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card" style="height: 500px">
                    <div class="card-header">{{ __('Chat Room') }}</div>
                    <div class="card-body overflow-auto" id="messages_area" style="height: 300px">
                        @foreach ($chats as $chat)
                            @if ($chat->user_id == auth()->id())
                                @php
                                    $from = 'Me';
                                    $row_class = 'row justify-content-start';
                                    $background_class = 'text-dark alert-light';
                                @endphp
                            @else
                                @php
                                    $from = \App\Models\User::find($chat->user_id)->name;
                                    $row_class = 'row justify-content-end';
                                    $background_class = 'alert-success';
                                @endphp
                            @endif
                            <div class="{{ $row_class }}">
                                <div class="col-sm-10">
                                    <div class="shadow-sm alert {{ $background_class }}">
                                        <b>{{ $from }} - </b>{{ $chat->message }}
                                        <div class="text-right">
                                            <small><i>{{ $chat->created_at }}</i></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <form method="POST" action="chat_room" class="mt-3">
                    @csrf

                    <div class="input-group">
                        <input type="text" class="form-control" id="chat_message" placeholder="Type a message"
                            data-parsley-maxlength="50" data-parsley-pattern="/^[a-zA-Z0-9\s]+$/" autocomplete="off" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </div>
                </form>
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

            ws.onmessage = function(e) {
                var data = JSON.parse(e.data);

                var row_class = '', background_class = '';

                if (data.user == 'Me') {
                    row_class = 'row justify-content-start';
                    background_class = 'text-dark alert-light';
                } else {
                    row_class = 'row justify-content-end';
                    background_class = 'alert-success';
                }

                var html_data =
                    `<div class="${row_class}"><div class="col-sm-10"><div class="shadow-sm alert ${background_class}"><b>${data.user} - </b>${data.msg}<div class="text-right"><small><i>${data.dt}</i></small></div></div></div></div>`;

                $('#messages_area').append(html_data);
                $('#messages_area').scrollTop($('#messages_area')[0].scrollHeight);
            };

            $('form').submit(function(e) {
                e.preventDefault();

                var messageText = $('#chat_message').val();
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
                        $("#chat_message").val("");
                        // do somthing here...
                    },
                    error: function(err) {
                        console.log(err);
                    },
                });

            });
        });

    </script>

@endpush
