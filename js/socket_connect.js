$(document).ready(function () {
    var wsURL;

    $.ajax({
        async: false,
        url: "http://192.168.0.110:8000/js/master.json",
        success: function (json) {
            wsURL = json.wsURL;
        }
    });

    let socket = new WebSocket(wsURL);

    socket.onopen = function (event) {
        // alert("Connected");
    }

    socket.onmessage = function (event) {
        var r = JSON.parse(event.data);

        switch(r.command){
            case 'connected_users':
                $("#connected").html(' ');
                for (let i = 0; i < r.addresses.length; i++) {
                    var ip = r.addresses[i];
                    var id = ip.replaceAll('.', '_');

                    $("#connected").append(`<div id="${id}"><p><div class="connected-dot"></div>${ip}</p></div>`);
                    $("#" + id).on('click', function () {
                        $("#destination_ip").html(ip);
                    })
                }
                break;
            case 'new_connection':
                var id = r.address.replaceAll('.', '_');
                $("#connected").append(`<div id="${id}"><p><div class="connected-dot"></div>${r.address}</p></div>`);
                    $("#" + id).on('click', function () {
                        $("#destination_ip").html(r.address);
                    })
                break;
            case 'disconnected_user':
                // Remove ip from connected list
                var id = r.address.replaceAll('.', '_');
                $("#" + id).remove();
                break;
            default:
                console.log("There's no function for this command");
        }
    }

    socket.onclose = function (event) {
        // $("#text").html('Connection closed');
    }

    socket.onerror = function (event) {
        console.log(event.data);
    }

    // Send message
    $("#btn_send").on('click', function () {
        $("#messages").append(`
            <div class="card mb-2">
                <div class="card-body">
                    <p class="card-text"><span class="text-muted">You: </span>${$("#input_message").val()}</p>
                </div>
            </div>
        `);

        var response = {
            "command": "private_message",
            "to": $("#destination_ip").val(),
            "message": $("#input_message").val(),
        };

        $("#input_message").val('');
        socket.send(JSON.stringify(response));
    });
});