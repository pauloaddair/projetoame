<?php
$titulo = "Modelo";
// include_once('./include/conexao.php');
include_once('./include/funcoes.php');
// include_once('./include/head.php');
?>
<style>
/* 
Define this in your CSS 
.easeAnimation = Replace it by the name you want to give your animation
.easeAnimObj = Assign this class to elements to which you intend to apply the animation
*/

.easeAnimObj {
	position: relative;
	/* Chrome, Safari*/
	-webkit-animation-name: easeAnimation;
	-webkit-animation-duration: 5s;
	-webkit-animation-timing-function: ease;
	-webkit-animation-delay: 2s;
	-webkit-animation-iteration-count: infinite;
	-webkit-animation-direction: alternate;
	-webkit-animation-play-state: running;
	/* Mozilla */
	-moz-animation-name: easeAnimation;
	-moz-animation-duration: 5s;
	-moz-animation-timing-function: ease;
	-moz-animation-delay: 2s;
	-moz-animation-iteration-count: infinite;
	-moz-animation-direction: alternate;
	-moz-animation-play-state: running;	
	/* Standard syntax */
	animation-name: easeAnimation;
	animation-duration: 5s;
	animation-timing-function: ease;
	animation-delay: 2s;
	animation-iteration-count: infinite;
	animation-direction: alternate;
	animation-play-state: running;
}

/* 
Define the keyframe and changes
*/

/* Chrome, Safari */
@-webkit-keyframes easeAnimation {
	0% {
		left: 0;
		top: 0;
	}
	100% {
		left: 200px;
		top: 0;
	}
}

/* Firefox */
@-moz-keyframes easeAnimation {
	0% {
		left: 0;
		top: 0;
	}
	100% {
		left: 200px;
		top: 0;
	}
}

/* Standard syntax */
@keyframes easeAnimation {
	0% {
		left: 0;
		top: 0;
	}
	100% {
		left: 200px;
		top: 0;
	}
}
/* 
Define this in your CSS 
.bgColor = Replace it by the name you want to give your animation
.bgAnimObj = Assign this class to the object you want to apply the effect
*/

.bgAnimObj {
	background: #1abc9c;
	/* Chrome, Safari */
	-webkit-animation: bgColor 5s;
	/* Firefox */
	-moz-animation: bgColor 5s;
	/* Standard Syntax */
	animation: bgColor 5s;
}

/* 
Define the keyframe and changes
*/

/* Chrome, Safari */
@-webkit-keyframes bgColor {
	from {
 		background: #1abc9c;
	} 
	to {
 		background: #ebebeb;
	}
}

/* Firefox */
@-moz-keyframes bgColor {
    from {
        background: #1abc9c;
    }
    to {
        background: #ebebeb;
    }
}

/* Standard syntax */
@keyframes bgColor {
	from {
		background: #1abc9c;
	}
	to {
		background: #ebebeb;
	}
}
/* For non-retina based devices that have a smaller screen */
@media only screen and (min-width: 320px) {

}

/* Retina enabled devices with smaller screen */
@media  only screen and (-webkit-min-device-pixel-ratio: 2) and (min-width: 320px), 
only screen and ( -o-min-device-pixel-ratio: 2/1) and (min-width: 320px),  
only screen and ( min-device-pixel-ratio: 2) and (min-width: 320px),  
only screen and ( min-resolution: 192dpi) and (min-width: 320px),  
only screen and ( min-resolution: 2dppx) and (min-width: 320px) {

}

/*Non- retina based devices with medium screen size */
@media only screen and (min-width: 768px) {

}

/* Retina devices with medium screen size */
@media only screen and (-webkit-min-device-pixel-ratio: 2) and (min-width: 768px),  
only screen and ( -o-min-device-pixel-ratio: 2/1) and (min-width: 768px),  
only screen and ( min-device-pixel-ratio: 2) and (min-width: 768px),  
only screen and ( min-resolution: 192dpi) and (min-width: 768px),  
only screen and ( min-resolution: 2dppx) and (min-width: 768px) {

}

