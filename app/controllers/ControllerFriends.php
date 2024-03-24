<?php
namespace App\controllers;
use App\core\Controller;
use App\core\View;
use App\models\DB;

// контроллер страницы собеседников (выбранных как друзья)
class ControllerFriends extends Controller {

	public function __construct()	{				
	 	$this->view = new View();						// инициализация объекта представления
	}

	function action_index()	{	
   		$data = [								
			'auth' => false,							// авторизация
			'main_text' => 'Собеседники'
		];
   
		if (isset($_SESSION['id'])) { 	
			$data['auth'] = true;
            $id = $_SESSION['id'];
            // обработка кнопки удаления друга    метод  delete_friend ($author_id, $friend_id)
            if (isset($_GET['friend_id_delete'])) {
                DB::delete_friend ($id, $_GET['friend_id_delete']);
                unset ($_GET['friend_id_delete']);
            }

            $data['author_avatar_path']=DB::get_user_by_id($id)['picture'];

            //имя пользователя
			$data['login']=(DB::get_user_by_id($id)['nick']) .' '.  (DB::get_user_by_id($id)['login']);
            
            $friends_non_sort = DB::load_friends ($id);  // получаем массив всех друзей (id=friend_one и id=friend_two) несортированный
            
            $id_list = array();                         // извлекаем только id из друзей
			foreach ($friends_non_sort  as $item)   {
                if ($item['friend_one_id'] == $id) {  array_push($id_list, $item['friend_two_id']);}
                else array_push($id_list, $item['friend_one_id']);
            }

            // получаем массив друзей
            $friends = array();                         
            foreach ($id_list as $item)        {    
                $temp = DB::get_user_by_id($item);
                array_push($friends, $temp);
            }

            $data['friends'] = $friends;         

		}   // if (isset($_SESSION['id']))

		$this->view->generate('friends_view.php', 'template_view.php', $data);		// генерация изображения
	}
}


