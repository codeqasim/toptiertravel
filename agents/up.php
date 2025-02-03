<?php

$username = 'codeqasim';
$repo_name = 'v9';
$branch = 'main'; // Specify the branch you want to track

// Personal access token for authentication
$token = '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';

// Construct the URL for the API endpoint to get the commits of the main branch
$commits_url = "https://api.github.com/repos/{$username}/{$repo_name}/commits?sha={$branch}&per_page=10";

// Set up the authentication header
$headers = [
    'Authorization: token ' . $token,
    'User-Agent: Your-User-Agent', // Replace Your-User-Agent with your actual user agent
    'Accept: application/vnd.github.v3+json'
];

// Initialize cURL session for commits request
$ch = curl_init();

// Set cURL options for commits request
curl_setopt($ch, CURLOPT_URL, $commits_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL request to get commits
$response = curl_exec($ch);

// Check for errors
if(curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
    exit;
}

// Get the HTTP status code for commits request
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Close cURL session
curl_close($ch);

// Check for HTTP status code indicating an error for commits request
if($http_status !== 200) {
    echo "HTTP Status Code: {$http_status}\n";
    echo "Response: {$response}\n";
    exit;
}

// Decode JSON response for commits
$commits = json_decode($response, true);

// Create a directory to store zip files
$zip_dir = 'zip_files';
if (!is_dir($zip_dir)) {
    mkdir($zip_dir, 0755, true);
}

// Generate unique zip files for each push event
foreach($commits as $commit) {
    // Get the commit SHA and message
    $commit_sha = $commit['sha'];
    $commit_message = $commit['commit']['message'];

    // Generate a unique name for the zip file based on commit SHA
    $zip_file_name = "{$commit_sha}.zip";
    $zip_file_path = "{$zip_dir}/{$zip_file_name}";

    // Initialize a new zip archive
    $zip = new ZipArchive();
    if ($zip->open($zip_file_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        echo "Failed to create zip file\n";
        exit;
    }

    // Get the files associated with this commit
    $files_url = "https://api.github.com/repos/{$username}/{$repo_name}/commits/{$commit_sha}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $files_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $files_response = curl_exec($ch);
    curl_close($ch);

    if (!$files_response) {
        echo "Failed to fetch files for commit: {$commit_sha}\n";
        continue;
    }

    // Decode JSON response for files
    $files_data = json_decode($files_response, true);

    // Add each file to the zip archive
    foreach($files_data['files'] as $file) {
        $file_content_url = $file['raw_url'];
        $file_content = file_get_contents($file_content_url);
        $zip->addFromString($file['filename'], $file_content);
    }

    // Close the zip archive
    $zip->close();

    // Output the download link for the zip file with JavaScript function to handle the download
    echo "<a href='#' onclick='downloadZip(\"{$zip_file_path}\")'>Download Zip (Commit: {$commit_sha})</a><br>";
}

?>

<script>
    function downloadZip(zipPath) {
        // Create a hidden anchor element
        var link = document.createElement('a');
        link.href = zipPath;
        link.download = zipPath.split('/').pop(); // Extract filename for download
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
