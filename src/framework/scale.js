/* Src: /home/kerzenziehen/Nextcloud/Kerzenziehen-Kasse/kerzenziehen-pc-infra/Kerzenziehen-Waage/PC/websocket.html
 * Opens a websocket to the docker container "scale-gateway" to receive the scale value. */

/**
 * Opens a websocket connection to the scale-gateway container on this host
 * (port 8085) and registers the websocket event handlers.
 */
function doConnect()
{
    console.log("Connecting to scale @ " + location.hostname + "...");
    update_display(null, "Verbinden...", false);
    websocket = new WebSocket("ws://" + location.hostname + ":8085/");
    /** @param {Event} evt - Websocket open event. */
    websocket.onopen = function(evt) { onOpen(evt) };
    /** @param {CloseEvent} evt - Websocket close event. */
    websocket.onclose = function(evt) { onClose(evt) };
    /** @param {MessageEvent} evt - Websocket message event carrying scale data. */
    websocket.onmessage = function(evt) { onMessage(evt) };
    /** @param {Event} evt - Websocket error event. */
    websocket.onerror = function(evt) { onError(evt) };
}

/**
 * Websocket open handler; updates the scale display to 'Verbunden'.
 *
 * @param {Event} evt - Websocket open event.
 */
function onOpen(evt)
{
    console.log("Scale connected\n");
    update_display(null, "Verbunden", false);
}

/**
 * Websocket close handler; marks the scale as disconnected and reconnects
 * after one second.
 *
 * @param {CloseEvent} evt - Websocket close event.
 */
function onClose(evt)
{
    console.log("Scale disconnected!\n");
    update_display(null, "Nicht verbunden!", false);
    setTimeout(doConnect, 1000);
}

/**
 * Websocket message handler; parses the JSON scale data and updates the
 * weight display or shows an error state.
 *
 * @param {MessageEvent} evt - Websocket message event; evt.data contains JSON
 *        with 'status', 'weight' or an error 'code'.
 */
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
        //console.log('Scale: Error: ' + data.code + ' (' + data.description + ')!\n');
        //console.log('Scale: data: ' + evt.data);
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

/**
 * Websocket error handler; shows a communication error, closes the socket
 * and reconnects after one second.
 *
 * @param {Event} evt - Websocket error event.
 */
function onError(evt)
{
    console.log('Scale: Error handler: ' + evt.data + '!\n');
    update_display(null, "Kommunikationsfehler", false);
    websocket.close();
    setTimeout(doConnect, 1000);
}


doConnect();
