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
                    if(obj.response.empty == "true") {
                        console.log("Basket is empty");
                        // load booking into basket
                        firework.launch("Booking " + bookingId + " wurde in den Warenkorb geladen.", 'success', 5000);
                        // load main page after 1 second
                        setTimeout(function(){ window.location.replace("index.php"); }, 1000);                        
                    }
                    else{
                        console.log("Basket is not empty");
                        firework.launch("Bitte zuerst Warenkorb leeren!", 'error', 5000);
                    }
                }
            };
            
            showProgressBar();   

            xhttp.open("POST", "ajax/basketIsEmpty.php", true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.send();
        }
    ); 
       
});

