<?php
$api_key = '';
$project_url = '';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $project_url . "/attachments.xml");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $api_key . ':X');
$return = curl_exec($ch);

$xml = new SimpleXMLElement($return);
foreach ($xml->attachment as $attachment) {
	$download_url = (string)$attachment->{'download-url'};
	$pieces = parse_url($download_url);
	$path = pathinfo($pieces['path']);

	$filename = getFilename($path['basename']);
	$fp = fopen($filename, 'w');
	echo "Downloading $filename...\n";
	
	curl_setopt($ch, CURLOPT_URL, $download_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);
	
	fclose($fp);
}

// close cURL resource, and free up system resources
curl_close($ch);


function getFilename($filename)
{
	if (!file_exists($filename)) {
		return $filename;
	} else {
		$path = pathinfo($filename);
		$new_filename = $path['filename'] . '-1.' . $path['extension'];
		return getFilename($new_filename);
	}
}
