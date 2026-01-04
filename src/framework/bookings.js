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
            console.log("printing receipt for booking " + bookingId);
            
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
                        
                        // Call receipt-generator API for printing
                        callReceiptGenerator(bookingData, 'print');
                        
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

