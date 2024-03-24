<?php
namespace App\controllers;
use App\core\Controller;
use App\core\View;
use App\models\DB;

//контроллер страницы поиска
class ControllerFind extends Controller	{

	public function __construct()	 {	
	  	$this->view = new View();	
	}

	function action_index()	{	

		$data = [
            'auth' => false,						// авторизация
            'main_text' => 'Поиск'
		];

      	if (isset($_SESSION['id'])) { 						 
			$data['auth'] = true;
			$data['author_avatar_path']=DB::get_user_by_id($_SESSION['id'])['picture'];

			//имя пользователя
			$data['login']=(DB::get_user_by_id($_SESSION['id'])['nick']) .' '.  (DB::get_user_by_id($_SESSION['id'])['login']);
	  	}
		
		$this->view->generate('find_view.php', 'template_view.php', $data);	
	}
}