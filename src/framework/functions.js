// var contentsLoaded = 6;

function blockLoaded(){
//     contentsLoaded = contentsLoaded - 1;
//     console.log("Loaded: " + (6 - contentsLoaded)); 
//     if(contentsLoaded == 0){ 
//         console.log("all loaded");
//         
//         console.log("hide full page overlay");
//         $('.fullPageOverlay').hide();
//     }
}


function showArticles() {
    console.log("load articles");
    $("#dipArticlesDiv").load("subpages/dipArticles.php", blockLoaded());
    $("#pourArticlesDiv").load("subpages/pourArticles.php", blockLoaded());
    $("#manualArticlesDiv").load("subpages/manualArticles.php", blockLoaded());
}




function showBasket() {  
    console.log("load basket");
    $("#basketDiv").load("subpages/basket.php", blockLoaded());
    $("#basketButtonsDiv").load("subpages/basketButtons.php", blockLoaded());
}

function loadBibleVerse() {
//     console.log("loadBibleVerse");   
    $("#bibleVerseDiv").load("subpages/bibleVerse.php", blockLoaded());
    $("#bibleVerseDiv").fadeIn(1000);
}



function changeBibleVerse() {    
//     console.log("changeBibleVerse");   
    $("#bibleVerseDiv").fadeOut(1000, loadBibleVerse);
}


function showFullPageOverlay(content) {
    console.log("Showing full page overlay");
    content = content + "<br><br><input type=button class=fullPageOverlayContentButton value=Schliessen onclick=hideFullPageOverlay()>";
    $('.fullPageOverlayContent').html(content);
    $('.fullPageOverlay').show();
}

function hideFullPageOverlay() {    
    console.log("Hiding full page overlay");
    $('.fullPageOverlay').hide(500);
}



function startClock() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = updateClock(m);
//     s = updateClock(s);
    document.getElementById('clockText').innerHTML =
//     h + ":" + m + ":" + s;
    h + ":" + m;
    var t = setTimeout(startClock, 1000);
    
    if((s == 0) && (m % 10 == 0)) { //every 5 minutes
        changeBibleVerse();
    }
}


function updateClock(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}





function getNumberOfDecimalPoints(data){
    data = "0" + data;
    return data.split(".").length - 1
}

function getDigitsBehindDecimalPoints(data){  
    data = "0" + data;
    arr = data.split(".");
    if(arr.length > 1){
        return data.split(".")[1].length;
    }
    else {
        return 0;
    }
}

function getDigitsAheadDecimalPoints(data){  
    data = "0" + data;
    arr = data.split(".");
    if(arr.length > 1){
        return data.split(".")[0].length;
    }
    else {
        return data.length;
    }
}


function formatCurrency(value){  
//     console.log(value)
//     console.log("decimal points:", getNumberOfDecimalPoints(value))
    if(getNumberOfDecimalPoints(value) > 0) { // there is a Decimal Point
        digitsBehindDecimalPoints = getDigitsBehindDecimalPoints(value);
        if(digitsBehindDecimalPoints > 2) { // cut sufix away
            // TODO round properly
            value = value.toString();
            value = value.substring(0, value.length - digitsBehindDecimalPoints + 2);
//             console.log(value);
//             value = Math.round(parseInt(value * 100) / 100.0);
//             console.log(value);
        }
        else if(digitsBehindDecimalPoints == 1) { // add 0
            value = value + "0"; 
        }
        else if(digitsBehindDecimalPoints == 0) { //add 00
            value = value + "00"; 
        }
        else { // add prefix                    
            
        }
    }      
    else { // no decimal point yet
        value = value + ".00";   
    }
    
    // format left of decimal point
    if(getDigitsAheadDecimalPoints(value) < 1) {
        value = "0" + value;
    }            

    return value;
}





function formatCurrencyField(inputField){
    selectionStart = document.getElementById(inputField).selectionStart
    
    // cut off leading zeros
    var value = ($("#" + inputField).val() * 1.0).toString()
    
    // format right of decimal point
    if(getNumberOfDecimalPoints(value) > 0) { // there is a Decimal Point
        digitsBehindDecimalPoints = getDigitsBehindDecimalPoints(value);
        if(digitsBehindDecimalPoints > 2) { //cut prefix away
//                     $("#" + inputField).val(value.substring(0, value.length - digitsBehindDecimalPoints));
            value = value.substring(0, value.length - digitsBehindDecimalPoints + 2);
        }
        else if(digitsBehindDecimalPoints == 1) { // add 0
            value = value + "0"; 
        }
        else if(digitsBehindDecimalPoints == 0) { //add 00
            value = value + "00"; 
        }
        else { // add prefix                    
            
        }
    }      
    else { // no decimal point yet
        value = value + ".00";   
    }
    
    // format left of decimal point
    if(getDigitsAheadDecimalPoints(value) < 1) {
        value = "0" + value;
    }            
                
    $("#" + inputField).val(value);
    
    document.getElementById(inputField).selectionStart = selectionStart;
    document.getElementById(inputField).selectionEnd = selectionStart;   
}



