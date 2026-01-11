<? 

$root=".";
include "$root/framework/header.php";
include "$root/framework/statsDataProvider.php";
include "statsDiagramsFunctions.php";



$statsData = getStatsData();
$totalPerDayAndYear = $statsData['totalPerDayAndYear'];
$totalPerDayAndYearSummed = $statsData['totalPerDayAndYearSummed'];
$totalWaxPerDayAndYear = $statsData['totalWaxPerDayAndYear'];
$totalFoodPerDayAndYear = $statsData['totalFoodPerDayAndYear'];
$totalWaxPerDayAndYearInKg = $statsData['totalWaxPerDayAndYearInKg'];
$totalWaxPerDayAndYearInKgSummed = $statsData['totalWaxPerDayAndYearInKgSummed']; 

?>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div id="body">
<h1>Umsatz und Wachs pro Tag und Jahr</h1>
<div style="display: flex; align-items: flex-start; gap: 20px 60px; margin-bottom: 20px;">
	<div style="background: rgba(248, 249, 250, 0.65); border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; backdrop-filter: blur(5px); flex: 0 0 auto;">
		<h4 style="margin: 0 0 15px 0; color: rgba(73, 80, 87, 0.65); font-size: 16px; font-weight: 600;">Diagramme:</h4>
		<ul style="margin: 0; padding-left: 20px;">
			<li><a href=#Wax+Gastro_Currency>Umsatz pro Tag (Wachs + Gastronomie)</a><br><br></li>
			<li><a href=#Wax+Gastro_Currency_summed>Umsatz aufsummiert (Wachs + Gastronomie)</a><br><br></li>
			<li><a href=#Wax_Currency>Umsatz Wachs</a><br><br></li>
			<li><a href=#Gastro_Currency>Umsatz Gastronomie</a><br><br></li>
			<li><a href=#Wax_amount>Wachsmenge</a><br><br></li>
			<li><a href=#WaxAmountSummed>Wachsmenge aufsummiert</a></li>
		</ul>
	</div>
	
	<div style="background: rgba(248, 249, 250, 0.65); border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; backdrop-filter: blur(5px); flex: 0 0 auto; margin-left: auto;">
		<h4 style="margin: 0 0 15px 0; color: rgba(73, 80, 87, 0.65); font-size: 16px; font-weight: 600;">Hinweise:</h4>
		<ul style="margin: 0; padding-left: 20px; color: rgba(73, 80, 87, 0.65); font-size: 16px;">
			<li style="margin-bottom: 10px;">2020 konnte das Kerzenziehen wegen COVID-19 nicht öffentlich durchgeführt werden.</li>
			<li style="margin-bottom: 10px;">Wachspreise:
				<table style="margin-top: 8px; border-collapse: collapse; width: 100%; font-size: 15px;">
					<thead>
						<tr style="border-bottom: 1px solid rgba(73, 80, 87, 0.3);">
							<th style="padding: 1px 8px; text-align: left; color: rgba(73, 80, 87, 0.65); font-weight: 600;">Jahr</th>
							<th style="padding: 1px 8px; text-align: left; color: rgba(73, 80, 87, 0.65); font-weight: 600;">Bienenwachs</th>
							<th style="padding: 1px 8px; text-align: left; color: rgba(73, 80, 87, 0.65); font-weight: 600;">Parafinwachs</th>
						</tr>
					</thead>
					<tbody>
						<tr style="border-bottom: 1px solid rgba(73, 80, 87, 0.2);">
							<td style="padding: 1px 8px;">Ab 2023</td>
							<td style="padding: 1px 8px;">CHF 4.60</td>
							<td style="padding: 1px 8px;">CHF 3.60</td>
						</tr>
						<tr style="border-bottom: 1px solid rgba(73, 80, 87, 0.2);">
							<td style="padding: 1px 8px;">2022</td>
							<td style="padding: 1px 8px;">CHF 4.50</td>
							<td style="padding: 1px 8px;">CHF 3.50</td>
						</tr>
						<tr>
							<td style="padding: 1px 8px;">Bis 2021</td>
							<td style="padding: 1px 8px;">CHF 4.40</td>
							<td style="padding: 1px 8px;">CHF 3.30</td>
						</tr>
					</tbody>
				</table>
			</li>
            <li><a href="statsDiagrams.php">Neue Diagram-Seite</a></li>
		</ul>
	</div>
</div>


<!-- <p><a href="?nocss" target="_self">Ohne Hintergrundbild anzeigen</a><br>&nbsp;</p> -->

<hr>

<a name=Wax+Gastro_Currency></a><h2>Umsatz pro Tag (Wachs + Gastronomie) <span style="font-size: 70%"></span></h2>
<? showDiagram("Common", "Umsatz in CHF", $totalPerDayAndYear, ": Öffentlich", ": Schule/Geschlossene Gesellschaft/Private Gruppe", 0, 0, "CHF", "", 2, "chart-bg-public-school-old.png"); ?>  
<hr>

<a name=Wax+Gastro_Currency_summed></a><h2>Umsatz aufsummiert (Wachs + Gastronomie) <span style="font-size: 70%"></span></h2>
<? showDiagram("CommonSummed", "Umsatz aufsummiert in CHF", $totalPerDayAndYearSummed, "", "", -2, 2, "CHF", "", 2, "chart-bg-old.png"); ?>  
<hr>

<a name=Wax_Currency></a><h2>Umsatz Wachs <span style="font-size: 70%"></span></h2>
<? showDiagram("Wax", "Umsatz in CHF", $totalWaxPerDayAndYear, ": Öffentlich", ": Schule/Geschlossene Gesellschaft/Private Gruppe", 0, 0, "CHF", "", 2, "chart-bg-public-school-old.png"); ?> 
<hr>

<a name=Gastro_Currency></a><h2>Umsatz Gastronomie <span style="font-size: 70%"></span></h2>
<? showDiagram("Food", "Umsatz in CHF", $totalFoodPerDayAndYear, "", "", -8, 8, "CHF ", "", 2, "chart-bg-old.png"); ?> 
<hr>

<a name=Wax_amount></a><h2>Wachsmenge <span style="font-size: 70%"></span></h2>
<? showDiagram("WaxAmount", "Wachsmenge in kg", $totalWaxPerDayAndYearInKg, ": Parafinwachs", ": Bienenwachs", -20, 20, "", "kg", 1, "chart-bg-bee-parafin-old.png"); ?> 

<a name=WaxAmountSummed></a><h2>Wachsmenge aufsummiert<span style="font-size: 70%"></span></h2>
<? showDiagram("WaxAmountSummed", "Wachsmenge in kg", $totalWaxPerDayAndYearInKgSummed, ": Parafinwachs", ": Bienenwachs", -10, 10, "", "kg", 1, "chart-bg-bee-parafin-old.png"); ?> 

<hr>

<?
include "$root/framework/footer.php"; 
?>
    
