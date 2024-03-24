<?php
namespace App\core;
use App\controllers\Controller404;
use App\models\DB;

class Route	{

	static function start()	{
		// контроллер и действие по умолчанию
		$controller_name = 'Input';							// стартуем со страницы input
		$action_name = 'index';
		
		$routes = explode('/', $_SERVER['REQUEST_URI']);

		// получаем имя контроллера
		 if ( !empty($routes[1]) )	 {	
		 	$controller_name = $routes[1];					//   /profile   /find   
		 }
		
		 //получаем имя экшена
		if ( !empty($routes[2]) )	 {
		 	$action_name = $routes[2];							 
		}

		$controller_name = 'Controller'.ucfirst (strtolower($controller_name));  		//  ControllerFriendsall

		$action_name = 'action_'.$action_name;											//action_index		
	
		// в роутере не должно быть обработки GET запроса
		// но при переходе из friends_view по ссылке "http://localhost/chat/index/?friend_id=... браузер теряет стили
		// хотя при переходе из friendsall_view на chatall стили не теряются.
		// переходим на страницу чата - Chat  и проверяем, что friend_id существует
		if (isset($_GET['friend_id'])) { 
			if (DB::get_user_by_id ($_GET['friend_id'])) {$controller_name = 'ControllerChat';}
			else Route::ErrorPage();
		}

		// подключаем файл с классом контроллера
		$controller_file = $controller_name.'.php';					
		$controller_path = CONTROLLERS.$controller_file;			

		if (file_exists($controller_path))	{
			include CONTROLLERS.$controller_file;						//   подключаем файл контроллера
		}
		else   {	
			Route::ErrorPage();			
			return;
		}
		
		// создаем контроллер
		$controller_class= CONTROLLERS_NAMESPACE.$controller_name;		   
		$controller = new $controller_class;																				
		$action = $action_name;										

		if (method_exists($controller, $action))	{
					$controller->$action(); 							// вызываем действие контроллера
		}				
		else   {
			Route::ErrorPage();						
			return;		
		}
	}

	public static function ErrorPage()	{
		require_once CONTROLLERS.'Controller404.php';												
		$controller = new Controller404; 
		$controller->action_index();

         $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
         header('HTTP/1.1 404 Not Found');
		 header("Status: 404 Not Found");
		 header('Location:'.$host.'404');
    }
}

