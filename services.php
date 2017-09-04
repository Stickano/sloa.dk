<!DOCTYPE html>
<html lang="en">
  <head>

    <?php include("php/meta.php"); ?>

  </head>
  <body>

    <!--
    # Version: 1b
    # Started: 26. August 2015
    # Author: Henrik Jeppesen, info@sloa.dk
    # License: Creative Commons (CC), Non-Commercial (NC)
    # Document updated: June 26 2016
    -->


    <?php

    include("php/header.php");

    #En lille tekst, der forklarer priser
    echo'
        <h3 class="headline">Ydelser & Priser</h3>
        <p>
            Her kan du skabe dig et overblik over en håndfuld af de yderlser der tilbydes. Projekter og deres størrelse er oftest varierende, og derfor er det en vejledende pris, som er angivet. Hvis du sidder med et IT relateret problem, som ikke er angivet på listen, så tøv ikke med at kontakte mig - min IT interesse strækker sig langt. Priserne er regnet i danske kroner. En ca. estimeret timeløn er 150,-
        </p>';



        #Hent kategorier først
        $sql = "SELECT * FROM categories WHERE services=1 ORDER BY id ASC";
        $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
        $br2 = 0;
        while($resultCat = mysqli_fetch_array($query)){

            $sqlService = "SELECT * FROM services WHERE category=".$resultCat['id'];
            $queryService = mysqli_query($conn,$sqlService)or die(mysqli_error($conn));
            $br = 0;
            $top = "4%;";
            if($br2 == 0){
                $top = "2%;";
                $br2++;
            };
             echo'<div class="panel panel-default" style="margin-top:'.$top.';">';
                echo'<div class="panel-heading"><b class="text-info">'.ucfirst($resultCat['category']).'</b></div>';
                 echo'<table class="table table-striped">';
                    echo'<thead class="text-muted servicesHead">';
                        echo'<tr>';
                            echo'<th style="text-align:center;">#</th>';
                            echo'<th>Produkt</th>';
                            echo'<th style="text-align:right;">Pris</th>';
                        echo'</tr>';
                    echo'</thead>';
                    echo'<tbody>';

            #Hent derefter indholdet i kategorierne
            while($result = mysqli_fetch_array($queryService)){

                $br++;
                        echo'<tr>';
                            echo'<td style="width:5%; text-align:center; vertical-align:middle;">'.$br.'</td>';
                            echo'<td style="width:75%;">';
                                echo'<b>'.ucfirst($result['head']).'</b>';
                                if(!empty($result['para'])){
                                    echo'<br />';
                                    echo'<small>'.$result['para'].'</small>';
                                };
                            echo'</td>';
                            echo'<td style="width:20%; text-align:right; vertical-align:middle;">';
                                echo'<small class="text-success" style="letter-spacing:.4px;"><b>'.$result['price'].'</b></small>';
                            echo'</td>';
                        echo'</tr>';

            };

                    echo'</tbody>';
                echo'</table>';
            echo'</div>';
        };
            #afsluttende tekst
            echo'<p class="text-center"><small><b>* Indenfor rimelighedens grænser. Et overslag kan forekomme.</b></small></p>';


     include("php/footer.php"); ?>

  </body>
</html>


