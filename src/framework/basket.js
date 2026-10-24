var cancelClearBasketQuestionDialogId = null;


$(document).on({    
    /**
     * Global ajaxStop handler; hides the progress bar when all pending
     * ajax requests have finished.
     */
    ajaxStop: function() { 
        hideProgressBar();
    }    
});
    
/**
 * Initializes the basket page: binds the click handler for removing
 * single entries from the basket.
 */
$(document).ready(function(){    
    console.log("Basket loaded");
        
//     startInputIdleTimer();
    
//     updateBasketButtonsStates();

    
    $(".removeFromBasketButton").on('click', 
        /**
         * Click handler for the remove-from-basket button; sends a request
         * to ajax/removeFromBasket.php to delete the entry.
         *
         * @param {jQuery.Event} event - Click event; the target's id holds the basket entry ID.
         */
        function(event){
            var basketEntryId = $(event.target).attr('id');   
            console.log("removeFromBasket basketEntryId=" + basketEntryId);

            var xhttp = new XMLHttpRequest();
            /**
             * Handles the removeFromBasket ajax response; refreshes the
             * basket view on success or shows an error notification.
             */
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

//             setcashButtonStateEnabled(false);
//             setCancelButtonStateEnabled(false);
//             setUpdateButtonStateEnabled(false);
            showProgressBar();  
                
            xhttp.open("POST", "ajax/removeFromBasket.php", true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.send(params);
        }
    );
	
    
  /*  $(".cashButton").on('click', 
        function(event){
            if ($("#basketTotalMoney").val() != 0) { // Basket contains something
                moveBasketToBookings(false);
            }
            else { // Basket is empty
                firework.launch("Der Warenkorb ist leer1!", 'warning', 5000);
            }
        }
    ); 
        
    
    $(".twintButton").on('click', 
        function(event){
			console.log("twintButton");
			twintClicked();
        }
    );  
        
    
    $(".invoiceButton").on('click', 
        function(event){
			console.log("invoiceButton");
			invoiceClicked();
        }
    );
    
    
    $(".cancelButton").on('click', 
        function(event){
            clearBasket();
        }
    ); 
    
    
    $(".updateButton").on('click', 
        function(event){
            updateBasketinBookings();
        }
    ); */
		
    console.log("basket jquery loaded");  
});








/**
 * Handles a click on the Twint payment button; completes the booking
 * with payment method 'twint' if the basket is not empty.
 */
function twintClicked() {
	console.log("twintClicked");
	if ($("#basketTotalMoney").val() != 0) { // Basket contains something
		moveBasketToBookings('twint');
	}
	else { // Basket is empty
		firework.launch("Der Warenkorb ist leer!", 'warning', 5000);
	}	
}


/**
 * Handles a click on the invoice payment button; completes the booking
 * with payment method 'invoice' if the basket is not empty.
 */
function invoiceClicked() {
	console.log("invoiceClicked");
	if ($("#basketTotalMoney").val() != 0) { // Basket contains something
		moveBasketToBookings('invoice');
	}
	else { // Basket is empty
		firework.launch("Der Warenkorb ist leer!", 'warning', 5000);
	}	
}
	
	
/**
 * Handles a click on the cash payment button; completes the booking
 * with payment method 'cash' if the basket is not empty.
 */
function cashClicked(){
	console.log("cashClicked");
	if ($("#basketTotalMoney").val() != 0) { // Basket contains something
		moveBasketToBookings('cash');
	}
	else { // Basket is empty
		firework.launch("Der Warenkorb ist leer!", 'warning', 5000);
	}
}


/**
 * Handles a click on the update button; saves the current basket changes
 * back to the edited booking.
 */
function updateClicked(){
	console.log("updateClicked");
	updateBasketinBookings();
}


/**
 * Handles a click on the cancel button; asks for confirmation before
 * clearing the basket, or clears extra data directly if it is empty.
 */
function cancelClicked(){
	console.log("cancelClicked");
	if ($("#basketTotalMoney").val() != 0) { // Basket contains something
		clearBasket();
	}
	else { // Basket is empty
        definitlyClearBasket(); // clear extra data
		//firework.launch("Der Warenkorb ist leer!", 'warning', 5000);
	}
}


/**
 * Reloads the extra data (Schulklasse, Leiter/in) via ajax/getExtraData.php
 * and updates the summary line shown next to the extra-data button.
 */
function refreshExtraSummary() {
	console.log("refreshExtraSummary called");
	var xhttp = new XMLHttpRequest();
	/**
	 * Handles the getExtraData ajax response; writes a summary of the
	 * extra data into the 'extraSummaryText' element.
	 */
	xhttp.onreadystatechange = function() {
		if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
			try {
				var obj = JSON.parse(this.responseText);
				var extra = obj.success && obj.data ? obj.data : {};
				var parts = [];
				if (extra.schulklasse) parts.push("Klasse: " + extra.schulklasse);
				if (extra.leiter) parts.push("Leiter/in: " + extra.leiter);
				var summary = parts.length ? parts.join(", ") : "Angabe von Schulklasse, Leiter/in";
				var el = document.getElementById("extraSummaryText");
				if (el) el.innerText = summary;
				console.log("Extra summary updated:", summary);
			} catch (e) {
				console.error("Failed to parse extra summary", e);
			}
		} else if (this.readyState == XMLHttpRequest.DONE) {
			console.error("Failed to load extra data. Status:", this.status, "Response:", this.responseText);
		}
	};
	xhttp.open("GET", "ajax/getExtraData.php", true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.send();
}

/**
 * Handles a click on the extra-data edit button; loads the current extra
 * data and shows a full page overlay form to edit Schulklasse and Leiter/in.
 */
function editExtraDataClicked(){
	console.log("editExtraDataClicked");
	// Load current extra data
	var xhttp = new XMLHttpRequest();
	/**
	 * Handles the getExtraData ajax response; builds the extra-data edit
	 * form and shows it in a full page overlay.
	 */
	xhttp.onreadystatechange = function() {
		if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
			var obj = JSON.parse(this.responseText);
			var extra = obj.success && obj.data ? obj.data : {};
			var schulklasse = extra.schulklasse || "";
			var leiter = extra.leiter || "";

			var content = ""
				+ "<h2 style='margin-bottom: 20px; color: #333;'>Extra-Daten</h2>"
                + "<p style='margin-bottom: 20px; color: #666;'>Diese Informationen werden auf der Rechnung/Quittung verwendet.</p>" 
				+ "<table style='margin: 0 auto; border-collapse: collapse; width: 500px;'>"
				+ "<tr><td style='padding: 15px; text-align: right; font-weight: bold; width: 120px; font-size: 20px;'>Schulklasse:</td><td style='padding: 15px;'><input type=text id='extraSchulklasse' placeholder='Schulklasse eingeben' value='" + schulklasse + "' style='width: 320px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 20px;'></td></tr>"
				+ "<tr><td style='padding: 15px; text-align: right; font-weight: bold; font-size: 20px;'>Leiter/in:</td><td style='padding: 15px;'><input type=text id='extraLeiter' placeholder='Leiter/in eingeben' value='" + leiter + "' style='width: 320px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 20px;'></td></tr>"
				+ "</table>"
				+ "<br><div style='text-align: center; margin-top: 30px;'><button class='cashButton' onclick='saveExtraData()' style='margin-right: 20px; width: 150px; height: 60px; font-size: 20px;'>Speichern</button><button class='cancelButton' onclick='hideFullPageOverlay()' style='width: 150px; height: 60px; font-size: 20px;'>Abbrechen</button></div>";
			showFullPageOverlay(content, false);
		}
	};
	xhttp.open("GET", "ajax/getExtraData.php", true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.send();
}

/**
 * Saves the extra data (Schulklasse, Leiter/in) entered in the overlay
 * form via ajax/setExtraData.php.
 */
function saveExtraData(){
	var schulklasse = document.getElementById('extraSchulklasse').value;
	var leiter = document.getElementById('extraLeiter').value;
	var extra = {schulklasse: schulklasse, leiter: leiter};

	var xhttp = new XMLHttpRequest();
	/**
	 * Handles the setExtraData ajax response; closes the overlay and
	 * refreshes the summary line on success.
	 */
	xhttp.onreadystatechange = function() {
		if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
			var obj = JSON.parse(this.responseText);
			if (obj.success) {
				console.log("Extra data saved:", extra);
				firework.launch("Extra-Daten gespeichert.", 'success', 3000);
				hideFullPageOverlay();
				refreshExtraSummary(); // Update the summary line
			} else {
				firework.launch("Fehler beim Speichern: " + (obj.error || "Unbekannt"), 'error', 5000);
			}
		}
	};
	var params = "extra=" + encodeURIComponent(JSON.stringify(extra));
	xhttp.open("POST", "ajax/setExtraData.php", true);
	xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhttp.send(params);
}


