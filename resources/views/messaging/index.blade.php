@extends('adminlte::page')

@section('title', 'Messages | Bokod CMS')

@section('adminlte_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
@endsection

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-comments mr-2"></i>Messages
                <small class="text-muted">Chat with 
                    @if(Auth::user()->role === 'patient')
                        medical staff
                    @else
                        patients
                    @endif
                </small>
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Messages</li>
            </ol>
        </div>
    </div>
@endsection

@section('adminlte_css_pre')
<style>
/* Enhanced messaging UI styles - Modern system blend */
.messaging-wrapper {
    background: rgba(0, 0, 0, 0.05); /* Subtle system-like background */
    border-radius: 12px; /* More subtle rounded corners */
    padding: 1px;
    margin-bottom: 10px;
    border: 1px solid rgba(0, 0, 0, 0.08);
}

/* Compact layout for patients */
.messaging-container {
    margin: 0;
}

.messaging-container .card-body {
    padding: 0;
}

/* Patient-specific layout optimizations */
@media (max-width: 768px) {
    body.layout-fixed .wrapper .content-wrapper {
        margin-left: 0;
        margin-top: 57px;
    }
    
    .content-wrapper {
        padding: 5px 10px;
    }
    
    .content-header {
        padding: 15px 0.5rem;
    }
    
    .content-header h1 {
        font-size: 1.5rem;
        margin: 0;
    }
}

/* Reduce excessive padding on mobile */
@media (max-width: 768px) {
    .content-wrapper {
        padding: 10px 15px;
    }
    
    .messaging-wrapper {
        margin-bottom: 5px;
    }
    
    .card-header {
        padding: 15px 20px;
    }
    
    /* Compact mobile layout */
    .no-conversations {
        padding: 20px 10px;
        margin: 5px;
    }
    
    .no-messages {
        height: 300px;
        min-height: 250px;
        padding: 20px;
        margin: 5px;
    }
    
    .chat-header {
        padding: 12px 15px; /* More compact on mobile */
        flex-wrap: nowrap; /* Prevent wrapping */
        gap: 8px;
    }
    
    .chat-header .d-flex {
        min-width: 0; /* Allow text truncation */
        flex: 1;
    }
    
    .mobile-back-btn {
        flex-shrink: 0; /* Keep back button size */
        margin-right: 8px;
    }
    
    .messaging-container {
        min-height: 350px;
        max-height: calc(100vh - 180px);
    }
    
    .conversations-panel,
    .chat-panel {
        min-height: 350px;
        max-height: calc(100vh - 200px);
    }
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .messaging-wrapper {
        margin: 0;
        border-radius: 0;
        padding: 0;
    }
    
    .chat-panel {
        border-radius: 0;
        height: auto;
        min-height: 400px;
        max-height: calc(100vh - 160px);
    }
    
    .conversations-panel {
        height: auto;
        min-height: 400px;
        max-height: calc(100vh - 160px);
        position: relative;
        left: 0;
        width: 100%;
        z-index: 999;
        background: white;
        transform: translateX(0);
        transition: transform 0.3s ease;
    }
    
    .conversations-panel.show {
        transform: translateX(0);
    }
    
    .chat-panel-right {
        width: 100%;
        position: relative;
    }
    
    .chat-messages {
        height: calc(100vh - 200px);
        min-height: calc(100vh - 200px);
        max-height: calc(100vh - 200px);
        padding: 15px 15px 120px 15px; /* Extra padding for mobile keyboard */
    }
    
    .chat-header {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .mobile-back-btn {
        background: none;
        border: none;
        font-size: 20px;
        color: #667eea;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.2s ease;
        display: none;
    }
    
    .mobile-back-btn:hover {
        background: rgba(102, 126, 234, 0.1);
    }
    
    .mobile-back-btn.show {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Hide conversations panel by default on mobile */
    .messaging-body .col-md-4 {
        display: none !important;
    }
    
    .messaging-body .col-md-8 {
        display: block !important;
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
    
    /* Show conversations panel when back button is clicked */
    .messaging-body.mobile-show-conversations .col-md-4 {
        display: block !important;
        flex: 0 0 100% !important;
        max-width: 100% !important;
        position: relative;
        z-index: 1000;
    }
    
    .messaging-body.mobile-show-conversations .col-md-8 {
        display: none !important;
    }
    
    /* Ensure conversations panel is visible */
    .mobile-show-conversations .conversations-panel {
        transform: translateX(0) !important;
        position: relative !important;
        width: 100% !important;
        height: calc(100vh - 140px) !important;
        background: white !important;
        z-index: 1001;
    }
}

.chat-panel {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08); /* Softer shadow */
    border: 1px solid rgba(0,0,0,0.06);
    height: auto;
    min-height: 500px;
    max-height: 78vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.conversations-panel {
    background: #fafafa; /* System-like light gray */
    border-right: 1px solid rgba(0, 0, 0, 0.08);
    height: auto;
    min-height: 500px;
    max-height: 78vh;
    overflow-y: auto;
    overflow-x: hidden;
    position: relative;
}

/* Custom scrollbar for conversations panel */
.conversations-panel::-webkit-scrollbar {
    width: 6px;
}

.conversations-panel::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.conversations-panel::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.conversations-panel::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.conversation-item {
    border: none;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    cursor: pointer;
    transition: all 0.2s ease; /* Faster, more responsive */
    padding: 12px 16px !important;
    min-height: 72px;
    position: relative;
    margin: 0 6px 2px 6px;
    border-radius: 8px; /* Less rounded for system look */
    background: #ffffff;
    border: 1px solid transparent;
}

/* Mobile conversation items */
@media (max-width: 768px) {
    .conversation-item {
        margin: 0 4px 2px 4px;
        padding: 12px 14px !important;
        min-height: 70px;
        border-radius: 8px;
    }
    
    .conversation-content h6 {
        font-size: 13px;
        margin-bottom: 3px;
    }
    
    .conversation-content p {
        font-size: 12px;
        line-height: 1.3;
    }
    
    .conversation-avatar {
        width: 40px;
        height: 40px;
    }
}

.conversation-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 3px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 0 3px 3px 0;
    opacity: 0;
    transition: all 0.3s ease;
}

.conversation-content {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.conversation-content h6 {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-bottom: 5px;
    font-weight: 600;
    font-size: 14px;
    color: #2d3748;
}

.conversation-content p {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-bottom: 3px;
    font-size: 13px;
    color: #718096;
    line-height: 1.4;
}

.conversation-item:hover {
    background: rgba(0, 0, 0, 0.04); /* System-like hover */
    border-color: rgba(0, 0, 0, 0.08);
    transform: none; /* Remove slide effect */
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.conversation-item:hover::before {
    opacity: 0; /* Remove accent bar */
}

.conversation-item.active {
    background: #007acc; /* System blue accent */
    color: white;
    border-color: #005a9e;
    transform: none;
    box-shadow: 0 1px 3px rgba(0, 122, 204, 0.3);
}

.conversation-item.active::before {
    opacity: 1;
    background: rgba(255, 255, 255, 0.8);
}

.conversation-item.active .conversation-content h6,
.conversation-item.active .conversation-content p {
    color: white;
}

.conversation-item.active .text-muted {
    color: rgba(255,255,255,0.8) !important;
}

.conversation-item.active .badge-info {
    background-color: rgba(255,255,255,0.9) !important;
    color: #007bff !important;
}

.chat-messages {
    height: calc(78vh - 160px);
    min-height: calc(500px - 160px);
    max-height: calc(78vh - 160px);
    overflow-y: auto;
    overflow-x: hidden;
    padding: 15px 15px 100px 15px; /* Reduced padding */
    background: linear-gradient(180deg, #f8f9ff 0%, #ffffff 50%, #f8f9ff 100%);
    position: relative;
    background-size: 100% 200%;
    animation: gradientShift 20s ease infinite;
}

/* Add more spacing after last message */
.chat-messages::after {
    content: '';
    display: block;
    height: 40px;
    width: 100%;
}

/* Ensure last message is fully visible */
.chat-messages .message:last-child {
    margin-bottom: 30px;
}

@keyframes gradientShift {
    0% { background-position: 0% 0%; }
    50% { background-position: 0% 100%; }
    100% { background-position: 0% 0%; }
}

/* Custom scrollbar for chat messages */
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #007bff;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #0056b3;
}

.message {
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    animation: messageSlideIn 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    opacity: 0;
    animation-fill-mode: forwards;
}

@keyframes messageSlideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message.sent {
    justify-content: flex-end;
}

.message-content {
    max-width: 75%;
    padding: 12px 18px;
    border-radius: 18px;
    position: relative;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.message-content:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.message.sent .message-content {
    background: #007acc; /* System blue for sent messages */
    color: white;
    border-radius: 18px 18px 4px 18px;
}

.message.received .message-content {
    background: #f1f1f1; /* System gray for received messages */
    border: 1px solid rgba(0, 0, 0, 0.08);
    color: #1f1f1f; /* System text color */
    border-radius: 18px 18px 18px 4px;
}

.message.system .message-content {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    color: #718096;
    font-style: italic;
    text-align: center;
    max-width: 90%;
    border-radius: 25px;
    border: 1px solid rgba(102, 126, 234, 0.2);
    font-size: 13px;
}

.message-time {
    font-size: 11px;
    opacity: 0.6;
    margin-top: 6px;
    font-weight: 500;
    color: #718096;
    display: flex;
    align-items: center;
    gap: 4px;
}

.message.sent .message-time {
    color: rgba(255, 255, 255, 0.8);
}

.message-time i {
    font-size: 10px;
    margin-left: 4px;
}

.fa-check-double {
    color: #4ade80 !important;
}

.fa-check {
    color: rgba(255, 255, 255, 0.7);
}

/* Modern Message Input Styles */
.modern-message-input {
    padding: 20px;
    background: #ffffff;
    border-top: 1px solid #e5e7eb;
    position: sticky;
    bottom: 0;
    z-index: 100;
}

.message-input-container {
    display: flex;
    align-items: flex-end;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 20px;
    padding: 8px;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.message-input-container:focus-within {
    border-color: #007acc; /* System blue focus */
    box-shadow: 0 0 0 2px rgba(0, 122, 204, 0.2);
}

.input-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-right: 8px;
}

.action-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: transparent;
    border-radius: 50%;
    color: #6b7280;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.action-btn:hover {
    background: #e5e7eb;
    color: #374151;
    transform: scale(1.05);
}

.action-btn.active {
    background: #667eea;
    color: white;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.message-input-wrapper {
    flex: 1;
    display: flex;
    align-items: flex-end;
    position: relative;
}

.message-textarea {
    flex: 1;
    border: none;
    background: transparent;
    resize: none;
    padding: 12px 16px;
    font-size: 14px;
    line-height: 1.5;
    color: #374151;
    min-height: 24px;
    max-height: 120px;
    overflow-y: auto;
    outline: none;
    font-family: inherit;
}

.message-textarea::placeholder {
    color: #9ca3af;
}

.send-btn {
    width: 44px;
    height: 44px;
    border: none;
    background: #007acc; /* System blue */
    border-radius: 50%;
    color: white;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-left: 8px;
    box-shadow: 0 1px 3px rgba(0, 122, 204, 0.3);
}

.send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.send-btn:active {
    transform: scale(0.95);
}

.send-btn:disabled {
    background: #d1d5db;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Mobile input styling */
@media (max-width: 768px) {
    .modern-message-input {
        padding: 15px;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        z-index: 1000;
    }
    
    .message-input-container {
        border-radius: 20px;
        padding: 6px;
    }
    
    .action-btn {
        width: 36px;
        height: 36px;
        font-size: 14px;
    }
    
    .message-textarea {
        font-size: 14px;
        padding: 10px 12px;
    }
    
    .send-btn {
        width: 40px;
        height: 40px;
        font-size: 14px;
    }
    
    .file-preview-container {
        margin-bottom: 10px;
    }
    
    .file-preview-card {
        padding: 10px 12px;
        border-radius: 12px;
    }
    
    .file-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .dropdown-menu {
        margin-bottom: 8px;
        min-width: 140px;
    }
    
    .priority-indicator {
        margin-bottom: 6px;
    }
    
    .priority-badge {
        font-size: 11px;
        padding: 3px 8px;
    }
}

/* File Preview Styles */
.file-preview-container {
    margin-bottom: 12px;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.file-preview-card {
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    border-radius: 16px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.2s ease;
}

.file-preview-card:hover {
    background: #e5e7eb;
    border-color: #9ca3af;
}

.file-info {
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}

.file-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 16px;
    flex-shrink: 0;
}

.file-icon.image { background: #dcfce7; color: #16a34a; }
.file-icon.document { background: #fef3c7; color: #d97706; }
.file-icon.video { background: #e0e7ff; color: #6366f1; }
.file-icon.audio { background: #fce7f3; color: #ec4899; }
.file-icon.archive { background: #f3f4f6; color: #6b7280; }

.file-details {
    min-width: 0;
    flex: 1;
}

.file-name {
    display: block;
    font-weight: 500;
    color: #374151;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
}

.file-size {
    display: block;
    font-size: 12px;
    color: #6b7280;
}

.remove-file-btn {
    width: 28px;
    height: 28px;
    border: none;
    background: #fee2e2;
    border-radius: 50%;
    color: #dc2626;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-left: 8px;
}

.remove-file-btn:hover {
    background: #fecaca;
    transform: scale(1.1);
}

/* Priority Indicator Styles */
.priority-indicator {
    margin-bottom: 8px;
    display: flex;
    justify-content: flex-end;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.priority-badge {
    background: #f3f4f6;
    color: #6b7280;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
    border: 1px solid #d1d5db;
}

.priority-badge.low {
    background: #dcfce7;
    color: #16a34a;
    border-color: #bbf7d0;
}

.priority-badge.urgent {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
    animation: urgentPulse 1s ease-in-out infinite;
}

@keyframes urgentPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.02); }
}

/* Priority Dropdown Styles */
.dropdown-menu {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    padding: 4px;
    min-width: 160px;
    margin-top: -8px;
}

.dropdown-item {
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 14px;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: background 0.2s ease;
}

.dropdown-item:hover {
    background: #f3f4f6;
    color: #374151;
}

.dropdown-item.active {
    background: #eff6ff;
    color: #2563eb;
    font-weight: 500;
}

/* Auto-resize textarea */
.message-textarea {
    field-sizing: content;
}

/* Upload progress styles */
.upload-progress {
    position: absolute;
    bottom: 100%;
    left: 0;
    right: 0;
    background: white;
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.progress {
    height: 4px;
    background: #f3f4f6;
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 8px;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transition: width 0.3s ease;
}

/* Notification System */
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
}

.notification {
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    margin-bottom: 12px;
    padding: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transform: translateX(400px);
    animation: slideInRight 0.3s ease forwards;
    border-left: 4px solid #6b7280;
}

@keyframes slideInRight {
    to {
        transform: translateX(0);
    }
}

.notification-success {
    border-left-color: #10b981;
}

.notification-success .notification-content i {
    color: #10b981;
}

.notification-error {
    border-left-color: #ef4444;
}

.notification-error .notification-content i {
    color: #ef4444;
}

.notification-warning {
    border-left-color: #f59e0b;
}

.notification-warning .notification-content i {
    color: #f59e0b;
}

.notification-info {
    border-left-color: #3b82f6;
}

.notification-info .notification-content i {
    color: #3b82f6;
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.notification-content span {
    font-size: 14px;
    color: #374151;
    font-weight: 500;
}

.notification-close {
    width: 24px;
    height: 24px;
    border: none;
    background: transparent;
    color: #9ca3af;
    cursor: pointer;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.notification-close:hover {
    background: #f3f4f6;
    color: #6b7280;
}

/* Conversation Actions */
.conversation-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    opacity: 0.7;
    transition: opacity 0.2s ease;
}

.conversation-item:hover .conversation-actions {
    opacity: 1;
}

.conversation-menu .dropdown-toggle {
    border: none;
    background: none;
    box-shadow: none;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.conversation-item:hover .conversation-menu .dropdown-toggle {
    opacity: 0.7;
}

.conversation-menu .dropdown-toggle:hover {
    opacity: 1 !important;
}

.conversation-menu .dropdown-menu {
    font-size: 13px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    min-width: 140px;
}

.conversation-menu .dropdown-item {
    padding: 8px 12px;
    border-radius: 4px;
    margin: 2px;
    transition: all 0.2s ease;
}

.conversation-menu .dropdown-item:hover {
    background: #f3f4f6;
    color: #374151;
}

/* Enhanced notification badges */
.unread-count {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    color: white !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 3px 7px !important;
    border-radius: 10px !important;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3) !important;
    animation: notificationPulse 2s ease-in-out infinite;
    min-width: 18px;
    text-align: center;
}

@keyframes notificationPulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.9; }
}

/* Browser notification indicator */
.notification-status {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #10b981;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    display: none;
    animation: slideInRight 0.3s ease;
}

.notification-status.error {
    background: #ef4444;
}

/* Sound control button - moved to header for better mobile UX */
.sound-control {
    width: 32px;
    height: 32px;
    background: rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 6px;
    color: #666666;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 8px;
    /* Enhanced accessibility */
    outline: none;
    position: relative;
    /* Touch-friendly interaction */
    -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
    touch-action: manipulation;
}

.sound-control:focus {
    outline: 2px solid rgba(102, 126, 234, 0.5);
    outline-offset: 2px;
}

.sound-control:active {
    background: rgba(102, 126, 234, 0.3);
    border-color: #667eea;
    transform: translateY(1px);
}

.sound-control:hover {
    background: rgba(0, 0, 0, 0.08);
    border-color: rgba(0, 0, 0, 0.15);
    transform: none;
}

.sound-control.muted {
    background: rgba(0, 0, 0, 0.02);
    border-color: rgba(0, 0, 0, 0.06);
    color: #999999;
}

.sound-control.muted:hover {
    background: rgba(0, 0, 0, 0.06);
    border-color: rgba(0, 0, 0, 0.12);
}

/* Accessibility - Screen reader only text */
.sr-only {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}

/* Mobile specific adjustments */
@media (max-width: 768px) {
    .sound-control {
        width: 36px; /* Larger touch target for mobile */
        height: 36px;
        font-size: 13px;
        margin-left: 4px;
        /* Enhanced touch interaction */
        min-width: 36px; /* Ensure minimum touch target size */
        min-height: 36px;
    }
    
    .sound-control:active {
        /* Provide better visual feedback on mobile */
        background: rgba(102, 126, 234, 0.4);
        transform: scale(0.95);
        transition: all 0.1s ease;
    }
    
    /* Ensure card tools are properly spaced on mobile */
    .card-tools {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
    }
    
    .card-tools .btn {
        font-size: 12px;
        padding: 0.25rem 0.5rem;
        white-space: nowrap;
    }
    
    /* Chat header actions removed for cleaner mobile UI */
}

/* File upload styles */
.file-upload-section {
    background: rgba(248, 249, 255, 0.9);
    border: 2px dashed rgba(102, 126, 234, 0.3);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.file-upload-section.dragover {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}

.upload-area {
    text-align: center;
    cursor: pointer;
    padding: 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.upload-area:hover {
    background: rgba(102, 126, 234, 0.05);
}

.file-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.file-preview {
    margin-top: 15px;
    padding: 15px;
    background: white;
    border-radius: 8px;
    border: 1px solid rgba(102, 126, 234, 0.2);
    position: relative;
}

.file-preview-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
}

.file-preview-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.file-preview-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 18px;
}

.file-preview-details h6 {
    margin: 0;
    font-size: 14px;
    color: #2d3748;
}

.file-preview-details small {
    color: #718096;
    font-size: 12px;
}

.file-remove-btn {
    background: #fee;
    border: 1px solid #fcc;
    color: #c53030;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.file-remove-btn:hover {
    background: #fed7d7;
    border-color: #feb2b2;
}

/* Priority section styles */
.priority-section {
    margin-bottom: 15px;
    text-align: center;
}

.priority-buttons .btn {
    border-radius: 20px;
    padding: 5px 15px;
    font-size: 12px;
    font-weight: 600;
    margin: 0 2px;
    transition: all 0.3s ease;
}

.priority-buttons .priority-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
    transform: scale(1.05);
}

.priority-buttons .priority-btn:not(.active):hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

/* Enhanced input group buttons */
.input-group-prepend .btn {
    border-radius: 8px 0 0 8px;
    border-right: none;
    padding: 10px 12px;
    transition: all 0.3s ease;
}

.input-group-prepend .btn:hover {
    background: rgba(102, 126, 234, 0.1);
    border-color: #667eea;
    color: #667eea;
    transform: translateY(-1px);
}

.input-group-prepend .btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
}

/* Image preview styles */
.file-preview-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 12px;
    border: 2px solid rgba(102, 126, 234, 0.2);
}

/* Attachment badge in message input */
.input-group .form-control.has-attachment {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}

.attachment-indicator {
    position: absolute;
    right: 50px;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
    pointer-events: none;
}

/* Message attachment styles */
.message-attachment {
    margin-bottom: 10px;
}

/* Attachment button active state */
.action-btn.active {
    background-color: #28a745 !important;
    color: white !important;
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

/* Attachment button with file selected */
#attachment-btn.active::after {
    content: '';
    position: absolute;
    top: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    background: #dc3545;
    border: 2px solid white;
    border-radius: 50%;
    animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}

/* Message textarea with attachment */
.message-textarea.has-attachment {
    border-color: #28a745 !important;
    background: rgba(40, 167, 69, 0.05) !important;
    padding-right: 45px; /* Make room for attachment indicator */
}

.message-attachment {
    margin-bottom: 10px;
}

.attachment-image {
    margin-bottom: 8px;
    position: relative;
}

.attachment-image img.message-image {
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.1);
    display: block;
    width: 100%;
    max-width: 280px;
    height: auto;
    min-height: 120px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    object-fit: cover;
    cursor: pointer;
    opacity: 0;
    transform: scale(0.95);
    animation: imageAppear 0.4s ease forwards;
}

@keyframes imageAppear {
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.attachment-image img.message-image:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    border-color: rgba(102, 126, 234, 0.5);
}

.attachment-image img.message-image.loaded {
    background: transparent;
}

.attachment-image img.message-image[src=""], 
.attachment-image img.message-image:not([src]),
.attachment-image img.message-image[src="null"] {
    display: none;
}

/* Sent message images */
.message.sent .attachment-image img.message-image {
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
}

.message.sent .attachment-image img.message-image:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
}

/* Mobile message styling */
@media (max-width: 768px) {
    .message {
        margin-bottom: 15px;
        padding: 0 5px;
    }
    
    .message-content {
        max-width: 85%;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .message.sent .message-content {
        margin-left: auto;
    }
    
    .message.received .message-content {
        margin-right: auto;
    }
    
    .message-text {
        font-size: 14px;
        line-height: 1.4;
        padding: 12px 16px;
        border-radius: 18px;
        max-width: 100%;
        word-break: break-word;
    }
    
    .attachment-image img.message-image {
        max-width: 220px;
        border-radius: 12px;
    }
    
    .user-avatar {
        width: 32px;
        height: 32px;
        margin-right: 8px;
    }
    
    .message.sent .user-avatar {
        margin-left: 8px;
        margin-right: 0;
    }
    
    .message-time {
        font-size: 11px;
        margin-top: 5px;
        opacity: 0.7;
    }
    
    .file-attachment-box {
        max-width: 240px;
        padding: 10px;
    }
    
    .file-attachment-box .file-name {
        font-size: 13px;
    }
    
    .file-attachment-box .file-size {
        font-size: 11px;
    }
}

/* Image error state */
.image-error {
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    max-width: 300px;
}

.image-error-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.image-error-content i {
    font-size: 24px;
    opacity: 0.5;
}

.image-error-content p {
    font-size: 14px;
    margin: 0;
}

.image-error-content small {
    font-size: 12px;
    opacity: 0.7;
}

.file-attachment-box {
    display: flex;
    align-items: center;
    background: rgba(248, 249, 255, 0.8);
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 12px;
    padding: 12px;
    transition: all 0.3s ease;
    max-width: 280px;
}

.file-attachment-box:hover {
    background: rgba(102, 126, 234, 0.05);
    border-color: #667eea;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.file-attachment-box .file-icon {
    font-size: 24px;
    margin-right: 12px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.file-attachment-box .file-info {
    flex: 1;
    min-width: 0;
}

.file-attachment-box .file-name {
    font-weight: 600;
    color: #2d3748;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
}

.file-attachment-box .file-size {
    font-size: 12px;
    color: #718096;
}

.file-attachment-box .file-actions {
    margin-left: 8px;
}

.file-attachment-box .btn {
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 12px;
    transition: all 0.3s ease;
}

.file-attachment-box .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

/* Priority badge styles */
.message-priority .badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 600;
    animation: priorityPulse 2s ease-in-out infinite;
}

.message-priority .badge-danger {
    background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
    box-shadow: 0 2px 6px rgba(229, 62, 62, 0.3);
}

.message-priority .badge-success {
    background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
    box-shadow: 0 2px 6px rgba(56, 161, 105, 0.3);
}

@keyframes priorityPulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.9; }
}

/* Sent message attachment adjustments */
.message.sent .file-attachment-box {
    background: rgba(255, 255, 255, 0.9);
    border-color: rgba(102, 126, 234, 0.3);
}

.message.sent .file-attachment-box:hover {
    background: white;
    border-color: #667eea;
}

/* System message styling for attachments */
.message.system .message-attachment {
    opacity: 0.8;
}

/* Upload progress styles */
.upload-progress {
    background: rgba(248, 249, 255, 0.9);
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid rgba(102, 126, 234, 0.2);
}

.upload-progress .progress {
    margin-bottom: 5px;
    background: rgba(102, 126, 234, 0.1);
    border-radius: 3px;
}

/* Image modal styles (will be used for image preview) */
.image-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.8);
    backdrop-filter: blur(5px);
}

.image-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-width: 90%;
    max-height: 90%;
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
}

