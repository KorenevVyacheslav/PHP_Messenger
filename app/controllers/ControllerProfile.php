<?php
namespace App\controllers;
use App\core\Controller;
use App\core\View;
use App\models\DB;
use App\core\CheckPicture;

//контроллер страницы редактирования профиля
class ControllerProfile extends Controller	{

	public function __construct()	 {	
	  	$this->view = new View();	
	}

	function action_index()	{	
		$data = [
            'auth' => false,						// авторизация
            'main_text' => 'Мой профиль',
			'errors' => []							// массив ошибок
		];

      	if (isset($_SESSION['id'])) { 						 

			$data['auth'] = true;
      
			// обработка кнопок	
			if (isset($_POST['action']))	{

				// смена аватара
				if (!empty($_FILES['avatar']['name'])) {                   
					$check = CheckPicture::check_picture($data['login']); 		// метод проверки изображения
					if ($check['register'] == false) {							// изображение с ошибками
						$data['errors'] [] = $check['error'];}
					else { DB::save_picture ($_SESSION['id'], $check['filePath']);
					}		
				}
				// запись nick
				if (!empty($_POST['login'])) {	
					$nick = $_POST['login'];
					$rec = true;
					if (mb_strlen($nick) > 10)	{
						$data['errors'] [] = "Никнейм должен содержать не более 10 символов";	// проверка максимальной длины ника
						$rec=false;
					}

					//$pattern_name = '/^[а-яА-ЯЁёa-zA-Z0-9(\W)][^\s]+$/u';
					$pattern_name = '/^[^\s]+$/u';
					if (!preg_match($pattern_name, $nick))	{
						$data['errors'] [] = "Никнейм не должен содержать пробела";
						$rec=false;
					}
					if ($rec)	{ DB::save_nick ($_SESSION['id'], $nick);}
				}
			};

			// удаление аватара
			if (isset($_POST['delete']))	{  
				$picture = IMAGES . 'default.jpg';
				DB::save_picture ($_SESSION['id'], $picture);
			}

			// установка чекбокса скрытия e-mail
			if (isset($_POST['hide_email']))	{  
				$get_nick = DB::get_user_by_id($_SESSION['id'])['nick'];
				// нельзя скрыть e-mail, если не задан nick
				if (!($get_nick == ' '))	{
					DB::inverse_email_status($_SESSION['id']);
				}
				else $data['errors'] [] = "У вас нет никнейма";
			}
		}

		$data['author_avatar_path']=DB::get_user_by_id($_SESSION['id'])['picture'];
		//имя пользователя
		$data['login']=(DB::get_user_by_id($_SESSION['id'])['nick']) .' '.  (DB::get_user_by_id($_SESSION['id'])['login']);
		$data['nick'] =DB::get_user_by_id($_SESSION['id'])['nick'];
		$data['hide_email']=DB::get_user_by_id($_SESSION['id'])['hide_email'];

	$this->view->generate('profile_view.php', 'template_view.php', $data);	
	}
}