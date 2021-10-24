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
        
    
    
    $(".payButton").off().on('click', 
        function(event){
            if ($("#basketTotalMoney").val() != 0) { // Basket contains something
                moveBasketToBookings();
            }
            else { // Basket is empty
                firework.launch("Der Warenkorb ist leer!", 'warning', 5000);
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



function updateBasketEntry(basketEntryId, quantity) {
    console.log("basketEntryId: " + basketEntryId + ", quantity: " + quantity);
    
    if((basketEntryId == "basketDonationMoney") || (basketEntryId == "basketTotalMoney")){
        var price = quantity
        var quantity = 1;
        $("#" + basketEntryId).val(formatCurrency(price));
    }
    else { // its an article     
        $("#basketEntryId_" + basketEntryId + "_quantity").val(quantity);
        var price = 0; // no longer used in this case
    }        
    
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

     
    var params = "basketEntryId=" + basketEntryId + "&quantity=" + quantity + "&price=" + price;  
    console.log("Parameters:", params);

    showProgressBar();  
    
    xhttp.open("POST", "ajax/updateInBasket.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}
