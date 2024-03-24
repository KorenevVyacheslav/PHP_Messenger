<?php
namespace App\controllers;
use App\core\Controller;

class ControllerLogout extends Controller	{
	function action_index()	{	
		session_destroy();
		header("Location: http:\\");
	}
}