/* Non-retina devices with large screen sizes */
@media only screen and (min-width: 1200px) {

}

/* Retina devices with large screen sizes */
@media  only screen and (-webkit-min-device-pixel-ratio: 2) and (min-width: 1200px),  
only screen and ( -o-min-device-pixel-ratio: 2/1) and (min-width: 1200px),  
only screen and ( min-device-pixel-ratio: 2) and (min-width: 1200px),  
only screen and ( min-resolution: 192dpi) and (min-width: 1200px),  
only screen and ( min-resolution: 2dppx) and (min-width: 1200px) {

}
</style>
<body>
	<div class="container-fluid">
		<ul class="nav nav-pills">
   <li class="nav-item">
      <a class="nav-link active" href="#">Active</a>
   </li>
   <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Dropdown</a>
      <div class="dropdown-menu">
         <a class="dropdown-item" href="#">Action</a>
         <a class="dropdown-item" href="#">Another action</a>
         <a class="dropdown-item" href="#">Something else here</a>
         <div class="dropdown-divider"></div>
         <a class="dropdown-item" href="#">Separated link</a>
      </div>
   </li>
   <li class="nav-item">
      <a class="nav-link" href="#">Link</a>
   </li>
   <li class="nav-item">
      <a class="nav-link disabled" href="#">Disabled</a>
   </li>
</ul>
		<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
		  <li class="nav-item">
			<a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Home</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Profile</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Contact</a>
		  </li>
		</ul>
		<div class="tab-content" id="pills-tabContent">
		  <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">texto de exemplo 1</div>
		  <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">texto de exemplo 2</div>
		  <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">texto de exemplo 3</div>
		</div>
		<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
		  <ol class="carousel-indicators">
			<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
			<li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
			<li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
		  </ol>
		  <div class="carousel-inner">
			<div class="carousel-item active">
			  <img class="d-block w-100" src="/img/abup.jpg" alt="First slide">
			</div>
			<div class="carousel-item">
			  <img class="d-block w-100" src="/img/autocom2024.jpg" alt="Second slide">
			</div>
			<div class="carousel-item">
			  <img class="d-block w-100" src="/img/APS-FESPA-Expositores.jpg" alt="Third slide">
			</div>
		  </div>
		  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		  </a>
		  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		  </a>
		</div>
<?php
function create_slug($string, $charset = 'utf-8'){
	$string = htmlentities($string, ENT_NOQUOTES, $charset, false); // convert accented characters to entities
	// strip unwanted parts of entities to leave unaccented character
    $string = preg_replace('~&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);~', '\1', $string);
    $string = preg_replace('~&([A-za-z]{2})(?:lig);~', '\1', $string);
    $string = preg_replace('~&[^;]+;~', '', $string); // remove other entities
    return preg_replace('~[\s!*\'();:@&=+$,/?%#[\]]+~', '-', $string); // replace spaces and illegal characters with hyphens
}
echo create_slug("This is a sample test")."<br>"; //returns 'This-is-a-sample-test'
echo create_slug("L'été? est là &amp; &eacute;");// returns 'L-ete-est-la-e'
?>
		
<div class="card mb-3 easeAnimObj" style="max-width: 540px;">
  <div class="row no-gutters">
    <div class="col-md-4"> <img src="/img/1801287.png" class="card-img" alt="..."> </div>
    <div class="col-md-8">
      <div class="card-body">
        <h5 class="card-title">Card title</h5>
        <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
        <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
      </div>
    </div>
  </div>
</div>		<div class="card bg-dark text-white"> 
			<img src="/img/1801287.png" class="card-img" alt="Card image cap">
  <div class="card-img-overlay">
	  <span class="align-middle" style="height: 100%;">
		<h5 class="card-title">Card title</h5>
		<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
		<p class="card-text">Last updated 3 mins ago</p>
	  </span>
  </div>
