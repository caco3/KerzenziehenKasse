/* Src: /home/kerzenziehen/Nextcloud/Kerzenziehen-Kasse/kerzenziehen-pc-infra/Kerzenziehen-Waage/PC/websocket.html
 * Opens a websocket to the docker container "scale-gateway" to receive the scale value. */

function doConnect()
{
    console.log("Connecting to scale @ " + location.hostname + "...");
    update_display(null, "Verbinden...", false);
    websocket = new WebSocket("ws://" + location.hostname + ":8000/");
    websocket.onopen = function(evt) { onOpen(evt) };
    websocket.onclose = function(evt) { onClose(evt) };
    websocket.onmessage = function(evt) { onMessage(evt) };
    websocket.onerror = function(evt) { onError(evt) };
}

function onOpen(evt)
{
    console.log("Scale connected\n");
    update_display(null, "Verbunden", false);
}

function onClose(evt)
{
    console.log("Scale disconnected!\n");
    update_display(null, "Nicht verbunden!", false);
    setTimeout(doConnect, 1000);
}

function onMessage(evt)
{
    console.log("Scale: " + evt.data + '\n');
    data = JSON.parse(evt.data)

    if (data.status == "ok") {
        if ("weight" in data) {
            update_display(data.weight, data.weight + " g", true);
        }
        else {
        console.log("Scale: No weight parameter!\n");
            update_display(null, "Daten fehlerhaft", false);
        }
    }
    else { // Generic error handler
        console.log('Scale: Error: ' + data.code + ' (' + data.description + ')!\n');
        console.log('Scale: data: ' + evt.data);
        if (data.code == "NO_SCALE") {
            update_display(null, "Nicht verbunden!", false);
        }
        else if (data.code == "NO_DATA") {
            // update_display(null, "Datenfehler!", false);
            update_display(0, 0 + " g", true);
        }
        else {
            update_display(null, "Fehler: " + data.code, false);
        }
    }
}

function onError(evt)
{
    console.log('Scale: Error handler: ' + evt.data + '!\n');
    update_display(null, "Kommunikationsfehler", false);
    websocket.close();
    setTimeout(doConnect, 1000);
}


doConnect();
