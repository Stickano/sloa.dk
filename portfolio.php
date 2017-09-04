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
    # Document updated: June 22 2016
    -->


    <?php

    include("php/header.php");

    ### Individuel projekt visning (når man er klikket ind på et projekt)

if(isset($_GET['id'])){


    #Sikre ID'et er en tal-værdi
    $id = 0;
    if(is_numeric($_GET['id']) == true){
        $id = $_GET['id'];
    }else{
        $id = 0;
    };

    #Hent projektet
    $sql = "SELECT * FROM portfolio WHERE id='".$id."'";
    $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
    $result = mysqli_fetch_array($query);

    #Opdater counteren, når projektet bliver besøgt
    $counter = $result['counter']+1;
    $cSql = "UPDATE portfolio SET counter='".$counter."' WHERE id='".$result['id']."'";
    mysqli_query($conn,$cSql)or die(mysqli_error($conn));

    #Billeder (medie) til projektet
    $msql = "SELECT * FROM media WHERE portfolio=1 AND mid='".$id."' AND dl=0";
    $mquery = mysqli_query($conn,$msql)or die(mysqli_error($conn));

    #download
    $dlSql = "SELECT file FROM media WHERE portfolio=1 AND dl=1 AND mid='".$id."'";
    $dlQuery = mysqli_query($conn,$dlSql)or die(mysqli_error($conn));

    #Kategori
    $catsql = "SELECT * FROM categories WHERE portfolio=1 AND id='".$result['category']."'";
    $catquery = mysqli_query($conn,$catsql)or die(mysqli_error($conn));
    $catresult = mysqli_fetch_array($catquery);

    #tilbage knap
    echo'<div style="margin:2% 0 2%;">';
        echo'<a href="portfolio.php?'.$_SESSION['last'].'" title="Portfolio">Tilbage til oversigten</a>';
    echo'</div>';

    #Medie
    echo'<div style="float:left; width:40%;">';
    while($mresult = mysqli_fetch_array($mquery)){
        echo'
        <a class="thumbnail fancybox marginTop2" href="'.$mresult['file'].'" title="Fuld størrelse">
            <img itemprop="image" src="'.$mresult['file'].'" class="width100" alt="'.$mresult['txt'].'" />
            <div style=" text-align:center; width:100%;">'.$mresult['txt'].'</div>
        </a>';
    };
    echo'</div>';


    #Tekst
    echo'<div style="float:right; width:58%;" itemscope itemtype="https://schema.org/CreativeWork">';
        #overskrift
        echo '<h3 itemprop="headline" class="headline">'.$result['headline'].'</h3>';
        #dato
        echo '<small><b>Udgivet : </b></small><small style="color:blue;" itemprop="datePublished">'.$result['time'].'</small>';
        echo'<br />';
        #kategory
        echo'<small><b>Kategori : </b><span style="color:blue;" itemprop="genre">'.$catresult['category'].'</span></small>';
        echo'<br />';
        #Mulighed for download
        if(mysqli_num_rows($dlQuery) == true){
            $dlResult = mysqli_fetch_array($dlQuery);
            echo'<br />';
            echo'<a href="'.$dlResult['file'].'" type="button" class="btn btn-primary">Download</a>';
        };
        #reference
        if(!empty($result['reference'])){
            echo'<div class="alert alert-info marginTop2"><strong>Se mere på : <br /></strong><a itemprop="url" href="'.$result['reference'].'" target="_blank">'.$result['reference'].'</a></div>';
        };
        #beskrivelse
        echo'<div style="clear:both; margin-top:8%;">';
            echo '<p itemprop="text">'.$result['txt'].'</p>';
        echo'</div>';
    echo'</div>';

};


    ### Alt ovenstående er individuel projet visning (når man er klikket videre ind på et projekt)



    ### Alt nedenstående er liste visning af alle/filter valgte projekter ####

