<?php
$apiKey = 'AIzaSyD-unVLjTwvFzqbF8UO31fl9tARUrJjpNs';
$channelId = 'UCVtm5xC3IAvWdvvY4lDcv8A'; // Replace with your channel ID
$maxResults = 5;

// YouTube Data API URL
$apiURL = "https://www.googleapis.com/youtube/v3/search?key=$apiKey&channelId=$channelId&part=snippet,id&order=date&maxResults=$maxResults";

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $apiURL);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($curl);
curl_close($curl);

// Send as JSON
header('Content-Type: application/json');
echo $response;
