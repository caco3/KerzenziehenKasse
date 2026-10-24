// $(document).on({    
//     ajaxStop: function() { 
//         console.log("stop");
//         hideProgressBar();
//     }    
// });


// School flag confirmation dialog variables
var currentSchoolFlagBookingId = null;

/**
 * Shows the confirmation dialog for toggling the school-class flag of a booking.
 *
 * @param {string|number} bookingId - ID of the booking whose school flag should be toggled.
 */
function showSchoolFlagDialog(bookingId) {
    currentSchoolFlagBookingId = bookingId;
    document.getElementById('schoolFlagMessage').textContent = 
        'Soll die Schul-Markierung für Buchung ' + bookingId + ' wirklich umgeschaltet werden?';
    document.getElementById('schoolFlagDialog').style.display = 'flex';
}

/**
 * Closes the school flag confirmation dialog and clears the stored booking ID.
 */
function closeSchoolFlagDialog() {
    document.getElementById('schoolFlagDialog').style.display = 'none';
    currentSchoolFlagBookingId = null;
}

/**
 * Confirms the school flag dialog and sends the toggle request to
 * ajax/toggleSchoolFlag.php for the currently stored booking.
 */
function confirmSchoolFlag() {
    if (!currentSchoolFlagBookingId) {
        return;
    }
    
    var bookingId = currentSchoolFlagBookingId;
    closeSchoolFlagDialog();
    
    var xhttp = new XMLHttpRequest();
    /**
     * Handles the toggleSchoolFlag ajax response; toggles the school flag
     * button styling and shows a success or error notification.
     */
    xhttp.onreadystatechange = function() {                    
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            try {
                console.log("Raw response:", this.responseText);
                var response = JSON.parse(this.responseText);
                hideProgressBar();
                
                if (response.success) {
                    // Toggle the button appearance
                    var button = document.getElementById('school_' + bookingId);
                    
                    if (response.newSchoolFlag == 1) {
                        button.classList.add('active');
                        firework.launch("Die Buchung wurde als 'von einer Schulklasse' markiert", 'success', 3000);
                    } else {
                        button.classList.remove('active');
                        firework.launch("Die Buchung wurde als 'nicht von einer Schulklasse' markiert", 'success', 3000);
                    }
                } else {
                    firework.launch("Fehler: " + response.error, 'error', 5000);
                }
                
            } catch (e) {
                console.error("Error parsing response:", e);
                console.error("Response text:", this.responseText);
                hideProgressBar();
                firework.launch("Fehler beim Aktualisieren der Schul-Markierung!", 'error', 5000);
            }
        }
        else if (this.readyState == XMLHttpRequest.DONE) {
            hideProgressBar();
            console.error("Request failed. Status:", this.status, "Response:", this.responseText);
            firework.launch("Fehler beim Aktualisieren der Schul-Markierung!", 'error', 5000);
        }
    };
    
    showProgressBar();
    
    var params = "bookingId=" + bookingId;
    xhttp.open("POST", "ajax/toggleSchoolFlag.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}

/**
 * Initializes the bookings page: binds click handlers for the edit,
 * receipt-view, receipt-print and school-flag buttons.
 */
$(document).ready(function(){
    console.log("Bookings loaded");    
    
    $(".editButton").off().on('click', 
        /**
         * Click handler for the edit button; copies the booking into the basket
         * and redirects to the main page when the basket was empty.
         *
         * @param {jQuery.Event} event - Click event; the target's id holds the booking ID.
         */
        function(event){
            var bookingId = $(event.target).attr('id');
            console.log("Loading booking " + bookingId + " into basket");
            
            // checking if basket is empty
            var xhttp = new XMLHttpRequest();
            /**
             * Handles the copyBookingToBasket ajax response; shows a notification
             * and redirects to index.php on success.
             */
            xhttp.onreadystatechange = function() {                    
                if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
                    var obj = JSON.parse(this.responseText);
                    hideProgressBar();
                    if(obj.response.success == "true") {
//                         console.log("ok, basket is empty");
//                         copyBookingToBasket(bookingId)();
                        firework.launch("Buchung " + bookingId + " wurde in den Warenkorb geladen.<br>Du wirst gleich weitergeleitet...", 'success', 5000);
                        // load main page after 1 second
                        setTimeout(
                            /** Redirects the browser to the main page. */
                            function(){ window.location.replace("index.php"); }, 1000);                        
                    }
                    else if(obj.response.empty == "false") {
                        console.log("Basket is not empty");
                        firework.launch("Bitte zuerst Warenkorb leeren!", 'error', 5000);
                    }
                    else{
                        console.log("Unknown error");
                        firework.launch("Es ist ein Fehler aufgetreten: " + obj.response.Text + "!", 'error', 5000);
                    }
                }
            };
            
            showProgressBar();   

            var params = "bookingId=" + bookingId;
            xhttp.open("POST", "ajax/copyBookingToBasket.php", true);
//             xhttp.open("POST", "ajax/basketIsEmpty.php", true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.send(params);
        }
    ); 
    
    
    $(".receiptButtonView").off().on('click', 
        /**
         * Click handler for the receipt-view button; fetches the booking data
         * and triggers PDF generation via the receipt-generator API.
         *
         * @param {jQuery.Event} event - Click event; the target's id holds the booking ID.
         */
        function(event){
            var bookingId = $(event.target).attr('id');
            console.log("creating receipt for booking " + bookingId);
            
            // First get booking data from database
            var xhttp = new XMLHttpRequest();
            /**
             * Handles the getBookingData ajax response; on success calls
             * callReceiptGenerator() to create the PDF receipt.
             */
            xhttp.onreadystatechange = function() {                    
                if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
                    try {
                        var bookingData = JSON.parse(this.responseText);
                        hideProgressBar();
                        
                        if (bookingData.error) {
                            firework.launch("Fehler: " + bookingData.error, 'error', 5000);
                            return;
                        }
                        
                        // Call receipt-generator API for PDF
                        callReceiptGenerator(bookingData, 'pdf');
                        
                    } catch (e) {
                        console.error("Error parsing booking data:", e);
                        firework.launch("Fehler beim Lesen der Buchungsdaten!", 'error', 5000);
                    }
                }
                else if (this.readyState == XMLHttpRequest.DONE) {
                    hideProgressBar();
                    firework.launch("Fehler beim Abrufen der Buchungsdaten!", 'error', 5000);
                }
            };
            
            showProgressBar();
            xhttp.open("GET", "ajax/getBookingData.php?id=" + bookingId, true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.send();
        }
    );
    
    $(".receiptButtonPrint").off().on('click', 
        /**
         * Click handler for the receipt-print button; fetches the booking data
         * and opens the printer selection dialog.
         *
         * @param {jQuery.Event} event - Click event; the target's id holds the booking ID.
         */
        function(event){
            var bookingId = $(event.target).attr('id');
            console.log("print button clicked for booking " + bookingId);
            
            // First get booking data from database
            var xhttp = new XMLHttpRequest();
            /**
             * Handles the getBookingData ajax response; on success opens the
             * printer selection dialog via showPrinterDialog().
             */
            xhttp.onreadystatechange = function() {                    
                if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
                    try {
                        var bookingData = JSON.parse(this.responseText);
                        hideProgressBar();
                        
                        if (bookingData.error) {
                            firework.launch("Fehler: " + bookingData.error, 'error', 5000);
                            return;
                        }
                        
                        // Show printer selection dialog
                        showPrinterDialog(bookingData);
                        
                    } catch (e) {
                        console.error("Error parsing booking data:", e);
                        firework.launch("Fehler beim Lesen der Buchungsdaten!", 'error', 5000);
                    }
                }
                else if (this.readyState == XMLHttpRequest.DONE) {
                    hideProgressBar();
                    firework.launch("Fehler beim Abrufen der Buchungsdaten!", 'error', 5000);
                }
            };
            
            showProgressBar();
            xhttp.open("GET", "ajax/getBookingData.php?id=" + bookingId, true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.send();
        }
    );
    
    $(".schoolFlagButton").off().on('click', 
        /**
         * Click handler for the school-flag button; opens the confirmation
         * dialog for toggling the booking's school-class flag.
         *
         * @param {jQuery.Event} event - Click event on a button with id 'school_<bookingId>'.
         */
        function(event){
            var buttonId = $(event.target).closest('button').attr('id');
            var bookingId = buttonId.replace('school_', '');
            console.log("school flag button clicked for booking " + bookingId);
            
            // Show confirmation dialog
            showSchoolFlagDialog(bookingId);
        }
    );
    
});