/**
 * Completes the booking: sends the basket contents with the chosen
 * payment method to ajax/moveBasketToBookings.php.
 *
 * @param {string} paymentMethod - Payment method ('cash', 'twint' or 'invoice').
 */
function moveBasketToBookings(paymentMethod) {    
    console.log("pay (move basket to bookings)");
    
    var xhttp = new XMLHttpRequest();
    /**
     * Handles the moveBasketToBookings ajax response; refreshes the basket
     * view and shows the new booking ID on success.
     */
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
	
	var params = "paymentMethod=" + paymentMethod; 
	console.log("Parameters:", params);

    showProgressBar();  
        
    xhttp.open("POST", "ajax/moveBasketToBookings.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}



/**
 * Asks the user for confirmation before clearing the basket (or aborting
 * the edit of an existing booking in update mode).
 */
function clearBasket() {    
    console.log("user requests to clear basket");
    // Note: CSS style must be inlined since it will not get picked up from an external style sheet!
    
    if (document.getElementById("cashButton")) { // we are in normal mode
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
    "<br><button class=clearBasket onclick=\"definitlyClearBasket()\">Ja </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class=cancelClearBasketButton onclick=\"cancelClearBasket()\">Nein</button>", 'warning', 60000);
}



/**
 * Cancels the clear-basket confirmation dialog and removes the
 * notification.
 */
function cancelClearBasket() { 
    console.log("cancel clearing basket");
    firework.remove("#"+ cancelClearBasketQuestionDialogId);
    cancelClearBasketQuestionDialogId = null;
}


/**
 * Confirms clearing the basket: removes the confirmation notification and
 * empties the basket via ajax/clearBasket.php.
 */
function definitlyClearBasket() {    
    console.log("clearing basket");
    firework.remove("#"+ cancelClearBasketQuestionDialogId);
    cancelClearBasketQuestionDialogId = null;
    
    var xhttp = new XMLHttpRequest();
    /**
     * Handles the clearBasket ajax response; refreshes the basket view
     * and shows a notification.
     */
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






/**
 * Saves the changes made in the basket back to the existing booking via
 * ajax/updateBasketInBookings.php.
 */
function updateBasketinBookings() {    
    console.log("update basket in bookings");
    
    var xhttp = new XMLHttpRequest();
    /**
     * Handles the updateBasketInBookings ajax response; on success
     * redirects back to the bookings page.
     */
    xhttp.onreadystatechange = function() {                    
//                 console.log(this.responseText);
//                 console.log("Ready state: " + this.readyState + ", status: " + this.status);
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            var obj = JSON.parse(this.responseText);                    
            if(obj.response.success == "true") {
                console.log("Updated basket in bookings (ID: " + obj.response.bookingId + ")");
                showBasket();
                firework.launch("Buchung " + obj.response.bookingId + " erfolgreich aktualisiert.", 'success', 5000);
				
				setTimeout(
					/** Redirects the browser back to the updated booking on the bookings page. */
					function() { window.location.replace("bookings.php#" + obj.response.bookingId); }, (1000)); // load bookings page again
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






/**
 * Extracts the numeric basket entry ID from a basket input field ID such
 * as 'basketEntryId_12_quantity'.
 *
 * @param {string} inputFieldId - ID attribute of the basket input field.
 * @returns {string} The extracted basket entry ID.
 */
function getBasketEntryIdfromInputFieldId(inputFieldId){ 
    basketEntryId = inputFieldId.replace("basketEntryId_", "");
    basketEntryId = basketEntryId.replace("_quantity", "");
    basketEntryId = basketEntryId.replace("_price", "");
    
    return basketEntryId;
}



/**
 * Sends an updated quantity or amount for a basket entry to
 * ajax/updateInBasket.php. For the special fields 'basketDonationMoney'
 * and 'basketTotalMoney' the given quantity is treated as a price.
 *
 * @param {string} basketEntryId - ID of the basket entry or special field.
 * @param {number|string} quantity - New quantity, or new amount for the special fields.
 */
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
    /**
     * Handles the updateInBasket ajax response; updates all changed fields
     * (total, rounded total, donation, article prices) in the UI.
     */
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
                
                jQuery.each(obj.updatedFields, 
                    /**
                     * Applies one updated field returned by the server to the UI.
                     *
                     * @param {string} id - Field name ('total', 'totalRounded', 'donation' or 'article').
                     * @param {*} val - New value of the field.
                     */
                    function(id, val) { // total changed
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
                        jQuery.each(val, 
                            /**
                             * Updates the displayed price of a single basket entry.
                             *
                             * @param {string} $basketEntryId - ID of the basket entry.
                             * @param {Object} val2 - Updated entry data containing the new price.
                             */
                            function($basketEntryId, val2) {
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
