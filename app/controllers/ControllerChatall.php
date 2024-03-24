<?php
namespace App\controllers;
use App\core\Controller;
use App\core\View;
use App\models\DB;

// контроллер группового чата
class ControllerChatall extends Controller {

	public function __construct()	{				
	 	$this->view = new View();						// инициализация объекта представления
	}

	function action_index()	{	
   		$data = [								
			'auth' => false,									// авторизация
		];

		// заходим из friendsall, есть SESSION['id'] = id текущего пользователя, $_GET['groupp'] - группа автора чата, 
		// $_GET['author_id'] - id автора чата
		if (isset($_SESSION['id']) && isset($_GET['groupp']) && isset($_GET['chat_author_id'])) { 
			$data['auth'] = true;
			$id = $_SESSION['id'];
			$groupp = $_GET['groupp'];
			$chat_author_id = $_GET['chat_author_id'];
			$data['author_avatar_path']=DB::get_user_by_id($id)['picture'];
			
			//имя пользователя
			$data['login']=(DB::get_user_by_id($id)['nick']) .' '.  (DB::get_user_by_id($id)['login']);

			$data['main_text'] = 'Группа ' . $groupp . ' автора: '. (DB::get_user_by_id($chat_author_id)['nick']) .' '.  (DB::get_user_by_id($chat_author_id)['login']);

			$data['groupp'] = $groupp;
			$data['chat_author_id'] = $_GET['chat_author_id'];

			// считывание или запись состояния звука для текущего чата
			//load_save_mute_all ($id, $chat_author_id, $groupp, $rec, $mutes)
			//$rec=true/false - запись/чтение состояния звука		
			// обработка кнопки вкл/выкл звук
			if (isset($_POST['mute'])) {
				if ($_POST['mute'] == 'off') { DB::load_save_mute_all ($id, $chat_author_id, $groupp, TRUE, FALSE);} 		//запрещаем звук, сохраняем
				elseif ($_POST['mute'] == 'on')	{ DB::load_save_mute_all ($id, $chat_author_id, $groupp, TRUE, TRUE);}		//разрешаем звук, сохраняем
			} 
			// считываем состояние звука для текущего пользователя 
			$data['mute'] = DB::load_save_mute_all ($id, $chat_author_id, $groupp, FALSE);

		};//if (isset($_SESSION['id']....
		
		$this->view->generate('chatall_view.php', 'template_view.php', $data);		// генерация изображения
	}
}


