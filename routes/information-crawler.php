<?php
require_once __DIR__ . '/../helpers/logger.php';
require_once __DIR__ . '/../config/config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['first_name'], $data['last_name'])) {
    echo json_encode(['success' => false, 'message' => 'First name and last name are required']);
    http_response_code(400);
    exit();
}

$firstName = urlencode($data['first_name']);
$lastName = urlencode($data['last_name']);
$query = "{$firstName} {$lastName}";
$apiKey = $config['serp_api_key'];
$serpUrlTemplate = $config['serp_url'];

try {
    $serpApiUrl = str_replace(['{$query}', '{$apiKey}'], [$query, $apiKey], $serpUrlTemplate);
    $response = file_get_contents($serpApiUrl);
    $searchResults = json_decode($response, true);

    if (!$searchResults || !isset($searchResults['organic_results'])) {
        throw new Exception('Failed to fetch data from SerpAPI');
    }

    $results = [];
    foreach ($searchResults['organic_results'] as $result) {
        if (isset($result['title'], $result['link'])) {
            $results[] = [
                'title' => $result['title'],
                'url' => $result['link'],
                'snippet' => $result['snippet'] ?? 'No snippet available',
                'source' => $result['source'] ?? 'Unknown source',
                'favicon' => $result['favicon'] ?? null
            ];
        }
    }

    // Sort results by relevance (if provided)
    usort($results, function ($a, $b) {
        return strlen($b['snippet']) - strlen($a['snippet']);
    });

    // Limit results to top 5
    $results = array_slice($results, 0, 5);

    // Retrieve username and URL from cookies
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'unknown';
    $url = isset($_COOKIE['url']) ? $_COOKIE['url'] : 'unknown';

    // Log the action
    logAction($username, $url, "Crawled information for: {$data['name']} {$data['surname']}");

    echo json_encode([
        'success' => true,
        'message' => 'Information retrieved successfully',
        'results' => $results,
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
