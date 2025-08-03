<?php
spl_autoload_register(function($nomedoarquivo)
  {
	  if (file_exists('Controllers/' . $nomedoarquivo . '.php'))
	  {
		  require('Controllers/' . $nomedoarquivo . '.php');
		  
	  } elseif (file_exists('Models/' . $nomedoarquivo .'.php'))
	  {
		  require('Models/' . $nomedoarquivo . '.php');
		  
	  } elseif (file_exists('Core/' . $nomedoarquivo .'.php'))
	  {
		  require('Core/' . $nomedoarquivo . '.php');
		  
	  }
  });
?>