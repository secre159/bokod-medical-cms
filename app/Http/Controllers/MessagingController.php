<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\FileUploadService;

class MessagingController extends Controller
{
    /**
     * Display the messaging interface
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedConversationId = $request->get('conversation');
        
        // Get all non-archived conversations for the current user
        $conversations = Conversation::forUser($user->id)
            ->active()
            ->notArchivedFor($user->id)
            ->with(['patient', 'admin', 'latestMessage', 'messages' => function($q) use ($user) {
                $q->notSentBy($user->id)->unread();
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();
        
        $selectedConversation = null;
        $messages = collect();
        
        // If a specific conversation is selected, load its messages
        if ($selectedConversationId) {
            $selectedConversation = Conversation::with(['patient', 'admin'])
                ->forUser($user->id)
                ->find($selectedConversationId);
                
            if ($selectedConversation) {
                $messages = $selectedConversation->messages()->with('sender')->get();
                
                // Mark messages as read
                $selectedConversation->markAsReadFor($user->id);
            }
        } 
        // If no conversation selected but conversations exist, auto-select the first one
        elseif ($conversations->count() > 0) {
            $selectedConversation = $conversations->first();
            $selectedConversationId = $selectedConversation->id;
            $messages = $selectedConversation->messages()->with('sender')->get();
            
            // Mark messages as read
            $selectedConversation->markAsReadFor($user->id);
        }
        
        return view('messaging.index', compact(
            'conversations',
            'selectedConversation',
            'messages',
            'selectedConversationId'
        ));
    }
    
    /**
     * Send a new message
     */
    public function send(Request $request)
    {
        // Validate basic message requirements
        $rules = [
            'conversation_id' => 'required|exists:conversations,id',
            'priority' => 'nullable|in:low,normal,urgent',
        ];
        
        // Either message or file is required
        if (!$request->hasFile('attachment') && (empty($request->message) || empty(trim($request->message)))) {
            \Log::info('Message send validation failed', [
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role,
                'has_file' => $request->hasFile('attachment'),
                'message_empty' => empty($request->message),
                'message_trimmed_empty' => empty(trim($request->message ?? '')),
                'conversation_id' => $request->conversation_id,
                'request_data' => $request->only(['message', 'priority', 'conversation_id'])
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Either message text or file attachment is required.'
            ], 422);
        }
        
        if ($request->filled('message')) {
            $rules['message'] = 'string|max:1000';
        }
        
        if ($request->hasFile('attachment')) {
            $rules['attachment'] = 'file|max:10240'; // 10MB max
        }
        
        $request->validate($rules);
        
        $user = Auth::user();
        
        // Verify user has access to this conversation
        $conversation = Conversation::forUser($user->id)
            ->findOrFail($request->conversation_id);
        
        try {
            DB::beginTransaction();
            
            // Handle file upload if present
            $fileData = [];
            if ($request->hasFile('attachment')) {
                $fileUploadService = new FileUploadService();
                $fileData = $fileUploadService->uploadFile($request->file('attachment'), $conversation->id);
                $fileData['has_attachment'] = true;
            }
            
            // Determine message type
            $messageType = Message::TYPE_TEXT;
            if (!empty($fileData)) {
                $messageType = $fileData['file_type'] === Message::FILE_TYPE_IMAGE ? Message::TYPE_IMAGE : Message::TYPE_FILE;
            }
            
            // Create the message
            $message = Message::create(array_merge([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'message' => $request->message ?? '',
                'message_type' => $messageType,
                'priority' => $request->priority ?? Message::PRIORITY_NORMAL,
            ], $fileData));
            
            // Update conversation last message time
            $conversation->update([
                'last_message_at' => now()
            ]);
            
            DB::commit();
            
            // Load the message with sender for response
            $message->load('sender');
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'html' => view('messaging.partials.message', compact('message'))->render()
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            // If file was uploaded, try to clean it up
            if (!empty($fileData['file_path'])) {
                $fileUploadService = new FileUploadService();
                $fileUploadService->deleteFile($fileData['file_path']);
            }
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to send message: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Start a new conversation (for patients)
     */
    public function startConversation(Request $request)
    {
        $user = Auth::user();
        
        // Only patients can start new conversations
        if ($user->role !== 'patient') {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized. Only patients can start conversations.'
            ], 403);
        }
        
        try {
            // First, check if admin users exist
            $adminCount = User::where('role', 'admin')->where('status', 'active')->count();
            if ($adminCount === 0) {
                \Log::warning('No active admin users found for conversation creation', [
                    'patient_id' => $user->id,
                    'patient_name' => $user->name
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'No medical staff available at the moment. Please try again later or contact support.'
                ], 503);
            }
            
            // First, ensure the user has a patient record
            $patient = \App\Models\Patient::where('user_id', $user->id)->first();
            
            if (!$patient) {
                \Log::warning('Patient record not found for user', [
                    'user_id' => $user->id,
                    'user_name' => $user->name
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Patient record not found. Please contact support to complete your profile setup.'
                ], 404);
            }
            
            // Check if patient already has an active conversation
            $existingConversation = Conversation::where('patient_id', $patient->id)
                ->where('is_active', true)
                ->first();
                
            if ($existingConversation) {
                \Log::info('Patient already has active conversation', [
                    'patient_id' => $user->id,
                    'conversation_id' => $existingConversation->id
                ]);
                
                return response()->json([
                    'success' => true,
                    'conversation_id' => $existingConversation->id,
                    'redirect_url' => route('patient.messages.index', ['conversation' => $existingConversation->id]),
                    'message' => 'Redirecting to your existing conversation.'
                ]);
            }
            
            // Find or create conversation with any admin
            $conversation = Conversation::findOrCreateBetween($user->id);
            
            if (!$conversation) {
                throw new \Exception('Failed to create conversation - database error');
            }
            
            \Log::info('Conversation created/found', [
                'conversation_id' => $conversation->id,
                'patient_id' => $user->id,
                'admin_id' => $conversation->admin_id,
                'was_recently_created' => $conversation->wasRecentlyCreated
            ]);
            
            // Create welcome system message if this is a new conversation
            if ($conversation->wasRecentlyCreated) {
                $systemMessage = Message::createSystemMessage(
                    $conversation->id,
                    'Conversation started. A medical staff member will respond to you shortly.'
                );
                
                if (!$systemMessage) {
                    \Log::warning('Failed to create system message, but conversation exists', [
                        'conversation_id' => $conversation->id
                    ]);
                }
                
                $conversation->update(['last_message_at' => now()]);
            }
            
            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'redirect_url' => route('patient.messages.index', ['conversation' => $conversation->id]),
                'message' => 'Conversation started successfully!'
            ]);
            
        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Log::error('Failed to start conversation: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'admin_count' => User::where('role', 'admin')->where('status', 'active')->count(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return user-friendly error message
            $errorMessage = 'Failed to start conversation. Please try again.';
            
            if (str_contains($e->getMessage(), 'No admin users available')) {
                $errorMessage = 'No medical staff available at the moment. Please try again later.';
            } elseif (str_contains($e->getMessage(), 'database')) {
                $errorMessage = 'Database error. Please contact support if the problem persists.';
            }
            
            return response()->json([
                'success' => false,
                'error' => $errorMessage,
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get messages for a conversation (AJAX)
     */
    public function getMessages(Request $request, $conversationId)
    {
        $user = Auth::user();
        
        $conversation = Conversation::forUser($user->id)
            ->with(['patient', 'admin'])
            ->findOrFail($conversationId);
        
        // Load messages with reactions if requested
        $messagesQuery = $conversation->messages()->with(['sender']);
        
        // Always load with reactions for enhanced real-time updates
        $messagesQuery->with('reactions');
        
        $messages = $messagesQuery->orderBy('created_at', 'asc')->get();
        
        // Add formatted reactions to each message
        $messages->each(function ($message) {
            $message->formatted_reactions = $message->getFormattedReactions();
        });
        
        // Mark messages as read
        $conversation->markAsReadFor($user->id);
        
        return response()->json([
            'messages' => $messages,
            'conversation' => $conversation,
            'html' => view('messaging.partials.messages', compact('messages'))->render()
        ]);
    }
    
    /**
     * Get unread message count
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $unreadCount = Message::whereHas('conversation', function($q) use ($user) {
            $q->forUser($user->id);
        })
        ->notSentBy($user->id)
        ->unread()
        ->count();
        
        return response()->json(['unread_count' => $unreadCount]);
    }
    
    /**
     * Mark conversation as read
     */
    public function markAsRead(Request $request, $conversationId)
    {
        $user = Auth::user();
        
        $conversation = Conversation::forUser($user->id)
            ->findOrFail($conversationId);
        
        $conversation->markAsReadFor($user->id);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Download message attachment
     */
    public function downloadAttachment(Request $request, $messageId)
    {
        $user = Auth::user();
        
        // Find the message and verify user has access
        $message = Message::whereHas('conversation', function($q) use ($user) {
            $q->forUser($user->id);
        })->findOrFail($messageId);
        
        if (!$message->hasFileAttachment()) {
            abort(404, 'File not found');
        }
        
        $filePath = storage_path('app/public/' . $message->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
        }
        
        return response()->download($filePath, $message->file_name, [
            'Content-Type' => $message->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $message->file_name . '"'
        ]);
    }
    
    /**
     * Get file upload info (for frontend validation)
     */
    public function getUploadInfo()
    {
        return response()->json([
            'max_file_size' => Message::MAX_FILE_SIZE,
            'max_file_size_formatted' => FileUploadService::getMaxFileSizeFormatted(),
            'allowed_extensions' => Message::ALLOWED_EXTENSIONS,
            'allowed_extensions_string' => FileUploadService::getAllowedExtensionsString(),
        ]);
    }
    
    /**
     * Archive a conversation
     */
    public function archiveConversation(Request $request, $conversationId)
    {
        $user = Auth::user();
        
        $conversation = Conversation::forUser($user->id)
            ->findOrFail($conversationId);
        
        try {
            $conversation->archiveFor($user->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Conversation archived successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to archive conversation'
            ], 500);
        }
    }
    
    /**
     * Unarchive a conversation
     */
    public function unarchiveConversation(Request $request, $conversationId)
    {
        $user = Auth::user();
        
        $conversation = Conversation::forUser($user->id)
            ->findOrFail($conversationId);
        
        try {
            $conversation->unarchiveFor($user->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Conversation unarchived successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to unarchive conversation'
            ], 500);
        }
    }
    
    /**
     * Get archived conversations
     */
    public function getArchivedConversations(Request $request)
    {
        $user = Auth::user();
        
        $archivedConversations = Conversation::forUser($user->id)
            ->active()
            ->archivedFor($user->id)
            ->with(['patient', 'admin', 'latestMessage'])
            ->orderBy('archived_at', 'desc')
            ->get();
            
        return response()->json([
            'conversations' => $archivedConversations
        ]);
    }
    
    /**
     * Start a new conversation with a patient (for admins)
     */
    public function startConversationWithPatient(Request $request)
    {
        $user = Auth::user();
        
        // Only admins can start conversations with patients
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'initial_message' => 'nullable|string|max:1000'
        ]);
        
        // Verify the patient_id belongs to a patient user, then find patient record
        $patientUser = User::where('id', $request->patient_id)
                          ->where('role', 'patient')
                          ->first();
        
        if (!$patientUser) {
            return response()->json([
                'success' => false,
                'error' => 'Patient user not found or invalid patient ID'
            ], 404);
        }
        
        // Find the corresponding patient record
        $patient = \App\Models\Patient::where('user_id', $patientUser->id)->first();
        
        if (!$patient) {
            return response()->json([
                'success' => false,
                'error' => 'Patient record not found. Patient profile may be incomplete.'
            ], 404);
        }
        
        try {
            // Find or create conversation between admin and patient
            $conversation = Conversation::findOrCreateBetween($patientUser->id, $user->id);
            
            // Send initial message if provided
            if ($request->filled('initial_message') && !empty(trim($request->initial_message))) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $user->id,
                    'message' => trim($request->initial_message),
                    'message_type' => Message::TYPE_TEXT,
                    'priority' => Message::PRIORITY_NORMAL,
                ]);
                
