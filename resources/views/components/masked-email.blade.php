@php
    $e = $email ?? '';
    if (is_string($e) && strpos($e, '@') !== false) {
        [$local, $domain] = explode('@', $e, 2);
        $domainParts = explode('.', $domain);
        $tld = array_pop($domainParts);
        $domainCore = implode('.', $domainParts);

        $localVisible = function_exists('mb_substr') ? mb_substr($local, 0, min(3, (function_exists('mb_strlen') ? mb_strlen($local) : strlen($local)))) : substr($local, 0, min(3, strlen($local)));
        $localMasked = $localVisible . ((function_exists('mb_strlen') ? mb_strlen($local) : strlen($local)) > 3 ? '…' : '');

        $dots = str_repeat('•', max(3, strlen($domainCore)));
        echo e($localMasked.'@'.$dots.'.'.$tld);
    } else {
        echo e($e);
    }
@endphp
