<?php
$consumerKey = 'jAUghrNniPvG6zHACGaO8QeN6b2zQ1pcGu0v35N1u7jdbt8LY';
$consumerSecret = '1G7Eyp1o8WunEm60Vca6KrjCTwxoVFbfGs8z3SfxMy0w4SQj2FHfGIsCtjbxE1YL';
$credentials = base64_encode($consumerKey . ':' . $consumerSecret);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response);
$accessToken = $result->access_token;
?>