/**
 * Calls the receipt-generator API to create a PDF receipt for a booking
 * and triggers a download of the resulting PDF in the browser.
 *
 * @param {Object} bookingData - Booking data returned by ajax/getBookingData.php.
 * @param {string} bookingData.value - Total value of the booking.
 * @param {string|number} bookingData.booking_id - ID of the booking.
 * @param {string} bookingData.teacher - Name of the teacher.
 * @param {string} bookingData.class - Class name.
 * @param {string} bookingData.payment_type - Payment type of the booking.
 * @param {string} outputType - Output type; only 'pdf' is supported.
 */
function callReceiptGenerator(bookingData, outputType) {
    console.log("Calling receipt-generator with data:", bookingData, "outputType:", outputType);
    
    if (outputType !== 'pdf') {
        console.error("callReceiptGenerator only supports PDF output now. Use callReceiptGeneratorWithPrinter for printing.");
        firework.launch("Fehler: Ungültiger Ausgabetyp!", 'error', 5000);
        return;
    }
    
    var message = "Beleg f&uuml;r Buchung " + bookingData.booking_id + " wird erstellt.<br><br>Das Dokument wird in einigen Sekunden ge&ouml;ffnet...";
    firework.launch(message, 'success', 5000);
    
    var xhttp = new XMLHttpRequest();
    /**
     * Handles the receipt-generator ajax response; on success converts the
     * response blob into a downloaded PDF file.
     */
    xhttp.onreadystatechange = function() {                    
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            hideProgressBar();
            
            if (outputType === 'pdf') {
                // Create blob from response and download as PDF
                var blob = new Blob([this.response], { type: 'application/pdf' });
                var url = window.URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'Kerzenziehen-Beleg-' + bookingData.booking_id + '.pdf';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                
                console.log("PDF generated and downloaded successfully");
            }
        }
        else if (this.readyState == XMLHttpRequest.DONE) {
            hideProgressBar();
            console.error("Error generating receipt:", this.responseText);
            firework.launch("Fehler beim Erstellen des Belegs!", 'error', 5000);
        }
    };
    
    showProgressBar();
    
    // Prepare data for receipt-generator API
    var requestData = {
        value: bookingData.value,
        booking_id: bookingData.booking_id,
        teacher: bookingData.teacher,
        class: bookingData.class,
        payment_type: bookingData.payment_type,
        output_type: outputType
    };
    
    console.log("Sending request to receipt-generator:", requestData);
    
    xhttp.open("POST", receiptGeneratorUrl + "/api/generate-receipt", true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    
    if (outputType === 'pdf') {
        xhttp.responseType = 'blob';
    }
    
    xhttp.send(JSON.stringify(requestData));
}


