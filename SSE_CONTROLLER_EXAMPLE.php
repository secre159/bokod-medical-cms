<?php
/**
 * Example Laravel Controller for Server-Sent Events Real-time Messaging
 * 
 * Add this to your existing MessagingController or create a new one
 * This enables real-time messaging like Messenger, WhatsApp, etc.
 */

// Add these methods to your existing MessagingController:

/**
 * Stream real-time messages using Server-Sent Events
 * 
 * Route: GET /patient/messages/stream/{conversation} (for patients)
 * Route: GET /admin/messages/stream/{conversation} (for admins)
 */
public function streamMessages(Request $request, $conversationId)
{
    // Set headers for Server-Sent Events
    return response()->stream(function () use ($request, $conversationId) {
        // SSE headers
        echo "data: " . json_encode([
            'type' => 'connection',
            'message' => 'Connected to real-time messaging'
        ]) . "\n\n";
        ob_flush();
        flush();
        
        $lastMessageId = $request->get('last_id', 0);
        $startTime = time();
        $maxRunTime = 300; // 5 minutes max connection time
        
        while (time() - $startTime < $maxRunTime) {
            // Check for new messages since last ID
            $conversation = Conversation::findOrFail($conversationId);
            
            // Security: Verify user can access this conversation
            if (Auth::user()->role === 'patient') {
                if ($conversation->patient_id !== Auth::id()) {
                    break;
                }
            } else {
                if ($conversation->admin_id !== Auth::id()) {
                    break;
                }
            }
            
            // Get new messages
            $newMessages = $conversation->messages()
                ->where('id', '>', $lastMessageId)
                ->with(['sender'])
                ->orderBy('created_at', 'asc')
                ->get();
            
            if ($newMessages->count() > 0) {
                foreach ($newMessages as $message) {
                    // Render message HTML
                    $messageHtml = view('messaging.partials.message', [
                        'message' => $message,
                        'isCurrentUser' => $message->sender_id === Auth::id()
                    ])->render();
                    
                    // Send new message via SSE
                    echo "data: " . json_encode([
                        'type' => 'new_message',
                        'message_id' => $message->id,
                        'html' => $messageHtml,
                        'preview' => Str::limit($message->message ?? 'New attachment', 50),
                        'sender' => $message->sender->name ?? 'Unknown',
                        'timestamp' => $message->created_at->toISOString()
                    ]) . "\n\n";
                    
                    $lastMessageId = $message->id;
                    ob_flush();
                    flush();
                }
            }
            
            // Send heartbeat every 30 seconds
            if (time() % 30 === 0) {
                echo "data: " . json_encode([
                    'type' => 'heartbeat',
                    'timestamp' => now()->toISOString()
                ]) . "\n\n";
                ob_flush();
                flush();
            }
            
            // Check every 2 seconds
            sleep(2);
            
            // Break if client disconnected
            if (connection_aborted()) {
                break;
            }
        }
        
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
        'X-Accel-Buffering' => 'no', // Disable Nginx buffering
    ]);
}

/**
 * Alternative: Check for new messages (AJAX fallback)
 * 
 * Route: GET /patient/messages/check/{conversation} (for patients)
 * Route: GET /admin/messages/check/{conversation} (for admins)
 */
public function checkNewMessages(Request $request, $conversationId)
{
    $conversation = Conversation::findOrFail($conversationId);
    
    // Security check
    if (Auth::user()->role === 'patient') {
        if ($conversation->patient_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    } else {
        if ($conversation->admin_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }
    
    $lastMessageId = $request->get('last_message_id', 0);
    
    // Get new messages since last ID
    $newMessages = $conversation->messages()
        ->where('id', '>', $lastMessageId)
        ->with(['sender'])
        ->orderBy('created_at', 'asc')
        ->get();
    
    $newMessagesHtml = [];
    foreach ($newMessages as $message) {
        $newMessagesHtml[] = view('messaging.partials.message', [
            'message' => $message,
            'isCurrentUser' => $message->sender_id === Auth::id()
        ])->render();
    }
    
    return response()->json([
        'has_new_messages' => $newMessages->count() > 0,
        'new_messages' => $newMessagesHtml,
        'last_message_id' => $newMessages->last()->id ?? $lastMessageId,
        'count' => $newMessages->count()
    ]);
}

// Add these routes to your web.php:

/*
|--------------------------------------------------------------------------
| Real-time Messaging Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Patient routes
    Route::prefix('patient/messages')->name('patient.messages.')->group(function () {
        Route::get('stream/{conversation}', [MessagingController::class, 'streamMessages'])
            ->name('stream');
        Route::get('check/{conversation}', [MessagingController::class, 'checkNewMessages'])
            ->name('check');
    });
    
    // Admin routes
    Route::prefix('admin/messages')->name('admin.messages.')->group(function () {
        Route::get('stream/{conversation}', [MessagingController::class, 'streamMessages'])
            ->name('stream');
        Route::get('check/{conversation}', [MessagingController::class, 'checkNewMessages'])
            ->name('check');
    });
});