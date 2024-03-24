<?php
namespace App\controllers;
use App\core\Controller;
use App\core\View;
use App\core\CSRF;
use App\models\DB;

//контроллер входной страницы
class ControllerInput extends Controller	{

	public function __construct()	{				
	 	$this->view = new View();					// инициализация объекта представления
	}

	function action_index()	{	

		// подготовка первого запроса для получения токена
		$clientId     = '1111111'; 						// ID приложения
		$redirectUri  = 'https://localhost.ru/vk'; 		// Адрес, на который будет переадресован пользователь после прохождения авторизации
		$clientSecret = parent::generateCode(10); 		// Защищённый ключ
	
		// Формируем ссылку для авторизации
		$params = array (
		'client_id'     => $clientId,
		'redirect_uri'  => $redirectUri,
		'response_type' => 'code',
		'v'             => '5.199', 					// (обязательный параметр) версии API https://vk.com/dev/versions
		// Права доступа приложения https://vk.com/dev/permissions
		// Если указать "offline", полученный access_token будет "вечным" (токен сбросится, если пользователь сменит свой пароль или удалит приложение).
		'scope'         => 'photos,offline'
	   );

	   $data = [								
		'errors' => [],							// массив ошибок
		'messages'=>[],							// массив сообщений
		'auth' => false,						// авторизация   
		'token' => true,						// проверка токена CSRF
		'params' => http_build_query($params)	// запрос при нажатии кнопки
	];		
	   

		// блок авторизации через сайт
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// обработка кнопки "Войти"
		if (isset($_POST['action']) && isset ($_POST['login']) && isset ($_POST['password']))	{

			if (! CSRF::validate($_POST['token1']))	{						// проверка токена CSRF в форме авторизации
				$data ['errors'] [] ="Ошибка проверки токена!";
				$data['token'] = false;
			} else {
				$userarray=DB::get_user_bylogin ($_POST['login']);			// токен проверен, загружаем пользователя из БД
				if ($userarray) {
					if ($userarray['password'] == md5(md5(trim($_POST['password']), $userarray['sault'])))	{	// проверяем пароль пользователя
						$_SESSION['id'] = $userarray['id'];
						$data['messages'] [] = "Вы успешно авторизовались в системе!";	
						$data['auth']=true;																			// авторизация
					} else {
				 		$data ['errors'] [] ="Вы ввели неправильный пароль";
					}
				} else {
					$data ['errors'] [] ="Такой пользователь не зарегистрирован";
				}
			}
		}	//isset($_POST['action']

	// блок авторизации через VK
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if (isset($_GET['code']))		{	
			$reg=true; 											// инициализация регистрации
			$redirectUri  = 'https://localhost.ru/vk';			// Адрес, на который будет переадресован пользователь после получения токена  										
			$params = array (
			   'client_id'     => $clientId,
			   'client_secret' => $clientSecret,
			   'code'          => $_GET['code'],
			   'redirect_uri'  => $redirectUri
			);

			// отправляем запрос
		    if (!$content = @file_get_contents('https://oauth.vk.com/access_token?'. http_build_query($params))) {
				$error = error_get_last(); $reg=false;
			 	throw new \Exception('HTTP request failed. Error: ' . $error['message']);
			} 
		
			$response = json_decode($content);				// ответ получен
		
			// Если при получении токена произошла ошибка
		   	if (isset($response->error)) { 
				$reg=false;
		   		throw new \Exception('При получении токена произошла ошибка. Error: ' . $response->error . '. Error description: ' . $response->error_description);
		   	} else {
		  		//выполняем код, если все прошло хорошо
		   		$token = $response->access_token;   		// Токен
				$userId = $response->user_id;				// ID авторизовавшегося пользователя
				$_SESSION['token'] = $token;
				$_SESSION['user_id'] = $userId;
			}
		}

		// токен получен, извлекаем информацию о пользователе
		if (isset($_SESSION['token']))	{ 
			$params = array (
				'user_ids'     => $_SESSION['user_id'],
				'v' 		   => '5.199',
				'access_token' => $_SESSION['token'], 
				'fields' 	   => 'nickname,email,photo_50,has_photo'
	 		);

			// отправляем запрос
	 		if (!$content = @file_get_contents('https://api.vk.com/method/users.get?' . http_build_query($params))) {
				$error = error_get_last(); $reg=false;
				throw new \Exception('HTTP request failed. Error: ' . $error['message']);
			}

			$response = json_decode($content);

			if (isset($response->error)) {
				$reg=false;
				throw new \Exception('При получении данных о пользователе произошла ошибка. Error: ' . $response->error . '. Error description: ' . $response->error_description);
			} else {

				$response = $response->response;
				$nick = $response->nickname;				// извлекаем ник
				$login = $response->email;					// извлекаем логин (e-mail)
				$exist_photo = $response->has_photo;		// установил пользователь фото или нет
				$avatar_exist = false;

				// получаем фото
				if ($exist_photo)	{
					$login_corr = str_replace('@', '_', $login);					// чтобы сохранить имя файла уникальным
					$photo_name = str_replace('.', 'x', $login_corr );				// заменяем "@" на "_" и "." на "x"
					$filePath = IMAGES . $photo_name . ".jpg";						// создаём имя файла
					$url_photo = $response->photo_50;								// получаем url аватара. Для проверки 'https://vk.com/images/camera_50.png';

					file_put_contents($filePath , file_get_contents($url_photo));	// сохраняем файл аватара

					if (file_exists($filePath)) { $avatar_exist = true;}
					else {	$data ['errors'] [] ="Во время загрузки файла произошла ошибка";	$reg=false;}		
				}

				if ($reg)	{
					$userarray=DB::get_user_bylogin($login);
					if ($userarray) { 
						$_SESSION['id'] = $userarray['id'];
						$data['auth']=true;
					} else {
						$sault = 0; $password = 0;	$role = 'vk';						// для сохранения в БД
						if (!$avatar_exist) $filePath = IMAGES . 'default.jpg';	   		// аватар по умолчанию 
						DB::save_user($login, $password, $sault, $role, $filePath, $nick);		// запись пользователя в БД
						$user=DB::get_id ($login);												// проверяем, что пользователь записан
		
						if ($user) { 
							$_SESSION['id'] = $user;
							$data['auth']=true;
						}	else $data ['errors'] [] ="Во время записи пользователя в БД произошла ошибка";
					}
				}	else $data ['errors'] [] ="Во время регистрации через VK произошла ошибка";

		 		unset ($_GET['code']); unset ($_SESSION['token']);								// чтобы запрос не сработал второй раз 		 																
			}	//	if (isset($response->error))
		}	//	if (isset($_SESSION['token']))
			//для проверки
			// $data['auth'] = true;$_SESSION['id'] = 33;
		$this->view->generate('input_view.php', 'template_view.php', $data);		// генерация изображения
	}
}

