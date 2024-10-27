<? 
$root=str_replace("customerScreen.php", "", $_SERVER['PHP_SELF'],);
?>


<!DOCTYPE html>
<html lang="de">
<head>

<!--     <meta http-equiv="refresh" content="1" /> -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kerzenziehen</title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="shortcut icon" href="favicon.ico">
        
    <script src="<? echo("$root"); ?>/framework/jquery.min.js"></script>
    <script src="<? echo("$root"); ?>/framework/jquery-ui.min.js"></script>
    <script src="<? echo("$root"); ?>/framework/functions.js"></script>

    <link href="<? echo("$root"); ?>/framework/jquery.firework.css" rel="stylesheet">
    <script src="<? echo("$root"); ?>/framework/jquery.firework.js"></script>
    <script src="<? echo("$root"); ?>/framework/browser_detect.js"></script>
    
    <link rel="stylesheet" href="<? echo("$root"); ?>/framework/easy-numpad.css">
    <script src="<? echo("$root"); ?>/framework/easy-numpad.js"></script>
    <script src="<? echo("$root"); ?>/framework/functions.js"></script>
    
    <link rel="stylesheet" href="<? echo("$root"); ?>/framework/style.css"> 
    
    <style>
        body {
            height: 600px;
            margin: 10px;
			background-position: -80px 0px;
			overflow: hidden; /* Hide scrollbars */
        }
        
        h1 {
            text-align: center;
             margin-top: 5px; 
             margin-bottom: 20px; 
            line-height: 35px;
        }
        table {
            width: 100%;
        }
		
		p {
			font-size: 150%;
			margin-top: 30px;
			margin-bottom: 20px;
		}
        
        .cellCost, .cellAmount, .total {
            text-align: right;
        }
	    
		.cellAmount, .cellName, .cellCost{
            font-size: 150%;
        }
		
		.cellName {
			width: 100%; /* Used to make sure it fills full table width */
		}
        
        .total {
            margin-top: 5px;
            margin-bottom: 5px;
            font-size: 50px;
        }
		
		#legend {
			color: gray;
			font-size: 70%;
		}
		
		#roundingText {
			font-size: 80%;
			margin-top: 0;
			margin-bottom: 10px;
			text-align: right;
			
		}
				
		.bookingsTable {
			display:  block;
			overflow: auto;
			max-height: 606px;
		}
		
		.bookingsTableTotal {
			margin-top: 10px;
		}
		
		#screensaver {
			position: fixed; /* Sit on top of the page content */
			display: none; /* Hidden by default */
			width: 100%; /* Full width (cover the whole page) */
			height: 100%; /* Full height (cover the whole page) */
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: rgba(0,0,0, 1); /* Black background with opacity */
			z-index: 3;
		}
		
		#screensaverImg {
			position: fixed; /* Sit on top of the page content */
			top: 0;
			left: 0;
		}
		
		#alert {
			position: fixed; /* Sit on top of the page content */
			display: none; /* Hidden by default */
			width: 100%; /* Full width (cover the whole page) */
			height: 100%; /* Full height (cover the whole page) */
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: rgba(0,0,0, 0.8); /* Black background with opacity */
			z-index: 2;
		}
		
		#alertText {
			margin-top: 300px;
			color: red;
		}
    </style>

    <script>
		var updateInterval = 500; // in ms
		var watchdogInterval = 500; // in ms
		var screensaverTimeout = 60 * 60 * 1000; // 1h in ms
		var screensaverMoveInterval = 5 * 1000; // 5s in ms
		var alertTimeout = 10 * 1000; // 10s in ms
	
	
		var lastUpdateTimestamp = Date.now();
		var lastChangeTimestamp = Date.now();
		var lastChangeData = "";
		var moveScreensaverTimerHandle = "";
	
        $(document).ready(function() {        
            //console.log("start");
            periodicallyUpdatePage();
            setInterval(periodicallyUpdatePage, updateInterval);
            setInterval(watchdog, watchdogInterval);	
        });
		
		
		function watchdog() {
			//console.log(Date.now() + ", " + lastUpdateTimestamp + " (" + (Date.now() - lastUpdateTimestamp) + "), " + lastChangeTimestamp);			
			document.getElementById("lastUpdateTimestamp").innerHTML = Date.now() - lastUpdateTimestamp;
			document.getElementById("lastChangeTimestamp").innerHTML = Date.now() - lastChangeTimestamp;
			
			if (Date.now() - lastUpdateTimestamp > alertTimeout) {			
				document.getElementById("alert").style.display = "block"; // Show alert			
			}
			else {
				document.getElementById("alert").style.display = "none"; // Hide alert
			}			
		}

        function periodicallyUpdatePage() {
            //console.log("fetch");
            let basket = fetch( 
            "<? echo("$root"); ?>/ajax/getBasket.php"); 
            // basket is the promise to resolve 
            // it by using.then() method 
            basket.then(res => 
                res.json()).then(data => {
					//console.log(lastChangeData);
					//console.log(data);
					if (JSON.stringify(data) !== JSON.stringify(lastChangeData)) {
						//console.log("Data changed");
						lastChangeTimestamp = Date.now();
						lastChangeData = data;
						document.getElementById("screensaver").style.display = "none"; // Hide screensaver
						if (moveScreensaverTimerHandle != "") {
							clearInterval(moveScreensaverTimerHandle);
						}
					}
					else {
						if (Date.now() - lastChangeTimestamp > screensaverTimeout) {
							document.getElementById("screensaver").style.display = "block"; // Show screensaver (Screensaver)
							if (moveScreensaverTimerHandle == "") {
								moveScreensaverTimerHandle = setInterval(moveScreensaverImg, screensaverMoveInterval);
							}
						}
					}
                    updatePage(data);
                }); 
        }
		
		
		var screensaverImgWidth = 200;
		var screensaverImgHeight = 92;
		var screensaverImgMaxX = 600 - screensaverImgWidth;
		var screensaverImgMaxY = 1024 - screensaverImgHeight;
		//var screensaverImgX = getRandomInt(screensaverImgMaxX);
		//var screensaverImgY = getRandomInt(screensaverImgMaxY);
		//var screensaverImgTargetX = getRandomInt(screensaverImgMaxX);
		//var screensaverImgTargetY = getRandomInt(screensaverImgMaxY);
		
		
		function getRandomInt(max) {
			return Math.floor(Math.random() * (max + 1));
		}
		
		
		function moveScreensaverImg() {
			document.getElementById("screensaverImg").style.setProperty("top", getRandomInt(screensaverImgMaxY) + "px");
			document.getElementById("screensaverImg").style.setProperty("left", getRandomInt(screensaverImgMaxX) + "px");

			/*if ((screensaverImgX == screensaverImgTargetX) && (screensaverImgY == screensaverImgTargetY)) { // Target reached
				screensaverImgTargetX = getRandomInt(screensaverImgMaxX);
				screensaverImgTargetY = getRandomInt(screensaverImgMaxY);				
			}
			else { // Move to target
				if (screensaverImgX < screensaverImgTargetX) {
					screensaverImgX += 1;
				}
				else if (screensaverImgX > screensaverImgTargetX) {
					screensaverImgX -= 1;
				}
				
				if (screensaverImgY < screensaverImgTargetY) {
					screensaverImgY += 1;
				}
				else if (screensaverImgY > screensaverImgTargetY) {					
					screensaverImgY -= 1;
				}
				//console.log("Screensaver Img: " + screensaverImgX + "/" + screensaverImgY + " (Target: " + screensaverImgTargetX + "/" + screensaverImgTargetY + ")");
				document.getElementById("screensaverImg").style.setProperty("top", screensaverImgY + "px");
				document.getElementById("screensaverImg").style.setProperty("left", screensaverImgX + "px");				
			}*/
		}
    
    
        function updatePage(data) {
            //console.log("update page");
			lastUpdateTimestamp = Date.now();
            //console.log(data);
            document.getElementById("total").innerHTML = data["total"];
            
            const table = document.getElementById("bookingsTable");
            
            while(table.rows.length > 0) {
                table.deleteRow(0);
            }
            
            /* Header */
//             let header = table.createTHead();
//             let row = header.insertRow();
            let row = table.insertRow();
//             let cellImage = row.insertCell();
            let cellAmount = row.insertCell();
            let cellName = row.insertCell();
            let cellCost = row.insertCell();

            cellAmount.innerHTML = "<b>Menge</b>";
            cellName.innerHTML = "<b>Artikel</b>";
			cellCost.innerHTML = "<b>Preis</b>";
			cellAmount.className = "cellAmount";
			cellName.className = "cellName";
			cellCost.className = "cellCost";
            
            
//             let body = table.createTBody();
            data["entries"].forEach( item => {
//                 let row = body.insertRow();
                let row = table.insertRow();
//                 let cellImage = row.insertCell();
                let cellAmount = row.insertCell();
                let cellName = row.insertCell();
                let cellCost = row.insertCell();
                
                let quantity = item.quantity;
                let price = item.price;
                let suffix = "";
                if (item.unit == "g") {
                    suffix = " g";
                }
                else if (item.unit == "CHF") {
                     quantity = "";
                }

                cellAmount.innerHTML = "<nobr>" + quantity + suffix + "</nobr>";
//                 cellImage.innerHTML = "<img src=images/articles_small/" + item.image + " height=40px>";
                cellName.innerHTML = item.name;
//                 cellCost.innerHTML = (item.price * item.quantity).toFixed(2);
                cellCost.innerHTML = (price * 1.0).toFixed(2);
                
                
                cellAmount.className = "cellAmount";
                cellName.className = "cellName";
                cellCost.className = "cellCost";
            });
            
            
            if (data["donation"] != "0.00") { 
                let row = table.insertRow();     
                let cellAmount = row.insertCell();
                let cellName = row.insertCell();
                let cellCost = row.insertCell();
                
                cellAmount.className = "cellAmount";
                cellName.className = "cellName";
                cellCost.className = "cellCost";
                
                cellName.innerHTML = "<span style=\"color:red\">❤️</span>&nbsp;<i>Spende</i>";
                cellCost.innerHTML = "<i>" + (data["donation"] * 1.0).toFixed(2) + "</i>";
            }
			
		
			/* Make sure the Twint Code is always visible */
			console.log(document.getElementById("bookingTableDiv").offsetHeight);
			if (document.getElementById("bookingTableDiv").offsetHeight > 450) {
				document.getElementById("headerBanner").style.display = "none"; // Hide Banner
				document.getElementById("bookingsTable").style.maxHeight = "600px";
			}
			else {
				document.getElementById("headerBanner").style.display = "block"; // Show Banner
				document.getElementById("bookingsTable").style.maxHeight = "1000px";
			}
        }
    
    </script>

