<?php
namespace App\core;

class View	{

	  function generate($content_view, $template_view, $data = null)  {

		include VIEWS.$template_view;
	}
}