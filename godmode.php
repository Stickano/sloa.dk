<!DOCTYPE html>
<html lang="en">
  <head>

    <?php include("php/meta.php"); ?>

  </head>
  <body>

    <!--
    # Version: 1b
    # Started: 8. September 2015
    # Author: Henrik Jeppesen, info@sloa.dk
    # License: Creative Commons (CC), Non-Commercial (NC)
    # Updated: 1. September 2015
    -->


<?php
  #Sikkerhed
  if(!isset($_SESSION['sloaLogged'])){
    $client = mysqli_real_escape_string($conn,clientIP());
    $time = mysqli_real_escape_string($conn,timeMe());
    $sql = "INSERT INTO events (ip,event,time,danger,rel) VALUES ('".$client."','Illegal dokument tilgang (godmode.php)','".$time."',1,'sikkerhed')";
    mysqli_query($conn,$sql)or die(mysqli_error($conn));
    session_destroy();
    header("location:/");
    exit;
  };

  #Husk sidste (nuværende) side - når man skal retur fra godmode/php mappen
  $qs = NULL;
  if(!empty($_SERVER['QUERY_STRING'])){
    $qs = "?".$_SERVER['QUERY_STRING'];
  };
  $_SESSION['last'] = $_SERVER['PHP_SELF'].$qs;

