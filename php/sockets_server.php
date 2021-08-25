<?php
include_once('socket_lib.php');
$master_info = json_decode(file_get_contents("../js/master.json"));

// Create & configure socket server (master)
$master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($master, SOL_SOCKET, SO_REUSEADDR, true);
socket_bind($master, $master_info->address, $master_info->port);
socket_listen($master);

while (true) {
    $copy_clients = $connected_clients;
    $null = NULL;
    
    array_push($copy_clients, $master);

    // Attend new connection request
    socket_select($copy_clients, $null, $null, 0, 10);

    if(in_array($master, $copy_clients)){
        // Create & connect new socket
        $new_socket = socket_accept($master);
        $header = socket_read($new_socket, 1024);
        perform_handshaking($header, $new_socket, $master_info->address, $master_info->port);

        // Get all connected ip's
        socket_getpeername($new_socket, $new_address);
        $clients_addresses = socket_getConnectedAddresses();
        array_push($clients_addresses, $new_address);

        // New user recieves all connected ips
        $response = socket_formatResponse(array(
            'command' => 'connected_users',
            'addresses' => $clients_addresses
        ));

        socket_write($new_socket, $response, strlen($response));
        
        // Already connected users recieve only the new connection's ip
        $response = socket_formatResponse(array(
            'command' => 'new_connection',
            'address' => $new_address
        ));

        socket_sendForAll($response);
    
        // Add new user to connected clients array
        array_push($connected_clients, $new_socket);
        
        // Remove master from array
        $index = array_search($master, $copy_clients);
        unset($copy_clients[$index]);
    }

    // Attend clients request
    @socket_select($copy_clients, $null, $null, 0, 10);

    foreach($copy_clients as $client){
        //check for any incomming data
		while(socket_recv($client, $buffer, 1024, 0) >= 1){
			// Decode client request
            $request = json_decode(unmask($buffer), true);
            socket_getpeername($client, $address);

            switch($request->command){
                // Personal message (Client to Client)
                case 'private_message':
                    $to_socket = socket_getPeerAddress($request->to);

                    if($to_socket){
                        $response = socket_formatResponse(array(
                            'command' => $request->command,
                            'from' => $address,
                            'message' => $request->message
                        ));

                        socket_write($to_socket, $response, strlen($response));
                    }

                    break;
                default:
            }
            
			break 2;
		}
		
        // Check disconnected client
		$buffer = @socket_read($client, 1024, PHP_NORMAL_READ);

		if (!$buffer) {
            socket_getpeername($client, $address);

			// remove client for $clients array
			$index = array_search($client, $connected_clients);
			unset($clients[$index]);
			
			//notify all users about disconnected connection
            $response = socket_formatResponse(array(
                'command' => 'disconnected_user',
                'address' => $address
            ));
            
            socket_sendForAll($response);
		}
    }
}

socket_close($server_socket);
