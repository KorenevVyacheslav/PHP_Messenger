<?php
namespace App\controllers;
use App\core\Controller;
use App\core\View;
use App\models\DB;

//  контроллер для чата с одним человеком
class ControllerChat extends Controller {

	public function __construct()	{				
	 	$this->view = new View();						// инициализация объекта представления
	}

	function action_index()	{	
   		$data = [								
			'auth' => false,							// авторизация
			'edit_message' => false						// переменная для редактирования сообщения
		];
		
		$_SESSION ['id_message'] = 1;					// чтобы не было ошибки на методе Edit в части JS (если не нажата кнопка редактирования)

		// заходим из поиска/списка друзей, есть SESSION['id'] = id текущего пользователя, $_GET['friend_id'] 
		if (isset($_SESSION['id']) && isset($_GET['friend_id'])) { 	
			$data['auth'] = true;
			$friend_id = $_GET['friend_id'];
			$id = $_SESSION['id'];
			// сохраняем друга в таблице, если его там нет
			$check_friend = DB::check_friends ($id, $friend_id);
			if (!$check_friend) {DB::save_friend( $id, $friend_id);}

			$data['author_avatar_path']=DB::get_user_by_id($id)['picture'];
			$data['friend_avatar_path']=DB::get_user_by_id($friend_id)['picture'];
			$data['friend_id'] = $friend_id;
			
			//имя пользователя
			$data['login']=(DB::get_user_by_id($id)['nick']) .' '.  (DB::get_user_by_id($id)['login']);
			// имя собеседника
			$data['main_text']=(DB::get_user_by_id($friend_id)['nick']) .' '.  (DB::get_user_by_id($friend_id)['login']);

			// считывание или запись состояния звука для текущего чата load_save_mute_all ($id, $chat_author_id, $groupp, $rec, $mutes)
			// $rec=true/false - запись/чтение состояния звука	
			// обработка кнопки вкл/выкл звук 
			if (isset($_POST['mute'])) {
				if ($_POST['mute'] == 'off') { DB::load_save_mute ($id, $friend_id, TRUE, FALSE);} 		//запрещаем звук и сохраняем
				elseif ($_POST['mute'] == 'on')	{ DB::load_save_mute ($id, $friend_id, TRUE, TRUE);}	//разрешаем звук и сохраняем
			}  
			// считываем состояние звука для текущего пользователя
			$data['mute'] = DB::load_save_mute ($id, $friend_id, FALSE);

			// обработка кнопки выпадающего меню - удалить
			// удаляем сообщение методом GET 
			if (isset($_GET['message_delete'])) { 
				$id_mes = (int)$_GET['message_delete'];
				DB::delete_message ($id_mes);
				unset ($_GET['message_delete']);
			} 

			// редактирование сообщения методом POST выпадающего меню (редактировать)
			// через метод POST получаем $_POST['edit_message'] = on после нажатия редактирования
			//  в $_POST['id_message'] получаем id редактируемого сообщения
			if (isset($_POST['edit_message'])) {
				// через $_SESSION ['id_message'] отправляем в JS номер редактирумого сообщения (функция Edit)
				isset ($_POST['id_message']) ? $_SESSION ['id_message'] = $_POST['id_message'] : $_SESSION ['id_message'] = 1;
				if ($_POST['edit_message'] == 'on') {
					$data['edit_message'] = true;			// для отображения сообщения "Введите новый текст" 	
				}	
				else if ($_POST['edit_message'] == 'off')	{
					$data['edit_message'] = false;
				}
			} 

			// для обработки кнопки "переслать" выпадающего меню
			// формируем список друзей
            $friends_non_sort = DB::load_friends ($id);  	// получаем массив всех друзей (id=friend_one и id=friend_two) несортированный 
			$id_list = array();
			// получаем массив id друзей                         
			foreach ($friends_non_sort as $item)   {													
                if ($item['friend_one_id'] == $id ) {  array_push($id_list, $item['friend_two_id']);}
                else array_push($id_list, $item['friend_one_id']);
            }

			foreach ($id_list as $key => $item)   {							// удаляем id текущего друга, т.к. ему не пересылаем
                if ($item == $friend_id) {  unset ($id_list[$key]);}
            }
			$id_list = array_values($id_list);								//нумерация массива с нуля
            // получаем массив друзей, где не будет текущего собеседника
			$friends = array();                         
            foreach ($id_list as $item)	    {    							
                $temp = DB::get_user_by_id($item);
                array_push($friends, $temp);
            }
            $data['friends'] = $friends;
			
			// обработка кнопки пересылки сообщения методом GET
			if (isset($_GET['message_recent'])) { 
				 $id_mes = (int)$_GET['message_recent'];	
				 $friend_id=(int)$_GET['friend_id'];
				 $text = DB::get_message_by_id ($id_mes) ;		//получаем из БД текст сообщения
				 DB::save_message ($text, $friend_id, $id); 	// сохраняем новое сообщение save_message ($text, $friend_id, $author_id)
			} 
		};	//if (isset($_SESSION['id'])...

		$this->view->generate('chat_view.php', 'template_view.php', $data);		// генерация изображения
	}
}