// function copyBookingToBasket(bookingId) {
//     console.log("Copying booking " + bookingId + " to basket");
//     
//     // checking if basket is empty
//     var xhttp = new XMLHttpRequest();
//     xhttp.onreadystatechange = function() {                    
//         if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
//             var obj = JSON.parse(this.responseText);
//             hideProgressBar();
//             if(obj.response.empty == "true") {
//                 
// //                 xxx
//                 
//                 
//             }
//             else{
// //                 xxx
//                 
//             }
//         }
//     };
//     
//     showProgressBar();   
// 
//     xhttp.open("POST", "ajax/copyBookingToBasket.php", true);
//     xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
//     xhttp.send();
//     
//     
// }

// Printer selection dialog functions
var currentBookingData = null;
var selectedPrinter = null;

// Debug function to track variable changes
/**
 * Logs the current state of the printer-dialog variables for debugging.
 *
 * @param {string} context - Label describing where the debug call was made.
 */
function debugVariables(context) {
    console.log("DEBUG [" + context + "] - currentBookingData:", currentBookingData, "selectedPrinter:", selectedPrinter);
}

/**
 * Opens the printer selection dialog for a booking, resets its state and
 * starts loading the list of available printers.
 *
 * @param {Object} bookingData - Booking data returned by ajax/getBookingData.php.
 */
