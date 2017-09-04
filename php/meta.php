<?php


    @session_start();
    include("php/connection.php");
    include("php/functions.php");

    #Bekræfter vi anvender ssl/tls (https)
    if(!isset($_SERVER['HTTPS'])){ 
        $qs = NULL;
        if(!empty($_SERVER['QUERY_STRING'])){
            $qs = "?".$_SERVER['QUERY_STRING'];
        };
        echo'<script> window.location = "https://sloa.dk'.$qs.'"; </script>';
        exit;
    };
    

    #CMS styre metadata (title,description,keywords,robot,author)

    #Lidt standard værdier
    $title = "sloa.dk";
    $description = "Hjemmesider, web elementer & design løsninger";
    $keywords = "Webløsninger, hjemmesider, online, print design, hjemmeside design, logo design, wordpress, IT relateret";
    $follow = "noindex, nofollow";

    #Find ud af hvilken database der skal hentes fra
    if($_SERVER['PHP_SELF'] == "/index.php"){
        $sel = "main=1";
    };
    if($_SERVER['PHP_SELF'] == "/blog.php"){
        $sel = "blog=1";
    };
    if($_SERVER['PHP_SELF'] == "/info.php"){
        $sel = "info=1";
    };
    if($_SERVER['PHP_SELF'] == "/kontakt.php"){
        $sel = "contact=1";
    };
    if($_SERVER['PHP_SELF'] == "/portfolio.php"){
        $sel = "portfolio=1";
    };
    if($_SERVER['PHP_SELF'] == "/services.php"){
        $sel = "services=1";
    };
    if($_SERVER['PHP_SELF'] == "/godmode.php"){
        $sel = "godmode=1";
    };
    if($_SERVER['PHP_SELF'] == "/preGodmode.php"){
        $sel = "pregodmode=1";
    };

    #Hent fra den valgte database og sæt metadataen
    if(isset($sel)){
        $sql = "SELECT * FROM meta WHERE ".$sel."";
        $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
        $result = mysqli_fetch_array($query);
        $title = $result['title'];
        $keywords = $result['keywords'];
        $descrition = $result['description'];
        $follow = $result['follow'];
    };

    #Sidens udgiver
    $sql = "SELECT description FROM meta WHERE author=1";
    $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
    $result = mysqli_fetch_array($query);


    #Det essentielle
    echo'<meta charset="utf-8">';
    echo'<meta http-equiv="X-UA-Compatible" content="IE=edge">';
    echo'<meta name="viewport" content="width=device-width, initial-scale=0.8">';
    echo'<meta http-equiv="content-language" content="da">';
    echo'<title>'.$title.'</title>';
    echo'<meta name="description" content="'.$description.'">';
    echo'<meta name="keywords" content="'.$keywords.'" />';
    echo'<meta name="robot" content="'.$follow.'"/>';
    echo'<meta name="author" content="'.$result['description'].'" />';
    echo'<link rel="alternate" href="https://sloa.dk" hreflang="dk" />';

    echo'<script src="js/jquery.min.js"></script>';

    echo'<link href="css/font-awesome.min.css" rel="stylesheet">';

    #<!-- Bootstrap -->
    echo'<link href="css/bootstrap.min.css" rel="stylesheet">';
    echo'<script src="js/bootstrap.min.js"></script>';
    echo'<script type="text/javascript" src="js/bootstrap-filestyle.min.js"> </script>';


    if($_SERVER['PHP_SELF'] == "/blog.php" || $_SERVER['PHP_SELF'] == "/portfolio.php"){
        #<!-- Add fancyBox -->
        echo'<link rel="stylesheet" href="js/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />';
        echo'<script type="text/javascript" src="js/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>';

        #<!-- Optionally add helpers - button, thumbnail and/or media -->
        echo'<link rel="stylesheet" href="js/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />';
        echo'<script type="text/javascript" src="js/fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>';
        echo'<script type="text/javascript" src="js/fancybox/helpers/jquery.fancybox-media.js?v=1.0.6"></script>';

        echo'<link rel="stylesheet" href="js/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />';
        echo'<script type="text/javascript" src="js/fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>';
    };

    if($_SERVER['PHP_SELF'] == "/godmode.php"){
        #Summernote WYSIWYG editor
        echo'<link href="js/summernote/summernote.css" type="text/css" rel="stylesheet"/>';
        echo'<script src="js/summernote/summernote.js"></script>';
    };


    #<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    #<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    echo'<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->';


    #<!-- #sloa logo - font-family: 'Abel', sans-serif; -->
    echo"<link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>";
    #<!-- #footer - font-family: 'Raleway', sans-serif; -->
    echo"<link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>";
    #<!-- #knapper italic - font-family: 'Open Sans Condensed', sans-serif; -->
    echo"<link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,300italic' rel='stylesheet' type='text/css'>";
    #<!-- #overskrift - font-family: 'Roboto Condensed', sans-serif; -->
    echo"<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>";

    #<!-- custom CSS / stylesheet -->
    echo'<link href="css/styles.css" rel="stylesheet">';


    #Hent bruger oplysninger, hvis man er logget ind
    if(isset($_SESSION['sloaLogged'])){
        $sloaLogged = mysqli_real_escape_string($conn,$_SESSION['sloaLogged']);
        $sql = "SELECT * FROM users WHERE id='".$sloaLogged."'";
        $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
        $user = mysqli_fetch_array($query);
    };

?>