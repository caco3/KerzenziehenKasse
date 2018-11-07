<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Gussform', 'Anzahl'],
<?
            foreach($articles as $articleId => $article) {
//                 print_r($article);
                if ($article['quantity'] == 0) { // no sales for this article, ignore it 
                    continue;
                }
                                
                if ($article['type'] != "guss") { // Do not show dipping articles
                    continue;
                }
                else if ($article['type'] == "custom") { // Do not show custom articles
                    continue;
                }
                echo("['" . $article['text'] . "', " . $article['quantity'] . "],\n");    
            }
?> 
        ]);

        var options = { 
            title: 'Gussformen',
            titleTextStyle: { fontSize: 18 },
            backgroundColor: 'transparent',
            chartArea: {'width': '100%', 'height': '80%'}, 
            is3D:true
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }
</script>

<div id="piechart" style="width: 900px; height: 600px;"></div>
