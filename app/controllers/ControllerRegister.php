<?php
namespace App\controllers;
use App\core\Controller;
use App\core\View;
use App\models\DB;
use App\core\CheckPicture;

//контроллер страницы регистрации
class ControllerRegister extends Controller	{
	function __construct()	{
		$this->view = new View();
	}
	
	function action_index()	{
		$data = [
			'errors' => [],							// массив ошибок
			'messages'=>[],							// массив сообщений
			'auth' => false,						// авторизация
		];

		// обработка кнопки регистрации
		if (isset($_POST['action']) && isset ($_POST['login']) && isset ($_POST['password']))	{ 			
	
			$reg=true; 															// инициализация регистрации

			// проверка длины логина не более 40 символов (дублирует ограничение на уровне html)
			if (mb_strlen($_POST['login']) > 40)	{
				$data['errors'] [] = "Логин должен быть не больше 40 символов";	
				$reg=false;	
			} 

			// проверка правильности e-mail 
			if (!filter_var($_POST['login'], FILTER_VALIDATE_EMAIL))	{
				$data['errors'] [] = "Логин содержит недопустимые для e-mail символы, проверьте и введите ещё раз";	
				$reg=false;	
			} 	
			
			if (mb_strlen($_POST['password']) < 4)	{
				$data['errors'] [] = "Пароль должен быть не менее 4 символов";	// проверка минимальной длины пароля
				$reg=false;	
			} 

			if (mb_strlen($_POST['password']) > 15)	{
				$data['errors'] [] = "Пароль должен быть не более 15 символов";	// проверка максимальной длины пароля
				$reg=false;	
			}

			// проверка пароля: кирилица, латиница, цифры, спецсимволы
			//$pattern_name = '/^[а-яА-ЯЁёa-zA-Z0-9(\W)][^\s]{3,13}+$/u';
			$pattern_name = '/^[а-яА-ЯЁёa-zA-Z0-9(\W)][^\s]+$/u';
			if (!preg_match($pattern_name, $_POST['password']))	{
				$data['errors'] [] = "Пароль должен состоять только из букв английского/русского алфавита, цифр и спецсимволов non-word (кроме пробела)."; 
				$reg=false;	
			}

			// проверка совпадения паролей 
			if (!($_POST['password'] == $_POST['password_repeat']))	{
				$data['errors'] [] = "Пароли не совпадают";	
				$reg=false;	
			} 

			// проверка на существующего пользователя
			 $userarray=DB::get_user_bylogin($_POST['login']);
			if ($userarray) { 
				$data['errors'] [] = "Такой пользователь уже существует"; 
				$reg=false;
			}

			$avatar_exist = false;
			// проверка изображения
			if (empty($data['errors']) && !empty($_FILES['avatar']['name'])) { 				                    
				$check = CheckPicture::check_picture($_POST['login']); 
				if ($check['register'] == false) {
					$data['errors'] [] = $check['error']; 
					$reg=false;}
				else { $filePath = $check['filePath']; $avatar_exist = true;}		
			}

			// запись пользователя в БД
			if ($reg==true) {
				$login = $_POST['login'];										   
				$sault = parent::generateCode(10);									// генерация случайной 10-значной строки
				$password = md5(md5(trim($_POST['password']), $sault));				// формирование пароля с солью
				$picture = IMAGES . 'default.jpg';

				if ($avatar_exist) $picture = $filePath ; 

				DB::save_user($login, $password, $sault, 'notvk', $picture);		// запись пользователя в БД
				$user=DB::get_id ($_POST['login']);									// проверяем, что пользователь записан
				if ($user) {
					$data['auth']=true;
					$_SESSION['id'] = $user;
				}	else $data['errors'] [] = "Во время записи пользователя в БД произошла ошибка";
			}
		} 	//	if (isset($_POST['action'])...

		$this->view->generate('register_view.php', 'template_view.php', $data);
	}
}

