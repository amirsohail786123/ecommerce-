<?php
function getDeviceIP() {
    // Check for shared Internet/ISP IP
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    // Check for IP from a proxy or load balancer
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    // Check for remote address (default)
    if (filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
        return $_SERVER['REMOTE_ADDR'];
    }

    // If all else fails
    return 'Unknown';
}

function getDeviceName() {
    // Get the host name of the server
    return gethostbyaddr($_SERVER['REMOTE_ADDR']);
}

// Example usage:
$deviceIP = getDeviceIP();
$deviceName = getDeviceName();

echo "Device IP: $deviceIP<br>";
echo "Device Name: $deviceName<br>";
?>
