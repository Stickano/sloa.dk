<!DOCTYPE html>
<html lang="en">
  <head>

    <?php include("php/meta.php"); ?>

  </head>
  <body>

    <!--  sloa.dk, development started late 2015  -->
    <!--  Written by Henrik Jeppesen              -->
    <!--  Using Bootstrap framework               -->
    <!--                                          -->
    <!--  \o/  "Hi there,"                        -->
    <!--   |      "I'm a flaming developer!"      -->
    <!--  /\                                      -->


    <!--
    # Version: 1b
    # Started: 26. August 2015
    # Author: Henrik Jeppesen, info@sloa.dk
    # License: Creative Commons (CC), Non-Commercial (NC)
    # Document updated: June 18 2016
    -->


    <?php

      include("php/header.php");


      #Hent indhold (velkomst)
      $sql = "SELECT txt FROM main WHERE id=1";
      $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
      $result = mysqli_fetch_array($query);

      echo $result['txt'];

        #Karrusel

        $sql = "SELECT * FROM media WHERE front=1";
        $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));

        if(mysqli_num_rows($query) == true){

          echo'

          <div id="sloaSlide" class="carousel slide" data-ride="carousel" style="margin-top:10%;">
            <!-- Indicators -->
            <ol class="carousel-indicators">
              <li data-target="#sloaSlide" data-slide-to="0" class="active"></li>
              <li data-target="#sloaSlide" data-slide-to="1"></li>
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">';

              while($result = mysqli_fetch_array($query)){
                $active = NULL;
                if($result['preview'] == true){
                  $active = "active";
                };
                echo'
                <div class="item '.$active.'">
                  <img src="'.$result['file'].'" alt="Portfolio Billede">
                </div>';

              };

          echo'
            </div>

            <!-- Controls -->
            <a class="left carousel-control" href="#sloaSlide" role="button" data-slide="prev">
              <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
              <span class="sr-only">Tilbage</span>
            </a>
            <a class="right carousel-control" href="#sloaSlide" role="button" data-slide="next">
              <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
              <span class="sr-only">Videre</span>
            </a>
          </div>';

        };


        echo'
        <div style="margin:2% 1%;">
          <a href="preGodmode.php" class="socBut" title="Administrator" style="color:black;">
            <span class="glyphicon glyphicon-lock pull-right" aria-hidden="true"></span>
          </a>
        </div>';

    include("php/footer.php");

    ?>


  </body>
</html>