?>

    <div class="container-fluid">
      <div class="row">

        <!-- Menuen <- venstre side -->
        <div class="col-md-2 godMenCon">
          <span class="sloaGodLogo pull-right">sloa.dk</span>

          <?php
            #Hent hvonår brugeren sidst var logget ind
            $logSql = "SELECT time FROM events WHERE uid='".$user['id']."' AND event='Succesfuldt login' ORDER BY id DESC LIMIT 1, 1";
            $logQuery = mysqli_query($conn,$logSql)or die(mysqli_error($conn));
            if(mysqli_num_rows($logQuery) == true){
              $logResult = mysqli_fetch_array($logQuery)or die(mysqli_error($conn));
              $lastLogged = substr($logResult['time'],0,10);
            }else{
              $lastLogged = "Første login - Velkommen";
            };

            #Når man skifter til siden (front-end), send til aktuelle side
            $pageTo = NULL;
            if(isset($_GET['blog'])){
              $pageTo = "blog.php";
            }elseif(isset($_GET['portfolio'])){
              $pageTo = "portfolio.php";
            }elseif(isset($_GET['info'])){
              $pageTo = "info.php";
            }elseif(isset($_GET['services'])){
              $pageTo = "services.php";
            }elseif(isset($_GET['kontakt'])){
              $pageTo = "kontakt.php";
            };

            #Personlig top af menuen
            echo'<p style="color:white; margin:25% 0 0 5%;">';
              echo'<small><b>Hej,</b></small> '.$user['uname'];
              echo'<br />';
              echo'<small><b>Sidste login,</b></small> '.$lastLogged;
            echo'</p>';
            echo'<div style="margin:5% 0 20% 5%;">';
              echo'<a href="godmode.php?profil" title="Din Profil" class="btn btn-default btn-sm marRight2">';
                echo'<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ';
              echo'</a>';
              echo'<a href="/'.$pageTo.'" title="Gå til Sitet" class="btn btn-default btn-sm marRight2">';
                echo'<span class="glyphicon glyphicon-retweet" aria-hidden="true"></span> ';
              echo'</a>';
              echo'<a href="php/logout.php" title="Log ud" class="btn btn-default btn-sm marRight2">';
                echo'<span class="glyphicon glyphicon-off" aria-hidden="true"></span> ';
              echo'</a>';
            echo'</div>';
          ?>

          <!-- Menu knapperne - smidt i en table -->
          <table class="table table-hover" style="background-color:white;">
            <tbody>
              <!-- Sidens indhold -->
              <tr>
                <td>
                  <a href="godmode.php?blog" title="CMS - blog">
                    <span style="color:black;" class="glyphicon glyphicon-pencil marRight2" aria-hidden="true"></span>
                    <small><b >Blog</b></small>
                  </a>
                </td>
              </tr>
              <tr>
                <td>
                  <a href="godmode.php?portfolio" title="CMS - portfolio">
                    <span style="color:black;" class="glyphicon glyphicon-th marRight2" aria-hidden="true"></span>
                    <small><b>Portfolio</b></small>
                  </a>
                </td>
              </tr>
              <tr>
                <td>
                  <a href="godmode.php?forsiden" title="CMS - forsiden">
                    <span style="color:black;" class="glyphicon glyphicon-home marRight2" aria-hidden="true"></span>
                    <small><b>Forsiden</b></small>
                  </a>
                </td>
                </tr>
              <tr>
                <td>
                  <a href="godmode.php?services" title="CMS - services">
                    <span style="color:black;" class="glyphicon glyphicon-wrench marRight2" aria-hidden="true"></span>
                    <small><b>Services</b></small>
                  </a>
                </td>
                </tr>
                <tr>
                <td>
                  <a href="godmode.php?kontakt" title="CMS - kontakt">
                    <span style="color:black;" class="glyphicon glyphicon-send marRight2" aria-hidden="true"></span>
                    <small><b>Kontakt</b></small>
                  </a>
                </td>
              </tr>
              <tr>
                <td>
                  <a href="godmode.php?info" title="CMS - info">
                    <span style="color:black;" class="glyphicon glyphicon-info-sign marRight2" aria-hidden="true"></span>
                    <small><b>Info</b></small>
                  </a>
                </td>
              </tr>
              <tr class="active"><td></td></tr>
              <!-- Sidens grundlæggende -->
              <tr>
                <td>
                  <a href="godmode.php?meta" title="Sidens metadata">
                    <span style="color:black;" class="glyphicon glyphicon-globe marRight2" aria-hidden="true"></span>
                    <small><b>Metadata</b></small>
                  </a>
                </td>
              </tr>
              <tr>
                <td>
                  <a href="godmode.php?footer" title="Sidens footer">
                    <span style="color:black;" class="glyphicon glyphicon-hand-down marRight2" aria-hidden="true"></span>
                    <small><b>Footer</b></small>
                  </a>
                </td>
              </tr>
              <tr class="active"><td></td></tr>
              <!-- Sidens administrative del -->
              <tr>
                <td>
                  <a href="godmode.php?admin" title="Organiser administratorer">
                    <span style="color:black;" class="glyphicon glyphicon-king marRight2" aria-hidden="true"></span>
                    <small><b>Administratorer</b></small>
                  </a>
                </td>
              </tr>
              <tr>
                <td>
                  <a href="godmode.php?log" title="Log oversigt">
                    <span style="color:black;" class="glyphicon glyphicon-eye-open marRight2" aria-hidden="true"></span>
                    <small><b>Log</b></small>
                  </a>
                    <?php
                      #Henter advarsels ikon (nye hændelser)
                      $sql = "SELECT seen FROM events WHERE seen=0 AND danger=1";
                      $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
                      $num = mysqli_num_rows($query);
                      if($num == TRUE){
                        echo'<span class="badge pull-right">'.$num.'</span>';
                      };
                    ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-md-10" style="margin-left:16.6%; padding-top:1%;">
          <?php
            #Hent de forskellige sider - mere information i de inkluderede filer
            if(!isset($_GET['meta'])&& isset($_GET['blog'])){
              include("godmode/blog.php");
            };
            if(!isset($_GET['meta'])&& isset($_GET['portfolio'])){
              include("godmode/portfolio.php");
            };
            if(!isset($_GET['meta'])&& isset($_GET['forsiden'])){
              include("godmode/forsiden.php");
            };
            if(!isset($_GET['meta'])&& isset($_GET['services'])){
              include("godmode/services.php");
            };
            if(!isset($_GET['meta'])&& isset($_GET['kontakt'])){
              include("godmode/kontakt.php");
            };
            if(!isset($_GET['meta'])&& isset($_GET['info'])){
              include("godmode/info.php");
            };
            if(!isset($_GET['meta'])&& isset($_GET['admin'])){
              include("godmode/admin.php");
            };
            if(!isset($_GET['meta'])&& isset($_GET['log'])){
              include("godmode/log.php");
            };
            if(!isset($_GET['meta'])&& isset($_GET['profil'])){
              include("godmode/profil.php");
            };
            if(!isset($_GET['meta'])&& isset($_GET['footer'])){
              include("godmode/footer.php");
            };
            if(isset($_GET['meta'])){
              include("godmode/meta.php");
            };

          ?>
        </div>
      </div>
    </div>

  </body>
</html>