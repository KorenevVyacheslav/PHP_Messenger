<?php
namespace App\controllers;
use App\core\Controller;
use App\core\View;
use App\models\DB;

// контроллер страницы групповых чатов 
class ControllerFriendsall extends Controller   {

	public function __construct()	{				
	 	$this->view = new View();						// инициализация объекта представления
	}

	function action_index()	{	
   		$data = [								
			'auth' => false,									// авторизация
			'main_text' => 'Листинг ваших групповых чатов',
            'group1' => false,                                   // группа 1 созданная пользователем
            'group2' => false,                                   // группа 2 созданная пользователем
            'error' => false                                     // ошибка создания группы
		];
   
		if (isset($_SESSION['id'])) { 	
			$data['auth'] = true;
            $id = $_SESSION['id'];

            $data['author_avatar_path']=DB::get_user_by_id($id)['picture'];
            //имя пользователя
			$data['login']=(DB::get_user_by_id($id)['nick']) .' '.  (DB::get_user_by_id($id)['login']);
            
            $friends_non_sort = DB::load_friends ($id);     // получаем несортированный массив всех друзей
            
            $id_list = array();                             // получаем массив id друзей
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

            // обработка кнопки удаления созданной текущим пользователем группы 
            if (isset ($_GET['groupp_delete'])) {
                DB::delete_group ($id, $_GET['groupp_delete']);
           }

            // загружаем количество созданных групп пользователя
            $group= DB::load_group_by_id ($id);
            if ($group['group1']) $data['group1'] = true;
            if ($group['group2']) $data['group2'] = true;

            // получаем группы, в которых состоит пользователь
            if (isset ($group['subgroup'])) {
                $data['subgroup'] = $group['subgroup'];
                // заменяем id пользователя на login или nick для оторажения на экране
                foreach ($data['subgroup'] as $key => $item)     {
                    $nick = DB::get_user_by_id ($item['author_id'])['nick'];            
                    $data['subgroup'][$key]['nick'] = $nick;

                    $login = DB::get_user_by_id ($item['author_id'])['login'];
                    $data['subgroup'][$key]['login'] = $login;
                }
            } else $data['subgroup'] = false;           // текущий пользователь не состоит в чужих группах

            // нажата кнопка старта чата
            if (isset ($_POST['start']))  {
 
                // извлекаем id всех друзей, полученных методом POST со строкой user_include
                $friends = $_POST;
                $friends_id = [];
                foreach ($friends as $key => $item) {
                    if (preg_match('/user_include/', $key)) {array_push ($friends_id, (int) $item);};
                }

                // если выделено меньше двух друзей-участников, то показываем ошибку
                ((count ($friends_id)) <= 1) ? $error=true: $error = false; 
                $data['error'] =  $error;

                //если не две группы и нет ошибки, то начинаем сохранение текущей группы
                if (!($data['group1']&&$data['group2']) && !$error) {
                        //сохраняем в таблице friendsall автора и его собеседников  save_friendsall($author_id, $friend_id, $group)
                        if ((!$data['group1']&&!$data['group2']))   {              // если групп нет сохраняем первую группу
                            foreach ($friends_id as $item) {
                                DB::save_friendsall($id, $item, 1); 
                            }
                            header("Location:". DIRECTORY_SEPARATOR. "chatall/index/?groupp=1&chat_author_id=$id"); die();     // переход в групповой чат
                        }

                        if ($data['group2']){                                           // если есть группа 2, то сохраняем группу 1
                            foreach ($friends_id as $item) {
                                DB::save_friendsall($id, $item, 1);         
                            }                   
                            header("Location:". DIRECTORY_SEPARATOR. "chatall/index/?groupp=1&chat_author_id=$id"); die();	       // переход в групповой чат
                        } else {                                                        // если есть группа 1, то сохраняем группу 2
                            foreach ($friends_id as $item) {
                                DB::save_friendsall($id, $item, 2);            
                            }  
                            header("Location:". DIRECTORY_SEPARATOR. "chatall/index/?groupp=2&chat_author_id=$id"); die();	
                        }
                }
            }       //isset ($_POST['start']
		};  //isset($_SESSION['id'])
		$this->view->generate('friendsall_view.php', 'template_view.php', $data);		// генерация изображения
	}
}


