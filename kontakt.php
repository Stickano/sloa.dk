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
    # Document updated: June 20 2016
    -->


    <?php include("php/header.php"); 

    #Teksten <- venstre
    $sql = "SELECT * FROM contact WHERE id=1";
    $query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
    $result = mysqli_fetch_array($query);
    echo'<div class="pull-left" style="width:49%;">';
    echo $result['txt'];
    echo'</div>';

    #Kontakt applicationen -> højre

    # Mulighed for at bladre mellem kontakt formularen og PGP nøglen
    $formDis = "disabled='disabled'";
    $pgpDis = NULL;
    if(isset($_GET['pgp'])){
        $formDis = NULL;
        $pgpDis = "disabled='disabled'";
    };
    echo'<div class="pull-right" style="width:49%; margin-left:1%;">';
        echo'<div style="width:100%; text-align:left;">';
        echo'<a href="kontakt.php" class="btn btn-default btn-sm" role="button" style="margin-right:2%;" '.$formDis.'>Kontakt Formular</a>';
        echo'<a href="kontakt.php?pgp" class="btn btn-default btn-sm" role="button" '.$pgpDis.'>PGP Nøgle</a>';
        echo'</div>';
    echo'</div>';
    
    #Hvis mailsuccess er sat (en mail er allerede afsendt) - for at undgå spam
    echo'<div class="pull-right" style="width:49%; margin-left:1%;">';
    if(!isset($_GET['pgp'])){
        if(!isset($_SESSION['mailSendt'])){
            echo'<div class="well well-lg" style="margin-top:2%;">';


                #Udskriv fejl, hvis en er sat
                if(isset($_SESSION['mailError'])){
                    echo'<div class="alert alert-danger" role="alert"><b>'.$_SESSION['mailError'].'</b></div>';
                    unset($_SESSION['mailError']);
                }else{
                    echo'<div class="alert alert-info" role="alert"><b>Bemærk!</b> Alle felter skal udfyldes!</div>';
                };

                #Kontakt formen
                echo'<form method="post" action="php/mailer.php">';

                    echo'<div class="input-group">';
                      echo'<span class="input-group-addon" id="basic-addon1">@</span>';
                      echo'<input type="email" name="mail" class="check form-control" placeholder="Udfyld din E-mail" aria-describedby="basic-addon1" required autofocus>';
                    echo'</div>';

                    echo'<div class="input-group marginTop2">';
                      echo'<span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></span>';
                      echo'<input type="text" name="subject" class="check form-control" placeholder="Emne" aria-describedby="basic-addon1" required>';
                    echo'</div>';

                  echo'<textarea class="check form-control marginTop2" name="txt" rows="5" placeholder="Smid mig en besked!" required></textarea>';

                  echo'<input type="submit" name="okMail" class="btn btn-primary btn-block marginTop2" value="Send" />';

                echo'</form>';
            echo'</div>';

        #Hvis mailsuccess SESSION er sat - vis inaktiv mail form
        }else{

           echo'<div class="well well-lg" style="margin-top:15%;">';

                echo'<div class="alert alert-success" role="alert"><b>Tak!</b> Jeg vender tilbage hurtigst muligt!';

                #For at nulstille mailsuccess
                echo'<button type="button" class="close" onClick="window.location = \'php/clear.php\';" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo'</div>';

                #Inaktiv form
                echo'<fieldset disabled>';
                    echo'<div class="input-group">';
                      echo'<span class="input-group-addon" id="basic-addon1">@</span>';
                      echo'<input type="email" name="mail" class="form-control" placeholder="Udfyld din E-mail" aria-describedby="basic-addon1">';
                    echo'</div>';

                    echo'<div class="input-group marginTop2">';
                      echo'<span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></span>';
                      echo'<input type="text" name="subject" class="form-control" placeholder="Emne" aria-describedby="basic-addon1" required>';
                    echo'</div>';

                  echo'<textarea class="check form-control marginTop2" name="txt" rows="5" placeholder="Smid mig en besked!" required></textarea>';

                  echo'<input type="submit" name="okMail" class="btn btn-primary btn-block marginTop2" value="Send" />';
                echo'</fieldset>';

            echo'</div>';

        };

    # Viser PGP nøglen!
    }else{
        echo'<div class="well well-lg" style="margin-top:2%; display:flex; overflow-x:scroll;">';
            echo '<p>'.nl2br($result['pgp']).'</p>';
        echo'</div>';
    };
    echo'</div>';

?>



    <?php include("php/footer.php"); ?>

    <script>

        //Udskift den typiske fejl besked, hvis felterne mangler at blive udfyldt
        document.addEventListener("DOMContentLoaded", function() {
            var elements = document.getElementsByClassName("check");
            for (var i = 0; i < elements.length; i++) {
                elements[i].oninvalid = function(e) {
                    e.target.setCustomValidity("");
                    if (!e.target.validity.valid) {
                        e.target.setCustomValidity("Du glemte noget!");
                    }
                };
                elements[i].oninput = function(e) {
                    e.target.setCustomValidity("");
                };
            }
        })


    </script>


  </body>
</html>