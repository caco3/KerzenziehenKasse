$(document).on({    
    ajaxStop: function() { 
        hideProgressBar();
    }    
});


$(document).ready(function(){
//     console.log("Articles loaded");   
       
         
    $(".articleQuantityInput").keydown(
        function(event){            
            // Note: No debouncing on keydown since this will break the ignoring functionality on autorepeat! 
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
    
    
    $(".articleQuantityInput").keyup( 
        function(event){
            if(checkKeyDebouncing() == false){ return; }
//             console.log("keyup which: " + event.which);

            var inputFieldId = $(event.target).attr('id');     
            
//             if($("#" + inputFieldId).val() == "") { //prevent empty field
//                 $("#" + inputFieldId).val(0);
//             } 
//             else{
//                 $("#" + inputFieldId).val($("#" + inputFieldId).val() * 1);
//             }

            if(event.which == 13) { // enter key                
                addToBasket(inputFieldId.replace("quantity_", ""));
            }
                        
            // TODO keep selection
            
        }
    );
    
     
    
    $(".articleMoneyInput").keydown(
        function(event){
            // Note: No debouncing on keydown since this will break the ignoring functionality on autorepeat! 
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
            
            // TODO: on decimal point press, remove old key, add decimal point on new position
            
            
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
    
    
    $(".articleMoneyInput").keyup(
        function(event){
            if(checkKeyDebouncing() == false){ return; }
//             console.log("keyup which: " + event.which);
            
            var inputFieldId = $(event.target).attr('id');     
             
//             if($("#" + inputFieldId).val() == "") { //prevent empty field
//                 $("#" + inputFieldId).val(0);
//             } 
//             else{
//                 $("#" + inputFieldId).val($("#" + inputFieldId).val() * 1);
//             }

            if(event.which == 13) { // enter key                
                addToBasket(inputFieldId.replace("quantity_", ""));
            }
                        
            // TODO keep selection
            
        }
    );
    
    
    
    $("#customArticleDescriptionInput").keydown(
        function(event){
            // Note: No debouncing on keydown since this will break the ignoring functionality on autorepeat! 
            console.log("keydown which: " + event.which);
                                            
            if( // The following key are not to be ignored:          
                ((event.which >= 48 && event.which <= 57) && !event.shiftKey && !event.altKey )      ||     // numbers (without shift or alt key)              
                (event.which >= 65 && event.which <= 105)     ||     // keypad numbers     
                (event.which >= 65 && event.which <= 90)      ||     // letters 
                (event.which == 32)      ||     // whitespace 
//                 (event.which >= 8 && event.which <= 13)       ||     // backspace, tab, enter   
//                 ($.inArray(event.which, [ 8, 9, 13, 35, 36, 37, 39, 46, 110, 116, 144, 190]) !== -1)  // backspace, tab, enter, end, home, left arrow, right arrow, delete, decimal point, F5, num lock, period
                ($.inArray(event.which, [ 8, 9, 13, 35, 36, 37, 39, 46, 116, 144]) !== -1) || // backspace, tab, enter, end, home, left arrow, right arrow, delete, F5, num lock
                (event.which == 0) // Special characters like äöü => to be handled in keypress() handler
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
    
    
    
    $("#customArticleDescriptionInput").keyup( 
        function(event){
            if(checkKeyDebouncing() == false){ return; }
//             console.log("keyup which: " + event.which);

            if(event.which == 13) { // enter key       
                addToBasket("custom");
            }
        }
    );
    
        

    $(".addToBasketButton").off().on('click', 
        function(event){
            addToBasket($(event.target).attr('id'));
        }
    );    
});





// global debouncing timer
var keyDebouncingTimer = 0;

function checkKeyDebouncing() {
    var now = Date.now();
//     console.log("Current time: " + now + ", last time: " + keyDebouncingTimer + " (Difference: " + (now - keyDebouncingTimer));
    if(keyDebouncingTimer > (now - 50)){ //less than 50 ms since last key press, ignoring it
        keyDebouncingTimer = now;
        return false;
    }
    else {
        keyDebouncingTimer = now;
        return true;
    }
}




function addToBasket(inputFieldId) {
    var price = 0;
    var quantity = 1;
    var text = "";
    
    console.log("addToBasket id="+inputFieldId);
                
    if(inputFieldId == 'custom'){ //custom article
        price =  $("#quantity_"+inputFieldId).val();
        text = $("#customArticleDescriptionInput").val();
        console.log("Manual Article, Text: " + text + ", price: " + price);
        if(text == "") {
            firework.launch("Fehlender Text für freie Eingabe!", 'error', 5000);
            return;
        }
        else if(price == "") {
            firework.launch("Fehlender oder ungültiger Preis für freie Eingabe!", 'error', 5000);
            return;
        }
        else { //ok
            
        }
    }  
    else{ //pouring or dipping  
        quantity =  $("#quantity_"+inputFieldId).val();
        if(quantity == ""){ // no weight value entered for dipping articles
            firework.launch("Bitte Gewicht eingeben!", 'error', 5000);
            return;
        }
        else if(quantity == 0){ // weight value not useful
            firework.launch("Bitte sinnvolles Gewicht eingeben!", 'error', 5000);
            return;
        }  
    }
    
    
    //add to basket
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {                    
//                 console.log(this.responseText);
//                 console.log("Ready state: " + this.readyState + ", status: " + this.status);
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            var obj = JSON.parse(this.responseText);
            
            if(obj.response.success == "true") {
//                             console.log(obj.data.inputFieldId);
                showBasket();
                console.log("added to basket.\nResponse: " + this.responseText);
                $("#customArticleDescriptionInput").val(""); // clear custom article field
                $("#quantity_custom").val(""); // clear custom article field
                $("#quantity_1").val(""); // clear article field (parafin wax)
                $("#quantity_2").val(""); // clear article field (bee wax)
            }
            else{
                firework.launch("Konnte Artikel nicht zum Warenkorb hinzufügen!", 'error', 5000);
            }
        }
    };
    var params = "id=" + inputFieldId + "&quantity=" + quantity + "&price=" + price + "&text=" + text;
    console.log(params);

    showProgressBar();   

    xhttp.open("POST", "ajax/addToBasket.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}
