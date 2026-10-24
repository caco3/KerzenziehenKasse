

/**
 * Callback invoked after a content block has been loaded.
 * Currently a no-op kept for jQuery .load() compatibility.
 */
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


/**
 * Loads the article subpages into the article container divs.
 */
function showArticles() {
    console.log("load articles");
    $("#dipArticlesDiv").load("subpages/dipArticles.php", blockLoaded());
//     $("#pourArticlesNormalDiv").load("subpages/pourArticlesNormal.php", blockLoaded());
//     $("#pourArticlesFloatingCandlesDiv").load("subpages/pourArticlesFloatingCandles.php", blockLoaded());
//     $("#pourArticlesPreMadeDiv").load("subpages/pourArticlesPreMade.php", blockLoaded());
//     $("#customArticleDiv").load("subpages/customArticle.php", blockLoaded());
    
    $("#articlesDiv").load("subpages/articles.php", blockLoaded());
}




/**
 * Reloads the basket view: basket contents, basket buttons and the
 * extra-info section.
 */
function showBasket() {  
    console.log("load basket");
    $("#basketDiv").load("subpages/basket.php", blockLoaded());
    $("#basketButtonsDiv").load("subpages/basketButtons.php", blockLoaded());
    $("#extraInfoDiv").load("subpages/extraInfo.php", 
        /**
         * Callback after the extra-info subpage loaded; refreshes the
         * extra-data summary if the refreshExtraSummary() function exists.
         */
        function() {
        console.log("extraInfoDiv loaded, checking for refreshExtraSummary function");
        blockLoaded();
        if (typeof refreshExtraSummary === 'function') {
            console.log("refreshExtraSummary function exists, calling it");
            refreshExtraSummary();
        } else {
            console.error("refreshExtraSummary function not found");
        }
    });
}

/**
 * Loads the bible verse subpage into the bibleVerse div and fades it in.
 */
function loadBibleVerse() {
//     console.log("loadBibleVerse");   
    $("#bibleVerseDiv").load("subpages/bibleVerse.php", blockLoaded());
    $("#bibleVerseDiv").fadeIn(1000);
}

/**
 * Loads the navigation subpage into the navigation div and fades it in.
 */
function loadNavigation() {
//     console.log("loadNavigation");   
    $("#navigationDiv").load("subpages/navigation.php", blockLoaded());
    $("#navigationDiv").fadeIn(1000);
}



/**
 * Fades out the current bible verse and loads a new one.
 */
function changeBibleVerse() {    
//     console.log("changeBibleVerse");   
    $("#bibleVerseDiv").fadeOut(1000, loadBibleVerse);
}


/**
 * Shows the loading progress bar by adding the 'loading' class to the body.
 */
function showProgressBar() {
    $("body").addClass("loading"); 
}

/**
 * Hides the loading progress bar by removing the 'loading' class from the body.
 */
function hideProgressBar() {
    $("body").removeClass("loading"); 
//     console.log("hided progress bar");
}


/**
 * Shows the full page overlay with the given HTML content.
 *
 * @param {string} content - HTML content to display in the overlay.
 * @param {boolean} [addCloseButton=true] - Whether to wrap the content in an
 *        easy-numpad container and append a 'Schliessen' button.
 */
function showFullPageOverlay(content, addCloseButton = true) {
    console.log("Showing full page overlay");
    // Only wrap in easy-numpad-container if close button is needed
    if (addCloseButton) {
        var wrappedContent = "<div class='easy-numpad-container'>" + content + "</div>";
        wrappedContent = wrappedContent + "<br><br><input type=button class=fullPageOverlayContentButton value=Schliessen onclick=hideFullPageOverlay()>";
        $('.fullPageOverlayContent').html(wrappedContent);
    } else {
        $('.fullPageOverlayContent').html(content);
    }
    $('.fullPageOverlay').show();
}

/**
 * Hides the full page overlay.
 */
function hideFullPageOverlay() {    
    console.log("Hiding full page overlay");
    $('.fullPageOverlay').hide();
}



/**
 * Updates the clock display every second (blinking separator) and triggers
 * a bible verse change every 10 minutes.
 */
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


/**
 * Prepends a leading zero to single digit numbers.
 *
 * @param {number|string} i - Number to pad.
 * @returns {number|string} The padded value.
 */
function addLeadingZero(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}





/**
 * Counts how many decimal point characters occur in a value.
 *
 * @param {number|string} data - The value to inspect.
 * @returns {number} Number of '.' characters in the value.
 */
function getNumberOfDecimalPoints(data){
    data = "0" + data;
    return data.split(".").length - 1
}

/**
 * Returns the number of digits behind the decimal point of a value.
 *
 * @param {number|string} data - The value to inspect.
 * @returns {number} Count of digits after the decimal point.
 */
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

/**
 * Returns the number of digits ahead of the decimal point of a value.
 *
 * @param {number|string} data - The value to inspect.
 * @returns {number} Count of digits before the decimal point.
 */
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


/**
 * Formats a numeric value as a currency string with two digits behind the
 * decimal point (cuts or pads decimals, pads the integer part).
 *
 * @param {number|string} value - The value to format.
 * @returns {string} The formatted currency string.
 */
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


/**
 * Formats the value of an input field as currency (two decimals), while
 * preserving the cursor position.
 *
 * @param {string} inputField - ID of the input field to format.
 */
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


/**
 * Handles a click on the minimize button; asks the local helper service
 * to minimize the kiosk window.
 */
function minimizeClicked(){
	console.log("minimizeClicked");

    var xhttp = new XMLHttpRequest();
    firework.launch("Die Kasse wird gleich minimiert...", 'success', 2000);
    xhttp.open("GET", "http://localhost:8086/minimize", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send();
}
