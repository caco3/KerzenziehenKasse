var watchdogInterval = 100; // in ms
var watchdogCounterStartValue = 9;
                                        
var watchdogMonitoredFieldId = null;
var watchdogCounter = 0;
var watchdogTimerId =  null;


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
    setPayButtonStateEnabled(false);
    console.log("Updated InputIdleTimer");
    startInputIdleTimer();
}

    
$(document).ready(function(){    
    console.log("Basket loaded");
        
//     startInputIdleTimer();
    
    updatePayButtonState();
    
    $(".removeFromBasketButton").off().on('click', function(event){
            var basketId = $(event.target).attr('id');   
            console.log("removeFromBasket basketId=" + basketId);

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
                }
            };
            var params = "basketId=" + basketId;
//             console.log(params);

            showProgressBar();  
                
            xhttp.open("POST", "ajax/removeFromBasket.php", true);
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
//                 ($.inArray(event.which, [ 8, 9, 13, 35, 36, 37, 39, 46, 110, 116, 144, 190]) !== -1)  // backspace, tab, enter, end, home, left arrow, right arrow, remove, decimal point, F5, num lock, period
                ($.inArray(event.which, [ 8, 9, 13, 35, 36, 37, 39, 46, 116, 144]) !== -1)  // backspace, tab, enter, end, home, left arrow, right arrow, remove, F5, num lock
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
            console.log("keyup which: " + event.which);
            
            var inputFieldId = $(event.target).attr('id'); 

            if(event.which == 13) { // enter key
                console.log("directly send to server instead of waiting for timeout");
                // directly send to server instead of waiting for timeout
                updateBasketEntry(inputFieldId);
            }
            else if( // The following key are not to be ignored:          
                ((event.which >= 48 && event.which <= 57) && !event.shiftKey)      ||     // numbers (without shift key)               
                (event.which >= 96 && event.which <= 105)     ||     // keypad numbers
                ($.inArray(event.which, [ 8, 46]) !== -1)        // backspace, remove
            ) { // ok, refresh basket
//                 console.log("ok, accept key");
                    
                //prevent empty or zero field
    /*            if(($("#" + inputFieldId).val() == "") || ($("#" + inputFieldId).val() == 0)) {
                    $("#" + inputFieldId).val(1);
                }         */  

                // tell watchdog to update basket after timeout
                updateInputIdleTimer(inputFieldId);
            }
            else { //all other keys should not refresh basket
                return;
            }            
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
            console.log("focus losed", this.id);
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
            moveBasketToBookings();
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



function getBasketIdfromInputFieldId(inputFieldId){ 
    basketId = inputFieldId.replace("basketId_", "");
    basketId = basketId.replace("_quantity", "");
    basketId = basketId.replace("_price", "");
    
    return basketId;
}



function updateBasketEntry(basketInputFieldId) {
    basketId = getBasketIdfromInputFieldId(basketInputFieldId);
    
//         console.log("basketId:", basketId);

    if(basketId == "basketDonationMoney"){
        var quantity = 1;
        var price = $("#basketDonationMoney").val();   
        if(price == ""){
            $("#basketDonationMoney").val(0);
        }
    }
    else if(basketId == "basketTotalMoney"){
        var quantity = 1;
        var price = $("#basketTotalMoney").val();  
    }
    else { // its an article     
        var quantity = $("#basketId_" + basketId + "_quantity").val();   
        var price = $("#basketId_" + basketId + "_price").val();    
    }
    
    if(quantity == ""){
        quantity = 1;
        $("#basketId_" + basketId + "_quantity").val(quantity);
    }
    
    if(price == ""){
        price = 0;
//             $("#basketId_" + basketId + "_price").val(price);
    }
            
    price = formatCurrency(price)
    
    console.log("basketId:", basketId, "quantity:", quantity, "price:", price);
        
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
                            $("#basketId_" + $basketEntryId + "_price").val(formatCurrency(val2.price));
                        });
                    }
                });
                            
                // Monitor input field changes again                           
                hideProgressBar(); 
                updatePayButtonState();                 
            }
            else{
                firework.launch("Konnte Preis in Warenkorb nicht aktualisieren!", 'error', 5000);
            }
        }
    };

    
    var params = "basketId=" + basketId + "&quantity=" + quantity + "&price=" + price;    
    console.log("Parameters:", params);

    showProgressBar();  
    
    xhttp.open("POST", "ajax/updateInBasket.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}



function updatePayButtonState() {                         
    console.log("Total: _" + parseInt($("#" + 'basketTotalMoney').val() * 100) + "_");
    if (parseInt($("#" + 'basketTotalMoney').val() * 100) == 0) {
        setPayButtonStateEnabled(false);
        console.log("Pay Button disabled");
    }
    else {                            
        setPayButtonStateEnabled(true);
        console.log("Pay Button enabled");
    }   
}


function setPayButtonStateEnabled(state) {
    if (state == false) {
        console.log("Pay Button disabled");
    }
    else {                            
        console.log("Pay Button enabled");
    }
    $("#" + 'payButton').prop("disabled", !state);    
}
