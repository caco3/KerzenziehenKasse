$(document).on({    
    ajaxStop: function() { 
        hideProgressBar();
    }    
});


$(document).ready(function(){
//     console.log("Articles loaded");   

});



/* Called when one of the article divs get pressed */
function addArticleToBasket(id) {
    addToBasket(id, 1); 
}


/* Called when one of the article divs get pressed */
function addArticleWithQuantityToBasket(id, quantity) {
    addToBasket(id, quantity);
}


function addToBasket(id, quantity) {
    console.log("addToBasket id="+id+", quantity="+quantity);
    
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
    
    var params = "id="+id+"&quantity="+quantity+"&price=0&text=";
    console.log(params);

    showProgressBar();   

    xhttp.open("POST", "ajax/addToBasket.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(params);
}
