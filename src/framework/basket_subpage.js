var watchdogInterval = 100; // in ms
var watchdogCounterStartValue = 5;
                                        
var watchdogMonitoredField = null;
var watchdogCounter = 0;
var watchdogTimerId =  null;


function watchdog() {
    watchdogCounter = watchdogCounter - 1;   
    if (watchdogCounter > 0) { // not reached yet         
        console.log(watchdogMonitoredField + ": " + watchdogCounter);
    }
    else if (watchdogCounter == 0) { //timeout reached
        clearTimeout(watchdogTimerId);

        basketId = getBasketIdfromImputField(watchdogMonitoredField);
        
        console.log("basketId:", basketId);

        if(basketId == "basketDonationMoney"){
            var free = 0;
            var quantity = 1;
            var price = $("#basketDonationMoney").val();       
        }
        else if(basketId == "basketTotalMoney"){
            var free = 0;
            var quantity = 1;
            var price = $("#basketTotalMoney").val();  
        }
        else { // its an article
            var free = $("#basketId_" + basketId + "_free").val();       
            var quantity = $("#basketId_" + basketId + "_quantity").val();   
            var price = $("#basketId_" + basketId + "_price").val();    
        }
        
        if(quantity == ""){
            quantity = 1;
        }
        
        if(price == ""){
            price = 0;
        }
        
        
        price = formatCurrency(price)
        
        
        
        
        console.log("basketId:", basketId, "free:", free, "quantity:", quantity, "price:", price);
        
        updateBasketEntry(basketId, free, quantity, price);
        return;
    }        
    watchdogTimerId = setTimeout(watchdog, watchdogInterval); //reload watchdog timer
}
    
        
        

