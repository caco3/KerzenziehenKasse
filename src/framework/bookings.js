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
                        firework.launch("Booking " + bookingId + " wurde in den Warenkorb geladen. Du wirst gleich weitergeleitet...", 'success', 5000);
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
    
    
    $(".receiptButton").off().on('click', 
        function(event){
            var bookingId = $(event.target).attr('id');
            console.log("creating receipt for booking " + bookingId);
            
            window.location.replace("subpages/receipt.php?id=" + bookingId);
            
        }
    );
    
});




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