</div>

		<div class="card-deck">
		  <div class="card"> <img src="/img/alessandra.jpg" class="card-img-top" alt="Card Image Cap">
			<div class="card-body">
			  <h5 class="card-title">Card title</h5>
			  <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
			</div>
			<div class="card-footer"> <small class="text-muted">Last updated 3 mins ago</small> </div>
		  </div>
		  <div class="card"> <img src="/img/ANDRE-CAROLLO-foto.jpg" class="card-img-top" alt="Card Image Cap">
			<div class="card-body">
			  <h5 class="card-title">Card title</h5>
			  <p class="card-text">This card has supporting text below as a natural lead-in to additional content.</p>
			</div>
			<div class="card-footer"> <small class="text-muted">Last updated 3 mins ago</small> </div>
		  </div>
		  <div class="card"> <img src="/img/caio.jpg" class="card-img-top" alt="Card Image Cap">
			<div class="card-body">
			  <h5 class="card-title">Card title</h5>
			  <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This card has even longer content than the first to show that equal height action.</p>
			</div>
			<div class="card-footer"> <small class="text-muted">Last updated 3 mins ago</small> </div>
		  </div>
		</div>
		<div class="card mt-2">
		  <div class="card-body">
			<h5 class="card-title">Card title</h5>
			<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
			<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
		  </div>
		  <img src="/img/ame2023.jpg" class="card-img-top" alt="Card image cap">
		</div>
		<div class="row bgAnimObj">
			<div class="col-lg-4 text-center">.col-lg-4</div>
			<div class="col-lg-4 text-center">.col-lg-4</div>
			<div class="col-lg-4 text-center">.col-lg-4</div>
		</div>
		<figure class="figure">
  <img src="/img/focusfashionsummit.jpg" class="figure-img img-fluid rounded" alt="...">
  <figcaption class="figure-caption">A caption for the above image.</figcaption>
</figure>
		<picture>
  <source srcset="/img/1801287.svg" type="image/svg+xml">
  <img src="/img/1801287.png" class="img-fluid" alt="...">
</picture>
		<p> <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"> Link with href </a> </p>
		<div class="collapse" id="collapseExample">
		  <div class="card card-body">Mussum Ipsum, cacilds vidis litro abertis.  Casamentiss faiz malandris se pirulitá. Posuere libero varius. Nullam a nisl ut ante blandit hendrerit. Aenean sit amet nisi. Si num tem leite então bota uma pinga aí cumpadi! Detraxit consequat et quo num tendi nada.</div>
		</div>
		<span class="badge badge-danger">Danger</span>
		<div class="alert alert-danger" role="alert"> This is a danger alert—check it out! <span class="badge badge-info">Info</span></div>
		<div class="alert alert-warning alert-dismissible fade show" role="alert">
		   <strong>Holy guacamole!</strong> You should check in on some of those fields below.<a href="#" class="badge badge-warning">Warning</a>
		   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		   <span aria-hidden="true">&times;</span>
		   </button>
		</div>
		<button type="button" class="btn btn-primary btn-lg btn-block">Block level button</button>
		<button type="button" class="btn btn-outline-secondary">Secondary</button>
		<div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
		  <div class="btn-group mr-2" role="group" aria-label="First group">
			<button type="button" class="btn btn-secondary">1</button>
			<button type="button" class="btn btn-secondary">2</button>
			<button type="button" class="btn btn-secondary">3</button>
			<button type="button" class="btn btn-secondary">4</button>
		  </div>
		  <div class="btn-group mr-2" role="group" aria-label="Second group">
			<button type="button" class="btn btn-secondary">5</button>
			<button type="button" class="btn btn-secondary">6</button>
			<button type="button" class="btn btn-secondary">7</button>
		  </div>
		  <div class="btn-group" role="group" aria-label="Third group">
			<button type="button" class="btn btn-secondary">8</button>
		  </div>
		</div>
	</div>
<?php
include_once('./include/footer.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php 
include_once('./include/end.php');
?>