function startWatchdog(){
    clearTimeout(watchdogTimerId);
    watchdogTimerId = setTimeout(watchdog, watchdogInterval);   
}


    
$(document).ready(function(){    
    console.log("Basket loaded");
//     restoreFocus();
        
    
    
    startWatchdog();
    
    
    
    $(".deleteFromBasketButton").off().on('click', function(event){
            var basketId = $(event.target).attr('id');   
            console.log("deleteFromBasket basketId=" + basketId);

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {                    
//                 console.log(this.responseText);
//                 console.log("Ready state: " + this.readyState + ", status: " + this.status);
                if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
                    var obj = JSON.parse(this.responseText);                    
                    if(obj.response.success == "true") {
                        showBasket();
                        console.log("deleted from basket.\nResponse: " + this.responseText);
                    }
                    else{
//                         showFullPageOverlay("Fehler: Konnte Artikel nicht aus dem Warenkorb entfernen!");
                        firework.launch("Konnte Artikel nicht aus dem Warenkorb entfernen!", 'danger', 5000);
                    }
                }
            };
            var params = "basketId=" + basketId;
//             console.log(params);

            showProgressBar();  
                
            xhttp.open("POST", "ajax/deleteFromBasket.php", true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.send(params);
        }
    );
    
    
 
    
    $(".basketQuantityInput").keydown(
        function(event){
//             console.log("keydown which: " + event.which);
                                
            if( // The following key are not to be ignored:          
                ((event.which >= 48 && event.which <= 57) && !event.shiftKey)      ||     // numbers (without shift key)               
                (event.which >= 96 && event.which <= 105)     ||     // keypad numbers
//                 (event.which >= 8 && event.which <= 13)       ||     // backspace, tab, enter   
//                 ($.inArray(event.which, [ 8, 9, 13, 35, 36, 37, 39, 46, 110, 116, 144, 190]) !== -1)  // backspace, tab, enter, end, home, left arrow, right arrow, delete, decimal point, F5, num lock, period
                ($.inArray(event.which, [ 8, 9, 13, 35, 36, 37, 39, 46, 116, 144]) !== -1)  // backspace, tab, enter, end, home, left arrow, right arrow, delete, F5, num lock
            ) { // accept key press
//                 console.log("ok, accept key");
                return;
            }
            else { // undo key press
                event.preventDefault();
//                 console.log("undo key press");
            }
        }
    );
    
    
    $(".basketQuantityInput").keyup(
        function(event){
//             console.log("keyup which: " + event.which);
            
            if( // The following key are not to be ignored:          
                ((event.which >= 48 && event.which <= 57) && !event.shiftKey)      ||     // numbers (without shift key)               
                (event.which >= 96 && event.which <= 105)     ||     // keypad numbers
                ($.inArray(event.which, [ 8, 13, 46]) !== -1)        // backspace, enter, delete
            ) { // ok, refresh basket
//                 console.log("ok, accept key");
            }
            else { //all other keys should not refresh basket
                return;
            }            
            
            var inputField = $(event.target).attr('id');     
                        
//             basketId = inputField.replace("basketId_", "");
//             basketId = basketId.replace("_quantity", "");
            
            //prevent empty or zero field
/*            if(($("#" + inputField).val() == "") || ($("#" + inputField).val() == 0)) {
                $("#" + inputField).val(1);
            }         */  

            // tell watchdog to update basket after timeout
            watchdogMonitoredField = inputField;
            watchdogCounter = watchdogCounterStartValue;           
        }
    );
    
    
    
    
     
    
    $(".basketMoneyInput").keydown(
        function(event){
//             console.log("keydown which: " + event.which);
                                
            // special handling of decimalpoint and period
            if((event.which == 190) || (event.which == 110)) { // dot or decimal point pressed
                var inputField = $(event.target).attr('id');  
                var value = $("#" + inputField).val()
                if(getNumberOfDecimalPoints(value) > 0) { // there was already a Decimal Point
                    event.preventDefault();
                    console.log("undo key press (Decimal Point)");
                }
            }
            
            // TODO: on decimal point prtess, remove odl key, add decimal point on new position
            
            
            if( // The following key are not to be ignored:          
                ((event.which >= 48 && event.which <= 57) && !event.shiftKey)      ||     // numbers (without shift key)            
                (event.which >= 96 && event.which <= 105)     ||     // keypad numbers
                ($.inArray(event.which, [ 8, 9, 13, 35, 36, 37, 39, 46, 116, 144]) !== -1) || // backspace, tab, enter, end, home, left arrow, right arrow, delete, F5, num lock
                ($.inArray(event.which, [ 110, 190]) !== -1)         // decimal point, period
            ) { // accept key press
//                 console.log("ok, accept key");
                return;
            }
            else { // undo key press
                event.preventDefault();
//                 console.log("undo key press");
            }
        }
    );
    
    
    $(".basketMoneyInput").keyup(
        function(event){
//             console.log("keyup which: " + event.which);
                        
            if( // The following key are not to be ignored:          
                ((event.which >= 48 && event.which <= 57) && !event.shiftKey)      ||     // numbers (without shift key)                
                (event.which >= 96 && event.which <= 105)     ||     // keypad numbers
                ($.inArray(event.which, [ 8, 13, 46]) !== -1) ||     // backspace, enter, delete
                ($.inArray(event.which, [ 110, 190]) !== -1)         // decimal point, period
            ) { // ok, refresh basket
//                 console.log("ok, accept key");
            }
            else { //all other keys should not refresh basket
                return;
            }            

            
//             // Formating field
//             var inputField = $(event.target).attr('id');  
//             formatCurrencyField(inputField);
                      
                                             
//             console.log("### update basket");
//             var inputField = $(event.target).attr('id');  
//             basketId = inputField.replace("basketId_", "");
//             basketId = basketId.replace("_quantity", "");
//             var quantity = $("#" + inputField).val(); 
//             updateBasketEntry(inputField, quantity);
                                 
                                 
            // tell watchdog to update basket after timeout
            var inputField = $(event.target).attr('id');  
            watchdogMonitoredField = inputField;
            watchdogCounter = watchdogCounterStartValue;  
            
            // TODO: improve handling (add timeout, keep selection, ...)
        }
    ); 
    
    
    $(".basketMoneyInput").focusout(
        function(event){
            inputField = this.id;
            console.log("focus losed", inputField);
            formatCurrencyField(inputField);
        }
    ); 
    
    
    
    $(".basketMoneyInput").each(
        function(id, obj) {
//             console.log("basketMoneyInput: " + id + ", " + this.id);
            inputField = this.id;
            formatCurrencyField(inputField);
        }
    ); 
    
    
    
    $(".payButton").off().on('click', 
        function(event){
            moveBasketToBookings();
        }
    );
    
    
//     $(".createReceiptButton").off().on('click', 
//         function(event){
//             moveBasketToBookings(true);
//         }
//     );
    
});




