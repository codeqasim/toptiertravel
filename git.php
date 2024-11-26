<?php
echo "<pre>";
echo "Current directory: " . getcwd() . "\n";
echo "Running git pull...\n";

$output = [];
$return_var = null;

// Set Git remote URL with the token
$token = 'github_pat_11AKR2EDQ04wGKhHG8UOYa_vSy9FZee9cfchj2b7ykaZtvT6GPDyuL7ABl0AReHyKoUQG2FZ5F9C4JTOO';
$remoteUrl = "https://{$token}@github.com/codeqasim/toptiertravel.com.git";
exec("git remote set-url origin {$remoteUrl}", $output, $return_var);

shell_exec( 'git pull origin main' );

?>