function showPrinterDialog(bookingData) {
    console.log("showPrinterDialog called with bookingData:", bookingData);
    currentBookingData = bookingData;
    selectedPrinter = null;
    debugVariables("after showPrinterDialog init");
    
    // Reset dialog state
    document.getElementById('printerList').innerHTML = '';
    document.getElementById('printerLoading').style.display = 'block';
    document.getElementById('confirmPrintBtn').disabled = true;
    document.getElementById('printerSelectionDialog').style.display = 'flex';
    
    // Load available printers
    loadPrinters();
}

/**
 * Closes the printer selection dialog and resets the stored booking and
 * printer selection.
 */
function closePrinterDialog() {
    document.getElementById('printerSelectionDialog').style.display = 'none';
    currentBookingData = null;
    selectedPrinter = null;
}

/**
 * Loads the list of available printers from the receipt-generator API
 * (/api/printer-status) and displays them in the printer dialog.
 */
function loadPrinters() {
    var xhttp = new XMLHttpRequest();
    /**
     * Handles the printer-status ajax response; on success renders the
     * printer list via displayPrinters().
     */
    xhttp.onreadystatechange = function() {
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            try {
                var response = JSON.parse(this.responseText);
                console.log("Printer status response:", response);
                displayPrinters(response.printers);
            } catch (e) {
                console.error("Error parsing printer data:", e);
                document.getElementById('printerLoading').innerHTML = '<p style="color: red;">Fehler beim Laden der Drucker</p>';
            }
        } else if (this.readyState == XMLHttpRequest.DONE) {
            console.error("Printer status request failed. Status:", this.status, "Response:", this.responseText);
            document.getElementById('printerLoading').innerHTML = '<p style="color: red;">Fehler beim Laden der Drucker</p>';
        }
    };
    
    console.log("Loading printers from:", receiptGeneratorUrl + '/api/printer-status');
    xhttp.open('GET', receiptGeneratorUrl + '/api/printer-status', true);
    xhttp.send();
}

/**
 * Renders the list of printers in the printer selection dialog and
 * auto-selects an enabled USB printer if one is found.
 *
 * @param {Object[]} printers - List of printers returned by the receipt-generator API.
 * @param {string} printers[].name - CUPS queue name of the printer.
 * @param {string} printers[].status - Printer status ('enabled', 'disabled' or other).
 * @param {string} [printers[].description] - Human readable printer description.
 * @param {string} [printers[].type] - Printer type (e.g. 'network').
 * @param {string} [printers[].ip_address] - IP address for network printers.
 */
