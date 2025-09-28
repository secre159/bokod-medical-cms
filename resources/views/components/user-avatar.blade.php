@php
    $size = $size ?? 'default';
    $width = $width ?? ($size === 'thumbnail' ? '50px' : '200px');
    $height = $height ?? ($size === 'thumbnail' ? '50px' : '200px');
    
    // Get user information
    if (!$user || !$user->name) {
        $initials = '??';
        $userName = 'User';
        $hasProfilePicture = false;
        $profilePictureUrl = '';
        $initialsAvatarUrl = 'data:image/svg+xml;base64,' . base64_encode('<svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="32" r="32" fill="#6c757d"/><text x="50%" y="50%" font-family="Arial, sans-serif" font-size="26" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="central">??</text></svg>');
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
        // Add cache-busting parameter if the profile was recently updated (within last 10 minutes)
        // Also check session for recent profile updates
        $recentlyUpdated = $user->updated_at && $user->updated_at->gt(now()->subMinutes(10));
        $sessionProfileUpdated = session('profile_updated_user_' . $user->id, false);
        
        $imageUrl = $profilePictureUrl;
        if ($recentlyUpdated || $sessionProfileUpdated) {
            // Aggressive cache-busting with multiple parameters
            $separator = strpos($imageUrl, '?') !== false ? '&' : '?';
            $timestamp = time();
            $version = $user->updated_at ? $user->updated_at->timestamp : $timestamp;
            $random = rand(1000, 9999);
            $imageUrl .= $separator . "t={$timestamp}&v={$version}&r={$random}&bust=" . microtime(true);
        }
    @endphp
    <img class="user-avatar {{ $class ?? '' }}" 
         src="{{ $imageUrl }}" 
         alt="{{ $userName }}'s profile picture" 
         style="width: {{ $width }}; height: {{ $height }}; border-radius: 50%; object-fit: cover;" 
         title="{{ $userName }}"
         data-user-id="{{ $user->id ?? '' }}"
         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
    <!-- Fallback SVG initials avatar -->
    <img class="user-avatar {{ $class ?? '' }}" 
         src="{{ $initialsAvatarUrl }}" 
         alt="{{ $userName }}'s initials" 
         style="display: none; width: {{ $width }}; height: {{ $height }}; border-radius: 50%;" 
         title="{{ $userName }}">
@else
    <!-- Default SVG initials avatar -->
    <img class="user-avatar {{ $class ?? '' }}" 
         src="{{ $initialsAvatarUrl }}" 
         alt="{{ $userName }}'s initials" 
         style="width: {{ $width }}; height: {{ $height }}; border-radius: 50%;" 
         title="{{ $userName }}">
@endif
