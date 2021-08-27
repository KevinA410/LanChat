<?php
include_once('socket_lib.php'); // Include socket librery
$master_info = json_decode(file_get_contents("../js/master.json")); // Get socket master info from Json

$master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP); // Create socket TCP
socket_set_option($master, SOL_SOCKET, SO_REUSEADDR, true); // Enable reusable port
socket_bind($master, $master_info->address, $master_info->port); // Connect port to master info
socket_listen($master); // Set socket to listen requests

while (true) { // Keep run
    $copy_clients = $connected_clients;
    $null = NULL;
    
    array_push($copy_clients, $master);

    // Writes clients who made a request in $copy_clients
    socket_select($copy_clients, $null, $null, 0, 10);

    // If there's a new connection request
    if(in_array($master, $copy_clients)){
        $new_socket = socket_accept($master); // Create new client socket
        $header = socket_read($new_socket, 1024); // Get header
        socket_handshaking($header, $new_socket, $master_info->address, $master_info->port); // Link to master

        socket_getpeername($new_socket, $new_address); // Get ip of new socket
        $clients_addresses = socket_getClientsAddresses(); //Get ip of connected clients

        // New user recieves all connected ips
        $response = socket_encodeResponse(array(
            'command' => 'connected_users',
            'own_address' => $new_address,
            'addresses' => $clients_addresses
        ));

        socket_write($new_socket, $response, strlen($response)); // Send response
        
        // Already connected users recieve only the new connection's ip
        $response = socket_encodeResponse(array(
            'command' => 'new_connection',
            'address' => $new_address
        ));

        socket_sendForAll($response); // Send response for all connected users
        array_push($connected_clients, $new_socket); // Add new user to connected clients array
        socket_remove($master, $copy_clients); // Remove master from $copy_clients
    }

    foreach($copy_clients as $client){ // Attend all clients requests
		while(socket_recv($client, $buffer, 1024, 0) >= 1){ // Check for incomming data
            $request = socket_decodeResponse($buffer); // Decode client's request

            if($request){ // If there's a request
                socket_getpeername($client, $address); // Get ip address

                switch($request['command']){
                    case 'private_message': // Personal message (Client to Client)
                        $to_socket = socket_getPeerAddress($request['to']); // Get destionation socket
                        $dt = new DateTime();
                        $hour = $dt->format('H') . ':' . $dt->format('i');
    
                        if($to_socket){ // If the destionation socket exists
                            $callback = socket_encodeResponse(array(
                                'command' => 'verfied_message',
                                'to' => $request['to'],
                                'message' => $request['message'],
                                'hour' => $hour
                            ));

                            $response = socket_encodeResponse(array(
                                'command' => $request['command'],
                                'from' => $address,
                                'message' => $request['message'],
                                'hour' => $hour
                            ));
                            
                            socket_write($client, $callback, strlen($callback)); // Send message
                            socket_write($to_socket, $response, strlen($response)); // Send message
                        }

                        break;
                    default:
                }
            }
            
			break 2;
		}
		
		$buffer = @socket_read($client, 1024, PHP_NORMAL_READ);

		if ($buffer === false) { // If the client disconnected
            socket_getpeername($client, $address); // Get ip address
            socket_remove($client, $connected_clients); // Remove client for connected clients array
			
            $response = socket_encodeResponse(array(
                'command' => 'disconnected_user',
                'address' => $address
            ));
            
            socket_sendForAll($response); // Notify all users about disconnected client
		}
    }
}

socket_close($master); // Close master socket 