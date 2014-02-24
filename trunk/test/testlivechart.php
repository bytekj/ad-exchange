<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<html>
    <head>
        <!--Load the AJAX API-->
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script src="../js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript">
    
            // Load the Visualization API and the piechart package.
            google.load('visualization', '1', {'packages':['corechart']});
      
            // Set a callback to run when the Google Visualization API is loaded.
            google.setOnLoadCallback(drawChart);
      
            function drawChart() {
                
          
                var refresh = setInterval(function() { 
                    var jsonData = $.ajax({
                        url: "livechartsource.php?camp=18",
                        dataType:"json",
                        async: false
                    }).responseText;
                    
                    var data = new google.visualization.DataTable(jsonData);

                    // Instantiate and draw our chart, passing in some options.
                    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
                    chart.draw(data, {width: 600, height: 400, curveType: "function"});
                }, 5000);    
                // Create our data table out of JSON data loaded from server.
                
            }

        </script>
    </head>

    <body>
        <!--Div that will hold the pie chart-->
        <div id="chart_div"></div>
    </body>
</html>