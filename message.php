<?php 
header('Content-Type: text/plain'); 
 
if (!isset($_POST['text']) || empty($_POST['text'])) { 
    echo "No input provided."; 
    exit; 
} 
 
$userMessage = $_POST['text']; 

$apiKey = "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"; 
$apiUrl = "https://openrouter.ai/api/v1/chat/completions";

// Set concise and professional tone
$data = json_encode([
    "model" => "mistralai/mixtral-8x7b-instruct",
    "messages" => [
        ["role" => "system", "content" => "Please provide brief, professional, and clear responses."],
        ["role" => "user", "content" => $userMessage]
    ],
    "max_tokens" => 50
]);

$ch = curl_init($apiUrl);

curl_setopt_array($ch, [ 
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [ 
        "Authorization: Bearer $apiKey", 
        "Content-Type: application/json",
        "HTTP-Referer: https://yourwebsite.com",
        "X-Title: Free AI Chat"
    ], 
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_FOLLOWLOCATION => true
]); 

$response = curl_exec($ch); 
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch); 

if ($curlError) {
    echo "Curl error: $curlError";
    exit;
}

if ($httpCode !== 200) {
    echo "HTTP Error $httpCode. Response: $response";
    exit;
}

$responseData = json_decode($response, true); 

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON decode error: " . json_last_error_msg();
    echo "\nRaw response: $response";
    exit;
}

if (isset($responseData['choices'][0]['message']['content'])) {
    echo trim($responseData['choices'][0]['message']['content']);
} else {
    echo "Unexpected response format.";
    if (isset($responseData['error'])) {
        echo "\nError: " . $responseData['error']['message'];
    }
    echo "\nRaw response: $response";
}
?>
