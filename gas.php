<?php
// Function to sanitize and format the input number
function sanitizePhoneNumber($phoneNumber) {
    // Remove non-digit characters
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
    
    // Remove leading zero
    if (substr($phoneNumber, 0, 1) === "0") {
        $phoneNumber = substr($phoneNumber, 1);
    }
    
    // Prepend country code "66" if not already present
    if (substr($phoneNumber, 0, 2) !== "66") {
        $phoneNumber = "66" . $phoneNumber;
    }
    
    return $phoneNumber;
}

// Function to perform a cURL request to a specified URL with given headers
function performCurlRequest($phoneNumber) {
    $url = "https://www.mstrans.id/req-otp-alt?tipe=otp-sms&telp=" . $phoneNumber;
    $headers = [
        "User-Agent: Mozilla",
        "Content-Type: application/json",
        "Accept: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ["response" => $response, "httpStatus" => $httpStatus];
}

// Function to retry the cURL request multiple times if it fails
function retryRequest($phoneNumber, $maxRetries) {
    $attempt = 1;

    while ($attempt <= $maxRetries) {
        $result = performCurlRequest($phoneNumber);
        $httpStatus = $result["httpStatus"];

        if ($httpStatus === 200) {
            echo "[$attempt] $phoneNumber => Success!\n";
            return;
        } else {
            echo "[$attempt] $phoneNumber => Failed\n";
        }

        sleep(2);
        $attempt++;
    }
}

// Prompt user for input number
echo "Input Number: ";
$phoneNumber = trim(fgets(STDIN));
$phoneNumber = sanitizePhoneNumber($phoneNumber);

// Prompt user for number of retries
echo "Input Total Retries: ";
$maxRetries = trim(fgets(STDIN));

// Call the retry function with the sanitized number and retry count
retryRequest($phoneNumber, $maxRetries);
?>