                $conversation->update(['last_message_at' => now()]);
            } else {
                // Create welcome system message if this is a new conversation without an initial message
                if ($conversation->wasRecentlyCreated) {
                    Message::createSystemMessage(
                        $conversation->id,
                        'Conversation started by medical staff. Please feel free to ask any questions.'
                    );
                    
                    $conversation->update(['last_message_at' => now()]);
                }
            }
            
            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'redirect_url' => route('admin.messages.index', ['conversation' => $conversation->id]),
                'message' => 'Conversation started successfully with ' . $patientUser->name
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to start conversation with patient: ' . $e->getMessage(), [
                'admin_id' => $user->id,
                'patient_id' => $request->patient_id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to start conversation. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Get list of patients for admin to start conversations with
     */
    public function getPatientsList(Request $request)
    {
        $user = Auth::user();
        
        // Only admins can access patient list
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $search = $request->get('search', '');
        $limit = min($request->get('limit', 20), 50); // Max 50 results
        
        $patients = User::where('role', 'patient')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->select('id', 'name', 'email', 'profile_picture', 'created_at')
            ->orderBy('name')
            ->limit($limit)
            ->get();
        
        // Add existing conversation info
        $patients = $patients->map(function($patientUser) use ($user) {
            // Find the patient record for this user
            $patient = \App\Models\Patient::where('user_id', $patientUser->id)->first();
            
            $existingConversation = null;
            if ($patient) {
                $existingConversation = Conversation::where('patient_id', $patient->id)
                    ->where('admin_id', $user->id)
                    ->first();
            }
            
            $patientUser->has_existing_conversation = (bool) $existingConversation;
            $patientUser->conversation_id = $existingConversation ? $existingConversation->id : null;
            $patientUser->has_patient_record = (bool) $patient;
            
            return $patientUser;
        });
        
        return response()->json([
            'patients' => $patients,
            'total' => User::where('role', 'patient')->count()
        ]);
    }
    
    /**
     * Update typing status for real-time typing indicators
     */
    public function updateTypingStatus(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'is_typing' => 'required|boolean'
        ]);
        
        $user = Auth::user();
        $conversationId = $request->conversation_id;
        $isTyping = $request->is_typing;
        
        // Verify user has access to this conversation
        $conversation = Conversation::forUser($user->id)->find($conversationId);
        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found or access denied'], 404);
        }
        
        $cacheKey = "typing_status_{$conversationId}_{$user->id}";
        
        if ($isTyping) {
            // Set typing status with 5-second expiration
            Cache::put($cacheKey, [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'started_at' => now()->timestamp
            ], 5); // 5 seconds
        } else {
            // Remove typing status
            Cache::forget($cacheKey);
        }
        
        return response()->json([
            'success' => true,
            'status' => $isTyping ? 'started_typing' : 'stopped_typing'
        ]);
    }
    
    /**
     * Get typing status for a conversation
     */
    public function getTypingStatus(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id'
        ]);
        
        $user = Auth::user();
        $conversationId = $request->conversation_id;
        
        // Verify user has access to this conversation
        $conversation = Conversation::forUser($user->id)->find($conversationId);
        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found or access denied'], 404);
        }
        
        // Get the other user in the conversation
        $otherUserId = null;
        if ($user->role === 'patient') {
            // For patients, check if admin is typing
            $otherUserId = $conversation->admin_id;
        } else {
            // For admins, check if patient is typing  
            $otherUserId = $conversation->patient_id;
        }
        
        if (!$otherUserId) {
            return response()->json(['is_typing' => false]);
        }
        
        // Check if the other user is typing
        $cacheKey = "typing_status_{$conversationId}_{$otherUserId}";
        $typingData = Cache::get($cacheKey);
        
        if ($typingData && is_array($typingData)) {
            // Check if typing status is still valid (not older than 5 seconds)
            if (time() - $typingData['started_at'] <= 5) {
                return response()->json([
                    'is_typing' => true,
                    'user_id' => $typingData['user_id'],
                    'user_name' => $typingData['user_name'],
                    'user_role' => $typingData['user_role']
                ]);
            } else {
                // Expired typing status, clean it up
                Cache::forget($cacheKey);
            }
        }
        
        return response()->json([
            'is_typing' => false
        ]);
    }
    
    /**
     * Add or remove a reaction to a message
     */
    public function toggleReaction(Request $request, Message $message)
    {
        $request->validate([
            'emoji' => 'required|string|max:10'
        ]);
        
        $user = Auth::user();
        
        // Verify user has access to this message's conversation
        $conversation = Conversation::forUser($user->id)->find($message->conversation_id);
        if (!$conversation) {
            return response()->json(['error' => 'Message not found or access denied'], 404);
        }
        
        // Valid emoji list for medical chat
        $allowedEmojis = ['👍', '❤️', '😂', '😊', '👏', '🙌', '🤝', '💪'];
        
        if (!in_array($request->emoji, $allowedEmojis)) {
            return response()->json(['error' => 'Invalid emoji'], 400);
        }
        
        try {
            $result = $message->toggleReaction($request->emoji, $user->id);
            
            return response()->json([
                'success' => true,
                'reactions' => $message->getFormattedReactions(),
                'user_reacted' => $result['user_reacted']
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to toggle reaction: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'message_id' => $message->id,
                'emoji' => $request->emoji
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to update reaction'
            ], 500);
        }
    }
}
