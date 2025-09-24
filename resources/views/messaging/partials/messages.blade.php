@foreach($messages as $message)
    @include('messaging.partials.message', ['message' => $message])
@endforeach

@if($messages->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="fas fa-comment-alt fa-3x mb-3"></i>
        <p>No messages yet. Start the conversation!</p>
    </div>
@endif