function displayPrinters(printers) {
    console.log("displayPrinters called with printers:", printers);
    document.getElementById('printerLoading').style.display = 'none';
    
    if (!printers || printers.length === 0) {
        document.getElementById('printerList').innerHTML = '<p style="padding: 20px; text-align: center; color: #666;">Keine Drucker gefunden</p>';
        return;
    }
    
    var printerListHtml = '';
    var usbPrinterName = null;
    
    printers.forEach(
        /**
         * Builds the HTML list entry for a single printer and detects an
         * enabled USB printer for auto-selection.
         *
         * @param {Object} printer - Printer object as returned by the API.
         */
        function(printer) {
        var statusClass = printer.status === 'enabled' ? 'enabled' : 
                         printer.status === 'disabled' ? 'disabled' : 'unknown';
        var statusText = printer.status === 'enabled' ? 'Bereit' : 
                        printer.status === 'disabled' ? 'Deaktiviert' : 'Unbekannt';
        
        var displayName = printer.description && printer.description.trim() ? printer.description : printer.name;
        console.log("Processing printer:", printer.name, "display:", displayName, "status:", printer.status);
        
        // Check if this printer has USB in description and is enabled
        if (printer.status === 'enabled' && displayName.toLowerCase().includes('usb')) {
            usbPrinterName = printer.name;
            console.log("Found USB printer:", usbPrinterName);
        }
        
        var details = [];
        if (printer.type === 'network' && printer.ip_address) {
            details.push('IP: ' + printer.ip_address);
        }
        
        var isSelected = (printer.name === usbPrinterName) ? 'selected' : '';
        
        printerListHtml += '<div class="printer-item ' + isSelected + '" onclick="selectPrinter(\'' + printer.name + '\', \'' + printer.status + '\')">' +
            '<input type="radio" name="printer" class="printer-radio" id="printer_' + printer.name.replace(/[^a-zA-Z0-9]/g, '_') + '"' + (isSelected ? ' checked' : '') + '>' +
            '<div class="printer-info">' +
                '<div class="printer-name">' + displayName + '</div>' +
                (details.length > 0 ? '<div class="printer-details">' + details.join(' | ') + '</div>' : '') +
            '</div>' +
            '<!--<div class="printer-status ' + statusClass + '">' + statusText + '</div>-->' +
        '</div>';
    });
    
    document.getElementById('printerList').innerHTML = printerListHtml;
    
    // If USB printer was found and selected, enable the confirm button
    if (usbPrinterName) {
        selectedPrinter = usbPrinterName;
        document.getElementById('confirmPrintBtn').disabled = false;
        console.log("Auto-selected USB printer:", usbPrinterName);
        debugVariables("after USB auto-selection");
    } else {
        console.log("No USB printer found or enabled");
        debugVariables("after no USB found");
    }
}

/**
 * Handles the click on a printer entry in the printer selection dialog;
 * marks the printer as selected and enables the confirm button.
 *
 * @param {string} printerName - CUPS queue name of the selected printer.
 * @param {string} printerStatus - Status of the printer ('enabled' or 'disabled').
 */
