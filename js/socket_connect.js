$(document).ready(function () {
    // Global variables
    var wsURL;
    var currentChat = null;
    setChat(false);

    $.ajax({
        async: false,
        url: "http://192.168.0.110:8000/js/master.json",
        success: function (json) {
            wsURL = json.wsURL;
        }
    });

    let socket = new WebSocket(wsURL);

    // Socket events
    socket.onopen = function (event) {
    }

    socket.onmessage = function (event) {
        var r = JSON.parse(event.data);

        switch (r.command) {
            case 'connected_users':
                $("#connected").html(' ');

                r.addresses.forEach(address => {
                    var id = address.replaceAll('.', '_');

                    $("#connected").append(html_newConnection(id, address));

                    $("#" + id).on('click', function () {
                        if(!currentChat){
                            setChat(true);
                        }
                        $("#destination_address").html(address);
                        currentChat = address;
                        loadMessages(address);
                    });
                });
                break;
            case 'new_connection':
                var id = r.address.replaceAll('.', '_');

                $("#connected").append(html_newConnection(id, r.address));

                $("#" + id).on('click', function () {
                    if(!currentChat){
                        setChat(true);
                    }
                    $("#destination_address").html(r.address);
                    currentChat = r.address;
                    loadMessages(r.address);
                });
                break;
            case 'disconnected_user':
                // Remove ip from connected list
                var id = r.address.replaceAll('.', '_');
                $("#" + id).remove();

                if (currentChat == r.address) {
                    setChat(false);
                    currentChat = null;
                }
                break;
            case 'private_message':
                saveMessage(r.message, r.from);
                loadMessages(r.from);
                break;
            default:
                console.log("There's no function for this command");
        }
    }

    socket.onclose = function (event) {
    }

    socket.onerror = function (event) {
    }

    // General events
    $("#btn_send").on('click', function () {
        var to = $("#destination_address").html();
        var message = $("#input_message").val();

        $("#input_message ").val('');

        saveMessage(message, to, 'You');
        loadMessages(to);

        socket.send(JSON.stringify({
            "command": "private_message",
            "to": to,
            "message": message
        }));
    });


    // Auxiliar functions
    function html_newConnection(id, address){
        return `
            <div id="${id}">
                <div class="connected-dot"></div>${address}
            </div>
        `;
    }

    function setChat(option){
        var str = option ? 'd-block' : 'd-none';
        $("#chat-flag").attr('class', str);
    }

    function saveMessage(message, keyAddress, fromAddress = keyAddress){
        var conv = sessionStorage.getItem(keyAddress)
            ? JSON.parse(sessionStorage.getItem(keyAddress))
            : new Array();

        conv.push({
            from: fromAddress,
            message: message
        });

        sessionStorage.setItem(keyAddress, JSON.stringify(conv));
    }
    
    function loadMessages(keyAddress){
        var raw = '';
        var conv = sessionStorage.getItem(keyAddress)
            ? JSON.parse(sessionStorage.getItem(keyAddress))
            : new Array();
        
        conv.forEach(item => {
            raw += `
                <div class="card my-2 p-2">
                    <p class="card-text">
                        <span class="text-muted">${item.from}: </span>${item.message}
                    </p>
                </div>
            `;
        });

        $("#messages").html(raw);
    }
});