.image-modal-content img {
    max-width: 100%;
    max-height: 80vh;
    border-radius: 8px;
}

.image-modal-close {
    position: absolute;
    top: 10px;
    right: 15px;
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.image-modal-close:hover {
    color: #667eea;
}
.message-input .input-group {
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.message-input .input-group:focus-within {
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.2);
    transform: translateY(-2px);
}

.message-input .form-control {
    border: none;
    padding: 12px 20px;
    font-size: 14px;
    background: rgba(255, 255, 255, 0.95);
    color: #2d3748;
    border-radius: 25px 0 0 25px;
}

.message-input .form-control:focus {
    box-shadow: none;
    background: rgba(255, 255, 255, 1);
}

.message-input .btn {
    border: none;
    padding: 12px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 0 25px 25px 0;
    transition: all 0.3s ease;
}

.message-input .btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.message-input .btn:active {
    transform: scale(0.95);
}

.message-input .form-control.sending {
    opacity: 0.7;
    background: rgba(255, 255, 255, 0.8) !important;
}

/* Loading spinner animation */
.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.no-conversations {
    text-align: center;
    color: #666;
    padding: 30px 15px;
}

.no-messages {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 400px;
    min-height: 300px;
    text-align: center;
    color: #999;
}

.unread-badge {
    position: absolute;
    right: 15px;
    top: 15px;
}

.user-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-weight: 600;
    flex-shrink: 0;
    overflow: hidden;
    border: 3px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
    position: relative;
}

.user-avatar::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #667eea, #764ba2, #667eea);
    border-radius: 50%;
    z-index: -1;
    animation: avatarGlow 3s ease-in-out infinite;
}

@keyframes avatarGlow {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.05); }
}

.user-avatar:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.user-avatar .avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.message.sent .user-avatar {
    margin-left: 10px;
    margin-right: 0;
}

.chat-header {
    padding: 16px 20px;
    background: #f8f9fa; /* System-like header background */
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 12px 12px 0 0;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    min-height: 64px;
}

.chat-header .d-flex {
    width: 100%;
    overflow: hidden; /* Prevent overflow */
}

.chat-header .chat-header-avatar {
    flex-shrink: 0; /* Prevent avatar from shrinking */
}

.chat-header > div:last-child {
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    flex: 1;
    min-width: 0; /* Allow text to truncate */
}

.chat-header::before {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    opacity: 0.3;
}

.chat-header h6 {
    color: #2d3748;
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 2px;
    line-height: 1.2;
}

.chat-header small {
    color: #718096;
    font-size: 12px;
    line-height: 1.2;
}

/* Chat header actions styles removed - no longer needed for cleaner UI */

.start-conversation-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
}

.conversation-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid rgba(102, 126, 234, 0.2);
    transition: all 0.3s ease;
    position: relative;
}

.conversation-avatar::before {
    content: '';
    position: absolute;
    top: -1px;
    left: -1px;
    right: -1px;
    bottom: -1px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    z-index: -1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.conversation-item:hover .conversation-avatar::before,
.conversation-item.active .conversation-avatar::before {
    opacity: 1;
}

.conversation-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.conversation-item:hover .conversation-avatar-img {
    transform: scale(1.05);
}

/* Enhanced badges */
.badge-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
    animation: badgePulse 2s ease-in-out infinite;
}

@keyframes badgePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.conversation-item.active .badge-info {
    background: rgba(255, 255, 255, 0.9) !important;
    color: #667eea !important;
}

/* Enhanced buttons */
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 8px 16px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
}

.btn-primary:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4) !important;
}

.btn-primary:active {
    transform: translateY(0) !important;
}

/* Enhanced no-messages state */
.no-messages {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(255, 255, 255, 0.8) 100%);
    border-radius: 20px;
    padding: 25px;
    margin: 10px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(102, 126, 234, 0.1);
}

.no-messages i {
    color: #667eea;
    opacity: 0.7;
}

.no-conversations {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(255, 255, 255, 0.8) 100%);
    border-radius: 15px;
    margin: 10px;
    padding: 25px 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(102, 126, 234, 0.1);
}

.no-conversations i {
    color: #667eea;
    opacity: 0.7;
}

/* Typing indicator animation */
@keyframes typing {
    0%, 20% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}

.typing-indicator {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    margin: 10px 0;
}

.typing-indicator .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #667eea;
    margin: 0 2px;
    animation: typing 1.4s infinite;
}

.typing-indicator .dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator .dot:nth-child(3) {
    animation-delay: 0.4s;
}

.chat-header-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.chat-header-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Responsive design for mobile devices */
@media (max-width: 768px) {
    .chat-panel {
        height: auto;
        min-height: 400px;
        max-height: calc(100vh - 200px);
    }
    
    .conversations-panel {
        height: auto;
        min-height: 400px;
        max-height: calc(100vh - 200px);
    }
    
    .chat-messages {
        height: auto;
        min-height: 300px;
        max-height: calc(100vh - 280px);
    }
    
    .conversation-avatar {
        width: 40px;
        height: 40px;
    }
    
    .chat-header-avatar {
        width: 35px;
        height: 35px;
    }
}

/* Ensure proper flex layout and width expansion */
.messaging-container {
    height: auto;
    min-height: 500px;
    max-height: 82vh;
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: none;
}

/* Base width optimizations for all screens */
.messaging-wrapper {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
}

.messaging-body {
    width: 100%;
    max-width: 100%;
}

.messaging-body .row {
    width: 100%;
    max-width: 100%;
    margin-left: -0.25rem;
    margin-right: -0.25rem;
}

.messaging-body .col-md-4,
.messaging-body .col-md-8 {
    padding-left: 0.25rem;
    padding-right: 0.25rem;
}

/* Optimize width within content bounds */
@media (min-width: 768px) {
    /* Use available content width efficiently */
    .messaging-wrapper {
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding: 0 !important;
    }
}

/* Desktop content optimization */
@media (min-width: 992px) {
    .content-wrapper {
        padding: 0.5rem;
    }
    
    .messaging-wrapper {
        margin-bottom: 0;
        padding: 0;
        width: 100%;
        max-width: none;
    }
    
    .content-header {
        padding: 0.5rem 0;
        margin-bottom: 0.25rem;
    }
    
    .content-header h1 {
        font-size: 1.5rem;
        margin-bottom: 0;
    }
    
    /* Optimize width usage within bounds */
    .row {
        margin-left: -0.25rem;
        margin-right: -0.25rem;
    }
    
    .col-12 {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
    }
    
    .messaging-container {
        width: 100% !important;
        max-width: none !important;
    }
    
    .messaging-body {
        width: 100% !important;
        max-width: none !important;
    }
    
    /* Optimize panel widths for better space usage */
    .conversations-panel {
        flex: 0 0 35% !important;
        max-width: 35% !important;
    }
    
    .chat-panel-right {
        flex: 0 0 65% !important;
        max-width: 65% !important;
    }
    
    /* Optimize for no-scroll desktop experience */
    .messaging-container {
        height: auto !important;
        max-height: 80vh !important;
        min-height: 550px !important;
    }
    
    .messaging-body .row.h-100 {
        height: auto !important;
        max-height: 80vh !important;
        min-height: 550px !important;
    }
    
    .conversations-panel {
        height: auto !important;
        max-height: 80vh !important;
        min-height: 550px !important;
    }
    
    .chat-panel {
        height: auto !important;
        max-height: 80vh !important;
        min-height: 550px !important;
    }
    
    .chat-messages {
        height: calc(80vh - 150px) !important;
        max-height: calc(80vh - 150px) !important;
        min-height: 380px !important;
    }
}