function selectPrinter(printerName, printerStatus) {
    if (printerStatus !== 'enabled') {
        firework.launch('Dieser Drucker ist nicht verfügbar.', 'error', 3000);
        return;
    }
    
    selectedPrinter = printerName;
    debugVariables("after manual selection");
    
    // Update radio button selection
    document.querySelectorAll('.printer-item').forEach(
        /**
         * Removes the 'selected' CSS class from a printer list item.
         *
         * @param {Element} item - Printer list item element.
         */
        function(item) {
        item.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');
    
    // Update radio button
    var radioId = 'printer_' + printerName.replace(/[^a-zA-Z0-9]/g, '_');
    document.getElementById(radioId).checked = true;
    
    // Enable confirm button
    document.getElementById('confirmPrintBtn').disabled = false;
}

// Confirm print button click handler
/**
 * Attaches the click handler to the print confirmation button once the
 * DOM is fully loaded.
 */
document.addEventListener('DOMContentLoaded', function() {
    var confirmBtn = document.getElementById('confirmPrintBtn');
    if (confirmBtn) {
        /**
         * Click handler for the confirm-print button; starts the print job
         * for the selected printer and closes the dialog.
         */
        confirmBtn.addEventListener('click', function() {
            if (selectedPrinter && currentBookingData) {
                // Call the function BEFORE closing the dialog to preserve the variables
                callReceiptGeneratorWithPrinter(currentBookingData, 'print', selectedPrinter);
                closePrinterDialog();
            }
        });
    }
});

// Alternative click handler for inline onclick
/**
 * Confirms the printer selection and sends the print job via
 * callReceiptGeneratorWithPrinter(), then closes the dialog.
 */
function confirmPrint() {
    debugVariables("confirmPrint start");
    
    if (!selectedPrinter) {
        firework.launch('Bitte wählen Sie einen Drucker aus.', 'error', 3000);
        return;
    }
    
    if (!currentBookingData) {
        firework.launch('Keine Buchungsdaten verfügbar.', 'error', 3000);
        return;
    }
    
    debugVariables("before calling callReceiptGeneratorWithPrinter");
    
    // Call the function BEFORE closing the dialog to preserve the variables
    callReceiptGeneratorWithPrinter(currentBookingData, 'print', selectedPrinter);
    closePrinterDialog();
}

/**
 * Calls the receipt-generator API to send a receipt for a booking to a
 * specific printer queue.
 *
 * @param {Object} bookingData - Booking data returned by ajax/getBookingData.php.
 * @param {string} bookingData.value - Total value of the booking.
 * @param {string|number} bookingData.booking_id - ID of the booking.
 * @param {string} bookingData.teacher - Name of the teacher.
 * @param {string} bookingData.class - Class name.
 * @param {string} bookingData.payment_type - Payment type of the booking.
 * @param {string} outputType - Output type; expects 'print' for printing.
 * @param {string} printerName - CUPS queue name of the target printer.
 */
function callReceiptGeneratorWithPrinter(bookingData, outputType, printerName) {
    debugVariables("inside callReceiptGeneratorWithPrinter");
    console.log("Function parameters - bookingData:", bookingData, "outputType:", outputType, "printerName:", printerName);
    console.log("Globals - currentBookingData:", currentBookingData, "selectedPrinter:", selectedPrinter);
    
    if (!bookingData || !bookingData.booking_id) {
        console.error("Invalid booking data:", bookingData);
        firework.launch("Ungültige Buchungsdaten!", 'error', 5000);
        hideProgressBar();
        return;
    }
    
    // Find printer description from the stored printer data
    var printerDescription = printerName; // fallback to name
    // Try to get description from the dialog's printer list
    var printerItems = document.querySelectorAll('.printer-item');
    printerItems.forEach(
        /**
         * Finds the selected printer item and extracts its displayed
         * printer name as description for notifications.
         *
         * @param {Element} item - Printer list item element.
         */
        function(item) {
        var radio = item.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
            var nameElement = item.querySelector('.printer-name');
            if (nameElement) {
                printerDescription = nameElement.textContent.trim();
            }
        }
    });
    
    console.log("Calling receipt-generator with data:", bookingData, "outputType:", outputType, "printer:", printerName);
    
    firework.launch("Beleg f&uuml;r Buchung " + bookingData.booking_id + " wird an Drucker '" + printerDescription + "' gesendet...", 'success', 5000);
    
    var xhttp = new XMLHttpRequest();
    /**
     * Handles the receipt-generator ajax response for a print job and
     * shows a success or error notification.
     */
    xhttp.onreadystatechange = function() {
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            hideProgressBar();
            
            if (outputType === 'print') {
                // Handle print response
                try {
                    var response = JSON.parse(this.responseText);
                    if (response.status === 'printed') {
                        firework.launch("Beleg wurde erfolgreich an Drucker '" + printerDescription + "' gesendet!", 'success', 5000);
                        console.log("Print job sent successfully:", response);
                    } else {
                        firework.launch("Fehler beim Drucken!", 'error', 5000);
                    }
                } catch (e) {
                    console.error("Error parsing print response:", e);
                    firework.launch("Fehler beim Drucken!", 'error', 5000);
                }
            }
        }
        else if (this.readyState == XMLHttpRequest.DONE) {
            hideProgressBar();
            console.error("Error generating receipt:", this.responseText);
            firework.launch("Fehler beim Erstellen des Belegs!", 'error', 5000);
        }
    };
    
    showProgressBar();
    
    // Prepare data for receipt-generator API
    var requestData = {
        value: bookingData.value,
        booking_id: bookingData.booking_id,
        teacher: bookingData.teacher,
        class: bookingData.class,
        payment_type: bookingData.payment_type,
        output_type: outputType,
        cups_queue_name: printerName
    };
    
    console.log("Sending request to receipt-generator:", requestData);
    
    xhttp.open("POST", receiptGeneratorUrl + "/api/generate-receipt", true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify(requestData));
}

