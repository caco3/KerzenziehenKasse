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
/*             width: 100%; */
            margin: 5px;
			background-position: -80px 0px;
			overflow: hidden; /* Hide scrollbars */
        }
        
        h1 {
            text-align: center;
             margin-top: 10px; 
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
        
        .total {
            margin-top: 5px;
            margin-bottom: 5px;
/*             border: 1px solid black; */
            font-size: 50px;
        }
    </style>

    <script>
        $(document).ready(function() {        
            console.log("start");
            periodicallyUpdatePage();
            setInterval(periodicallyUpdatePage, 500);
        });

        function periodicallyUpdatePage() {
            console.log("fetch");
            let basket = fetch( 
            "<? echo("$root"); ?>/ajax/getBasket.php"); 
            // basket is the promise to resolve 
            // it by using.then() method 
            basket.then(res => 
                res.json()).then(data => { 
                    updatePage(data);
                }); 
        }
    
    
        function updatePage(data) {
            console.log("update page");
            console.log(data);
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
                
                cellName.innerHTML = "❤️&nbsp;<i>Spende</i>";
                cellCost.innerHTML = "<i>" + (data["donation"] * 1.0).toFixed(2) + "</i>";
            }
            
            
        }
    
    </script>

</head>

<body id=live>
<div id="container">
   <div id="header">
        <div style="clear:both;">
            <div id=logo>
<!--                 <h1><img src="images/candle.png" width=30px> Kerzenziehen<br>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/logo.png" height=25px></h1> -->
                <h1>&nbsp;&nbsp;&nbsp;Kerzenziehen<br>&nbsp;&nbsp;&nbsp;<img src="images/logo.png" height=25px></h1>
            </div>
<!--            <div id=clock>
                <p id=clockText></p>
            </div>-->
        </div>
<!--       <h2>Warenkorb</h2> -->
      
        
      <table id=bookingsTable></table>
      <table id=bookingsTable style="margin-top: 10px">
        <tr><td colspan=3>
		  <div style="float:left;">
			<h2 class=total>Total</h2>
		  </div>		  
		  <div style="float:right;">
			<h2 class=total>CHF <span id=total>0.00</span></h2>
		  </div>
		</td></tr>
      </table>
	  
	  

	  
	  
      
	  <p>Zahlungsmöglichkeiten:</p>
      <div style="margin: auto; width: 350px; margin-bottom: 20px;">
        <img src=images/twint-logo-black.jpg height=100px>
        <img src="" height=0 width=50px>
        <img src=images/bargeld.png height=100px>
      </div>
	  <hr>
      <div style="margin: auto; width: 500px; margin-top: 30px;">
		<p style="display:inline-block; vertical-align: text-bottom;">Wir sind Teil der &nbsp;&nbsp;&nbsp;</p><img src="images/viva-kirche.png" height=100px>
      </div>
    </div>
</div>
</body>


