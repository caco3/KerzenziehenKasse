var watchdogInterval = 100; // in ms
var watchdogCounterStartValue = 9;
                                        
var watchdogMonitoredFieldId = null;
var watchdogCounter = 0;
var watchdogTimerId =  null;

var cancelClearBasketQuestionDialogId = null;


$(document).on({    
    ajaxStop: function() { 
        hideProgressBar();
    }    
});
    
$(document).ready(function(){    
    console.log("Basket loaded");
        
//     startInputIdleTimer();
    
//     updateBasketButtonsStates();
    
    $(".removeFromBasketButton").off().on('click', function(event){
            var basketEntryId = $(event.target).attr('id');   
            console.log("removeFromBasket basketEntryId=" + basketEntryId);

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {                    
//                 console.log(this.responseText);
//                 console.log("Ready state: " + this.readyState + ", status: " + this.status);
                if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
                    var obj = JSON.parse(this.responseText);                    
                    if(obj.response.success == "true") {
                        showBasket();
                        console.log("removed from basket.\nResponse: " + this.responseText);
                    }
                    else{
                        firework.launch("Konnte Artikel nicht aus dem Warenkorb entfernen!", 'error', 5000);
                    }
//                     updateBasketButtonsStates();
                }
            };
            var params = "basketEntryId=" + basketEntryId;
//             console.log(params);

//             setPayButtonStateEnabled(false);
//             setCancelButtonStateEnabled(false);
//             setUpdateButtonStateEnabled(false);
            showProgressBar();  
                
            xhttp.open("POST", "ajax/removeFromBasket.php", true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.send(params);
        }
    );
    
    
 
    
//     $(".basketQuantityInput").keydown(
//         function(event){
// //             console.log("keydown which: " + event.which);
//                                 
//             if( // The following key are not to be ignored:          
//                 ((event.which >= 48 && event.which <= 57) && !event.shiftKey)      ||     // numbers (without shift key)               
//                 (event.which >= 96 && event.which <= 105)     ||     // keypad numbers
// //                 (event.which >= 8 && event.which <= 13)       ||     // backspace, tab, enter   
// //                 ($.inArray(event.which, [ 8, 9, 13, 35, 36, 37, 39, 46, 110, 116, 144, 190]) !== -1)  // backspace, tab, enter, end, home, left arrow, right arrow, remove, decimal point, F5, num lock, period
//                 ($.inArray(event.which, [ 8, 9, 13, 35, 36, 37, 39, 46, 116, 144]) !== -1)  // backspace, tab, enter, end, home, left arrow, right arrow, remove, F5, num lock
//             ) { // accept key press
// //                 console.log("ok, accept key");
//                 return;
//             }
//             else { // undo key press
//                 event.preventDefault();
// //                 console.log("undo key press");
//             }
//         }
//     );
    
    
//     $(".basketQuantityInput").keyup(
//         function(event){
//             console.log("keyup which: " + event.which);
//             
//             var inputFieldId = $(event.target).attr('id'); 
// 
//             if(event.which == 13) { // enter key
//                 console.log("directly send to server instead of waiting for timeout");
//                 // directly send to server instead of waiting for timeout
//                 updateBasketEntry(inputFieldId);
//             }
//             else if( // The following key are not to be ignored:          
//                 ((event.which >= 48 && event.which <= 57) && !event.shiftKey)      ||     // numbers (without shift key)               
//                 (event.which >= 96 && event.which <= 105)     ||     // keypad numbers
//                 ($.inArray(event.which, [ 8, 46]) !== -1)        // backspace, remove
//             ) { // ok, refresh basket
// //                 console.log("ok, accept key");
//                     
//                 //prevent empty or zero field
//     /*            if(($("#" + inputFieldId).val() == "") || ($("#" + inputFieldId).val() == 0)) {
//                     $("#" + inputFieldId).val(1);
//                 }         */  
// 
//                 // tell watchdog to update basket after timeout
//                 updateInputIdleTimer(inputFieldId);
//             }
//             else { //all other keys should not refresh basket
//                 return;
//             }            
//         }
//     );
    
    
    
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
            
            // TODO: on decimal point press, remove old key and add decimal point on new position
            
            
            if( // The following key are not to be ignored:          
                ((event.which >= 48 && event.which <= 57) && !event.shiftKey)      ||     // numbers (without shift key)            
                (event.which >= 96 && event.which <= 105)     ||     // keypad numbers
                ($.inArray(event.which, [ 8, 9, 13, 35, 36, 37, 39, 46, 116, 144]) !== -1) || // backspace, tab, enter, end, home, left arrow, right arrow, remove, F5, num lock
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
                        
            var inputFieldId = $(event.target).attr('id'); 
            
            if(event.which == 13) { // enter key
                console.log("directly send to server instead of waiting for timeout");
                // directly send to server instead of waiting for timeout
                updateBasketEntry(inputFieldId);
            }
            else if( // The following key are not to be ignored:          
                ((event.which >= 48 && event.which <= 57) && !event.shiftKey)      ||     // numbers (without shift key)                
                (event.which >= 96 && event.which <= 105)     ||     // keypad numbers
                ($.inArray(event.which, [ 8, 13, 46]) !== -1) ||     // backspace, enter, remove
                ($.inArray(event.which, [ 110, 190]) !== -1)         // decimal point, period
            ) { // ok, refresh basket
//                 console.log("ok, accept key");
                                 
                // tell watchdog to update basket after timeout
                updateInputIdleTimer(inputFieldId);
            }
            else { //all other keys should not refresh basket
                return;
            }            
            
            // TODO: improve handling (add timeout, keep selection, ...)
        }
    ); 
    
    
    $(".basketMoneyInput").focusout(
        function(event){
//             console.log("focus losed", this.id);
            formatCurrencyField(this.id);
        }
    ); 
    
    
    
    $(".basketMoneyInput").each(
        function(id, obj) {
            formatCurrencyField(this.id);
        }
    ); 
    
    
    
    $(".payButton").off().on('click', 
        function(event){
//             console.log("watchdogCounter: " + watchdogCounter);
            if(watchdogCounter > 0) {
                firework.launch("Aktualisiere Warenkorb...<br>Bitte versuch es in einer Sekunde noch einmal!", 'error', 5000);
            }
            else { //In sync with server
                if ($("#basketTotalMoney").val() != 0) { // Basket contains something
                    moveBasketToBookings();
                }
                else { // Basket is empty
                    firework.launch("Der Warenkorb ist leer!", 'warning', 5000);
                }
            }
        }
    ); 
    
    
    
    $(".cancelButton").off().on('click', 
        function(event){
            clearBasket();
        }
    ); 
    
    
    
    $(".updateButton").off().on('click', 
        function(event){
            updateBasketinBookings();
        }
    );     
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
                console.log("Moved basket to bookings (ID: " + obj.response.bookingId + ")");
                showBasket();
//                 console.log("removed from basket.\nResponse: " + this.responseText); 
//                 firework.launch("Buchung erfolgreich abgeschlossen. <a href=\"receipt.php\" target=\"_blank\">Beleg generieren</a>", 'success', 5000);
                firework.launch("Buchung " + obj.response.bookingId + " erfolgreich abgeschlossen.", 'success', 5000);
            }
            else{
                firework.launch("Konnte Warenkorb nicht freigeben!", 'error', 5000);
                hideProgressBar();
            }
        }
    };
    var params = "";

    showProgressBar();  
        
    xhttp.open("POST", "ajax/moveBasketToBookings.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}



function clearBasket() {    
    console.log("user requests to clear basket");
    // Note: CSS style must be inlined since it will not get picked up from an external style sheet!
    
    if (document.getElementById("payButton")) { // we are in normal mode
        if ($("#basketTotalMoney").val() == 0) { // Basket is already empty
            console.log("Basket is empty, no need to show a message");
            return;
        }   
        var message = "Soll der Warenkorb wirklich geleert werden?<br>Dieser Schritt kann nicht rückgängig gemacht werden!";
    }
    else { // we are in update mode
        var message = "Soll das Editieren dieser bestehenden Buchung abgebrochen werden?<br>Allfällige Änderungen gehen verloren!";
    }
    
    cancelClearBasketQuestionDialogId = firework.launch(message + 
    "<br><button style=\"font-size: 100%;\" onclick=\"definitlyClearBasket()\">Ja</button> <button style=\"font-size: 100%;\" onclick=\"cancelClearBasket()\">Nein</button>", 'warning', 60000);
}



function cancelClearBasket() { 
    console.log("cancel clearing basket");
    firework.remove("#"+ cancelClearBasketQuestionDialogId);
    cancelClearBasketQuestionDialogId = null;
}


function definitlyClearBasket() {    
    console.log("clearing basket");
    firework.remove("#"+ cancelClearBasketQuestionDialogId);
    cancelClearBasketQuestionDialogId = null;
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {                    
//                 console.log(this.responseText);
//                 console.log("Ready state: " + this.readyState + ", status: " + this.status);
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            var obj = JSON.parse(this.responseText);                    
            if(obj.response.success == "true") {
                console.log("Basket emptied");
                showBasket();
                firework.launch("Warenkorb ist nun leer.", 'success', 5000);
            }
            else{
                firework.launch("Konnte Warenkorb nicht leeren!", 'error', 5000);
                hideProgressBar();
            }
        }
    };
    var params = "";

    showProgressBar();  
        
    xhttp.open("POST", "ajax/clearBasket.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}






function updateBasketinBookings() {    
    console.log("update basket in bookings");
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {                    
//                 console.log(this.responseText);
//                 console.log("Ready state: " + this.readyState + ", status: " + this.status);
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            var obj = JSON.parse(this.responseText);                    
            if(obj.response.success == "true") {
                console.log("Updated basket in bookings (ID: " + obj.response.bookingId + ")");
                showBasket();
                firework.launch("Buchung " + obj.response.bookingId + " erfolgreich aktualisiert.", 'success', 5000);
            }
            else{
                firework.launch("Konnte Buchung nicht aktualisieren!", 'error', 5000);
                hideProgressBar();
            }
        }
    };
    var params = "";

    showProgressBar();  
        
    xhttp.open("POST", "ajax/updateBasketInBookings.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}






function getBasketEntryIdfromInputFieldId(inputFieldId){ 
    basketEntryId = inputFieldId.replace("basketEntryId_", "");
    basketEntryId = basketEntryId.replace("_quantity", "");
    basketEntryId = basketEntryId.replace("_price", "");
    
    return basketEntryId;
}



function updateBasketEntry(basketInputFieldId) {
    basketEntryId = getBasketEntryIdfromInputFieldId(basketInputFieldId);
    
//         console.log("basketEntryId:", basketEntryId);

    if(basketEntryId == "basketDonationMoney"){
        var quantity = 1;
        var price = $("#basketDonationMoney").val();   
        if(price == ""){
            $("#basketDonationMoney").val(0);
        }
    }
    else if(basketEntryId == "basketTotalMoney"){
        var quantity = 1;
        var price = $("#basketTotalMoney").val();  
    }
    else { // its an article     
        var quantity = $("#basketEntryId_" + basketEntryId + "_quantity").val();   
        var price = $("#basketEntryId_" + basketEntryId + "_price").val();    
    }
    
    if(quantity == ""){
        quantity = 1;
        $("#basketEntryId_" + basketEntryId + "_quantity").val(quantity);
    }
    
    if(price == ""){
        price = 0;
//             $("#basketEntryId_" + basketEntryId + "_price").val(price);
    }
            
    price = formatCurrency(price)
    
    console.log("basketEntryId:", basketEntryId, "quantity:", quantity, "price:", price);
        
    //if empty, set to 1 and cursor right to it
    if(quantity == ""){
        quantity = 0;
        inputField = document.getElementById(basketInputFieldId);
        inputField.selectionStart = 1;
    }
            
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() { 
//         console.log("Ready state: " + this.readyState + ", status: " + this.status);
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {                   
//             console.log(this.responseText);
            var obj = JSON.parse(this.responseText);            
            if(obj.response.success == "true") {                
//                 showBasket();
                console.log("Updated " + basketInputFieldId +" in basket.\nResponse: " + this.responseText);
                
                console.log("Updating changed fields:");                
//                 console.log(obj.updatedFields);
                
                jQuery.each(obj.updatedFields, function(id, val) { // total changed
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
                    
                    if(id == 'totalRounded'){
                        console.log("  Total (rounded): " + val);
                        $("#" + 'basketTotalMoneyRounded').html("CHF " + val)                       
                    }
                    
                    if(id == 'donation'){ // donation changed
                        console.log("  Donation: " + val);
                        $("#" + 'basketDonationMoney').val(formatCurrency(val))
                        // TODO round to 0.05
                        
                        if((obj.corrections) && (obj.corrections.action == 'uprounded')) { // There was a correction and the total was "uprounded"
                            // suppress success notification
                        }
                        else {
                            firework.launch("Spende in Warenkorb aktualisiert.", 'success', 5000);
                        }
                    }
                    
                    if(id == 'article'){ // an article changed              
                        jQuery.each(val, function($basketEntryId, val2) {
                            console.log("  Article "+ $basketEntryId + ": " + val2.price);
                            $("#basketEntryId_" + $basketEntryId + "_price").val(formatCurrency(val2.price));
                        });
                    }
                });
                            
                // Monitor input field changes again                           
                hideProgressBar(); 
//                 updateBasketButtonsStates();                 
            }
            else{
                firework.launch("Konnte Preis in Warenkorb nicht aktualisieren!", 'error', 5000);
            }
        }
    };

    
    var params = "basketEntryId=" + basketEntryId + "&quantity=" + quantity + "&price=" + price;    
    console.log("Parameters:", params);

    showProgressBar();  
    
    xhttp.open("POST", "ajax/updateInBasket.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}







function updateArticleQuantityInBasket(basketEntryId, quantity) {
    console.log("basketEntryId: " + basketEntryId + ", quantity: " + quantity);
    $("#basketEntryId_" + basketEntryId + "_quantity").val(quantity);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() { 
//         console.log("Ready state: " + this.readyState + ", status: " + this.status);
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {                   
//             console.log(this.responseText);
            var obj = JSON.parse(this.responseText);            
            if(obj.response.success == "true") {                
//                 showBasket();
//                 console.log("Updated " + basketInputFieldId +" in basket.\nResponse: " + this.responseText);
                console.log("Updated basket.\nResponse: " + this.responseText);
                
                console.log("Updating changed fields:");                
//                 console.log(obj.updatedFields);
                
                jQuery.each(obj.updatedFields, function(id, val) { // total changed
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
                    
                    if(id == 'totalRounded'){
                        console.log("  Total (rounded): " + val);
                        $("#" + 'basketTotalMoneyRounded').html("CHF " + val)                       
                    }
                    
                    if(id == 'donation'){ // donation changed
                        console.log("  Donation: " + val);
                        $("#" + 'basketDonationMoney').val(formatCurrency(val))
                        // TODO round to 0.05
                        
                        if((obj.corrections) && (obj.corrections.action == 'uprounded')) { // There was a correction and the total was "uprounded"
                            // suppress success notification
                        }
                        else {
                            firework.launch("Spende in Warenkorb aktualisiert.", 'success', 5000);
                        }
                    }
                    
                    if(id == 'article'){ // an article changed              
                        jQuery.each(val, function($basketEntryId, val2) {
                            console.log("  Article "+ $basketEntryId + ": " + val2.price);
                            $("#basketEntryId_" + $basketEntryId + "_price").val(formatCurrency(val2.price));
                        });
                    }
                });
                            
                // Monitor input field changes again                           
                hideProgressBar(); 
//                 updateBasketButtonsStates();                 
            }
            else{
                firework.launch("Konnte Preis in Warenkorb nicht aktualisieren!", 'error', 5000);
            }
        }
    };

    
    var params = "basketEntryId=" + basketEntryId + "&quantity=" + quantity + "&price=0";    
    console.log("Parameters:", params);

    showProgressBar();  
    
    xhttp.open("POST", "ajax/updateInBasket.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}


















// function updateBasketButtonsStates() {            
//     console.log("basketEntryId:", basketEntryId);
    
//     console.log("Total: _" + parseInt($("#" + 'basketTotalMoney').val() * 100) + "_");
//     if (parseInt($("#" + 'basketTotalMoney').val() * 100) == 0) { // total=0
//         setPayButtonStateEnabled(false);
//         setCancelButtonStateEnabled(false);
        /* Notes: 
         * Allow update button since we might want to update the booking to "empty" */
//         console.log("Buttons disabled");
//     }
//     else {                            
//         setPayButtonStateEnabled(true);
//         setCancelButtonStateEnabled(true);
//         setUpdateButtonStateEnabled(true);
//         console.log("Buttons enabled");
//     }   
//     setUpdateButtonStateEnabled(true); // keep update button enabled since we might want to update the booking to "empty"
//     setCancelButtonStateEnabled(true); // keep cancel button enabled since we always want to be able to cancel
// }


// function setPayButtonStateEnabled(state) {
//     $("#" + 'payButton').prop("disabled", !state);
//     if (state == false) {
//         console.log("Pay Button disabled (" + !$("#" + 'payButton').prop("disabled") + ")");
//     }
//     else {                            
//         console.log("Pay Button enabled (" + !$("#" + 'payButton').prop("disabled") + ")");
//     }    
// }
// 
// 
// function setCancelButtonStateEnabled(state) {
//     $("#" + 'cancelButton').prop("disabled", !state);  
//     if (state == false) {
//         console.log("Cancel Button disabled (" + !$("#" + 'cancelButton').prop("disabled") + ")");
//     }
//     else {                            
//         console.log("Cancel Button enabled (" + !$("#" + 'cancelButton').prop("disabled") + ")");
//     }  
// }
// 
// 
// function setUpdateButtonStateEnabled(state) {
//     $("#" + 'updateButton').prop("disabled", !state); 
//     if (state == false) {
//         console.log("Update Button disabled (" + !$("#" + 'updateButton').prop("disabled") + ")");
//     }
//     else {                            
//         console.log("Update Button enabled (" + !$("#" + 'updateButton').prop("disabled") + ")");
//     }   
// }



function watchdog() {
    watchdogCounter = watchdogCounter - 1;   
//     console.log(watchdogCounter);
    document.getElementById("timerIcon").src = "images/timer/" + (9 - watchdogCounter) + ".png";
    if (watchdogCounter > 0) { // not reached yet         
        console.log(watchdogMonitoredFieldId + ": " + watchdogCounter);
    }
    else if (watchdogCounter == 0) { //timeout reached
        stopInputIdleTimer(watchdogTimerId);

        updateBasketEntry(watchdogMonitoredFieldId);
        return;
    }        
    watchdogTimerId = setTimeout(watchdog, watchdogInterval); //reload watchdog timer
}
    

function startInputIdleTimer(){
    stopInputIdleTimer(watchdogTimerId);
    watchdogTimerId = setTimeout(watchdog, watchdogInterval);   
    console.log("Started InputIdleTimer");
}       
        

function stopInputIdleTimer(watchdogTimerId){
    clearTimeout(watchdogTimerId);
    console.log("Stopped InputIdleTimer");
}


function updateInputIdleTimer(inputFieldId) { 
    watchdogMonitoredFieldId = inputFieldId;
    watchdogCounter = watchdogCounterStartValue; 
//     setPayButtonStateEnabled(false);
//     setCancelButtonStateEnabled(false);
//     setUpdateButtonStateEnabled(false);
    console.log("Updated InputIdleTimer");
    startInputIdleTimer();
}