if(!isset($_GET['id'])){



    #querystring
    $qs = $_SERVER['QUERY_STRING'];
    $_SESSION['last'] = $qs;
    echo'<script> var qs = "'.$qs.'"; </script>';


      #sikre sig at querystring (side) er en tal værdi
      $page = 0;
      if(isset($_GET['side'])){
        if(is_numeric($_GET['side']) == true){
            $page = $_GET['side'];
        }else{
            $page = 0;
        };
      };


    #Filter søgning kriterier

    #standard værdier til formen (tjeks til mulighederne)
    $web = "checked";
    $design = "checked";
    $all = "checked";
    $latest = "checked";
    $random = NULL;
    $category = NULL;
    $countTo = 5;

    #Hent alt indhold (hvis query string ikke er sat)
    $count = $page*5;
    $sql = "SELECT * FROM portfolio ORDER BY id DESC LIMIT ".$count.", 5";

    #Hvis query string er sat (filter søgning er blevet anvendt)
    if(isset($_GET['filter'])){
        /*
            #Filter
            s = seneste
            k = kategori
            t = tilfældigt

            #Sortering
            w = web
            d = design
            a = alt
        */

        #nulstil standard værdier
        $web = NULL;
        $design = NULL;
        $all = NULL;



        #bryd query strængen op i individuelle karaktere
        $split = str_split($_GET['filter']);

        #sotering
        if(in_array('s',$split)){
            $order = "id DESC";
        }elseif(in_array('k',$split)){
            $order = "category ASC";
            $latest = NULL;
            $category = "checked";
        #}else{
         #   $order = "RAND()";
          #  $latest = NULL;
           # $random = "checked";
        };

        #Antal
        foreach ($split as $seaNum){
            if(is_numeric($seaNum)){
                $countTo = $seaNum;
            };
        };

        #Filter
        $arr = array();
        if(in_array('w',$split)){
            $arr[] = "1";
            $web = "checked";
        };
        if(in_array('d',$split)){
            $arr[] = "2";
            $design = "checked";
        };
        if(in_array('a',$split)){
            $arr[] = "3";
            $all = "checked";
        };

        #Vis antal fra - til
        $count = $page*$countTo;

        #Gør filter mulighederne (teksten/string) klar til databaseforbindelsen
        $select = implode(' , ',$arr);

        #Databaseforbindelsen
        $sql = "SELECT * FROM portfolio WHERE category IN(".$select.") ORDER BY ".$order." LIMIT ".$count.",".$countTo."";

    };



    #Hvis filter søgningen IKKE er lukket (viser filter boksen)
    if(!isset($_GET['fl'])){

    #Filter søgnings boksen er trukket lidt længere til venstre, end vanligt i designet

    #Filter søgning (boks)
    echo'

            <div class="panel panel-default pull-left" style="width:25%; margin-left:-2%;">
                <div class="panel-heading">
                    <b class="text-muted">Filter</b>
                    <button type="button" title="Luk filter" class="close" onClick="window.location = \'portfolio.php?fl&\' + qs;" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>';

    echo'
                <div class="panel-body">

                    <form method="post" action="php/set.php">

                        <div class="bg-primary" style="width:100%; padding:1%; margin-bottom:2%;"><small><b>Kategorier</b></small></div>
                        <input type="checkbox" name="web" '.$web.'> Web
                        <br />
                        <input type="checkbox" name="design" '.$design.'> Design
                        <br />
                        <input type="checkbox" name="everythingelse" '.$all.'> Alt det andet

                        <div class="bg-primary" style="width:100%; padding:1%; margin:7% 0 2%;"><small><b>Antal per side</b></small></div>
                        <select name="count" class="form-control" onchange="this.form.submit()">';
                            #Antal per side
                            $br = 1;
                            while($br <= 5){
                                $switch = 6-$br;
                                $checked = NULL;
                                if($countTo == $switch){
                                    $checked = "selected";
                                }
                                echo'<option value="'.$switch.'" '.$checked.'>'.$switch.'</option>';
                                $br++;
                            };

    echo'
                        </select>
                        <div class="bg-primary" style="width:100%; padding:1%; margin:7% 0 2%;"><small><b>Sorter efter</b></small></div>
                        <input type="radio" name="order" value="latest"  '.$latest.'> Seneste
                        <br />
                        <input type="radio" name="order" value="category" '.$category.'> Kategori';
                        /*
                        <br />
                        <input type="radio" name="order" value="random" '.$random.'> Tilfædig
                        */
    echo'
                        <input type="submit" value="Søg" name="okFilter" class="btn btn-success btn-sm btn-block marginTop2" />

                    </form>
                </div>
            </div>';

    #Den lukkede filterboks (knap til genåbning)
    }else{

        #Ryd den lukkede tilstand fra query string
        $flSearch = strpos($_SERVER['QUERY_STRING'],'fl&');
        $flClear = substr_replace($_SERVER['QUERY_STRING'],'',$flSearch,3);
        #lidt mystisk lavet link, men hva faen
        echo'<script> var qs = "'.$flClear.'"; </script>';
        echo'<button type="button" title="Filter muligheder" style="margin-left:-2%;" class="btn btn-default btn-sm" onClick="window.location = \'portfolio.php?\' + qs;" aria-label="Close"><span aria-hidden="true" class="glyphicon glyphicon-list"></span></button>';

    }; #slut for filter boksen


    #Indhold

    #databasen - henter fra filter kriterier (ovenstående)
    $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));

    #Hent indholdet
    $br = 0;
    echo'<div itemscope itemtype="https://schema.org/CreativeWork">';
    while($result = mysqli_fetch_array($query)){

        #Forbindelse til preview billedet
        $imgLimit = "LIMIT 1";
        if(isset($_GET['fl'])){ 
            $imgLimit = "LIMIT 3"; 
            if($countTo == 1){
                $imgLimit = NULL;
            };
        };
        $imgsql = "SELECT file,txt FROM media WHERE portfolio=1 AND mid=".$result['id']." AND dl=0 ORDER BY preview DESC ".$imgLimit."";
        $imgquery = mysqli_query($conn,$imgsql)or die(mysqli_error($conn));
        #$imgresult = mysqli_fetch_array($imgquery);
        $imgNum = mysqli_num_rows($imgquery);

        #Antal billeder til projektet
        $icsql = "SELECT id FROM media WHERE portfolio=1 AND mid=".$result['id']." AND dl=0";
        $icquery = mysqli_query($conn,$icsql)or die(mysqli_error($conn));
        $icnum  = mysqli_num_rows($icquery);

        #Download tjek
        $dlsql = "SELECT id,file FROM media WHERE portfolio=1 AND mid=".$result['id']." AND dl=1";
        $dlquery = mysqli_query($conn,$dlsql)or die(mysqli_error($conn));

        #Forbindelse til kategori
        $catsql = "SELECT category FROM categories WHERE portfolio=1 AND id=".$result['category']."";
        $catquery = mysqli_query($conn,$catsql)or die(mysqli_error($conn));
        $catresult = mysqli_fetch_array($catquery);

        #Opdater counteren for projekter, hvis man ser enkelt visning
        if($countTo == 1 && isset($_GET['fl'])){
            $counter = $result['counter']+1;
            $cSql = "UPDATE portfolio SET counter='".$counter."' WHERE id='".$result['id']."'";
            mysqli_query($conn,$cSql)or die(mysqli_error($conn));
        };

        #Ingen border i den første artikel - eller på med den border!
        $border = "1px solid rgba(0, 0, 0, .2)";
        if($br == 0){
            $border = "none";
            $br++;
        };

        #Designet ændres efter om filter-boksen er åben/lukket - defineres her
        $width = "73%";
        $imageWidth = "35%";
        $previewWidth = "100%";
        $previewMargin = "0";
        $imagesToCount = 1;
        $wordCount = 400;
        if(isset($_GET['fl'])){
            $width = "100%";
            $imageWidth = "100%";
            $previewWidth = "32%";
            $previewMargin = ".5%";
            $imagesToCount = 3;
            if($countTo == 1){
                $imagesToCount = $imgNum;
            };
            $wordCount = 800;
        };

        #HTML'en
        echo'<div style="padding:2% 0 2% 0; width:'.$width.'; border-top:'.$border.'; float:right;">';
            if(isset($_GET['fl'])){
                #overskrift og dl, hvis filter boksen er lukket
                echo '<h3 itemprop="headline" class="headline">';
                        if($countTo != 1 || !isset($_GET['fl'])){
                            echo'<a href="?id='.$result['id'].'" title="Åben projekt">';
                        };

                        echo $result['headline'];

                        if($countTo != 1 || !isset($_GET['fl'])){
                            echo'</a>';
                        }; 
                echo '</h3>';
                if(mysqli_num_rows($dlquery) == 1 && $countTo != 1){
                    echo'<small class="text-muted"><b>Mulighed for Download</b></small>';
                };
            };     
  
            #preview billede
            echo'<div style="float:left; margin:0 2% 2% 0; width:'.$imageWidth.';">';
            $imageCount = 0;
            while($imgresult = mysqli_fetch_array($imgquery)){

                #Vi definere lidt typisk
                $imageCount++;
                $previewLink = '?id='.$result['id'];
                $previewTitle = "Åben projekt";
                if($countTo == 1 && isset($_GET['fl'])){
                    $previewLink = $imgresult['file'];
                    $previewTitle = "Fuld størrelse";
                };
                if($imageCount <= $imagesToCount){

                    echo'
                    <a class="thumbnail fancybox" style="width:'.$previewWidth.'; padding:1%; margin:'.$previewMargin.'; float:left;" 
                        href="'.$previewLink.'" title="'.$previewTitle.'">';
                        if(!isset($_GET['fl'])){
                            echo'
                            <img itemprop="image" src="'.$imgresult['file'].'" 
                                style="width:100%;" alt="'.$imgresult['txt'].'" />';
                        }else{
                            echo'
                            <div style="width:100%; height:150px; background-image:url('.$imgresult['file'].'); 
                                background-position:center center; background-size:100%; background-repeat:no-repeat;"></div>';
                        };
                    echo'
                    </a>';
                };
            };
            echo'</div>';

            #tekst
            if(!isset($_GET['fl'])){
                echo'<div style="margin-bottom:3%;">';
                #overskrift
                echo '<h3 itemprop="headline" class="headline"><a href="?id='.$result['id'].'" title="Åben projekt">'.$result['headline'].'</a></h3>';
            
                #dato
                echo '<small><b>Udgivet : </b></small><small style="color:blue;" itemprop="datePublished">'.$result['time'].'</small>';
                echo'<br />';
                #kategory
                echo'<small><b>Kategori : </b><span style="color:blue;" itemprop="genre">'.$catresult['category'].'</span></small>';
                echo'<br />';
                #Antal billeder
                echo'<small><b>Billeder : </b></small><small style="color:blue;">'.$icnum.'</small>';
                #Hvis der er download-able indhold, vis besked deromkring
                if(mysqli_num_rows($dlquery) == true){
                    echo'<br />';
                    echo'<small class="text-muted"><b>Mulighed for Download</b></small>';
                };
                echo'</div>';
            };
            

            #Ved enkelt visning, giv reference og download punkter
            if($countTo == 1 && isset($_GET['fl'])){
                #Mulighed for download
                if(mysqli_num_rows($dlquery) == true){
                    $dlResult = mysqli_fetch_array($dlquery);
                    $marginBottom = NULL;
                    if(empty($result['reference'])){ $marginBottom = "margin-bottom:3%;"; };
                    echo'<div style="clear:both; width:100%;">';
                    echo'<a href="'.$dlResult['file'].'" type="button" style="'.$marginBottom.'" class="btn btn-primary">Download</a>';
                    echo'</div>';
                };
                #reference
                if(!empty($result['reference'])){
                    echo'<div style="clear:both; width:100%;">';
                    echo'<div class="alert alert-info marginTop2"><strong>Se mere på : </strong><a itemprop="url" href="'.$result['reference'].'" target="_blank">'.$result['reference'].'</a></div>';
                    echo'</div>';
                };
            };

            #beskrivelse
            echo'<div style="clear:both;">';
                $txt = $result['txt'];
                if(strlen($result['txt']) >= $wordCount && $countTo != 1){
                    $txt = substr($result['txt'],0,$wordCount)."...";
                };
                if($countTo != 1 || !isset($_GET['fl'])){ 
                    echo'<a href="?id='.$result['id'].'" title="Se mere!" class="txtA">'; 
                };

                echo '<p itemprop="text">'.$txt.'</p>';

                if($countTo != 1){ 
                    echo'</a>'; 
                };
            echo'</div>';

            #data, hvis filter-boksen er lukket
            if(isset($_GET['fl'])){
                echo'
                    <div style="width:33%; float:left; text-align:center; margin-bottom:5%; margin-top:5%;">
                        <small><b>Udgivet : </b></small><small style="color:blue;" itemprop="datePublished">'.$result['time'].'</small>
                    </div>
                    <div style="width:33%; float:left; text-align:center; margin-top:5%;">
                        <small><b>Kategori : </b><span style="color:blue;" itemprop="genre">'.$catresult['category'].'</span></small>
                    </div>
                    <div style="width:33%; float:left; text-align:center; margin-top:5%;">
                        <small><b>Billeder : </b></small><small style="color:blue;">'.$icnum.'</small>
                    </div>
                ';
            };            
        echo'</div>';

    };
    echo'</div>';


      #Pagination (styrer side-tallet)
      if(!isset($select)){ $select = "1,2,3"; };
      $sql = "SELECT id FROM portfolio WHERE category IN(".$select.")";
      $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
      $num = mysqli_num_rows($query);

      $p = $page - 1;
      $n = $page + 1;
      $nc = $num-(($page+1)*$countTo);

      $fl = NULL;
      $filter = NULL;
      if(isset($_GET['fl'])){
        $fl = "fl&";
      };
      if(isset($_GET['filter'])){
        $filter = "filter=".$_GET['filter']."&";
      };

      echo'<div style="width:100%; clear:both;">';
      if($page != 0){
        echo'<a class="btn btn-primary" href="?'.$fl.$filter.'side='.$p.'" title="">Forrige</a>';
      };
      if($nc != 0 && ($num / $nc) > 1){
        echo'<a class="btn btn-primary pull-right" href="?'.$fl.$filter.'side='.$n.'" title="">Næste</a>';
      };
      echo'</div>';

};  #Slut for ovenstående liste visning (alle projekter)


    echo'
        </div>
        <div class="col-md-3"></div>';


    include("php/footer.php");

    ?>

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