function moveBasketToBookings() {    
    console.log("pay (move basket to bookings)");
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {                    
//                 console.log(this.responseText);
//                 console.log("Ready state: " + this.readyState + ", status: " + this.status);
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            var obj = JSON.parse(this.responseText);                    
            if(obj.response.success == "true") {
                showBasket();
//                 console.log("deleted from basket.\nResponse: " + this.responseText); 
//                 firework.launch("Buchung erfolgreich abgeschlossen. <a href=\"receipt.php\" target=\"_blank\">Beleg generieren</a>", 'success', 5000);
                firework.launch("Buchung erfolgreich abgeschlossen.", 'success', 5000);
            }
            else{
//                 showFullPageOverlay("Fehler: Konnte Warenkorb nicht freigeben!");
                firework.launch("Konnte Warenkorb nicht freigeben!", 'danger', 5000);
                hideProgressBar();
            }
        }
    };
    var params = "";

    showProgressBar();  
        
    xhttp.open("POST", "ajax/pay.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}








function getBasketIdfromImputField(inputField){ 
    basketId = inputField.replace("basketId_", "");
    basketId = basketId.replace("_quantity", "");
    basketId = basketId.replace("_price", "");
    
    return basketId;
}



function updateBasketEntry(basketId, free, quantity, price) {
    // Store cursor position persistently
    
    //if empty, set to 1 and cursor right to it
    if(quantity == ""){
        quantity = 0;
        inputFieldId = document.getElementById(inputField);
        inputFieldId.selectionStart = 1;
    }
    
//     saveFocus(inputField);    
            
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() { 
//         console.log("Ready state: " + this.readyState + ", status: " + this.status);
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {                   
//             console.log(this.responseText);
            var obj = JSON.parse(this.responseText);            
            if(obj.response.success == "true") {                
//                 showBasket();
                console.log("Updated " + inputField +" in basket.\nResponse: " + this.responseText);
                
                console.log("Updating changed fields:");                
//                 console.log(obj.updatedFields);
                
                jQuery.each(obj.updatedFields, function(id, val) {
                    if(id == 'total'){
//                         console.log("Old  Total: " + $("#" + 'basketTotalMoney').val());
                        console.log("  Total: " + val);
                        $("#" + 'basketTotalMoney').val(formatCurrency(val))
                        
                        if((obj.corrections) && (obj.corrections.action == 'uprounded')) { // There was a correction and the total was "uprounded"
                            firework.launch("Total in Warenkorb korrigiert auf Mindestbetrag.", 'warning', 5000);
                        }
                        else {
                            firework.launch("Total in Warenkorb aktualisiert.", 'success', 5000);
                        }                        
                    }
                    if(id == 'donation'){
                        console.log("  Donation: " + val);
                        $("#" + 'basketDonationMoney').val(formatCurrency(val))
                        
                        if((obj.corrections) && (obj.corrections.action == 'uprounded')) { // There was a correction and the total was "uprounded"
                            // suppress success notification
                        }
                        else {
                            firework.launch("Spende in Warenkorb aktualisiert.", 'success', 5000);
                        }
                    }
                    if(id == 'article'){                        
                        jQuery.each(val, function($basketEntryId, val2) {
                            console.log("  Article "+ $basketEntryId + ": " + val2.price);
                            $("#basketId_" + $basketEntryId + "_price").val(formatCurrency(val2.price));
                        });
                    }
                });
                            
                // Monitor input field changes again
                startWatchdog();                                                
                hideProgressBar();                  
            }
            else{
//                 showFullPageOverlay("Fehler: Konnte Preis von Artikel " + inputField + " in Warenkorb nicht aktualisieren!");
                firework.launch("Konnte Preis von Artikel " + inputField + " in Warenkorb nicht aktualisieren!", 'danger', 5000);
            }
        }
    };

    
    
    
    
    var params = "basketId=" + basketId + "&free=" + free + "&quantity=" + quantity + "&price=" + price;    
    console.log("Parameters:", params);

    showProgressBar();  
    
    xhttp.open("POST", "ajax/updateInBasket.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}






// function saveFocus(inputField){    
//     // Store field persistently
//     inputFieldActive = inputField;
//     
//     // Store cursor position persistently
//     inputFieldId = document.getElementById(inputField);    
//     inputFieldSelection = inputFieldId.selectionStart;
//     
//     console.log("## Remembering to set focus to " + inputField + " after reloading basket (cursor position: " + inputFieldSelection + ")");
// }


// function restoreFocus(){
//     try { // try to restore focus and selection
//         if(inputFieldActive != null){
//             inputFieldId = document.getElementById(inputFieldActive);
//             inputFieldId.focus();
//             inputFieldId.selectionStart = inputFieldSelection;
//         console.log("## Setting focus to: " + inputFieldId + " (cursor position: " + inputFieldSelection + ")");
//         }
//         else {
//             console.log("Nothing to set focus to!");
//         }  
//     }
//     catch(error) {
//         // nothing to do
//     }
// }