/* Medium screen optimization */
@media (min-width: 992px) and (max-width: 1199px) {
    .messaging-container {
        min-height: 520px;
        max-height: 78vh;
    }
    
    .conversations-panel {
        min-height: 520px;
        max-height: 78vh;
    }
    
    .chat-panel {
        min-height: 520px;
        max-height: 78vh;
    }
    
    .chat-messages {
        height: calc(78vh - 150px);
        min-height: 350px;
        max-height: calc(78vh - 150px);
    }
    
    .no-messages {
        height: calc(78vh - 150px);
        min-height: 350px;
    }
}

/* Desktop full screen optimization */
@media (min-width: 1200px) {
    .messaging-container {
        min-height: 580px;
        max-height: 82vh;
    }
    
    .conversations-panel {
        min-height: 580px;
        max-height: 82vh;
    }
    
    .chat-panel {
        min-height: 580px;
        max-height: 82vh;
    }
    
    .chat-messages {
        height: calc(82vh - 160px);
        min-height: 400px;
        max-height: calc(82vh - 160px);
    }
    
    .no-messages {
        height: calc(82vh - 160px);
        min-height: 400px;
    }
}

/* Extra large desktop optimization - no scroll experience */
@media (min-width: 1400px) {
    .messaging-container {
        max-height: 85vh !important;
        min-height: 620px !important;
    }
    
    .conversations-panel {
        max-height: 85vh !important;
        min-height: 620px !important;
    }
    
    .chat-panel {
        max-height: 85vh !important;
        min-height: 620px !important;
    }
    
    .chat-messages {
        height: calc(85vh - 170px) !important;
        max-height: calc(85vh - 170px) !important;
        min-height: 430px !important;
    }
    
    .messaging-body .row.h-100 {
        max-height: 85vh !important;
        min-height: 620px !important;
    }
    
    /* Ultra-wide screen optimizations */
    .messaging-wrapper {
        width: 100% !important;
        max-width: none !important;
    }
    
    .conversations-panel {
        flex: 0 0 30% !important;
        max-width: 30% !important;
    }
    
    .chat-panel-right {
        flex: 0 0 70% !important;
        max-width: 70% !important;
    }
}

.messaging-body {
    flex: 1;
    display: flex;
    overflow: hidden;
    min-height: 0; /* Fix flex shrinking */
}

/* Allow messaging to fill available space without scrolling */
.messaging-body .row {
    height: 100% !important;
    min-height: 500px !important;
}

.messaging-body .row.h-100 {
    height: 100% !important;
    min-height: 500px;
    max-height: 82vh;
}

/* Allow conversations panel to fill space optimally */
.col-md-4.conversations-panel {
    height: 100% !important;
    max-height: 82vh !important;
    min-height: 500px !important;
}

.col-md-8.chat-panel-right {
    height: 100% !important;
    max-height: 82vh !important;
    min-height: 500px !important;
}

.chat-panel-right {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0; /* Fix flex shrinking */
    flex: 1;
}

/* Archive functionality styles */
.conversation-item.archiving {
    opacity: 0.3;
    transform: translateX(-100px) scale(0.95);
    background: rgba(220, 53, 69, 0.1);
    transition: all 0.4s ease;
    pointer-events: none;
}

.conversation-item.archiving::after {
    content: 'Archiving...';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(220, 53, 69, 0.9);
    color: white;
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    z-index: 20;
    animation: fadeInScale 0.3s ease;
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.8);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

.archived-item {
    background-color: rgba(248, 249, 250, 0.8);
    border-left: 3px solid #6c757d;
}

.archived-header {
    padding: 15px;
    background: linear-gradient(135deg, rgba(108, 117, 125, 0.1) 0%, rgba(255, 255, 255, 0.9) 100%);
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.archived-header h6 {
    margin: 0;
    color: #6c757d;
    font-weight: 600;
}

/* Swipe to Archive Functionality */
.conversation-item {
    position: relative;
    overflow: hidden;
    transform: translateX(0);
    transition: transform 0.3s ease, background-color 0.3s ease;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.conversation-item.long-pressing {
    background: rgba(102, 126, 234, 0.1);
    transform: scale(0.98);
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}

.conversation-item.swiping {
    transition: none;
}

.conversation-item.swiped-left {
    transform: translateX(-80px);
    background: rgba(220, 53, 69, 0.05);
}

/* Archive Action Button */
.archive-action {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.conversation-item.swiped-left .archive-action {
    opacity: 1;
    display: block !important;
}

.archive-btn {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
    border: none !important;
    color: white !important;
    padding: 8px 12px !important;
    font-size: 12px !important;
    border-radius: 20px !important;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3) !important;
    transition: all 0.3s ease !important;
    font-weight: 600 !important;
}

.archive-btn:hover {
    transform: scale(1.05) !important;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4) !important;
    background: linear-gradient(135deg, #c82333 0%, #bd2130 100%) !important;
}

.archive-btn:active {
    transform: scale(0.95) !important;
}

/* Long press indicator */
.conversation-item.long-pressing::after {
    content: 'Swipe left to archive';
    position: absolute;
    bottom: 5px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(102, 126, 234, 0.9);
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    white-space: nowrap;
    animation: fadeInUp 0.3s ease;
    z-index: 20;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

/* Reset button styles when swiped */
.conversation-item.swiped-left .conversation-content {
    opacity: 0.7;
}

/* Archive Confirmation Modal */
.archive-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    animation: modalFadeIn 0.3s ease;
}

.archive-modal.show {
    display: block;
}

.archive-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    animation: overlayFadeIn 0.3s ease;
}

.archive-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    max-width: 420px;
    width: 90%;
    overflow: hidden;
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes overlayFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translate(-50%, -60%);
        scale: 0.95;
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
        scale: 1;
    }
}

.archive-modal-header {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: white;
    padding: 24px;
    text-align: center;
    position: relative;
}

