@php
    $isSent = $message->sender_id === Auth::id();
    $isSystem = $message->message_type === 'system';
@endphp

<div class="message {{ $isSent ? 'sent' : 'received' }} {{ $isSystem ? 'system' : '' }}" data-message-id="{{ $message->id }}">
    @if(!$isSent && !$isSystem)
        <div class="user-avatar">
            <x-user-avatar :user="$message->sender" size="thumbnail" class="avatar-img" />
        </div>
    @endif
    
    <div class="message-content">
        <!-- Priority display removed for cleaner UI -->
        
        <!-- File attachment -->
        @if($message->hasFileAttachment())
            <div class="message-attachment mb-2">
                @if($message->isImage())
                    <!-- Image attachment - filename hidden for cleaner look -->
                    <div class="attachment-image">
                        <img src="{{ $message->getFileUrl() }}" 
                             alt="Image" 
                             class="img-fluid rounded message-image"
                             style="max-width: 300px; max-height: 200px; cursor: pointer;"
                             onclick="openImageModal('{{ $message->getFileUrl() }}', 'Image')">
                    </div>
                @elseif($message->isVideo())
                    <!-- Video attachment - filename hidden for cleaner look -->
                    <div class="attachment-video">
                        <video controls 
                               class="rounded message-video"
                               style="max-width: 300px; max-height: 200px;"
                               preload="metadata">
                            <source src="{{ $message->getFileUrl() }}" type="{{ $message->mime_type }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @else
                    <!-- File attachment -->
                    <div class="attachment-file">
                        <div class="file-attachment-box">
                            <div class="file-icon">
                                <i class="{{ $message->getFileIconClass() }}"></i>
                            </div>
                            <div class="file-info">
                                <div class="file-name">{{ $message->file_name }}</div>
                                <div class="file-size text-muted">{{ $message->getFileSizeFormatted() }}</div>
                            </div>
                            <div class="file-actions">
                                @if(Auth::user()->role === 'patient')
                                    <a href="{{ route('patient.messages.download', $message->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @else
                                    <a href="{{ route('admin.messages.download', $message->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
        
        <!-- Message text -->
        @if($message->message)
            <div class="message-text">
                {{ $message->message }}
            </div>
        @endif
        
        <!-- Message timestamp and status -->
        <div class="message-time">
            {{ $message->created_at->format('M d, Y g:i A') }}
            @if(!$isSystem && $message->sender_id === Auth::id())
                @if($message->is_read)
                    <i class="fas fa-check-double text-primary" title="Read"></i>
                @else
                    <i class="fas fa-check" title="Sent"></i>
                @endif
            @endif
        </div>
        
        <!-- Message Reactions -->
        @if(!$isSystem)
        <div class="message-reactions" id="reactions-{{ $message->id }}">
            @php
                $formattedReactions = $message->getFormattedReactions();
            @endphp
            
            @foreach($formattedReactions as $reaction)
                <div class="reaction-item {{ $reaction['user_reacted'] ? 'user-reacted' : '' }}" 
                     data-emoji="{{ $reaction['emoji'] }}" 
                     onclick="toggleReaction({{ $message->id }}, '{{ $reaction['emoji'] }}')" 
                     title="{{ $reaction['user_reacted'] ? 'Click to remove your reaction' : 'Click to add your reaction' }}">
                    <span class="reaction-emoji">{{ $reaction['emoji'] }}</span>
                    <span class="reaction-count">{{ $reaction['count'] }}</span>
                </div>
            @endforeach
            
            <!-- Add Reaction Button -->
            <div class="add-reaction-btn" 
                 onclick="showReactionPicker({{ $message->id }}, this)" 
                 title="Add reaction">
                😊
            </div>
        </div>
        @endif
    </div>
    
    @if($isSent && !$isSystem)
        <div class="user-avatar">
            <x-user-avatar :user="$message->sender" size="thumbnail" class="avatar-img" />
        </div>
    @endif
</div>
