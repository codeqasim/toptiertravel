<?php

// Include the credentials file
include "update_credentials.php";

// Commit SHA
$commit_sha = '9e40029cd1865a86064666ce95e7389625193c16';
// $token = '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';

// Set the owner and repository name
$owner = 'codeqasim';
$repo = 'v9';

// Construct the URL for downloading the zip archive
$url = "https://api.github.com/repos/{$owner}/{$repo}/zipball/{$commit_sha}";

// Set up cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/vnd.github+json",
    "Authorization: Bearer {$token}",
    "X-GitHub-Api-Version: 2022-11-28"
]);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as string

// Execute cURL request
$response = curl_exec($ch);

// Check for errors
if(curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
    exit;
}

// Get the HTTP status code
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Close cURL
curl_close($ch);

// Check HTTP status code
if($http_status !== 200) {
    echo "HTTP Status Code: {$http_status}\n";
    echo "Response: {$response}\n";
    exit;
}

// Output the response (zip archive)
echo $response;

?>