.archive-modal-icon {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 24px;
    animation: iconPulse 2s ease-in-out infinite;
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.archive-modal-title {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: white;
}

.archive-modal-body {
    padding: 24px;
    text-align: center;
}

.archive-modal-message {
    font-size: 16px;
    color: #2d3748;
    margin-bottom: 16px;
    line-height: 1.5;
}

.archive-modal-info {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 16px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    text-align: left;
}

.archive-modal-info i {
    color: #667eea;
    margin-top: 2px;
    flex-shrink: 0;
}

.archive-modal-info small {
    color: #4a5568;
    line-height: 1.4;
}

.archive-modal-footer {
    background: #f8f9fa;
    padding: 20px 24px;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.archive-modal-cancel {
    background: #6c757d !important;
    border: none !important;
    color: white !important;
    padding: 10px 20px !important;
    border-radius: 8px !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
}

.archive-modal-cancel:hover {
    background: #5a6268 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

.archive-modal-confirm {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
    border: none !important;
    color: white !important;
    padding: 10px 20px !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

.archive-modal-confirm:hover {
    background: linear-gradient(135deg, #c82333 0%, #bd2130 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
}

.archive-modal-confirm:active,
.archive-modal-cancel:active {
    transform: translateY(0) !important;
}

/* Mobile responsive modal */
@media (max-width: 768px) {
    .archive-modal-content {
        width: 95%;
        max-width: none;
        margin: 20px;
    }
    
    .archive-modal-header {
        padding: 20px;
    }
    
    .archive-modal-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
        margin-bottom: 12px;
    }
    
    .archive-modal-title {
        font-size: 18px;
    }
    
    .archive-modal-body {
        padding: 20px;
    }
    
    .archive-modal-footer {
        padding: 16px 20px;
        flex-direction: column-reverse;
    }
    
    .archive-modal-footer .btn {
        width: 100%;
        margin-bottom: 8px;
    }
    
    .archive-modal-footer .btn:last-child {
        margin-bottom: 0;
    }
}

/* Prevent body scroll when modal is open */
body.modal-open {
    overflow: hidden;
    padding-right: 15px; /* Prevent layout shift from scrollbar */
}

/* Additional modal animations */
.archive-modal.show .archive-modal-content {
    animation: modalSlideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Focus styles for accessibility */
.archive-modal-confirm:focus,
.archive-modal-cancel:focus {
    outline: 3px solid rgba(102, 126, 234, 0.5);
    outline-offset: 2px;
}

/* Loading state for archive button */
.archive-modal-confirm.loading {
    position: relative;
    color: transparent !important;
}

.archive-modal-confirm.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

@keyframes spin {
    to { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Mobile specific adjustments */
@media (max-width: 768px) {
    .conversation-item.swiped-left {
        transform: translateX(-100px);
    }
    
    .archive-action {
        right: 5px;
    }
    
    .archive-btn {
        padding: 6px 10px !important;
        font-size: 11px !important;
    }
    
    body.modal-open {
        padding-right: 0; /* No scrollbar compensation needed on mobile */
    }
}

/* Make dropdown always visible on mobile */
@media (max-width: 768px) {
    .conversation-menu .dropdown-toggle {
        opacity: 1 !important;
        background: rgba(108, 117, 125, 0.1) !important;
    }
}

/* New Conversation Modal Styles */
.patient-item {
    transition: all 0.3s ease;
    border-radius: 8px;
    margin: 2px;
}

.patient-item:hover {
    background: rgba(102, 126, 234, 0.1) !important;
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
}

.patient-avatar img,
.patient-avatar div {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

#selectedPatientInfo {
    border-left: 4px solid #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(255, 255, 255, 0.9) 100%);
}

#newConversationModal .modal-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
}

#newConversationModal .modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

#newConversationModal .btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 6px;
    transition: all 0.3s ease;
}

#newConversationModal .btn-success:hover {
    background: linear-gradient(135deg, #218838 0%, #1ea385 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.patient-list {
    background: #f8f9fa;
}

.patient-item.selected {
    background: rgba(102, 126, 234, 0.2) !important;
    border: 2px solid #667eea;
}

/* Loading states */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.conversation-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.unread-count {
    font-size: 11px;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Notification toast styles */
.notification-toast {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 8px;
}

.notification-toast .close {
    padding: 0.5rem;
    margin: -0.5rem -0.5rem -0.5rem auto;
}

/* Archive toggle button states */
#archive-toggle-btn.showing-archived {
    background-color: #6c757d;
    color: white;
    border-color: #6c757d;
}

#archive-toggle-btn.showing-archived:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

/* Mobile responsive archive styles */
@media (max-width: 768px) {
    .archived-header {
        padding: 10px;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .conversation-menu .dropdown-toggle {
        opacity: 1;
    }
    
    .card-tools {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    
    .card-tools .btn {
        font-size: 12px;
        padding: 0.25rem 0.5rem;
        white-space: nowrap;
    }
    
    .conversation-actions {
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
    }
    
    .conversation-content h6 {
        font-size: 14px;
    }
    
    .conversation-content p {
        font-size: 12px;
    }
    
    .conversation-content small {
        font-size: 11px;
    }
    
    .card-header {
        padding: 1rem 0.75rem;
    }
    
    .card-header .card-title {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }
    
    .messaging-body {
        flex-direction: column;
    }
    
    .conversations-panel {
        display: block;
    }
    
    .chat-panel-right {
        display: none;
    }
    
    .messaging-body.show-chat .conversations-panel {
        display: none;
    }
    
    .messaging-body.show-chat .chat-panel-right {
        display: flex;
    }
}
/* ===============================
   MESSAGE REACTIONS STYLES
   =============================== */

.message-reactions {
    margin-top: 8px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
}

.reaction-item {
    background: rgba(102, 126, 234, 0.1);
    border: 1px solid rgba(102, 126, 234, 0.3);
    border-radius: 12px;
    padding: 4px 8px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}

.reaction-item:hover {
    background: rgba(102, 126, 234, 0.2);
    border-color: rgba(102, 126, 234, 0.5);
    transform: translateY(-1px);
}

.reaction-item.user-reacted {
    background: rgba(102, 126, 234, 0.25);
    border-color: rgba(102, 126, 234, 0.6);
    font-weight: 600;
}

.reaction-emoji {
    font-size: 14px;
    line-height: 1;
}

.reaction-count {
    font-size: 11px;
    font-weight: 500;
    color: #667eea;
}

.add-reaction-btn {
    background: rgba(0, 0, 0, 0.05);
    border: 1px dashed rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    padding: 4px 8px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 24px;
    opacity: 0.7;
}

.add-reaction-btn:hover {
    background: rgba(102, 126, 234, 0.1);
    border-color: rgba(102, 126, 234, 0.3);
    color: #667eea;
    opacity: 1;
}

.message:hover .add-reaction-btn {
    opacity: 1;
}

/* Reaction Picker */
.reaction-picker {
    position: absolute;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(0, 0, 0, 0.1);
    padding: 8px;
    display: none;
    z-index: 1000;
    animation: reactionPickerAppear 0.2s ease;
}

@keyframes reactionPickerAppear {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(10px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.reaction-picker.show {
    display: flex;
    gap: 4px;
}

.reaction-option {
    font-size: 20px;
    padding: 6px 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}

.reaction-option:hover {
    background: rgba(102, 126, 234, 0.1);
    transform: scale(1.2);
}

/* Sent message reactions (right aligned) */
.message.sent .message-reactions {
    justify-content: flex-end;
}

/* Received message reactions (left aligned) */
.message.received .message-reactions {
    justify-content: flex-start;
}

/* ===============================
   TYPING INDICATORS STYLES
   =============================== */

.typing-indicator {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    margin: 10px 0;
    background: rgba(102, 126, 234, 0.05);
    border-radius: 18px;
    max-width: 200px;
    animation: typingSlideIn 0.3s ease;
    font-size: 14px;
    color: #667eea;
}

@keyframes typingSlideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.typing-indicator .typing-dots {
    display: flex;
    align-items: center;
    margin-left: 8px;
}

.typing-indicator .typing-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #667eea;
    margin: 0 2px;
    animation: typingDot 1.4s infinite;
}

.typing-indicator .typing-dot:nth-child(1) { animation-delay: 0s; }
.typing-indicator .typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator .typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingDot {
    0%, 60%, 100% {
        transform: scale(1);
        opacity: 0.5;
    }
    30% {
        transform: scale(1.2);
        opacity: 1;
    }
}

.typing-indicator .typing-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: bold;
    margin-right: 8px;
    flex-shrink: 0;
}

/* Header typing status */
.chat-header-typing {
    color: #667eea;
    font-size: 11px;
    font-style: italic;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

/* ===============================
   FILE DRAG & DROP STYLES
   =============================== */

.drag-drop-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(102, 126, 234, 0.9);
    backdrop-filter: blur(5px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    border-radius: 12px;
    border: 3px dashed rgba(255, 255, 255, 0.8);
    animation: dragOverlayAppear 0.3s ease;
}

@keyframes dragOverlayAppear {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.drag-drop-content {
    text-align: center;
    color: white;
    font-size: 18px;
    font-weight: 600;
}

.drag-drop-content i {
    font-size: 48px;
    margin-bottom: 16px;
    animation: dragIcon 1s ease-in-out infinite alternate;
}

@keyframes dragIcon {
    from {
        transform: translateY(-5px);
    }
    to {
        transform: translateY(5px);
    }
}

.drag-drop-content p {
    margin: 0;
    font-size: 16px;
    opacity: 0.9;
}

/* Drag over states */
.chat-messages.drag-over {
    border: 2px dashed #667eea;
    background: rgba(102, 126, 234, 0.02);
}

.message-input-container.drag-over {
    border: 2px dashed #667eea;
    background: rgba(102, 126, 234, 0.05);
}

/* File upload enhancement */
.file-input-enhanced {
    position: relative;
}

.file-upload-hint {
    position: absolute;
    top: -30px;
    right: 0;
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    white-space: nowrap;
    opacity: 0;
    transition: all 0.3s ease;
    pointer-events: none;
}

.message-input-container:hover .file-upload-hint {
    opacity: 1;
    transform: translateY(-2px);
}

</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="messaging-wrapper">
            <div class="card messaging-container">
            @if(Auth::user()->role === 'patient')
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-comments mr-2"></i>
                    Messages
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" onclick="startNewConversation()">
                        <i class="fas fa-plus mr-1"></i> New Message
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ml-2" onclick="toggleArchivedView()" id="archive-toggle-btn">
                        <i class="fas fa-archive mr-1"></i> Archived
                    </button>
                    <!-- Sound control will be added here by JavaScript for better UX -->
                </div>
            </div>
            @else
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-comments mr-2"></i>
                    Patient Messages
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" onclick="showNewConversationModal()">
                        <i class="fas fa-plus mr-1"></i> New Conversation
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ml-2" onclick="toggleArchivedView()" id="archive-toggle-btn">
                        <i class="fas fa-archive mr-1"></i> Archived
                    </button>
                    <!-- Sound control will be added here by JavaScript for better UX -->
                </div>
            </div>
            @endif
                <div class="card-body p-0 messaging-body" id="messaging-body">
                    <div class="row no-gutters h-100">
                        <!-- Conversations Panel -->
                        <div class="col-md-4 conversations-panel" id="conversations-panel">
                            @if($conversations->count() > 0)
                                @foreach($conversations as $conversation)
                                    <div class="conversation-item {{ $selectedConversationId == $conversation->id ? 'active' : '' }}" 
                                         onclick="selectConversation({{ $conversation->id }})">
                                        <div class="d-flex align-items-center">
                                            <div class="conversation-avatar mr-3">
                                                @if(Auth::user()->role === 'patient')
                                                    @php $otherUser = $conversation->admin; @endphp
                                                @else
                                                    @php $otherUser = $conversation->patient; @endphp
                                                @endif
                                                
                                                @if($otherUser && $otherUser->profile_picture)
                                                    <img src="{{ asset('storage/' . $otherUser->profile_picture) }}" 
                                                         alt="{{ $otherUser->name }}" 
                                                         class="conversation-avatar-img">
                                                @else
                                                    <div class="default-avatar" style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">
                                                        {{ strtoupper(substr($otherUser->name ?? 'U', 0, 2)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 conversation-content">
                                                <h6 class="mb-1">
                                                    @if(Auth::user()->role === 'patient')
                                                        {{ $conversation->admin->name ?? 'Medical Staff' }}
                                                    @else
                                                        {{ $conversation->patient->name }}
                                                    @endif
                                                </h6>
                                                <p class="mb-1 text-muted small">
                                                    @if($conversation->latestMessage)
                                                        {{ Str::limit($conversation->latestMessage->message, 50) }}
                                                    @else
                                                        No messages yet
                                                    @endif
                                                </p>
                                                <small class="text-muted">
                                                    {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : 'Recently' }}
                                                </small>
                                            </div>
                                            <div class="ml-2 conversation-actions">
                                                @if($conversation->messages->where('is_read', false)->count() > 0)
                                                    <span class="badge badge-info badge-pill unread-count" id="unread-{{ $conversation->id }}">
                                                        {{ $conversation->messages->where('is_read', false)->count() }}
                                                    </span>
                                                @endif
                                                
                                                <!-- Archive Button (hidden by default, shown on swipe) -->
                                                <div class="archive-action" data-conversation-id="{{ $conversation->id }}" style="display: none;">
                                                    <button class="btn btn-danger btn-sm archive-btn">
                                                        <i class="fas fa-archive"></i> Archive
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="no-conversations">
                                    <i class="fas fa-comments fa-3x mb-3 text-muted"></i>
                                    <h5>No conversations yet</h5>
                                    @if(Auth::user()->role === 'patient')
                                        <p class="text-muted">Click "New Message" to start a conversation with medical staff.</p>
                                    @else
                                        <p class="text-muted">Patient conversations will appear here.</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Chat Panel -->
                        <div class="col-md-8 chat-panel-right" id="chat-panel-right">
                            @if($selectedConversation)
                                <!-- Chat Header -->
                                <div class="chat-header">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <button class="mobile-back-btn" id="mobile-back-btn" onclick="showConversationsList()">
                                            <i class="fas fa-arrow-left"></i>
                                        </button>
                                        <div class="chat-header-avatar mr-3">
                                            @if(Auth::user()->role === 'patient')
                                                @php $chatUser = $selectedConversation->admin; @endphp
                                            @else
                                                @php $chatUser = $selectedConversation->patient; @endphp
                                            @endif
                                            
                                            @if($chatUser && $chatUser->profile_picture)
                                                <img src="{{ asset('storage/' . $chatUser->profile_picture) }}" 
                                                     alt="{{ $chatUser->name }}" 
                                                     class="chat-header-avatar-img">
                                            @else
                                                <div class="default-avatar" style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">
                                                    {{ strtoupper(substr($chatUser->name ?? 'U', 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">
                                                @if(Auth::user()->role === 'patient')
                                                    {{ $selectedConversation->admin->name ?? 'Medical Staff' }}
                                                @else
                                                    {{ $selectedConversation->patient->name }}
                                                @endif
                                            </h6>
                                            <small class="text-muted">
                                                <span id="user-status">
                                                    @if(Auth::user()->role === 'admin')
                                                        Patient ID: {{ $selectedConversation->patient->id }}
                                                    @else
                                                        @if($chatUser && $chatUser->role === 'admin')
                                                            Medical Staff
                                                        @endif
                                                    @endif
                                                </span>
                                                <span id="typing-status" class="chat-header-typing" style="display: none;">
                                                    is typing...
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                    <!-- Chat header actions removed for cleaner UI -->
                                    <!-- Auto-polling handles message refreshing automatically -->
                                </div>

                                <!-- Messages Area -->
                                <div class="chat-messages" id="chat-messages" style="position: relative;">
                                    @include('messaging.partials.messages', ['messages' => $messages])
                                    
                                    <!-- Drag & Drop Overlay -->
                                    <div class="drag-drop-overlay" id="drag-drop-overlay">
                                        <div class="drag-drop-content">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>Drop files here to upload</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modern Message Input -->
                                <div class="modern-message-input">
                                    <form id="message-form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="conversation_id" value="{{ $selectedConversation->id }}">
                                        <input type="hidden" name="priority" value="normal">
                                        
                                        <!-- File Preview Area -->
                                        <div class="file-preview-container" id="file-preview-container" style="display: none;">
                                            <div class="file-preview-card" id="file-preview-card">
                                                <div class="file-info">
                                                    <div class="file-icon" id="file-icon">
                                                        <i class="fas fa-file"></i>
                                                    </div>
                                                    <div class="file-details">
                                                        <span class="file-name" id="file-name">filename.ext</span>
                                                        <span class="file-size" id="file-size">0 KB</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="remove-file-btn" onclick="removeAttachment()">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Priority indicator removed for cleaner UI -->
                                        
                                        <!-- Main Input Container -->
                                        <div class="message-input-container">
                                            <div class="input-actions file-input-enhanced">
                                                <!-- File Upload Button -->
                                                <input type="file" name="attachment" id="file-input" class="d-none" 
                                                       accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.txt,.rtf,.mp4,.avi,.mov,.wmv,.mp3,.wav,.ogg,.m4a,.zip,.rar,.7z">
                                                <button type="button" class="action-btn" id="attachment-btn" onclick="document.getElementById('file-input').click()">
                                                    <i class="fas fa-paperclip"></i>
                                                </button>
                                                
                                                <!-- Upload Hint -->
                                                <div class="file-upload-hint">
                                                    Or drag & drop files
                                                </div>
                                                
                                                <!-- Priority functionality removed for cleaner UI -->
                                            </div>
                                            
                                            <!-- Message Input -->
                                            <div class="message-input-wrapper">
                                                <textarea class="message-textarea" name="message" id="message-textarea" 
                                                         placeholder="Type your message..." rows="1"></textarea>
                                                <button type="submit" class="send-btn" id="send-btn">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="no-messages">
                                    <i class="fas fa-comment-dots fa-4x mb-4"></i>
                                    <h4>Select a conversation</h4>
                                    <p class="text-muted">Choose a conversation from the sidebar to start messaging.</p>
                                    @if(Auth::user()->role === 'patient')
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-primary btn-lg" onclick="startNewConversation()">
                                                <i class="fas fa-plus mr-2"></i>Start New Conversation
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="image-modal">
    <div class="image-modal-content">
        <span class="image-modal-close" onclick="closeImageModal()">&times;</span>
        <img id="modalImage" src="" alt="">
        <div class="mt-2 text-center">
            <h6 id="modalImageName"></h6>
        </div>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div id="archiveConfirmModal" class="archive-modal">
    <div class="archive-modal-overlay"></div>
    <div class="archive-modal-content">
        <div class="archive-modal-header">
            <div class="archive-modal-icon">
                <i class="fas fa-archive"></i>
            </div>
            <h4 class="archive-modal-title">Archive Conversation</h4>
        </div>
        <div class="archive-modal-body">
            <p class="archive-modal-message">Are you sure you want to archive this conversation with <strong id="archiveUserName">this user</strong>?</p>
            <div class="archive-modal-info">
                <i class="fas fa-info-circle"></i>
                <small>You can find archived conversations in the "Archived" section and unarchive them anytime.</small>
            </div>
        </div>
        <div class="archive-modal-footer">
            <button class="btn btn-secondary archive-modal-cancel" id="archiveCancelBtn">
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
            <button class="btn btn-danger archive-modal-confirm" id="archiveConfirmBtn">
                <i class="fas fa-archive mr-2"></i>Archive
            </button>
        </div>
    </div>
</div>

@if(Auth::user()->role === 'admin')
<!-- New Conversation Modal -->
<div id="newConversationModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h4 class="modal-title">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Start New Conversation
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newConversationForm">
                    @csrf
                    <input type="hidden" name="patient_id" id="selectedPatientId">
                    
                    <!-- Patient Search -->
                    <div class="form-group">
                        <label for="patientSearch">Search Patient</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="patientSearch" placeholder="Search by name or email..." autocomplete="off">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Start typing to search for patients</small>
                    </div>
                    
                    <!-- Patient Results -->
                    <div class="form-group" id="patientResults" style="display: none;">
                        <label>Select Patient:</label>
                        <div class="patient-list" id="patientList" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                            <!-- Patient results will be populated here -->
                        </div>
                    </div>
                    
                    <!-- Selected Patient Display -->
                    <div class="form-group" id="selectedPatientDisplay" style="display: none;">
                        <label>Selected Patient:</label>
                        <div class="alert alert-info" id="selectedPatientInfo">
                            <!-- Selected patient info will be displayed here -->
                        </div>
                    </div>
                    
                    <!-- Initial Message -->
                    <div class="form-group">
                        <label for="initialMessage">Initial Message (Optional)</label>
                        <textarea class="form-control" id="initialMessage" name="initial_message" rows="4" 
                                  placeholder="Type your first message to the patient (optional)..."></textarea>
                        <small class="form-text text-muted">You can start with a message or leave blank to just open the conversation</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="startConversationBtn" disabled>
                    <i class="fas fa-comments mr-1"></i>Start Conversation
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('adminlte_js')
<script>
function selectConversation(conversationId) {
    // Check if mobile view
    if (window.innerWidth <= 768) {
        // Show chat panel on mobile
        $('.messaging-body').addClass('show-chat');
    }
    
    @if(Auth::user()->role === 'patient')
        window.location.href = '{{ route("patient.messages.index") }}?conversation=' + conversationId;
    @else
        window.location.href = '{{ route("admin.messages.index") }}?conversation=' + conversationId;
    @endif
}

// Mobile navigation functions
function showConversationsList() {
    $('.messaging-body').removeClass('show-chat');
}

function showChatView() {
    $('.messaging-body').addClass('show-chat');
}

function startNewConversation() {
    $.ajax({
        url: '{{ route("patient.messages.start") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            console.log('Response:', response);
            if (response.success) {
                window.location.href = response.redirect_url;
            } else {
                alert('Failed to start conversation: ' + response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr, status, error);
            console.error('Response Text:', xhr.responseText);
            
            let errorMessage = 'Failed to start conversation. Please try again.';
            
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            
            alert(errorMessage);
        }
    });
}

@if(Auth::user()->role === 'admin')
// Admin new conversation functions
let patientSearchTimeout;
let selectedPatientData = null;

function showNewConversationModal() {
    $('#newConversationModal').modal('show');
    $('#patientSearch').focus();
}

function searchPatients(query) {
    if (query.length < 2) {
        $('#patientResults').hide();
        $('#patientList').empty();
        return;
    }
    
    $.ajax({
        url: '{{ route("admin.messages.patientsList") }}',
        type: 'GET',
        data: {
            search: query,
            limit: 20
        },
        success: function(response) {
            displayPatientResults(response.patients);
        },
        error: function(xhr, status, error) {
            console.error('Error searching patients:', error);
            showNotification('Failed to search patients. Please try again.', 'error');
        }
    });
}

function displayPatientResults(patients) {
    const patientList = $('#patientList');
    patientList.empty();
    
    if (patients.length === 0) {
        patientList.append('<div class="p-3 text-center text-muted">No patients found matching your search</div>');
    } else {
        patients.forEach(function(patient) {
            const patientItem = $(`
                <div class="patient-item p-3 border-bottom" data-patient-id="${patient.id}" style="cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <div class="patient-avatar mr-3">
                            ${patient.profile_picture ? 
                                `<img src="{{ asset('storage/') }}/${patient.profile_picture}" 
                                      alt="${patient.name}" 
                                      style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">` :
                                `<div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    ${patient.name.substring(0, 2).toUpperCase()}
                                </div>`
                            }
                        </div>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold">${patient.name}</div>
                            <div class="text-muted small">${patient.email}</div>
                            ${patient.has_existing_conversation ? 
                                '<span class="badge badge-info mt-1">Existing Conversation</span>' : 
                                '<span class="badge badge-success mt-1">New</span>'
                            }
                        </div>
                    </div>
                </div>
            `);
            
            patientItem.on('click', function() {
                selectPatient(patient);
            });
            
            patientList.append(patientItem);
        });
    }
    
    $('#patientResults').show();
}

function selectPatient(patient) {
    selectedPatientData = patient;
    $('#selectedPatientId').val(patient.id);
    
    // Update selected patient display
    const selectedInfo = `
        <div class="d-flex align-items-center">
            <div class="patient-avatar mr-3">
                ${patient.profile_picture ? 
                    `<img src="{{ asset('storage/') }}/${patient.profile_picture}" 
                          alt="${patient.name}" 
                          style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">` :
                    `<div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px;">
                        ${patient.name.substring(0, 2).toUpperCase()}
                    </div>`
                }
            </div>
            <div class="flex-grow-1">
                <div class="font-weight-bold h5 mb-1">${patient.name}</div>
                <div class="text-muted">${patient.email}</div>
                ${patient.has_existing_conversation ? 
                    '<span class="badge badge-warning mt-1"><i class="fas fa-info-circle mr-1"></i>You already have a conversation with this patient</span>' : 
                    '<span class="badge badge-success mt-1"><i class="fas fa-plus-circle mr-1"></i>New conversation</span>'
                }
            </div>
        </div>
    `;
    
    $('#selectedPatientInfo').html(selectedInfo);
    $('#selectedPatientDisplay').show();
    $('#patientResults').hide();
    $('#patientSearch').val(patient.name);
    $('#startConversationBtn').prop('disabled', false);
    
    // Update button text based on existing conversation
    if (patient.has_existing_conversation) {
        $('#startConversationBtn').html('<i class="fas fa-comment-dots mr-1"></i>Open Conversation');
    } else {
        $('#startConversationBtn').html('<i class="fas fa-comments mr-1"></i>Start Conversation');
    }
}

function startConversationWithPatient() {
    if (!selectedPatientData) {
        showNotification('Please select a patient first.', 'warning');
        return;
    }
    
    const initialMessage = $('#initialMessage').val().trim();
    const $btn = $('#startConversationBtn');
    const originalText = $btn.html();
    
    // Show loading state
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Starting...');
    
    $.ajax({
        url: '{{ route("admin.messages.startWithPatient") }}',
        type: 'POST',
        data: {
            _token: $('input[name="_token"]').val(),
            patient_id: selectedPatientData.id,
            initial_message: initialMessage
        },
        success: function(response) {
            if (response.success) {
                $('#newConversationModal').modal('hide');
                showNotification(response.message, 'success');
                
                // Redirect to the conversation
                setTimeout(function() {
                    window.location.href = response.redirect_url;
                }, 1000);
            } else {
                showNotification(response.error || 'Failed to start conversation', 'error');
                $btn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error starting conversation:', error);
            let errorMessage = 'Failed to start conversation. Please try again.';
            
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            
            showNotification(errorMessage, 'error');
            $btn.prop('disabled', false).html(originalText);
        }
    });
}

// Reset modal when closed
$('#newConversationModal').on('hidden.bs.modal', function() {
    $('#patientSearch').val('');
    $('#initialMessage').val('');
    $('#patientResults').hide();
    $('#selectedPatientDisplay').hide();
    $('#patientList').empty();
    $('#selectedPatientId').val('');
    $('#startConversationBtn').prop('disabled', true).html('<i class="fas fa-comments mr-1"></i>Start Conversation');
    selectedPatientData = null;
});

// Patient search with debounce
$('#patientSearch').on('input', function() {
    const query = $(this).val().trim();
    
    // Clear previous timeout
    if (patientSearchTimeout) {
        clearTimeout(patientSearchTimeout);
    }
    
    // Hide selected patient if user is typing again
    if (query !== (selectedPatientData ? selectedPatientData.name : '')) {
        $('#selectedPatientDisplay').hide();
        selectedPatientData = null;
        $('#selectedPatientId').val('');
        $('#startConversationBtn').prop('disabled', true);
    }
    
    // Set new timeout
    patientSearchTimeout = setTimeout(function() {
        searchPatients(query);
    }, 300);
});

// Start conversation button click
$('#startConversationBtn').on('click', function() {
    startConversationWithPatient();
});
@endif

$(document).ready(function() {
    // Debug info
    console.log('User role:', '{{ Auth::user()->role }}');
    console.log('Selected conversation:', '{{ $selectedConversationId ?? "none" }}');
    console.log('Message form exists:', $('#message-form').length > 0);
    console.log('Archive functionality loaded');
    
    // Initialize UI enhancements
    initializeUIEnhancements();
    
    // Test swipe functionality after a short delay
    setTimeout(function() {
        const conversationItems = $('.conversation-item').length;
        const archiveActions = $('.archive-action').length;
        
        console.log('Swipe-to-archive functionality loaded');
        console.log('Conversation items found:', conversationItems);
        console.log('Archive actions found:', archiveActions);
        
        if (conversationItems > 0) {
            console.log('✅ Ready for long press + swipe left to archive');
            
            // Add instruction tooltip after a few seconds
            setTimeout(() => {
                if ($('.conversation-item').length > 0) {
                    showNotification('💡 Tip: Long press + swipe left on conversations to archive', 'info');
                }
            }, 3000);
        }
    }, 1000);
    
    // Handle message form submission
    $('#message-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        
        console.log('🔥 Message form submitted');
        console.log('📍 Form submission triggered at:', new Date().toISOString());
        console.log('👤 User role:', '{{ Auth::user()->role }}');
        const messageTextarea = form.find('#message-textarea');
        const message = messageTextarea.val().trim();
        const fileInput = form.find('#file-input')[0];
        const hasFile = fileInput && fileInput.files.length > 0;
        
        console.log('📝 Message text:', message ? `"${message}"` : '(empty)');
        console.log('📎 Has file:', hasFile);
        console.log('📁 File input element:', fileInput);
        console.log('📄 Files count:', fileInput ? fileInput.files.length : 'No input element');
        console.log('📄 File details:', hasFile ? {
            name: fileInput.files[0].name,
            size: fileInput.files[0].size,
            type: fileInput.files[0].type
        } : 'No file selected');
        console.log('🎯 Form triggered by:', document.activeElement ? document.activeElement.tagName + (document.activeElement.id ? '#' + document.activeElement.id : '') : 'unknown');
        
        // Debug attachment button state
        const attachmentBtn = $('#attachment-btn');
        console.log('📎 Attachment button state:', {
            hasActiveClass: attachmentBtn.hasClass('active'),
            classes: attachmentBtn.attr('class'),
            isVisible: attachmentBtn.is(':visible')
        });
        
        // Debug file preview state
        console.log('📋 File preview container:', {
            isVisible: $('#file-preview-container').is(':visible'),
            hasContent: $('#file-preview-container').children().length > 0
        });
        
        // Check if this might be an accidental submission
        if (!message && !hasFile) {
            console.warn('🚨 Potential accidental submission detected - no content provided');
        }
        
        // Either message or file is required
        if (!message && !hasFile) {
            console.log('Validation failed: no message or file');
            showNotification('Please enter a message or select a file to attach.', 'warning');
            return;
        }
        
        // Additional validation: don't send if message is just whitespace
        if (message && message.trim() === '') {
            console.log('Validation failed: message is only whitespace');
            showNotification('Please enter a message or select a file to attach.', 'warning');
            return;
        }
        
        // Final safeguard: Double-check we have content to send
        const finalMessage = message ? message.trim() : '';
        if (!finalMessage && !hasFile) {
            console.error('❌ FINAL VALIDATION FAILED: No content to send');
            console.log('Final message check:', { finalMessage, hasFile, originalMessage: message });
            showNotification('Cannot send empty message. Please type a message or attach a file.', 'error');
            return;
        }
        
        @if(Auth::user()->role === 'patient')
            const ajaxUrl = '{{ route("patient.messages.send") }}';
        @else
            const ajaxUrl = '{{ route("admin.messages.send") }}';
        @endif
        
        // Prepare form data
        const formData = new FormData();
        
        // Get CSRF token
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
        console.log('🔑 CSRF Token:', csrfToken ? 'Found' : 'MISSING!');
        
        if (csrfToken) {
            formData.append('_token', csrfToken);
        } else {
            console.error('🚨 CRITICAL: CSRF token not found!');
        }
        
        // Get conversation ID
        const conversationId = $('input[name="conversation_id"]').val();
        console.log('💬 Conversation ID:', conversationId);
        
        if (conversationId) {
            formData.append('conversation_id', conversationId);
        } else {
            console.error('🚨 CRITICAL: Conversation ID not found!');
        }
        
        if (message) {
            formData.append('message', message);
            console.log('📝 Adding message to FormData:', message);
        }
        
        if (hasFile) {
            formData.append('attachment', fileInput.files[0]);
            console.log('File attached:', fileInput.files[0].name, fileInput.files[0].size, 'bytes');
        }
        
        // Add priority
        const priority = $('input[name="priority"]').val() || 'normal';
        formData.append('priority', priority);
        console.log('Priority:', priority);
        
        console.log('AJAX URL:', ajaxUrl);
        console.log('🛠️ FormData entries:');
        let entryCount = 0;
        for (let pair of formData.entries()) {
            entryCount++;
            console.log(`  ${entryCount}. ${pair[0]}: ${typeof pair[1] === 'object' ? `[File: ${pair[1].name}]` : pair[1]}`);
        }
        console.log(`📊 Total FormData entries: ${entryCount}`);
        
        // Validate FormData before sending
        if (entryCount === 0) {
            console.error('🚨 CRITICAL: FormData is completely empty!');
            showNotification('Form data error. Please refresh the page and try again.', 'error');
            return;
        }
        
        // Add loading state and prevent double submission
        const sendButton = form.find('#send-btn');
        const originalButtonHtml = sendButton.html();
        
        // Check if already sending
        if (sendButton.prop('disabled')) {
            console.log('🚨 PREVENTED: Send button already disabled (request in progress)');
            return;
        }
        
        sendButton.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        messageTextarea.prop('disabled', true);
        
        console.log('📵 Button disabled, request starting...');
        
        // Show upload progress for files
        if (hasFile) {
            showUploadProgress();
        }
        
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                if (hasFile) {
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            updateUploadProgress(percentComplete);
                            console.log('Upload progress:', percentComplete + '%');
                        }
                    }, false);
                }
                return xhr;
            },
            success: function(response) {
                console.log('Message sent successfully:', response);
                if (response.success) {
                    // Add the new message with animation
                    addMessageWithAnimation(response.html);
                    
                    // Track the message ID to prevent duplication from polling
                    const $newMessageContainer = $('<div>').html(response.html);
                    const $newMessage = $newMessageContainer.find('.message[data-message-id]').first();
                    const messageId = $newMessage.attr('data-message-id');
                    
                    if (messageId) {
                        existingMessageIds.add(messageId);
                        console.log('🏷️ Tracked sent message ID:', messageId);
                        console.log('📊 Total tracked message IDs:', existingMessageIds.size);
                    } else {
                        console.warn('⚠️ No message ID found in sent message response');
                    }
                    
                    // Set flag to prevent polling duplication for a short time
                    justSentMessage = true;
                    setTimeout(() => {
                        justSentMessage = false;
                        console.log('▶️ Update polling resumed after send');
                    }, 2000); // Wait 2 seconds before resuming normal polling
                    
                    // Clear input and reset form
                    messageTextarea.val('').trigger('input'); // Trigger input to resize
                    resetFileUpload();
                    resetPriority();
                    
                    showNotification('Message sent successfully!', 'success');
                    
                    // Add success haptic feedback (if supported)
                    if (navigator.vibrate) {
                        navigator.vibrate(50);
                    }
                    
                    console.log('📊 Updated message count after send:', lastMessageCount);
                } else {
                    showNotification('Failed to send message: ' + response.error, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Message send error:', xhr, status, error);
                console.error('Response text:', xhr.responseText);
                console.error('Status code:', xhr.status);
                
                let errorMessage = 'Failed to send message. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showNotification(errorMessage, 'error');
            },
            complete: function() {
                // Restore button state
                sendButton.html(originalButtonHtml).prop('disabled', false);
                messageTextarea.prop('disabled', false).focus();
                hideUploadProgress();
                
                console.log('✅ Request completed, button re-enabled');
            }
        });
    });
    
    // Handle Enter key in message textarea
    $('#message-textarea').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            console.log('🚀 Enter key pressed - submitting form');
            
            // Check if button is already disabled (request in progress)
            const sendButton = $('#send-btn');
            if (!sendButton.prop('disabled')) {
                $(this).closest('form').trigger('submit');
            } else {
                console.log('🚨 Prevented submission - button disabled');
            }
        }
    });
    
    // Auto-focus message input
    $('#message-textarea').focus();
    
    // Scroll to bottom on load
    scrollToBottom();
    
    // Enhanced real-time message and reaction checking
    @if($selectedConversation)
    console.log('✅ Enhanced real-time messaging enabled');
    
    let isTyping = false;
    let typingTimeout;
    let justSentMessage = false; // Flag to prevent duplicate messages after sending
    let existingMessageIds = new Set(); // Track message IDs to prevent duplicates
    let lastReactionCheck = Date.now(); // Track when reactions were last checked
    
    // Initialize existing message IDs
    console.log('🆔 Initializing existing message IDs...');
    $('.message[data-message-id]').each(function() {
        const messageId = $(this).attr('data-message-id');
        if (messageId) {
            existingMessageIds.add(messageId);
        }
    });
    console.log(`🏷️ Initialized ${existingMessageIds.size} existing message IDs:`, Array.from(existingMessageIds));
    
    // Enhanced polling that checks both messages AND reactions
    function checkForUpdates() {
        // Skip polling temporarily if we just sent a message to prevent duplication
        if (justSentMessage) {
            console.log('⏸️ Skipping update check - just sent a message');
            return;
        }
        
        @if(Auth::user()->role === 'patient')
            const messagesUrl = '{{ route("patient.messages.messages", $selectedConversation->id) }}';
        @else
            const messagesUrl = '{{ route("admin.messages.messages", $selectedConversation->id) }}';
        @endif
        
        $.ajax({
            url: messagesUrl + '?include_reactions=1', // Add parameter to include reaction updates
            type: 'GET',
            success: function(response) {
                // Parse response into temp container
                const $tempContainer = $('<div>').html(response.html);
                const $allMessages = $tempContainer.find('.message[data-message-id]');
                
                let hasNewContent = false;
                
                // 1. Check for NEW messages
                const newMessages = [];
                $allMessages.each(function() {
                    const messageId = $(this).attr('data-message-id');
                    if (messageId && !existingMessageIds.has(messageId)) {
                        newMessages.push({
                            id: messageId,
                            html: this.outerHTML,
                            element: $(this)
                        });
                        existingMessageIds.add(messageId);
                        hasNewContent = true;
                    }
                });
                
                // 2. Check for REACTION updates on existing messages
                $allMessages.each(function() {
                    const messageId = $(this).attr('data-message-id');
                    if (messageId && existingMessageIds.has(messageId)) {
                        const newReactionsHtml = $(this).find('.message-reactions').html();
                        const existingReactionsHtml = $(`.message[data-message-id="${messageId}"] .message-reactions`).html();
                        
                        // Compare reaction content
                        if (newReactionsHtml !== existingReactionsHtml) {
                            console.log(`🔄 Reactions updated for message ${messageId}`);
                            $(`.message[data-message-id="${messageId}"] .message-reactions`).html(newReactionsHtml);
                            hasNewContent = true;
                        }
                    }
                });
                
                // 3. Add new messages with animation
                if (newMessages.length > 0) {
                    console.log(`🆕 Found ${newMessages.length} new messages:`, newMessages.map(m => m.id));
                    
                    newMessages.forEach((message, index) => {
                        setTimeout(() => {
                            addMessageWithAnimation(message.html);
                            console.log(`➕ Added new message ID: ${message.id}`);
                        }, index * 100);
                    });
                    
                    // Play notification sound for new messages
                    if (notificationManager && notificationManager.playNotificationSound) {
                        notificationManager.playNotificationSound();
                    }
                }
                
                if (hasNewContent) {
                    console.log('✨ Content updated!');
                } else {
                    console.log('ℹ️ No new content');
                }
                
                lastReactionCheck = Date.now();
            },
            error: function(xhr, status, error) {
                console.error('Failed to check for updates:', error);
            }
        });
    }
    
    // Start enhanced polling every 3 seconds (faster for better real-time feel)
    const updateInterval = setInterval(function() {
        if (!isTyping && !document.hidden) {
            checkForUpdates();
        }
    }, 3000);
    
    // Also check for typing indicators every 2 seconds
    const typingCheckInterval = setInterval(function() {
        if (!document.hidden) {
            pollTypingStatus();
        }
    }, 2000);
    
    // Pause message checking when user is typing (but keep reaction checking)
    $('#message-textarea').on('input focus keydown', function() {
        isTyping = true;
        clearTimeout(typingTimeout);
        
        typingTimeout = setTimeout(() => {
            isTyping = false;
            console.log('▶️ Resumed full update checking');
        }, 2000); // Resume after 2 seconds of no typing
    });
    
    // Manual refresh function removed - auto-polling handles message updates
    // Messages are automatically checked every 5 seconds when user is not typing
    @endif
});

// Notification Management System
class NotificationManager {
    constructor() {
        this.soundEnabled = localStorage.getItem('messageSoundEnabled') !== 'false';
        this.notificationPermission = 'default';
        this.notificationSound = null;
        this.init();
    }
    
    async init() {
        // Request notification permission
        if ('Notification' in window) {
            this.notificationPermission = await Notification.requestPermission();
        }
        
        // Create notification sound
        this.createNotificationSound();
        
        // Add sound control button
        this.addSoundControl();
        
        console.log('Notification Manager initialized', {
            permission: this.notificationPermission,
            soundEnabled: this.soundEnabled
        });
    }
    
    createNotificationSound() {
        // Create a subtle notification sound using Web Audio API
        if ('AudioContext' in window || 'webkitAudioContext' in window) {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                const audioContext = new AudioContext();
                
                this.playNotificationSound = () => {
                    if (!this.soundEnabled) return;
                    
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();
                    
                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);
                    
                    oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                    oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
                    
                    gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                    
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.3);
                };
            } catch (e) {
                console.warn('Audio context not available');
                this.playNotificationSound = () => {};
            }
        }
    }
    
    addSoundControl() {
        const soundButton = $(`
            <button class="btn sound-control ${this.soundEnabled ? '' : 'muted'}" 
                    id="sound-control" 
                    title="${this.soundEnabled ? 'Mute' : 'Unmute'} notification sounds"
                    aria-label="${this.soundEnabled ? 'Mute' : 'Unmute'} notification sounds"
                    aria-pressed="${this.soundEnabled ? 'false' : 'true'}">
                <i class="fas ${this.soundEnabled ? 'fa-volume-up' : 'fa-volume-mute'}" aria-hidden="true"></i>
                <span class="sr-only">${this.soundEnabled ? 'Sound enabled' : 'Sound muted'}</span>
            </button>
        `);
        
        // Add to global messaging header since sound control affects all conversations
        // Priority: 1) Main messaging header (global), 2) Body fallback if header not available
        if ($('.card-tools').length > 0) {
            $('.card-tools').append(soundButton);
            console.log('✅ Sound control added to global messaging header');
        } else {
            // Final fallback: add to body (old behavior)
            $('body').append(soundButton);
            console.log('⚠️ Sound control added to body (fallback)');
        }
        
        $('#sound-control').on('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggleSound();
        });
    }
    
    toggleSound() {
        this.soundEnabled = !this.soundEnabled;
        localStorage.setItem('messageSoundEnabled', this.soundEnabled);
        
        const $button = $('#sound-control');
        const $icon = $button.find('i');
        const $srText = $button.find('.sr-only');
        
        if (this.soundEnabled) {
            $button.removeClass('muted')
                   .attr('title', 'Mute notification sounds')
                   .attr('aria-label', 'Mute notification sounds')
                   .attr('aria-pressed', 'false');
            $icon.removeClass('fa-volume-mute').addClass('fa-volume-up');
            $srText.text('Sound enabled');
            this.showNotificationStatus('Sound enabled 🔊', 'success');
        } else {
            $button.addClass('muted')
                   .attr('title', 'Unmute notification sounds')
                   .attr('aria-label', 'Unmute notification sounds')
                   .attr('aria-pressed', 'true');
            $icon.removeClass('fa-volume-up').addClass('fa-volume-mute');
            $srText.text('Sound muted');
            this.showNotificationStatus('Sound muted 🔇', 'info');
        }
    }
    
    showBrowserNotification(title, body, conversation) {
        if (this.notificationPermission === 'granted' && document.hidden) {
            const notification = new Notification(title, {
                body: body,
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                tag: `conversation-${conversation.id}`,
                requireInteraction: false,
                silent: false
            });
            
            notification.onclick = () => {
                window.focus();
                selectConversation(conversation.id);
                notification.close();
            };
            
            setTimeout(() => notification.close(), 5000);
        }
    }
    
    showNotificationStatus(message, type = 'success') {
        const $status = $(`
            <div class="notification-status ${type}">
                ${message}
            </div>
        `);
        
        $('body').append($status);
        $status.show();
        
        setTimeout(() => {
            $status.fadeOut(300, () => $status.remove());
        }, 2000);
    }
    
    notifyNewMessage(message, conversation) {
        console.log('New message notification:', message);
        
        // Play sound
        if (this.playNotificationSound) {
            this.playNotificationSound();
        }
        
        // Show browser notification
        const senderName = message.sender ? message.sender.name : 'Someone';
        const messageText = message.message || 'Sent an attachment';
        
        this.showBrowserNotification(
            `New message from ${senderName}`,
            messageText.substring(0, 100),
            conversation
        );
        
        // Update unread count
        this.updateUnreadCount(conversation.id);
        
        // Update page title
        this.updatePageTitle();
    }
    
    updateUnreadCount(conversationId) {
        // This would typically fetch from server, but for now we'll increment
        const $badge = $(`#unread-${conversationId}`);
        if ($badge.length) {
            const current = parseInt($badge.text()) || 0;
            $badge.text(current + 1).show();
        } else {
            // Create new badge if it doesn't exist
            $(`.conversation-item[onclick*="${conversationId}"] .conversation-actions`)
                .prepend(`<span class="badge badge-info badge-pill unread-count" id="unread-${conversationId}">1</span>`);
        }
    }
    
    updatePageTitle() {
        const totalUnread = $('.unread-count').length;
        const baseTitle = 'Messages | Bokod CMS';
        
        if (totalUnread > 0) {
            document.title = `(${totalUnread}) ${baseTitle}`;
        } else {
            document.title = baseTitle;
        }
    }
}

// Initialize notification manager
const notificationManager = new NotificationManager();

// Swipe to Archive Functionality
let touchStartX = 0;
let touchStartY = 0;
let touchCurrentX = 0;
let touchCurrentY = 0;
let longPressTimer = null;
let isDragging = false;
let isLongPressing = false;
let currentSwipedItem = null;

// Touch event handlers for swipe functionality
$(document).on('touchstart mousedown', '.conversation-item', function(e) {
    if (e.type === 'mousedown' && e.which !== 1) return; // Only left mouse button
    
    const touch = e.originalEvent.touches ? e.originalEvent.touches[0] : e.originalEvent;
    touchStartX = touch.clientX;
    touchStartY = touch.clientY;
    isDragging = false;
    isLongPressing = false;
    
    const $item = $(this);
    
    // Reset any currently swiped items
    if (currentSwipedItem && currentSwipedItem[0] !== this) {
        resetSwipeState(currentSwipedItem);
    }
    
    // Start long press timer
    longPressTimer = setTimeout(() => {
        if (!isDragging) {
            isLongPressing = true;
            $item.addClass('long-pressing');
            
            // Haptic feedback if supported
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
            
            console.log('Long press detected - swipe left to archive');
        }
    }, 500); // 500ms for long press
    
    // Prevent text selection
    e.preventDefault();
});

$(document).on('touchmove mousemove', function(e) {
    if (!longPressTimer && !isLongPressing && !isDragging) return;
    
    const touch = e.originalEvent.touches ? e.originalEvent.touches[0] : e.originalEvent;
    touchCurrentX = touch.clientX;
    touchCurrentY = touch.clientY;
    
    const deltaX = touchCurrentX - touchStartX;
    const deltaY = touchCurrentY - touchStartY;
    
    // Check if user is scrolling vertically (don't interfere with normal scrolling)
    if (Math.abs(deltaY) > Math.abs(deltaX) && Math.abs(deltaY) > 10) {
        clearTimeout(longPressTimer);
        longPressTimer = null;
        return;
    }
    
    // If moving horizontally after long press, start swiping
    if (isLongPressing && Math.abs(deltaX) > 10) {
        isDragging = true;
        const $item = $('.conversation-item.long-pressing');
        
        if ($item.length > 0) {
            $item.removeClass('long-pressing').addClass('swiping');
            
            // Only allow left swipe
            if (deltaX < 0) {
                const swipeDistance = Math.min(0, Math.max(deltaX, -120));
                $item.css('transform', `translateX(${swipeDistance}px)`);
                
                // Show archive button when swiped enough
                if (swipeDistance <= -60) {
                    $item.addClass('swiped-left');
                    currentSwipedItem = $item;
                }
            }
        }
    }
    
    // Clear long press if user moves too much initially
    if (!isDragging && (Math.abs(deltaX) > 15 || Math.abs(deltaY) > 15)) {
        clearTimeout(longPressTimer);
        longPressTimer = null;
        $('.conversation-item.long-pressing').removeClass('long-pressing');
    }
});

$(document).on('touchend mouseup', function(e) {
    clearTimeout(longPressTimer);
    longPressTimer = null;
    
    const $item = $('.conversation-item.long-pressing, .conversation-item.swiping');
    
    if ($item.length > 0) {
        $item.removeClass('long-pressing');
        
        if (isDragging) {
            const deltaX = touchCurrentX - touchStartX;
            
            // If swiped left enough, keep it swiped
            if (deltaX <= -60) {
                $item.removeClass('swiping').addClass('swiped-left');
                $item.css('transform', 'translateX(-80px)');
                currentSwipedItem = $item;
            } else {
                // Snap back
                resetSwipeState($item);
            }
        } else {
            resetSwipeState($item);
        }
    }
    
    isDragging = false;
    isLongPressing = false;
});

// Reset swipe state
function resetSwipeState($item) {
    $item.removeClass('long-pressing swiping swiped-left')
         .css('transform', 'translateX(0)');
    
    if (currentSwipedItem && currentSwipedItem[0] === $item[0]) {
        currentSwipedItem = null;
    }
}

// Reset swipe when clicking elsewhere
$(document).on('click', function(e) {
    if (currentSwipedItem && !$(e.target).closest('.conversation-item, .archive-action').length) {
        resetSwipeState(currentSwipedItem);
    }
});

// Archive button click handler
$(document).on('click', '.archive-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const conversationId = $(this).closest('.archive-action').data('conversation-id');
    const $conversationItem = $(this).closest('.conversation-item');
    
    // Get user name for modal
    const userName = $conversationItem.find('.conversation-content h6').text().trim() || 'this user';
    
    // Show confirmation modal
    showArchiveModal(conversationId, $conversationItem, userName);
});

// Archive Modal Functions
let currentArchiveData = null;

function showArchiveModal(conversationId, $conversationItem, userName) {
    // Store current archive data
    currentArchiveData = {
        conversationId: conversationId,
        $conversationItem: $conversationItem,
        userName: userName
    };
    
    // Update modal content
    $('#archiveUserName').text(userName);
    
    // Show modal with animation
    const $modal = $('#archiveConfirmModal');
    $modal.addClass('show');
    
    // Focus on confirm button for accessibility
    setTimeout(() => {
        $('#archiveConfirmBtn').focus();
    }, 300);
    
    // Prevent body scroll
    $('body').addClass('modal-open');
    
    console.log('Archive modal shown for:', userName);
}

function hideArchiveModal() {
    const $modal = $('#archiveConfirmModal');
    
    // Add exit animation class if needed
    $modal.removeClass('show');
    
    // Reset current archive data
    if (currentArchiveData) {
        resetSwipeState(currentArchiveData.$conversationItem);
        currentArchiveData = null;
    }
    
    // Allow body scroll
    $('body').removeClass('modal-open');
    
    console.log('Archive modal hidden');
}

// Modal event handlers
$(document).on('click', '#archiveCancelBtn, .archive-modal-overlay', function(e) {
    e.preventDefault();
    hideArchiveModal();
});

$(document).on('click', '#archiveConfirmBtn', function(e) {
    e.preventDefault();
    
    if (currentArchiveData) {
        const { conversationId, $conversationItem } = currentArchiveData;
        
        // Add loading state to button
        const $confirmBtn = $(this);
        $confirmBtn.addClass('loading').prop('disabled', true);
        
        // Proceed with archiving
        archiveConversationWithModal(conversationId, $conversationItem);
    }
});

// Close modal with Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape' && $('#archiveConfirmModal').hasClass('show')) {
        hideArchiveModal();
    }
});

// Prevent modal content click from closing modal
$(document).on('click', '.archive-modal-content', function(e) {
    e.stopPropagation();
});

// Enhanced archive function for modal
function archiveConversationWithModal(conversationId, $conversationElement) {
    @if(Auth::user()->role === 'patient')
        const archiveUrl = `{{ route('patient.messages.archive', ':id') }}`.replace(':id', conversationId);
    @else
        const archiveUrl = `{{ route('admin.messages.archive', ':id') }}`.replace(':id', conversationId);
    @endif
    
    console.log('Archiving conversation:', conversationId);
    
    $.ajax({
        url: archiveUrl,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Archive response:', response);
            
            // Hide modal
            hideArchiveModal();
            
            if (response.success) {
                // Show success notification
                showNotification('Conversation archived successfully! \ud83d\udce6', 'success');
                
                // Animate out the conversation
                $conversationElement.addClass('archiving');
                
                setTimeout(() => {
                    $conversationElement.slideUp(400, function() {
                        $(this).remove();
                    });
                }, 300);
                
                // If this was the selected conversation, redirect
                @if($selectedConversation)
                if (conversationId == {{ $selectedConversation->id }}) {
                    setTimeout(() => {
                        window.location.href = @if(Auth::user()->role === 'patient') '{{ route("patient.messages.index") }}' @else '{{ route("admin.messages.index") }}' @endif;
                    }, 800);
                }
                @endif
                
            } else {
                showNotification('Failed to archive conversation: ' + (response.error || 'Unknown error'), 'error');
                resetSwipeState($conversationElement);
            }
        },
        error: function(xhr, status, error) {
            console.error('Archive error:', error, xhr);
            
            // Hide modal
            hideArchiveModal();
            
            let errorMessage = 'Failed to archive conversation. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            
            showNotification(errorMessage, 'error');
            resetSwipeState($conversationElement);
        },
        complete: function() {
            // Remove loading state from button (in case modal is still open)
            $('#archiveConfirmBtn').removeClass('loading').prop('disabled', false);
        }
    });
}

function archiveConversation(conversationId, $conversationElement) {
    @if(Auth::user()->role === 'patient')
        const archiveUrl = `{{ route('patient.messages.archive', ':id') }}`.replace(':id', conversationId);
    @else
        const archiveUrl = `{{ route('admin.messages.archive', ':id') }}`.replace(':id', conversationId);
    @endif
    
    // Add loading state
    $conversationElement.addClass('archiving').css('opacity', '0.5');
    
    $.ajax({
        url: archiveUrl,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Animate out the conversation
                $conversationElement.slideUp(300, function() {
                    $(this).remove();
                });
                
                showNotification('Conversation archived successfully', 'success');
                
                // If this was the selected conversation, show empty state
                @if($selectedConversation)
                if (conversationId == {{ $selectedConversation->id }}) {
                    window.location.href = @if(Auth::user()->role === 'patient') '{{ route("patient.messages.index") }}' @else '{{ route("admin.messages.index") }}' @endif;
                }
                @endif
                
            } else {
                showNotification('Failed to archive conversation', 'error');
                $conversationElement.removeClass('archiving').css('opacity', '1');
            }
        },
        error: function(xhr, status, error) {
            console.error('Archive error:', error);
            showNotification('Failed to archive conversation', 'error');
            $conversationElement.removeClass('archiving').css('opacity', '1');
        }
    });
}

// Add archived conversations view toggle
function toggleArchivedView() {
    const $body = $('.messaging-body');
    const $toggleBtn = $('#archive-toggle-btn');
    const isShowingArchived = $body.hasClass('showing-archived');
    
    if (isShowingArchived) {
        // Show normal conversations
        showNormalConversations();
        $toggleBtn.removeClass('showing-archived').html('<i class="fas fa-archive mr-1"></i> Archived');
    } else {
        // Show archived conversations
        showArchivedConversations();
        $toggleBtn.addClass('showing-archived').html('<i class="fas fa-inbox mr-1"></i> Active');
    }
}

function showArchivedConversations() {
    @if(Auth::user()->role === 'patient')
        const archivedUrl = '{{ route("patient.messages.archived") }}';
    @else
        const archivedUrl = '{{ route("admin.messages.archived") }}';
    @endif
    
    $.ajax({
        url: archivedUrl,
        type: 'GET',
        success: function(response) {
            const $conversationsPanel = $('.conversations-panel');
            const $body = $('.messaging-body');
            
            // Clear current conversations
            $conversationsPanel.html('<div class="archived-header"><h6><i class="fas fa-archive mr-2"></i>Archived Conversations</h6><button class="btn btn-sm btn-link" onclick="showNormalConversations()">← Back to Active</button></div>');
            
            if (response.conversations && response.conversations.length > 0) {
                response.conversations.forEach(function(conversation) {
                    const otherUser = @if(Auth::user()->role === 'patient') conversation.admin @else conversation.patient @endif;
                    const conversationHtml = createArchivedConversationHtml(conversation, otherUser);
                    $conversationsPanel.append(conversationHtml);
                });
            } else {
                $conversationsPanel.append('<div class="no-conversations"><i class="fas fa-archive fa-3x mb-3 text-muted"></i><h5>No archived conversations</h5></div>');
            }
            
            $body.addClass('showing-archived');
        },
        error: function() {
            showNotification('Failed to load archived conversations', 'error');
        }
    });
}

function showNormalConversations() {
    const $body = $('.messaging-body');
    const $toggleBtn = $('#archive-toggle-btn');
    
    // Reset button state
    $toggleBtn.removeClass('showing-archived').html('<i class="fas fa-archive mr-1"></i> Archived');
    $body.removeClass('showing-archived');
    
    // Reload to show normal conversations
    window.location.reload();
}

function createArchivedConversationHtml(conversation, otherUser) {
    const userName = otherUser ? otherUser.name : 'Unknown User';
    const userInitials = userName.substring(0, 2).toUpperCase();
    const lastMessage = conversation.latest_message ? conversation.latest_message.message : 'No messages yet';
    const archivedDate = new Date(conversation.archived_at).toLocaleDateString();
    
    return `
        <div class="conversation-item archived-item" onclick="selectConversation(${conversation.id})">
            <div class="d-flex align-items-center">
                <div class="conversation-avatar mr-3">
                    <div class="default-avatar" style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">
                        ${userInitials}
                    </div>
                </div>
                <div class="flex-grow-1 conversation-content">
                    <h6 class="mb-1">${userName}</h6>
                    <p class="mb-1 text-muted small">${lastMessage.substring(0, 50)}</p>
                    <small class="text-muted">Archived on ${archivedDate}</small>
                </div>
                <div class="ml-2">
                    <button class="btn btn-sm btn-outline-primary unarchive-conversation" 
                            data-conversation-id="${conversation.id}"
                            onclick="event.stopPropagation(); unarchiveConversation(${conversation.id}, this);"
                            title="Unarchive">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

function unarchiveConversation(conversationId, buttonElement) {
    @if(Auth::user()->role === 'patient')
        const unarchiveUrl = `{{ route('patient.messages.unarchive', ':id') }}`.replace(':id', conversationId);
    @else
        const unarchiveUrl = `{{ route('admin.messages.unarchive', ':id') }}`.replace(':id', conversationId);
    @endif
    
    const $conversationItem = $(buttonElement).closest('.conversation-item');
    
    $.ajax({
        url: unarchiveUrl,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $conversationItem.slideUp(300, function() {
                    $(this).remove();
                });
                showNotification('Conversation unarchived successfully', 'success');
            } else {
                showNotification('Failed to unarchive conversation', 'error');
            }
        },
        error: function() {
            showNotification('Failed to unarchive conversation', 'error');
        }
    });
}

// Utility function for showing notifications
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = $(`
        <div class="alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show notification-toast" 
             style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    // Add to body
    $('body').append(notification);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        notification.fadeOut(() => notification.remove());
    }, 4000);
}

function scrollToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        // Smooth scroll to bottom without flickering
        requestAnimationFrame(() => {
            chatMessages.scrollTo({
                top: chatMessages.scrollHeight,
                behavior: 'smooth'
            });
        });
    }
}

@if($selectedConversation)
// Simplified message checking - just use the working method  
function checkForNewMessages() {
    @if(Auth::user()->role === 'patient')
        const messagesUrl = '{{ route("patient.messages.messages", $selectedConversation->id) }}';
    @else
        const messagesUrl = '{{ route("admin.messages.messages", $selectedConversation->id) }}';
    @endif
    
    $.ajax({
        url: messagesUrl,
        type: 'GET',
        success: function(response) {
            const $chatMessages = $('#chat-messages');
            const wasScrolledToBottom = isScrolledToBottom($chatMessages[0]);
            
            $chatMessages.html(response.html);
            
            // Only auto-scroll if user was already at bottom
            if (wasScrolledToBottom) {
                scrollToBottom();
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to check for new messages:', error);
        }
    });
}

// Helper function to check if user is scrolled to bottom
function isScrolledToBottom(element) {
    return element.scrollTop + element.clientHeight >= element.scrollHeight - 10;
}
@endif

// Handle Enter key in message input
$('input[name="message"]').on('keypress', function(e) {
    if (e.which === 13) { // Enter key
        e.preventDefault();
        $('#message-form').submit();
    }
});

// UI Enhancement Functions
function initializeUIEnhancements() {
    // Add smooth scrolling to conversations
    $('.conversations-panel').css('scroll-behavior', 'smooth');
    $('.chat-messages').css('scroll-behavior', 'smooth');
    
    // Add typing indicator functionality
    let typingTimer;
    $('input[name="message"]').on('keyup', function() {
        clearTimeout(typingTimer);
        showTypingIndicator();
        
        typingTimer = setTimeout(function() {
            hideTypingIndicator();
        }, 1500);
    });
    
    // Add message hover effects
    $(document).on('mouseenter', '.message-content', function() {
        $(this).css('transform', 'translateY(-2px)');
    });
    
    $(document).on('mouseleave', '.message-content', function() {
        $(this).css('transform', 'translateY(0)');
    });
    
    // Add conversation item hover sound effect (optional)
    $('.conversation-item').on('mouseenter', function() {
        $(this).addClass('hovered');
    }).on('mouseleave', function() {
        $(this).removeClass('hovered');
    });
    
    // Animate new messages
    animateMessages();
}

function showTypingIndicator() {
    // This would show a typing indicator to other users
    // Implementation depends on your real-time messaging setup
}

function hideTypingIndicator() {
    // This would hide the typing indicator
}

function animateMessages() {
    $('.message').each(function(index) {
        $(this).css({
            'animation-delay': (index * 0.1) + 's'
        });
    });
}

// Enhanced message sending with animation
function addMessageWithAnimation(messageHtml) {
    const $newMessage = $(messageHtml);
    
    // Only check for duplicates if this isn't from a send action
    const messageId = $newMessage.attr('data-message-id');
    const existingMessage = messageId ? $(`#chat-messages .message[data-message-id="${messageId}"]`) : $();
    
    if (existingMessage.length > 0 && !justSentMessage) {
        console.warn(`⚠️ Prevented duplicate message with ID: ${messageId}`);
        return; // Don't add the message if it already exists and this isn't from sending
    }
    
    console.log(`➕ Adding message to UI with ID: ${messageId || 'No ID'}`);
    
    // If this is replacing an existing message (rare edge case), remove the old one
    if (existingMessage.length > 0) {
        console.log(`🔄 Replacing existing message ${messageId}`);
        existingMessage.remove();
    }
    
    $newMessage.css({
        'opacity': '0',
        'transform': 'translateY(20px)'
    });
    
    $('#chat-messages').append($newMessage);
    
    // Trigger animation
    setTimeout(function() {
        $newMessage.css({
            'opacity': '1',
            'transform': 'translateY(0)',
            'transition': 'all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)'
        });
    }, 50);
    
    scrollToBottom();
}

// Auto-resize textarea
$('#message-textarea').on('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Handle Enter key for form submission
$('#message-textarea').on('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        $('#message-form').submit();
    }
});

// File input change handler
$('#file-input').on('change', function(e) {
    const file = e.target.files[0];
    console.log('🎯 File input changed. File selected:', file ? file.name : 'None');
    
    if (file) {
        console.log('📁 File details:', {
            name: file.name,
            size: file.size,
            type: file.type,
            lastModified: new Date(file.lastModified)
        });
        showFilePreview(file);
        updateAttachmentIndicator(true, file.name);
    } else {
        console.log('❌ No file selected, clearing attachment indicators');
        updateAttachmentIndicator(false);
    }
});

// Priority functionality removed for cleaner UI interface

// Show file preview
function showFilePreview(file) {
    console.log('Showing file preview for:', file.name);
    
    const fileIcon = getFileIcon(file.name, file.type);
    const fileSize = formatFileSize(file.size);
    
    // Update file preview elements
    $('#file-icon').html(fileIcon.icon).removeClass().addClass('file-icon ' + fileIcon.class);
    $('#file-name').text(file.name);
    $('#file-size').text(fileSize);
    
    // Show preview container
    $('#file-preview-container').slideDown(300);
    
    // Mark attachment button as active
    $('#attachment-btn').addClass('active');
}

function removeAttachment() {
    console.log('🗑️ Removing attachment');
    
    // Clear file input
    $('#file-input').val('').trigger('change'); // Trigger change to update indicators
    
    // Hide preview container
    $('#file-preview-container').slideUp(300);
    
    // Update attachment indicator
    updateAttachmentIndicator(false);
    
    console.log('✅ Attachment removed successfully');
}

// Priority indicator function removed

// File input handling
$(document).on('change', '#file-input', function() {
    const file = this.files[0];
    if (file) {
        showFilePreview(file);
        updateAttachmentIndicator(true, file.name);
    }
});

// Drag and drop handling
$(document).on('dragover', '.upload-area', function(e) {
    e.preventDefault();
    $(this).parent().addClass('dragover');
});

$(document).on('dragleave', '.upload-area', function(e) {
    e.preventDefault();
    $(this).parent().removeClass('dragover');
});

$(document).on('drop', '.upload-area', function(e) {
    e.preventDefault();
    $(this).parent().removeClass('dragover');
    
    const files = e.originalEvent.dataTransfer.files;
    if (files.length > 0) {
        $('#file-input')[0].files = files;
        showFilePreview(files[0]);
        updateAttachmentIndicator(true, files[0].name);
    }
});

// Click upload area to trigger file input
$(document).on('click', '.upload-area', function() {
    $('#file-input').click();
});

// File preview functions
function showFilePreview(file) {
    const preview = $('#file-preview');
    const fileSize = formatFileSize(file.size);
    const fileIcon = getFileIcon(file.name);
    
    let previewHtml = `
        <div class="file-preview-item">
            <div class="file-preview-info">
                ${file.type.startsWith('image/') ? 
                    `<img src="${URL.createObjectURL(file)}" class="file-preview-image" alt="Preview">` :
                    `<div class="file-preview-icon bg-light">${fileIcon}</div>`
                }
                <div class="file-preview-details">
                    <h6>${file.name}</h6>
                    <small>${fileSize}</small>
                </div>
            </div>
            <button type="button" class="file-remove-btn" onclick="removeFile()">
                <i class="fas fa-times"></i> Remove
            </button>
        </div>
    `;
    
    preview.html(previewHtml).show();
}

function removeFile() {
    console.log('🗑️ Removing file (removeFile function)');
    
    // Clear file input and trigger change event for consistency
    $('#file-input').val('').trigger('change');
    
    // Hide and clear preview
    $('#file-preview').hide().html('');
    
    // Update attachment indicator
    updateAttachmentIndicator(false);
    
    // Hide file upload section
    $('#file-upload-section').slideUp(300);
    
    console.log('✅ File removed successfully');
}

function getFileIcon(filename, mimeType = '') {
    const extension = filename.split('.').pop().toLowerCase();
    
    if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(extension) || mimeType.startsWith('image/')) {
        return { icon: '<i class="fas fa-image"></i>', class: 'image' };
    } else if (['pdf'].includes(extension) || mimeType === 'application/pdf') {
        return { icon: '<i class="fas fa-file-pdf"></i>', class: 'document' };
    } else if (['doc', 'docx'].includes(extension) || mimeType.includes('word')) {
        return { icon: '<i class="fas fa-file-word"></i>', class: 'document' };
    } else if (['mp4', 'avi', 'mov', 'wmv', 'mkv', 'flv', 'webm'].includes(extension) || mimeType.startsWith('video/')) {
        return { icon: '<i class="fas fa-video"></i>', class: 'video' };
    } else if (['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac'].includes(extension) || mimeType.startsWith('audio/')) {
        return { icon: '<i class="fas fa-music"></i>', class: 'audio' };
    } else if (['zip', 'rar', '7z', 'tar', 'gz'].includes(extension) || mimeType.includes('zip') || mimeType.includes('compress')) {
        return { icon: '<i class="fas fa-file-archive"></i>', class: 'archive' };
    } else {
        return { icon: '<i class="fas fa-file"></i>', class: 'document' };
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function updateAttachmentIndicator(hasAttachment, filename = '') {
    const messageInput = $('#message-textarea'); // Fixed: Use the correct textarea selector
    const existingIndicator = $('.attachment-indicator');
    
    existingIndicator.remove();
    
    if (hasAttachment) {
        messageInput.addClass('has-attachment');
        const indicator = $(`<span class="attachment-indicator"><i class="fas fa-paperclip"></i></span>`);
        messageInput.parent().append(indicator);
        messageInput.attr('placeholder', `Type your message... (📎 ${filename} attached)`);
        
        // Also add visual feedback to the attachment button
        $('#attachment-btn').addClass('active');
        console.log('📎 File attachment indicator updated:', filename);
        console.log('✅ Textarea properly updated with has-attachment class');
    } else {
        messageInput.removeClass('has-attachment');
        messageInput.attr('placeholder', 'Type your message...');
        
        // Remove active state from attachment button  
        $('#attachment-btn').removeClass('active');
        console.log('📎 File attachment indicator removed');
        console.log('✅ Textarea has-attachment class removed');
    }
}

function resetFileUpload() {
    removeAttachment();
}

function resetPriority() {
    // Priority functionality removed - just reset hidden input
    $('input[name="priority"]').val('normal');
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = $(`
        <div class="notification notification-${type}">
            <div class="notification-content">
                <i class="fas ${getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `);
    
    // Add to page
    if (!$('.notification-container').length) {
        $('body').append('<div class="notification-container"></div>');
    }
    
    $('.notification-container').append(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 5000);
    
    // Click to remove
    notification.find('.notification-close').click(function() {
        notification.fadeOut(300, function() {
            $(this).remove();
        });
    });
}

function getNotificationIcon(type) {
    switch(type) {
        case 'success': return 'fa-check-circle';
        case 'error': return 'fa-exclamation-circle';
        case 'warning': return 'fa-exclamation-triangle';
        default: return 'fa-info-circle';
    }
}

// Upload progress functions
function showUploadProgress() {
    const progressHtml = `
        <div class="upload-progress" id="upload-progress">
            <div class="progress">
                <div class="progress-bar" style="width: 0%;"></div>
            </div>
            <small class="text-muted">Uploading file...</small>
        </div>
    `;
    
    $('.modern-message-input').prepend(progressHtml);
}

function updateUploadProgress(percent) {
    $('#upload-progress .progress-bar').css('width', percent + '%');
    if (percent >= 100) {
        $('#upload-progress small').text('Processing...');
    }
}

function hideUploadProgress() {
    $('#upload-progress').fadeOut(300, function() {
        $(this).remove();
    });
}

// Image modal functions
function openImageModal(imageSrc, imageName) {
    $('#modalImage').attr('src', imageSrc).attr('alt', imageName);
    $('#modalImageName').text(imageName);
    $('#imageModal').fadeIn(300);
    
    // Prevent body scrolling when modal is open
    $('body').css('overflow', 'hidden');
}

function closeImageModal() {
    $('#imageModal').fadeOut(300);
    $('body').css('overflow', 'auto');
}

// Close modal when clicking outside the image
$(document).on('click', '#imageModal', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Close modal with ESC key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape' && $('#imageModal').is(':visible')) {
        closeImageModal();
    }
});

// Handle image loading states
$(document).on('load', '.message-image', function() {
    $(this).addClass('loaded');
    console.log('Image loaded successfully:', $(this).attr('src'));
});

$(document).on('error', '.message-image', function() {
    console.error('Image failed to load:', $(this).attr('src'));
    const $img = $(this);
    const $container = $img.closest('.attachment-image');
    
    // Replace with error message
    $container.html(`
        <div class="image-error">
            <div class="image-error-content">
                <i class="fas fa-image text-muted"></i>
                <p class="text-muted mb-0">Image failed to load</p>
                <small class="text-muted">${$img.attr('alt') || 'Unknown file'}</small>
            </div>
        </div>
    `);
});

// Debug image URLs on page load
$(document).ready(function() {
    $('.message-image').each(function() {
        const src = $(this).attr('src');
        if (src && src !== '' && src !== 'null') {
            console.log('Found image:', src);
            // Test if image is accessible
            const testImg = new Image();
            testImg.onload = function() {
                console.log('✓ Image accessible:', src);
            };
            testImg.onerror = function() {
                console.error('✗ Image not accessible:', src);
            };
            testImg.src = src;
        } else {
            console.warn('Image with empty/null src found');
        }
    });
    
    // Initialize mobile UI
    initializeMobileUI();
    
    // Enhanced real-time initialization
    @if($selectedConversation)
    setTimeout(function() {
        console.log('🚀 Starting immediate real-time updates...');
        
        // Start immediate check for updates (don't wait for first interval)
        checkForUpdates();
        
        // Initialize typing indicators
        handleTyping();
        
        console.log('✅ All real-time features initialized!');
    }, 1000);
    
    // Cleanup intervals on page unload to prevent memory leaks
    $(window).on('beforeunload', function() {
        if (typeof updateInterval !== 'undefined') {
            clearInterval(updateInterval);
        }
        if (typeof typingCheckInterval !== 'undefined') {
            clearInterval(typingCheckInterval);
        }
        stopTypingPolling();
        
        // Send stop typing status if currently typing
        if (typeof isCurrentlyTyping !== 'undefined' && isCurrentlyTyping) {
            const conversationId = $('input[name="conversation_id"]').val();
            if (conversationId) {
                sendTypingStatus(conversationId, false);
            }
        }
    });
    @endif
});

// Mobile UI Management
function initializeMobileUI() {
    // Check if we're on mobile
    if (window.innerWidth <= 768) {
        @if($selectedConversation)
            // We have a selected conversation, show chat with back button
            $('#mobile-back-btn').addClass('show');
            $('#messaging-body').removeClass('mobile-show-conversations');
        @else
            // No conversation selected, show conversations list
            showConversationsList();
        @endif
    }
    
    // Handle window resize
    $(window).on('resize', function() {
        if (window.innerWidth > 768) {
            // Desktop view - reset mobile classes
            $('#messaging-body').removeClass('mobile-show-conversations');
            $('#mobile-back-btn').removeClass('show');
        } else {
            // Mobile view - initialize properly
            @if($selectedConversation)
                $('#mobile-back-btn').addClass('show');
                $('#messaging-body').removeClass('mobile-show-conversations');
            @else
                showConversationsList();
            @endif
        }
    });
}

function showConversationsList() {
    if (window.innerWidth <= 768) {
        $('#messaging-body').addClass('mobile-show-conversations');
        $('#mobile-back-btn').removeClass('show');
        
        // Ensure conversations panel is visible
        $('.conversations-panel').show();
        $('#conversations-panel').show();
    }
}

function showChatPanel() {
    if (window.innerWidth <= 768) {
        $('#messaging-body').removeClass('mobile-show-conversations');
        $('#mobile-back-btn').addClass('show');
    }
}


// Override the selectConversation function for mobile
function selectConversation(conversationId) {
    // Show chat panel on mobile
    showChatPanel();
    
    // Navigate to conversation
    @if(Auth::user()->role === 'patient')
        window.location.href = '{{ route("patient.messages.index") }}?conversation=' + conversationId;
    @else
        window.location.href = '{{ route("admin.messages.index") }}?conversation=' + conversationId;
    @endif
}

// ===============================
// MESSAGE REACTIONS FUNCTIONALITY
// ===============================

// Available reaction emojis
const availableReactions = ['👍', '❤️', '😂', '😮', '😢', '😡'];

// Show reaction picker
function showReactionPicker(messageId, buttonElement) {
    // Remove any existing reaction pickers
    $('.reaction-picker').remove();
    
    // Create reaction picker
    const picker = $(`
        <div class="reaction-picker" id="reaction-picker-${messageId}">
            ${availableReactions.map(emoji => 
                `<div class="reaction-option" onclick="toggleReaction(${messageId}, '${emoji}')">${emoji}</div>`
            ).join('')}
        </div>
    `);
    
    // Position picker relative to button
    const $button = $(buttonElement);
    const buttonPos = $button.offset();
    const buttonHeight = $button.outerHeight();
    
    picker.css({
        position: 'fixed',
        top: buttonPos.top - 50,
        left: buttonPos.left - 80
    });
    
    // Add to body and show
    $('body').append(picker);
    setTimeout(() => picker.addClass('show'), 10);
    
    // Close picker when clicking elsewhere
    $(document).on('click.reaction-picker', function(e) {
        if (!$(e.target).closest('.reaction-picker, .add-reaction-btn').length) {
            closeReactionPicker();
        }
    });
}

// Close reaction picker
function closeReactionPicker() {
    $('.reaction-picker').removeClass('show');
    setTimeout(() => $('.reaction-picker').remove(), 200);
    $(document).off('click.reaction-picker');
}

// Toggle reaction on message (REAL API CALL)
function toggleReaction(messageId, emoji) {
    console.log(`🎬 Toggling reaction ${emoji} for message ${messageId}`);
    
    // Close any open reaction picker
    closeReactionPicker();
    
    // Send API request to toggle reaction
    $.ajax({
        url: @if(Auth::user()->role === 'patient') 
                '{{ route("patient.messages.react", ":messageId") }}' 
             @else 
                '{{ route("admin.messages.react", ":messageId") }}' 
             @endif.replace(':messageId', messageId),
        method: 'POST',
        data: {
            emoji: emoji,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                console.log(`✅ Reaction toggled successfully`);
                
                // Update reactions display
                updateReactionsDisplay(messageId, response.reactions);
            } else {
                console.error(`❌ Failed to toggle reaction:`, response.error);
                showNotification('Failed to add reaction', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error(`❌ Error toggling reaction:`, error);
            showNotification('Failed to add reaction', 'error');
        }
    });
}

// Backward compatibility - redirect old addReaction calls to toggleReaction
function addReaction(messageId, emoji) {
    toggleReaction(messageId, emoji);
}

// Update reactions display after API response
function updateReactionsDisplay(messageId, reactions) {
    const reactionsContainer = $(`#reactions-${messageId}`);
    const addButton = reactionsContainer.find('.add-reaction-btn');
    
    // Remove all existing reaction items (but keep add button)
    reactionsContainer.find('.reaction-item').remove();
    
    // Add updated reactions before the add button
    reactions.forEach(reaction => {
        const reactionItem = $(`
            <div class="reaction-item ${reaction.user_reacted ? 'user-reacted' : ''}" 
                 data-emoji="${reaction.emoji}" 
                 onclick="toggleReaction(${messageId}, '${reaction.emoji}')" 
                 title="${reaction.user_reacted ? 'Click to remove your reaction' : 'Click to add your reaction'}">
                <span class="reaction-emoji">${reaction.emoji}</span>
                <span class="reaction-count">${reaction.count}</span>
            </div>
        `);
        
        addButton.before(reactionItem);
    });
    
    console.log(`🔄 Updated reactions for message ${messageId}`);
}

// Note: removeReaction function integrated into addReaction for better UX

// Simple notification function for error handling
function showNotification(message, type = 'error') {
    const notification = $(`
        <div class="alert alert-${type === 'error' ? 'danger' : 'info'} alert-dismissible fade show" 
             style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        notification.alert('close');
    }, 3000);
}

// Initialize reactions when page loads
$(document).ready(function() {
    console.log('✨ Real-Time Message Reactions initialized!');
    
    // Reactions are now loaded from database in the message template
    // No demo reactions needed
});

// ===============================
// TYPING INDICATORS FUNCTIONALITY
// ===============================

let typingTimer;
let isCurrentlyTyping = false;
const TYPING_TIMEOUT = 2000; // Stop showing typing after 2 seconds of no activity

// Show typing indicator (updated for real-time)
function showTypingIndicator(userName = 'User') {
    // Only add if not already showing for this user
    if ($('.typing-indicator').length === 0) {
        // Create typing indicator
        const typingIndicator = $(`
            <div class="typing-indicator" id="typing-indicator">
                <div class="typing-avatar">
                    ${userName.charAt(0).toUpperCase()}
                </div>
                <span>${userName} is typing</span>
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        `);
        
        // Add to chat messages area
        const chatMessages = $('#chat-messages');
        chatMessages.append(typingIndicator);
        
        // Scroll to bottom to show typing indicator
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
        
        console.log('🟡 Showing typing indicator for:', userName);
    } else {
        // Update existing indicator text for different user
        $('.typing-indicator span').text(userName + ' is typing');
        $('.typing-indicator .typing-avatar').text(userName.charAt(0).toUpperCase());
    }
    
    // Always update header
    $('#typing-status .typing-status-text').text(userName + ' is typing...');
    $('#typing-status').show();
    $('#user-status').hide();
}

// Hide typing indicator (updated for real-time)
function hideTypingIndicator() {
    $('.typing-indicator').fadeOut(300, function() {
        $(this).remove();
    });
    
    // Hide typing from header
    $('#typing-status').hide();
    $('#user-status').show();
    
    console.log('⚪ Hiding typing indicator');
}

// Handle user typing in message input (REAL-TIME)
function handleTyping() {
    const messageInput = $('#message-textarea');
    const conversationId = $('input[name="conversation_id"]').val();
    
    if (!conversationId) {
        console.warn('No conversation ID found for typing indicators');
        return;
    }
    
    messageInput.on('input focus', function() {
        const message = $(this).val().trim();
        
        if (message.length > 0 || $(this).is(':focus')) {
            if (!isCurrentlyTyping) {
                // Start typing indicator
                isCurrentlyTyping = true;
                console.log('🟡 User started typing...');
                sendTypingStatus(conversationId, true);
            }
            
            // Reset the timer
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                // Stop typing indicator
                isCurrentlyTyping = false;
                console.log('⚪ User stopped typing...');
                sendTypingStatus(conversationId, false);
            }, TYPING_TIMEOUT);
        } else {
            // Empty input - stop typing immediately
            if (isCurrentlyTyping) {
                isCurrentlyTyping = false;
                clearTimeout(typingTimer);
                console.log('⚪ User stopped typing (empty input)...');
                sendTypingStatus(conversationId, false);
            }
        }
    });
    
    // Stop typing when user presses Enter, sends message, or loses focus
    messageInput.on('keypress blur', function(e) {
        if ((e.type === 'keypress' && e.which === 13 && !e.shiftKey) || e.type === 'blur') {
            if (isCurrentlyTyping) {
                isCurrentlyTyping = false;
                clearTimeout(typingTimer);
                console.log('⚪ User stopped typing (sent/blur)...');
                sendTypingStatus(conversationId, false);
            }
        }
    });
}

// Send typing status to server
function sendTypingStatus(conversationId, isTyping) {
    // Only send if conversation exists
    if (!conversationId) return;
    
    $.ajax({
        url: @if(Auth::user()->role === 'patient') 
                '{{ route("patient.messages.typing.update") }}' 
             @else 
                '{{ route("admin.messages.typing.update") }}' 
             @endif,
        method: 'POST',
        data: {
            conversation_id: conversationId,
            is_typing: isTyping,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log(isTyping ? '📤 Typing status sent' : '📤 Stopped typing sent');
        },
        error: function(xhr, status, error) {
            console.error('❌ Failed to send typing status:', error);
        }
    });
}

// Poll for typing status from other users
function pollTypingStatus() {
    const conversationId = $('input[name="conversation_id"]').val();
    if (!conversationId) return;
    
    $.ajax({
        url: @if(Auth::user()->role === 'patient') 
                '{{ route("patient.messages.typing.get") }}' 
             @else 
                '{{ route("admin.messages.typing.get") }}' 
             @endif,
        method: 'GET',
        data: {
            conversation_id: conversationId
        },
        success: function(response) {
            if (response.is_typing && response.user_name) {
                showTypingIndicator(response.user_name);
            } else {
                hideTypingIndicator();
            }
        },
        error: function(xhr, status, error) {
            // Silently handle errors to avoid spam
            if (xhr.status !== 404) {
                console.error('❌ Failed to poll typing status:', error);
            }
        }
    });
}

// Start polling for typing status from other users
let typingPollInterval;

function startTypingPolling() {
    const conversationId = $('input[name="conversation_id"]').val();
    if (!conversationId) {
        console.warn('No conversation ID found for polling');
        return;
    }
    
    // Poll every 1.5 seconds for typing status
    typingPollInterval = setInterval(function() {
        pollTypingStatus();
    }, 1500);
    
    console.log('🔄 Started polling for typing status');
}

function stopTypingPolling() {
    if (typingPollInterval) {
        clearInterval(typingPollInterval);
        console.log('⏹️ Stopped polling for typing status');
    }
}

// Initialize typing indicators (REAL-TIME)
$(document).ready(function() {
    console.log('⌨️ Real-Time Typing Indicators initialized!');
    
    // Set up typing detection
    handleTyping();
    
    // Start real-time polling when chat is active
    if ($('#chat-messages').length > 0 && $('input[name="conversation_id"]').val()) {
        setTimeout(function() {
            startTypingPolling();
        }, 1000); // Wait 1 second before starting
    }
    
    // Stop polling when page unloads
    $(window).on('beforeunload', function() {
        stopTypingPolling();
        // Send stop typing if currently typing
        if (isCurrentlyTyping) {
            const conversationId = $('input[name="conversation_id"]').val();
            if (conversationId) {
                sendTypingStatus(conversationId, false);
            }
        }
    });
});

// ===============================
// FILE DRAG & DROP FUNCTIONALITY
// ===============================

let dragCounter = 0;

// Initialize drag & drop
function initializeDragAndDrop() {
    const chatMessages = $('#chat-messages');
    const messageInputContainer = $('.message-input-container');
    const overlay = $('#drag-drop-overlay');
    const fileInput = $('#file-input');
    
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        chatMessages[0].addEventListener(eventName, preventDefaults, false);
        messageInputContainer[0].addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    
    // Drag enter events
    ['dragenter', 'dragover'].forEach(eventName => {
        chatMessages[0].addEventListener(eventName, handleDragEnter, false);
        messageInputContainer[0].addEventListener(eventName, handleDragEnter, false);
    });
    
    // Drag leave events
    ['dragleave'].forEach(eventName => {
        chatMessages[0].addEventListener(eventName, handleDragLeave, false);
        messageInputContainer[0].addEventListener(eventName, handleDragLeave, false);
    });
    
    // Drop events
    ['drop'].forEach(eventName => {
        chatMessages[0].addEventListener(eventName, handleDrop, false);
        messageInputContainer[0].addEventListener(eventName, handleDrop, false);
    });
}

// Prevent default behaviors
function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

// Handle drag enter
function handleDragEnter(e) {
    dragCounter++;
    
    // Only show overlay for files
    if (e.dataTransfer.types.includes('Files')) {
        $('#drag-drop-overlay').css('display', 'flex');
        $('.chat-messages').addClass('drag-over');
        $('.message-input-container').addClass('drag-over');
        
        console.log('📎 Files dragged over chat area');
    }
}

// Handle drag leave
function handleDragLeave(e) {
    dragCounter--;
    
    if (dragCounter === 0) {
        $('#drag-drop-overlay').css('display', 'none');
        $('.chat-messages').removeClass('drag-over');
        $('.message-input-container').removeClass('drag-over');
    }
}

// Handle file drop
function handleDrop(e) {
    dragCounter = 0;
    
    // Hide overlay
    $('#drag-drop-overlay').css('display', 'none');
    $('.chat-messages').removeClass('drag-over');
    $('.message-input-container').removeClass('drag-over');
    
    // Get dropped files
    const files = e.dataTransfer.files;
    
    if (files.length > 0) {
        console.log(`📋 ${files.length} file(s) dropped:`, files);
        
        // Process the first file (for now)
        const file = files[0];
        
        // Validate file
        if (validateFile(file)) {
            // Set the file to the input
            const fileInput = $('#file-input')[0];
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            
            // Trigger the existing file preview functionality
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
            
            showNotification('success', `File "${file.name}" ready to send!`);
        } else {
            showNotification('error', 'File type not supported or file too large.');
        }
    }
}

// Validate dropped file
function validateFile(file) {
    const maxSize = 10 * 1024 * 1024; // 10MB
    const allowedTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain', 'application/rtf',
        'video/mp4', 'video/avi', 'video/quicktime', 'video/x-ms-wmv',
        'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4',
        'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'
    ];
    
    if (file.size > maxSize) {
        console.error('File too large:', file.size, 'bytes');
        return false;
    }
    
    if (!allowedTypes.includes(file.type)) {
        console.error('File type not allowed:', file.type);
        return false;
    }
    
    return true;
}

// Show notification
function showNotification(type, message) {
    // Create notification element
    const notification = $(`
        <div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show" 
             style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    // Add to body
    $('body').append(notification);
    
    // Auto-remove after 4 seconds
    setTimeout(() => {
        notification.alert('close');
    }, 4000);
}

// Enhanced file input change handler
function enhanceFileInput() {
    $('#file-input').on('change', function(e) {
        const files = e.target.files;
        if (files.length > 0) {
            const file = files[0];
            console.log('📁 File selected:', file.name, file.size, 'bytes');
        }
    });
}

// Initialize drag & drop when page loads
$(document).ready(function() {
    console.log('📋 File Drag & Drop initialized!');
    
    // Initialize drag and drop
    if ($('#chat-messages').length > 0) {
        initializeDragAndDrop();
        enhanceFileInput();
    }
    
    // Demo notification
    setTimeout(() => {
        showNotification('success', 'You can now drag & drop files into the chat! 📋');
    }, 4000);
});

</script>
@endsection
