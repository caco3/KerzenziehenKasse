$(document).on({
    
    // Hide progress indicator
    ajaxStop: function() { 
        $("body").removeClass("loading");
    }
     
});


$(document).ready(function(){
//     console.log("Articles loaded");   
     
    
//     $(".articleQuantityInput").keydown(
//         function(event){
//             // Allow: backspace, delete, tab, escape, enter and .
//             if ($.inArray(event.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
//                 // Allow: Ctrl+A, Command+A
//                 (event.keyCode === 65 && (event.ctrlKey === true || event.metaKey === true)) || 
//                 // Allow: home, end, left, right, down, up
//                 (event.keyCode >= 35 && event.keyCode <= 40)) {
//                     // let it happen, don't do anything
//                     return;
//             }
//             // Ensure that it is a number and stop the keypress
//             if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105)) {
//                 event.preventDefault();
//             }
//         }
//     );
//     
//     $(".articleQuantityInput").keyup(
//         function(event){
//             var inputField = $(event.target).attr('id');     
//             //prevent empty field
//             if($("#" + inputField).val() == "") {
//                 $("#" + inputField).val(0);
//             } 
//         }
//     );
    
    
    
//     TODO support return key to send

    
 
    
    $(".articleQuantityInput").keydown(
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
    
    
    $(".articleQuantityInput").keyup(
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
            //prevent empty field
            if($("#" + inputField).val() == "") {
                $("#" + inputField).val(0);
            } 
            else{
                $("#" + inputField).val($("#" + inputField).val() * 1);
            }
                        
            // TODO keep selection
            
        }
    );
    
    
    
    
    
    
    
    
     
    
    $(".articleMoneyInput").keydown(
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
            //prevent empty field
            if($("#" + inputField).val() == "") {
                $("#" + inputField).val(0);
            } 
            else{
                $("#" + inputField).val($("#" + inputField).val() * 1);
            }
                        
            // TODO keep selection
            
        }
    );
    
    
    
    
    
    
    
    
    
    
    
    
     
    
    $("#manualArticleDescriptionInput").keydown(
        function(event){
//             console.log("keydown which: " + event.which);
                                            
            if( // The following key are not to be ignored:          
                ((event.which >= 48 && event.which <= 57) && !event.shiftKey)      ||     // numbers (without shift key)              
                (event.which >= 65 && event.which <= 105)     ||     // keypad numbers     
                (event.which >= 65 && event.which <= 90)      ||     // letters 
                (event.which == 32)      ||     // whitespace 
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
    
    
    
    

    $(".addToBasketButton").off().on('click', function(event){
            var id = $(event.target).attr('id');   
            var price = 0;
            var quantity = 1;
            var text = "";
            var free = false;
            
            console.log("addToBasket id="+id + ", quantity: " + quantity);
                        
            if(id == 0){ //manual entry
                free = true;
                price =  $("#quantity"+id).val();
                text = $("#manualArticleDescriptionInput").val();
                console.log("Manual Article, Text: " + text + ", price: " + price);
                if(text == "") {
                    showFullPageOverlay("Fehler: Fehlender Text für freie Eingabe!");
                    return;
                }
                else if(price == "") {
                    showFullPageOverlay("Fehler: Fehlender oder ungültiger Preis für freie Eingabe!");
                    return;
                }
                else { //ok
                    
                }
            }  
            else{ //pouring or dipping  
                quantity =  $("#quantity"+id).val();
                if(quantity == ""){ // no weight value entered for dipping articles
                    showFullPageOverlay("Fehler: Bitte Gewicht eingeben!");
                    return;
                }
                else if(quantity == 0){ // weight value not useful
                    showFullPageOverlay("Fehler: Bitte sinnvolles Gewicht eingeben!");
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
//                             console.log(obj.data.id);
                        showBasket();
                        console.log("added to basket.\nResponse: " + this.responseText);
                        $("#manualArticleDescriptionInput").val(""); // clear manual entry field
                        $("#quantity0").val(""); // clear manual entry field
                    }
                    else{
                        showFullPageOverlay("Fehler: Konnte Artikel nicht zum Warenkorb hinzufügen!");
                    }
                }
            };
            var params = "id=" + id + "&quantity=" + quantity + "&price=" + price + "&free=" + free + "&text=" + text;
            console.log(params);

            // Show progress indicator
            $("body").addClass("loading");   
    
            xhttp.open("POST", "ajax/addToBasket.php", true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.send(params);
        }
    );
    
});
