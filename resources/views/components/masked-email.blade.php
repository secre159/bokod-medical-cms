@php
    $e = $email ?? '';
    if (is_string($e) && strpos($e, '@') !== false) {
        [$local] = explode('@', $e, 2);
        echo e($local . '@...');
    } else {
        echo e($e);
    }
@endphp
