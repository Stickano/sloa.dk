<DOCTYPE html>
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
    # Document updated: 12. September 2015
    -->

    <?php
      #Hvis man allerede er logget ind, send videre
      if(isset($_SESSION['sloaLogged'])){
        echo '<script>window.location = "godmode.php";</script>';
        exit;
      };
    ?>

    <div class="container">
    	<div class="row">
    		<div class="col-md-3"></div>
    		<div class="col-md-6" style="padding-top:20%;">

    			<div class="panel panel-default">
	    			<div class="panel-heading">
	    				<a href="/" role="button" class="btn btn-default">
	    					<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
	    					<small><b>Tilbage til anstændige omgivelser!</b></small>
	    				</a>
	    			</div>

	    			<div class="panel-body">
	    				<div class="alert alert-danger" role="alert">


                <?php
                #Skifter advarsels besked hvis man laver et mislykket login
                if(!isset($_SESSION['logFailed'])){
	               echo'<b>Du er kommet til en administrativ del af sitet.</b>';
                }else{
                 echo'<b>Ugyldigt login!</b>';
                 unset($_SESSION['logFailed']);
                };
                ?>
	    				</div>


						<p>
							Der tilbydes ikke brugerprofiler på nuværende tidspunkt - men kig tilbage senere, det står på <a href="to-do.php" target="_blank" title="Tager dig til 'to-do'-listen"><small><b>'to-do'-listen</b></small></a>.
              <br /><br />
              Hos sloa.dk kan du afprøve det administrative panel med en gæstprofil. Du vil ikke få hele oplevelsen, men det giver et indtryk af, hvordan kontrollen kunne være på dit site!
              <br /><br />
              Gæstprofilen: <a href="#" onClick="autofil();" title="Udfylder login-formen"><small><b>Gæst # 100%logiskLøsning</b></small></a>
						</p>
	    				<form method="post" action="php/login.php">
	    					<input type="text" name="mail" id="mail" class="form-control" placeholder="E-mail" required autofocus/>
	    					<input type="password" name="upass" id="upass" class="form-control" placeholder="Kodeord" style="margin-top:1.8%;" required/>
	    					<input type="submit" name="okLogin" class="form-control" value="Login" style="margin-top:1.8%;">
	    				</form>
	    			</div>
    			</div>
    		</div>
    		<div class="col-md-3"></div>
    	</div>
    </div>


<script>
  //Udfylder form med gæst oplysninger
  function autofil(){
    document.getElementById('mail').value = "Gæst";
    document.getElementById('upass').value = "100%logiskLøsning";
  };
</script>


  </body>
</html>
