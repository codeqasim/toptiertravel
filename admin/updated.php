<?php

include "update_credentials.php";

// Initialize an empty array to store all commits
$all_commits = [];

// Fetch commits page by page
for ($page = 1; $page <= 4; $page++) { // Fetching up to 4 pages (100 commits per page)
    // Construct the URL for the API endpoint to get the commits of the main branch
    $commits_url = "https://api.github.com/repos/{$username}/{$repo_name}/commits?sha={$branch}&per_page=500&page={$page}";

    // Set up the authentication header
    $headers = [
        'Authorization: token ' . $token,
        'User-Agent: PHP'
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

    // Check for HTTP status code indicating success for commits request
    if($http_status !== 200) {
        echo "HTTP Status Code: {$http_status}\n";
        echo "Response: {$response}\n";
        exit;
    }

    // Decode JSON response for commits
    $commits = json_decode($response, true);

    // Append fetched commits to the array of all commits
    $all_commits = array_merge($all_commits, $commits);

    // If the total number of commits exceeds or equals 100, break the loop
    if (count($all_commits) >= 500) {
        break;
    }
}

// Output the table header
echo "<table class='table'>";
echo "<thead><tr><th>Commit</th><th>Message</th><th>Date</th><th>Action</th></tr></thead>";
echo "<tbody>";

// Output the commits in table rows
foreach($all_commits as $commit) {
    // Get the commit SHA, message, and date
    $commit_sha = $commit['sha'];
    $commit_message = $commit['commit']['message'];
    $commit_date = date("Y-m-d H:i:s", strtotime($commit['commit']['author']['date']));

    // Output commit information in table rows
    echo "<tr><td>{$commit_sha}</td><td>{$commit_message}</td><td>{$commit_date}</td><td><a href='update_download.php?commit={$commit_sha}' class='btn btn-primary'>Install</a></td></tr>";
}

echo "</tbody>";
echo "</table>";

?>
