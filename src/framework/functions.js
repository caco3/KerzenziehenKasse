

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
//     $("#pourArticlesNormalDiv").load("subpages/pourArticlesNormal.php", blockLoaded());
//     $("#pourArticlesFloatingCandlesDiv").load("subpages/pourArticlesFloatingCandles.php", blockLoaded());
//     $("#pourArticlesPreMadeDiv").load("subpages/pourArticlesPreMade.php", blockLoaded());
//     $("#customArticleDiv").load("subpages/customArticle.php", blockLoaded());
    
    $("#articlesDiv").load("subpages/articles.php", blockLoaded());
}




function showBasket() {  
    console.log("load basket");
    $("#basketDiv").load("subpages/basket.php", blockLoaded());
    $("#basketButtonsDiv").load("subpages/basketButtons.php", blockLoaded());
    $("#extraInfoDiv").load("subpages/extraInfo.php", function() {
        blockLoaded();
        if (typeof refreshExtraSummary === 'function') {
            refreshExtraSummary();
        }
    });
}

function loadBibleVerse() {
//     console.log("loadBibleVerse");   
    $("#bibleVerseDiv").load("subpages/bibleVerse.php", blockLoaded());
    $("#bibleVerseDiv").fadeIn(1000);
}

function loadNavigation() {
//     console.log("loadNavigation");   
    $("#navigationDiv").load("subpages/navigation.php", blockLoaded());
    $("#navigationDiv").fadeIn(1000);
}



function changeBibleVerse() {    
//     console.log("changeBibleVerse");   
    $("#bibleVerseDiv").fadeOut(1000, loadBibleVerse);
}


function showProgressBar() {
    $("body").addClass("loading"); 
}

function hideProgressBar() {
    $("body").removeClass("loading"); 
//     console.log("hided progress bar");
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
	//console.log("Start Clock");
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    h = addLeadingZero(h);
    m = addLeadingZero(m);
//     s = addLeadingZero(s);
	try {
		if (s % 2 == 0) {
			document.getElementById('clockText').innerHTML = h + ":" + m;
		} else {
			document.getElementById('clockText').innerHTML = h + " " + m;
		}
	}
	catch {
		console.log("clockText not loaded yet");
	}

    var t = setTimeout(startClock, 1000);
    
    if((s == 0) && (m % 10 == 0)) { //every 5 minutes
        changeBibleVerse();
    }
}


function addLeadingZero(i) {
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


function minimizeClicked(){
	console.log("minimizeClicked");

    var xhttp = new XMLHttpRequest();
    firework.launch("Die Kasse wird gleich minimiert...", 'success', 2000);
    xhttp.open("GET", "http://localhost:8081/minimize", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send();
}
