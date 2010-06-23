<?php
// Sanity checks
if (is_null($_SERVER['PATH_INFO']))
{
  throw new InvalidArgumentException('Please specify path info.');
}

// Call service
$curl = curl_init(sprintf('http://data.musiques-incongrues.net/%s?%s', $_SERVER['PATH_INFO'], $_SERVER['QUERY_STRING']));
curl_exec($curl);

// Clean up
curl_close($curl);