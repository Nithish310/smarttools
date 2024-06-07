<?php

function generateText($prompt) {
    $apiKey = 'AIzaSyCoLuwJP6V8QMsFILnFu5pxsOclzxjBRVc'; // Replace with your actual API key
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=$apiKey";
    
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $jsonData = json_encode($data);

    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n",
            'method' => 'POST',
            'content' => $jsonData
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        return null;
    }

    return extractTextFromResponse($result);
}

function extractTextFromResponse($responseBody) {
    $jsonObject = json_decode($responseBody, true);
    $candidates = $jsonObject['candidates'] ?? null;

    if ($candidates && count($candidates) > 0) {
        $firstCandidate = $candidates[0];
        $content = $firstCandidate['content'] ?? null;
        $parts = $content['parts'] ?? null;

        if ($parts && count($parts) > 0) {
            return $parts[0]['text'];
        }
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prompt = $_POST['prompt'] ?? '';

    if (!empty($prompt)) {
        $generatedText = generateText($prompt);
        header('Content-Type: application/json');
        echo json_encode(['text' => $generatedText]);
    } else {
        echo json_encode(['error' => 'No prompt provided']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

?>
