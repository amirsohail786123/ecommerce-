<?php
function generateUniqueNumber() {
    $timestamp = time(); // This is a 10-digit number
    
    // Generate a random 10-digit number
    $randomNumber = mt_rand(1000000000, 9999999999); // 10-digit random number

    // Concatenate timestamp and random number, then trim to ensure it’s exactly 20 digits
    $uniqueNumber = $timestamp . $randomNumber;
    
    // Ensure the result is exactly 20 digits
    if (strlen($uniqueNumber) > 20) {
        $uniqueNumber = substr($uniqueNumber, 0, 20); // Trim to 20 digits if necessary
    } elseif (strlen($uniqueNumber) < 20) {
        $uniqueNumber = str_pad($uniqueNumber, 20, '0'); // Pad with leading zeros if necessary
    }

    return $uniqueNumber;

}

?>
