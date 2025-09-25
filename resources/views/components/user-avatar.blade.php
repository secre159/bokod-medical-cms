@php
    use App\Services\ProfilePictureService;
    
    $size = $size ?? 'default';
    $cssClasses = $class ?? '';
    $width = $width ?? ($size === 'thumbnail' ? '50px' : '200px');
    $height = $height ?? ($size === 'thumbnail' ? '50px' : '200px');
    
    if (!$user) {
        $avatarUrl = ProfilePictureService::getProfilePictureUrl(null, $size);
        $initials = '??';
    } else {
        $avatarUrl = ProfilePictureService::getProfilePictureUrl($user, $size);
        $initials = $user->getInitials();
    }
    
    $isInitialsAvatar = strpos($avatarUrl, 'data:image/svg+xml') === 0;
@endphp

<div class="user-avatar {{ $cssClasses }}" style="display: inline-block; width: {{ $width }}; height: {{ $height }};">
    @if($isInitialsAvatar)
        {{-- Initials avatar --}}
        <img src="{{ $avatarUrl }}" 
             alt="{{ $user->name ?? 'User' }}" 
             style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;"
             title="{{ $user->name ?? 'User' }}">
    @else
        {{-- Real uploaded image --}}
        <img src="{{ $avatarUrl }}" 
             alt="{{ $user->name ?? 'User' }}" 
             style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;"
             title="{{ $user->name ?? 'User' }}"
             onerror="this.src='{{ ProfilePictureService::getProfilePictureUrl($user ?? null, $size) }}';">
    @endif
</div>