<?php
if (function_exists('opcache_reset')) {
    $ok = opcache_reset();
    echo $ok ? 'OPcache reset: OK' : 'OPcache reset: FAILED';
} else {
    echo 'OPcache function not available on this server.';
}
