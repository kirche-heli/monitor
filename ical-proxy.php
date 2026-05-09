<?php
/**
 * ical-proxy.php
 * Einfacher Proxy für den ChurchDesk iCal-Feed.
 * Auf dem Webserver unter https://www.evangelische-kirche-heli.de/ical-proxy.php ablegen.
 *
 * Aufruf: ical-proxy.php?url=https%3A%2F%2Fapi2.churchdesk.com%2F...
 */

// Nur den eigenen ChurchDesk-Feed durchlassen
$allowed = 'https://api2.churchdesk.com/ical/parish/67323';

$url = isset($_GET['url']) ? urldecode($_GET['url']) : '';

if (strpos($url, $allowed) !== 0) {
    http_response_code(403);
    exit('Nicht erlaubt');
}

// CORS-Header damit der Browser auf dem Monitor zugreifen darf
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/calendar; charset=utf-8');
header('Cache-Control: max-age=1800'); // 30 Min cachen

$ctx = stream_context_create([
    'http' => [
        'timeout'       => 10,
        'user_agent'    => 'Mozilla/5.0 (compatible; GemeindeMonitor/1.0)',
        'ignore_errors' => true,
    ]
]);

$data = @file_get_contents($url, false, $ctx);

if ($data === false) {
    http_response_code(502);
    exit('Feed nicht erreichbar');
}

echo $data;
