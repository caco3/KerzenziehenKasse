// $(document).on({    
//     ajaxStop: function() { 
//         console.log("stop");
//         hideProgressBar();
//     }    
// });


$(document).ready(function(){
    console.log("Bookings loaded");    
    
    $(".editButton").off().on('click', 
        function(event){
            var bookingId = $(event.target).attr('id');
            console.log("Loading booking " + bookingId + " into basket");
            
            // checking if basket is empty
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {                    
                if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
                    var obj = JSON.parse(this.responseText);
                    hideProgressBar();
                    if(obj.response.success == "true") {
//                         console.log("ok, basket is empty");
//                         copyBookingToBasket(bookingId)();
                        firework.launch("Buchung " + bookingId + " wurde in den Warenkorb geladen.<br>Du wirst gleich weitergeleitet...", 'success', 5000);
                        // load main page after 1 second
                        setTimeout(function(){ window.location.replace("index.php"); }, 1000);                        
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
        function(event){
            var bookingId = $(event.target).attr('id');
            console.log("creating receipt for booking " + bookingId);
            
            // First get booking data from database
            var xhttp = new XMLHttpRequest();
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
        function(event){
            var bookingId = $(event.target).attr('id');
            console.log("print button clicked for booking " + bookingId);
            
            // First get booking data from database
            var xhttp = new XMLHttpRequest();
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
    
});

function callReceiptGenerator(bookingData, outputType) {
    console.log("Calling receipt-generator with data:", bookingData, "outputType:", outputType);
    
    var message = outputType === 'print' ? 
        "Beleg f&uuml;r Buchung " + bookingData.booking_id + " wird gedruckt..." :
        "Beleg f&uuml;r Buchung " + bookingData.booking_id + " wird erstellt.<br><br>Das Dokument wird in einigen Sekunden ge&ouml;ffnet...";
    
    firework.launch(message, 'success', 5000);
    
    var xhttp = new XMLHttpRequest();
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
            } else if (outputType === 'print') {
                // Handle print response
                try {
                    var response = JSON.parse(this.responseText);
                    if (response.status === 'printed') {
                        firework.launch("Beleg wurde erfolgreich an Drucker gesendet!<br>Job ID: " + response.job_id, 'success', 5000);
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
        teacher: bookingData.teacher || "",
        class: bookingData.class || "",
        payment_type: bookingData.payment_type,
        output_type: outputType
    };
    
    console.log("Sending request to receipt-generator:", requestData);
    
    xhttp.open("POST", receiptGeneratorUrl + "/api/generate-receipt", true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    
    if (outputType === 'pdf') {
        xhttp.responseType = 'blob'; // Important for PDF download
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
function debugVariables(context) {
    console.log("DEBUG [" + context + "] - currentBookingData:", currentBookingData, "selectedPrinter:", selectedPrinter);
}

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

function closePrinterDialog() {
    document.getElementById('printerSelectionDialog').style.display = 'none';
    currentBookingData = null;
    selectedPrinter = null;
}

function loadPrinters() {
    var xhttp = new XMLHttpRequest();
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

function displayPrinters(printers) {
    console.log("displayPrinters called with printers:", printers);
    document.getElementById('printerLoading').style.display = 'none';
    
    if (!printers || printers.length === 0) {
        document.getElementById('printerList').innerHTML = '<p style="padding: 20px; text-align: center; color: #666;">Keine Drucker gefunden</p>';
        return;
    }
    
    var printerListHtml = '';
    var usbPrinterName = null;
    
    printers.forEach(function(printer) {
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

function selectPrinter(printerName, printerStatus) {
    if (printerStatus !== 'enabled') {
        firework.launch('Dieser Drucker ist nicht verfügbar.', 'error', 3000);
        return;
    }
    
    selectedPrinter = printerName;
    debugVariables("after manual selection");
    
    // Update radio button selection
    document.querySelectorAll('.printer-item').forEach(function(item) {
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
document.addEventListener('DOMContentLoaded', function() {
    var confirmBtn = document.getElementById('confirmPrintBtn');
    if (confirmBtn) {
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
    
    console.log("Calling receipt-generator with data:", bookingData, "outputType:", outputType, "printer:", printerName);
    
    firework.launch("Beleg f&uuml;r Buchung " + bookingData.booking_id + " wird an Drucker '" + printerName + "' gesendet...", 'success', 5000);
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            hideProgressBar();
            
            if (outputType === 'print') {
                // Handle print response
                try {
                    var response = JSON.parse(this.responseText);
                    if (response.status === 'printed') {
                        firework.launch("Beleg wurde erfolgreich an Drucker '" + printerName + "' gesendet!<br>Job ID: " + response.job_id, 'success', 5000);
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

