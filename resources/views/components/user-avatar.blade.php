@php
    $size = $size ?? 'default';
    $width = $width ?? ($size === 'thumbnail' ? '50px' : '200px');
    $height = $height ?? ($size === 'thumbnail' ? '50px' : '200px');
    
    // Get user information with better fallback handling
    if (!$user) {
        $initials = 'NA';
        $userName = 'User';
        $hasProfilePicture = false;
        $profilePictureUrl = '';
        $initialsAvatarUrl = 'data:image/svg+xml;base64,' . base64_encode('<svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="32" fill="#6c757d"/><text x="50%" y="50%" font-family="Arial, sans-serif" font-size="26" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="central">NA</text></svg>');
    } elseif (!$user->name || trim($user->name) === '') {
        $initials = 'UN';
        $userName = 'Unknown User';
        $hasProfilePicture = false;
        $profilePictureUrl = '';
        $initialsAvatarUrl = 'data:image/svg+xml;base64,' . base64_encode('<svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="32" fill="#6c757d"/><text x="50%" y="50%" font-family="Arial, sans-serif" font-size="26" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="central">UN</text></svg>');
    } else {
        $userName = $user->name;
        $initials = $user->getInitials();
        $hasProfilePicture = !empty($user->profile_picture);
        $profilePictureUrl = $user->profile_picture ?? '';
        
        // Get avatar size for SVG generation (extract number from width)
        $avatarSize = (int) filter_var($width, FILTER_SANITIZE_NUMBER_INT) ?: 64;
        $initialsAvatarUrl = $user->generateInitialsAvatar($avatarSize);
    }
@endphp

@if($hasProfilePicture)
    @php
        // ALWAYS add cache-busting parameters for profile pictures to ensure they update properly
        $imageUrl = $profilePictureUrl;
        
        // Check multiple conditions that indicate the image should be fresh
        $recentlyUpdated = $user->updated_at && $user->updated_at->gt(now()->subMinutes(30)); // Extended to 30 minutes
        $sessionProfileUpdated = session('profile_updated_user_' . $user->id, false);
        $hasSuccessMessage = session('status') && str_contains(session('status'), 'profile');
        $hasProfileParam = request()->has('profile_updated');
        $isFromCloudinary = str_contains($imageUrl, 'res.cloudinary.com');
        $isFromImgBB = str_contains($imageUrl, 'ibb.co');
        
        // Force cache-busting if any condition is met OR if it's an external image service
        if ($recentlyUpdated || $sessionProfileUpdated || $hasSuccessMessage || $hasProfileParam || $isFromCloudinary || $isFromImgBB) {
            // Remove existing cache-busting parameters first
            $cleanUrl = preg_replace('/[?&](t|v|r|bust|cache|refresh|_)=[^&]*/', '', $imageUrl);
            
            // Add comprehensive cache-busting
            $separator = strpos($cleanUrl, '?') !== false ? '&' : '?';
            $timestamp = time();
            $microtime = microtime(true);
            $version = $user->updated_at ? $user->updated_at->timestamp : $timestamp;
            $random = rand(10000, 99999);
            $imageUrl = $cleanUrl . $separator . "t={$timestamp}&v={$version}&r={$random}&bust={$microtime}&refresh=" . date('YmdHis');
        }
    @endphp
    <img class="user-avatar {{ $class ?? '' }}" 
         src="{{ $imageUrl }}" 
         alt="{{ $userName }}'s profile picture" 
         style="width: {{ $width }}; height: {{ $height }}; border-radius: 50%; object-fit: cover;" 
         title="{{ $userName }}"
         data-user-id="{{ $user->id ?? '' }}"
         onerror="this.src='{{ $initialsAvatarUrl }}'; this.alt='{{ $userName }} initials';">
@else
    <!-- Default SVG initials avatar -->
    <img class="user-avatar {{ $class ?? '' }}" 
         src="{{ $initialsAvatarUrl }}" 
         alt="{{ $userName }}'s initials" 
         style="width: {{ $width }}; height: {{ $height }}; border-radius: 50%;" 
         title="{{ $userName }}">
@endif
