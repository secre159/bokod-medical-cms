<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avatar System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .avatar-demo {
            display: inline-block;
            margin: 10px;
            text-align: center;
        }
        .avatar-info {
            margin-top: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Avatar System Test</h1>
        <p class="text-muted">Testing the new initials-based avatar fallback system</p>
        
        <div class="row">
            @foreach($avatarTests as $test)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $test['name'] }}</h5>
                            
                            <!-- Avatar Component Test -->
                            <div class="avatar-demo">
                                <h6>Avatar Component (64px)</h6>
                                <x-user-avatar :user="App\Models\User::find($test['user_id'])" width="64px" height="64px" />
                                <div class="avatar-info">
                                    <small class="text-muted">Using &lt;x-user-avatar&gt;</small>
                                </div>
                            </div>
                            
                            <!-- Direct Avatar URL Test -->
                            <div class="avatar-demo">
                                <h6>Direct Avatar URL (32px)</h6>
                                <img src="{{ $test['avatar_url_32'] }}" width="32" height="32" alt="Avatar" style="border-radius: 50%;">
                                <div class="avatar-info">
                                    <small class="text-muted">getAvatarUrl(32)</small>
                                </div>
                            </div>
                            
                            <!-- AdminLTE Image Test -->
                            <div class="avatar-demo">
                                <h6>AdminLTE Image (48px)</h6>
                                <img src="{{ $test['adminlte_image'] }}" width="48" height="48" alt="AdminLTE Avatar" style="border-radius: 50%;">
                                <div class="avatar-info">
                                    <small class="text-muted">adminlte_image()</small>
                                </div>
                            </div>
                            
                            <!-- User Info -->
                            <div class="mt-3">
                                <h6>User Info</h6>
                                <ul class="list-unstyled small">
                                    <li><strong>ID:</strong> {{ $test['user_id'] }}</li>
                                    <li><strong>Initials:</strong> {{ $test['initials'] }}</li>
                                    <li><strong>Color:</strong> <span style="color: {{ $test['initials_color'] }}">{{ $test['initials_color'] }}</span></li>
                                    <li><strong>Has Profile Picture:</strong> {{ $test['has_profile_picture'] ? 'Yes' : 'No' }}</li>
                                    @if($test['profile_picture'])
                                        <li><strong>Picture URL:</strong> <small>{{ Str::limit($test['profile_picture'], 30) }}</small></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h3>Test Cases</h3>
                <div class="alert alert-info">
                    <h6>What to look for:</h6>
                    <ul class="mb-0">
                        <li><strong>Users with profile pictures:</strong> Should display their actual photos</li>
                        <li><strong>Users without profile pictures:</strong> Should display colorful initials avatars</li>
                        <li><strong>All three methods:</strong> Should show consistent results</li>
                        <li><strong>Initials:</strong> Should be first letter of first and last name, or first two letters if single name</li>
                        <li><strong>Colors:</strong> Should be consistent for the same user across page loads</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>