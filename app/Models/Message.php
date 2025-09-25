<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Models\User;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'is_read',
        'read_at',
        // Note: Removed columns that don't exist in production database:
        // 'message_type', 'attachments', 'file_name', 'file_path', 'file_type', 
        // 'file_size', 'mime_type', 'has_attachment', 'priority', 'is_system_message', 'reactions'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_system_message' => 'boolean',
        'has_attachment' => 'boolean',
        'file_size' => 'integer',
        'reactions' => 'array',
    ];

    // Message types
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';
    const TYPE_SYSTEM = 'system';
    
    // Priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_URGENT = 'urgent';
    
    // File types
    const FILE_TYPE_IMAGE = 'image';
    const FILE_TYPE_DOCUMENT = 'document';
    const FILE_TYPE_VIDEO = 'video';
    const FILE_TYPE_AUDIO = 'audio';
    const FILE_TYPE_OTHER = 'other';
    
    // Allowed file extensions for medical messaging
    const ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', // Images
        'pdf', 'doc', 'docx', 'txt', 'rtf', // Documents
        'mp4', 'avi', 'mov', 'wmv', // Videos
        'mp3', 'wav', 'ogg', 'm4a', // Audio
        'zip', 'rar', '7z', // Archives
    ];
    
    // Maximum file size (10MB for medical files)
    const MAX_FILE_SIZE = 10485760; // 10MB in bytes

    /**
     * Get the conversation this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the sender of this message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Check if message is sent by current user
     */
    public function getIsMineAttribute()
    {
        return $this->sender_id === auth()->id();
    }

    /**
     * Get formatted time
     */
    public function getTimeAttribute()
    {
        return $this->created_at->format('H:i');
    }

    /**
     * Get formatted date
     */
    public function getDateAttribute()
    {
        if ($this->created_at->isToday()) {
            return 'Today';
        } elseif ($this->created_at->isYesterday()) {
            return 'Yesterday';
        } else {
            return $this->created_at->format('M d, Y');
        }
    }

    /**
     * Get human-readable time ago
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for messages not sent by specific user
     */
    public function scopeNotSentBy($query, $userId)
    {
        return $query->where('sender_id', '!=', $userId);
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }
    
    /**
     * Create a system message with only existing columns
     */
    public static function createSystemMessage($conversationId, $message)
    {
        // Use the first admin user as sender for system messages
        $adminUser = User::where('role', 'admin')->where('status', 'active')->first();
        
        return static::create([
            'conversation_id' => $conversationId,
            'sender_id' => $adminUser ? $adminUser->id : auth()->id(),
            'message' => $message,
            'is_read' => false,
        ]);
    }

    
    /**
     * Check if message has file attachment
     */
    public function hasFileAttachment()
    {
        return (bool) $this->has_attachment && !empty($this->file_path);
    }
    
    /**
     * Get the file URL for display
     */
    public function getFileUrl()
    {
        if (!$this->hasFileAttachment()) {
            return null;
        }
        
        // Check if we're using Cloudinary
        $defaultDisk = config('filesystems.default');
        
        if ($defaultDisk === 'cloudinary' && config('filesystems.disks.cloudinary.cloud_name')) {
            // For Cloudinary, get the URL from the cloudinary disk
            $disk = \Illuminate\Support\Facades\Storage::disk('cloudinary');
            if ($disk->exists($this->file_path)) {
                return $disk->url($this->file_path);
            }
        }
        
        // For local storage or fallback, use asset URL
        return asset('storage/' . $this->file_path);
    }
    
    /**
     * Check if the attachment is an image
     */
    public function isImage()
    {
        return $this->file_type === self::FILE_TYPE_IMAGE;
    }
    
    /**
     * Check if the attachment is a document
     */
    public function isDocument()
    {
        return $this->file_type === self::FILE_TYPE_DOCUMENT;
    }
    
    /**
     * Check if the attachment is a video
     */
    public function isVideo()
    {
        return $this->file_type === self::FILE_TYPE_VIDEO;
    }
    
    /**
     * Check if the attachment is audio
     */
    public function isAudio()
    {
        return $this->file_type === self::FILE_TYPE_AUDIO;
    }
    
    /**
     * Get human readable file size
     */
    public function getFileSizeFormatted()
    {
        if (!$this->file_size) {
            return null;
        }
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->file_size;
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Get file extension from filename
     */
    public function getFileExtension()
    {
        if (!$this->file_name) {
            return null;
        }
        
        return strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
    }
    
    /**
     * Get file icon CSS class based on file type
     */
    public function getFileIconClass()
    {
        switch ($this->file_type) {
            case self::FILE_TYPE_IMAGE:
                return 'fas fa-image text-success';
            case self::FILE_TYPE_DOCUMENT:
                $extension = $this->getFileExtension();
                if (in_array($extension, ['pdf'])) {
                    return 'fas fa-file-pdf text-danger';
                } elseif (in_array($extension, ['doc', 'docx'])) {
                    return 'fas fa-file-word text-primary';
                } else {
                    return 'fas fa-file-alt text-info';
                }
            case self::FILE_TYPE_VIDEO:
                return 'fas fa-video text-purple';
            case self::FILE_TYPE_AUDIO:
                return 'fas fa-music text-warning';
            default:
                return 'fas fa-file text-secondary';
        }
    }
    
    /**
     * Determine file type from extension
     */
    public static function getFileTypeFromExtension($extension)
    {
        $extension = strtolower($extension);
        
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
            return self::FILE_TYPE_IMAGE;
        } elseif (in_array($extension, ['pdf', 'doc', 'docx', 'txt', 'rtf', 'xls', 'xlsx', 'ppt', 'pptx'])) {
            return self::FILE_TYPE_DOCUMENT;
        } elseif (in_array($extension, ['mp4', 'avi', 'mov', 'wmv', 'mkv', 'flv', 'webm'])) {
            return self::FILE_TYPE_VIDEO;
        } elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac'])) {
            return self::FILE_TYPE_AUDIO;
        } else {
            return self::FILE_TYPE_OTHER;
        }
    }
    
    /**
     * Add or remove a reaction from this message
     */
    public function toggleReaction($emoji, $userId)
    {
        $reactions = $this->reactions ?? [];
        
        // Initialize emoji if it doesn't exist
        if (!isset($reactions[$emoji])) {
            $reactions[$emoji] = ['count' => 0, 'users' => []];
        }
        
        // Check if user already reacted with this emoji
        if (in_array($userId, $reactions[$emoji]['users'])) {
            // Remove user's reaction
            $reactions[$emoji]['users'] = array_values(array_diff($reactions[$emoji]['users'], [$userId]));
            $reactions[$emoji]['count'] = max(0, $reactions[$emoji]['count'] - 1);
            
            // Remove emoji if no users left
            if ($reactions[$emoji]['count'] === 0) {
                unset($reactions[$emoji]);
            }
        } else {
            // Add user's reaction (limit: 1 reaction per user per message)
            // First, remove user from any other emoji reactions
            foreach ($reactions as $existingEmoji => $data) {
                if (in_array($userId, $data['users'])) {
                    $reactions[$existingEmoji]['users'] = array_values(array_diff($reactions[$existingEmoji]['users'], [$userId]));
                    $reactions[$existingEmoji]['count'] = max(0, $reactions[$existingEmoji]['count'] - 1);
                    
                    // Remove emoji if no users left
                    if ($reactions[$existingEmoji]['count'] === 0) {
                        unset($reactions[$existingEmoji]);
                    }
                }
            }
            
            // Add new reaction
            if (!isset($reactions[$emoji])) {
                $reactions[$emoji] = ['count' => 0, 'users' => []];
            }
            $reactions[$emoji]['users'][] = $userId;
            $reactions[$emoji]['count']++;
        }
        
        // Save reactions
        $this->update(['reactions' => $reactions]);
        
        return [
            'success' => true,
            'reactions' => $reactions,
            'user_reacted' => in_array($userId, $reactions[$emoji]['users'] ?? [])
        ];
    }
    
    /**
     * Check if user reacted with specific emoji
     */
    public function hasUserReacted($emoji, $userId)
    {
        $reactions = $this->reactions ?? [];
        return isset($reactions[$emoji]) && in_array($userId, $reactions[$emoji]['users']);
    }
    
    /**
     * Get formatted reactions for display
     */
    public function getFormattedReactions()
    {
        $reactions = $this->reactions ?? [];
        $formatted = [];
        
        foreach ($reactions as $emoji => $data) {
            if ($data['count'] > 0) {
                $formatted[] = [
                    'emoji' => $emoji,
                    'count' => $data['count'],
                    'users' => $data['users'],
                    'user_reacted' => in_array(auth()->id(), $data['users'])
                ];
            }
        }
        
        return $formatted;
    }
}
