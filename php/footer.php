<?php
				
			echo'</div>';
			echo'</div>';
		echo'</div>';
	echo'</div>';	



	echo'<div class="container-fluid">';
		echo'<div class="row">';
			echo'<div class="col-md-3"></div>';
			echo'<div class="col-md-6" style="background-color:whitesmoke; padding:.8%; border-radius:4px;">';	

					#Kontakt oplysninger
					$sql = "SELECT name,mail,adress,phone,bitcoin FROM footer WHERE id=1";
					$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
					$result = mysqli_fetch_array($query);
					echo'<div style="text-align:right;">';
					echo'<address itemscope itemtype="http://schema.org/Person" style="margin-bottom:1%;">';
						echo'<small>';
							echo'<b>sloa.dk </b>';
							echo'<br />';
							echo'<span itemprop="name"><b>'.$result['name'].'</b></span>';
							echo'<br />';
							echo'<span itemprop="addres">'.$result['adress'].'</span>';
							echo'<br />';
							echo'<span itemprop="telephone">'.$result['phone'].'</span> &nbsp;';
							echo'<abbr title="Telefon"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span></abbr>';
							echo'<br />';
							echo'<a href="mailto:'.$result['mail'].'" title="Åbner din E-mail klient"><span itemprop="email">'.$result['mail'].'</span></a> &nbsp;';
							echo'<abbr title="E-mail"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></abbr>';
							echo'<br />';
							echo '<span class="small">'.$result['bitcoin'].'</span> &nbsp;';
							echo'<abbr title="Bitcoin"><span class="glyphicon glyphicon-bitcoin" aria-hidden="true"></span></abbr>';
						echo'</small>';
					echo'</address>';
					echo'</div>';


					#Social media
					$sql = "SELECT * FROM socialmedia ORDER BY id DESC";
					$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));

					$br = 0;

					echo'<div style="width:100%; height:20px;">';
					while($result = mysqli_fetch_array($query)){
						if($result['active'] == 1){
							if($br == 3){
								echo'<br />';
							};
							echo'<a href="'.$result['link'].'" target="_blank" title="'.$result['link_title'].'" class="socBut"> <img src="'.$result['icon'].'" class="socImg" style="margin-left:5px;" /> </a>';
							$br++;
						};
					};
					echo'</div>';


					#Licens
					$sql = "SELECT * FROM licenses WHERE active=1";
					$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
					$result = mysqli_fetch_array($query);
					$icon = substr($result['icon'],0,15);
					if($icon == "media/licenses/"){
						$icon = '<img src="'.$result['icon'].'" style="height:14px; margin-right:.3%; margin-top:-.4%;" />';
					}else{
						$icon = $result['icon'];
					};
					echo'<div itemscope itemtype="https://schema.org/CreativeWork" style="text-align:center; margin:1% 0 0 0;">';
						echo $icon;
						echo'<a href="'.$result['link'].'" title="Læs mere" class="">Creative Commons, Non-Commercial</a>';
					echo'</div>';


			echo'</div>';
			echo'<div class="col-md-3"></div>';
		echo'</div>';
	echo'</div>';

	#Enjoy the view^
	echo'<div style="width:100%; height:99.5%;"></div>';

?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-100899620-1', 'auto');
  ga('send', 'pageview');

</script>