</head>

<body id=live>
<div id="screensaver"><img id=screensaverImg src="<? echo("$root"); ?>/images/Logo-Kirche-Neuwies.png" width=200px></img></div> 
<div id="alert"><h1 id=alertText><img id=alertImg src="<? echo("$root"); ?>/images/alert.png" width=200px></img><br><br>Keine Verbindung<br>zur Kasse!</h1></div> 
<div id="container">
   <div id="header">
        <div id=headerBanner style="clear:both; display: block;">
            <div id=logo>
<!--                 <h1><img src="images/candle.png" width=30px> Kerzenziehen<br>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/logo.png" height=25px></h1> -->
                <h1 style="font-size: 50px; margin-left: -150px">Kerzenziehen</h1>
				<img src="images/logo.png" height=40px style="margin-left: 100px; margin-bottom: 10px">
            </div>
<!--            <div id=clock>
                <p id=clockText></p>
            </div>-->
        </div>
<!--       <h2>Warenkorb</h2> -->
      
        
      <div id=bookingTableDiv><table id=bookingsTable class=bookingsTable></table></div>
      <table id=bookingsTable class=bookingsTableTotal>
        <tr><td colspan=3>
		  <div style="float:left;">
			<h2 class=total>Total</h2>
		  </div>		  
		  <div style="float:right;">
			<h2 class=total>CHF <span id=total>0.00</span></h2>
			<p id=roundingText>(Auf 10 Rappen gerundet)</p>
		  </div>
		</td></tr>
      </table>

	  <p>Zahlungsmöglichkeiten:</p>
      <div style="margin: auto; width: 580px; margin-bottom: 20px;">
      <!--  <img src=images/twint-logo-black.jpg height=100px>
        <img src="" height=0 width=10px>-->
        <img src="" height=0 width=20px>
        <img src=images/bargeld.png height=160px>
        <img src="" height=0 width=20px>
        <img id=twintCode src=images/twint-code.png height=200px>
      </div>
	  <hr>
      <div style="margin: auto; width: 500px; margin-top: 30px;">
		<p style="display:inline-block; vertical-align: text-bottom;">Wir sind Teil der &nbsp;&nbsp;&nbsp;</p><img src="images/viva-kirche.png" height=100px>
      </div>
    </div>
</div>
<p></p>
	<hr>
	<div id=legend>
	  Letztes Update: <span id=lastUpdateTimestamp>?</span> ms, 
	  Last Change: <span id=lastChangeTimestamp>?</span> ms.
	  </div>
</body>


