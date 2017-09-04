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

      #sikre sig at querystring (side) er en tal værdi
      $page = 0;
      if(isset($_GET['side'])){
        if(is_int($_GET['side']) == false){
            $page = $_GET['side'];
        }else{
            $page = 0;
        };
      };

        #database - Hent artikler (siden x 5)
        $count = $page*5;
        $sql = "SELECT * FROM blog ORDER BY id DESC LIMIT ".$count.", 5";
        $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));

        #Definer artikler, hvis vi sortere efter keywords
        /*
        if(isset($_GET['key'])){
          $keyArr = array();
          $key = mysqli_real_escape_string($conn,$_GET['key']);
          $keySql = "SELECT mid,keyword FROM keywords WHERE keyword='".$key."'";
          $keyQuery = mysqli_query($conn,$keySql)or die(mysqli_error($conn));
          if(mysqli_num_rows($keyQuery) == true){
            while($keyResult = mysqli_fetch_array($keyQuery)){
              $keyArr[] = $keyResult['mid'];
            };
          };
        };

        #db - hent keywords
        $keySql = "SELECT keyword,mid FROM keywords WHERE blog=1 ORDER BY keyword ASC";
        $keyQuery = mysqli_query($conn,$keySql)or die(mysqli_error($conn));

        #Keywords!
        echo'<div style="width:100%;" class="alert alert-info" role="alert">';
          echo'<form method="post" action="php/set.php">';
            echo'<table style="width:100%;"><tr>';
            echo'<td style="width:40%; text-align:right; padding:6px 2% 0; vertical-align:top;"><small><b>Keywords! :
                  <br /><small>Klik igen, for at ryde dit valg </small></b></small> &nbsp;</td>';
            echo'<td style="width:60%; padding-right:20%;">';
            $arr = array();
            $br = 0;
            while($keyResult = mysqli_fetch_array($keyQuery)){
              if(!in_array($keyResult['keyword'],$arr)){

                $arr[] = $keyResult['keyword'];

                #Giver dem lidt random styling værdier
                $size = rand(90,150);
                $bold = NULL;
                $small = NULL;
                if(rand(1,3) == 2){
                  $small = "small";
                };
                if(rand(1,3) == 2){
                  $bold = "font-weight:bold;";
                };

                $href = "?".$n.$d."key=".$keyResult['keyword'];

                if(isset($_GET['key']) && $_GET['key'] == $keyResult['keyword']){
                  $bold = "font-weight:bold; ";
                  $size = 180;
                  $href = "?";
                  $small = NULL;
                };

                $comma = ", &nbsp;";
                if($br == 0){
                  $comma = NULL;
                  $br++;
                };

                echo $comma.'<span style="font-size:'.$size.'%;">
                      <a style="'.$bold.'" href="'.$href.'" class="'.$small.'">'.$keyResult['keyword'].'</a></span>';
              };
            }; 
            echo'</td>';
            echo'</tr></table>';
          echo'</form>';
        echo'</div>';*/

        #Henter artikler i en table - guderne må vide hvad fik mig til at opbygge det sådan
        $br = 0;
        $img = 0;
        echo'<table class="table" class="width100">';
        while($result = mysqli_fetch_array($query)){

          #if(!isset($_GET['key']) || isset($_GET['key']) && in_array($result['id'],$keyArr)){

          #db - find billede til artikel
          $msql = "SELECT * FROM media WHERE blog=1 AND mid=".$result['id'];
          $mquery  = mysqli_query($conn,$msql)or die(mysqli_error($conn));
          $num = mysqli_num_rows($mquery);

            #ingen border, hvis det er den første artikel
            $border = NULL;
            if($br == 0){
                $border = "border:none;";
                $br++;
            };

            echo'<tr>';
                echo'<td itemscope itemtype="https://schema.org/Blog" class="blogMediaCon" style="'.$border.'">';

                    #Hvis der er et billede til artikel
                    if($num == TRUE){
                      $mresult = mysqli_fetch_array($mquery);
                        echo'
                        <a class="thumbnail fancybox blogMediaLink" href="'.$mresult['file'].'" title="Fuld størrelse">
                            <img itemprop="image" src="'.$mresult['file'].'" class="width100" alt="'.$mresult['txt'].'" />
                            '.$mresult['txt'].'
                        </a>';

                    #Hvis der ikke er et billede - hvis et typisk ikon
                    }else{
                        echo'<img itemprop="image" src="media/none.png" class="blogNoMedia"/>';
                    };

                echo'</td>';

                #Tekst (artikel, overskrift m.m.)
                echo'<td itemscope itemtype="https://schema.org/Blog" style="'.$border.' margin-bottom:3%;">';
                    echo '<h3 itemprop="headline" class="headline">'.$result['headline'].'</h3>';
                    echo '<div itemprop="datePublished" class="blogDateCon headline"><small>'.$result['time'].'</small></div>';
                    echo '<p itemprop="text">'.$result['txt'].'</p>';
                echo'</td>';
            echo'</tr>';
          #};
        };
        echo'</table>';


      #Pagination (styrer side-tallet)
      $sql = "SELECT id FROM blog";
      $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
      $num = mysqli_num_rows($query);

      $p = $page - 1;
      $n = $page + 1;
      $nc = $num-(($page+1)*5);

      if($page != 0){
        echo'<a class="btn btn-primary" href="?side='.$p.'" title="">Forrige</a>';
      };
      if($nc != 0 && ($num / $nc) > 1){
        echo'<a class="btn btn-primary pull-right" href="?side='.$n.'" title="">Næste</a>';
      };


     ?>

    <?php include("php/footer.php"); ?>

    <!-- fancybox tilføjelse -->
    <script type="text/javascript">
        $(document).ready(function() {

            $(".fancybox").fancybox({
                openEffect : 'elastic',
                openSpeed  : 150,

                closeEffect : 'elastic',
                closeSpeed  : 150,

                helpers : {
                    title: false,
                    overlay: false
                }
            });
        });
    </script>

  </body>
</html>


