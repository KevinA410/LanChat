<?php
// Global variables
$connected_clients = array();

// Send message for specified group of clients
function socket_sendForAll($message){
	global $connected_clients;
	
	foreach($connected_clients as $client){
		@socket_write($client, $message, strlen($message));
	}
	
	return true;
}

// Get all ip addresses for connected clients
function socket_getConnectedAddresses(){
	global $connected_clients;
	$connected_addresses = array();

	foreach($connected_clients as $client){
		socket_getpeername($client, $address);

		if(!in_array($address, $connected_addresses))
			array_push($connected_addresses, $address);
	}
	
	return $connected_addresses;
}

// Format the response to send
function socket_formatResponse($response_array){
	return mask(json_encode($response_array));
}

// Get socket by ip address
function socket_getPeerAddress($address){
	global $connected_clients;

	foreach($connected_clients as $client){
		socket_getpeername($client, $client_address);

		if($address == $client_address){
			return $client;
		}
	}

	return false;
}

//Unmask incoming framed message
function unmask($text)
{
	$length = ord($text[1]) & 127;
	if ($length == 126) {
		$masks = substr($text, 4, 4);
		$data = substr($text, 8);
	} elseif ($length == 127) {
		$masks = substr($text, 10, 4);
		$data = substr($text, 14);
	} else {
		$masks = substr($text, 2, 4);
		$data = substr($text, 6);
	}
	$text = "";
	for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i % 4];
	}
	return $text;
}

//Encode message for transfer to client.
function mask($text)
{
	$b1 = 0x80 | (0x1 & 0x0f);
	$length = strlen($text);

	if ($length <= 125)
		$header = pack('CC', $b1, $length);
	elseif ($length > 125 && $length < 65536)
		$header = pack('CCn', $b1, 126, $length);
	elseif ($length >= 65536)
		$header = pack('CCNN', $b1, 127, $length);
	return $header . $text;
}

//handshake new client.
function perform_handshaking($receved_header, $client_conn, $host, $port)
{
	$headers = array();
	$lines = preg_split("/\r\n/", $receved_header);
	foreach ($lines as $line) {
		$line = chop($line);
		if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
			$headers[$matches[1]] = $matches[2];
		}
	}

	$secKey = $headers['Sec-WebSocket-Key'];
	$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
	//hand shaking header
	$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
		"Upgrade: websocket\r\n" .
		"Connection: Upgrade\r\n" .
		"WebSocket-Origin: $host\r\n" .
		"WebSocket-Location: ws://$host:$port/demo/shout.php\r\n" .
		"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($client_conn, $upgrade, strlen($upgrade));
}

?>