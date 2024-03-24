<?php
namespace App\models;
use \RedBeanPHP\R as R;					

if(!R::testConnection()){
	R::setup('mysql:host='. HOST.';dbname='.DB_NAME, USER, PASSWORD);
}

// класс - обертка для работы с БД
class DB {
	// метод загрузки списка друзей
	public static function load_friends ($author_id)	{
		$friends = R::getAssocRow('SELECT * FROM friend WHERE friend_one_id = ? OR friend_two_id = ?', 
		[$author_id, $author_id]);
		return ($friends);
	}

	// метод чтения или записи в БД переменной mute, отвечающей за звук у автора (author_id) по отношению к собеседнику ($friend_id)
	// rec = false/true чтение/запись
	public static function load_save_mute ($author_id, $friend_id, $rec, $mutes=null)	{
		$string = R::findOne('friend', '(friend_one_id = ? AND friend_two_id = ?) OR (friend_two_id = ? AND friend_one_id = ?)', 
		[$author_id, $friend_id, $author_id, $friend_id]);
		$id = $string->id;

		if (($string->friend_one_id) == $author_id) { $mute = $string->mute_one;}
			else {$mute = $string->mute_two;}

		if ($rec == false)	{						// без записи, только чтение mute
		return ($mute);
		} else {									// запись нового значения mute
			$string = R::load('friend', $id);
			if (($string->friend_one_id) == $author_id) { $string->mute_one=$mutes;}	// запись состояния звука у $author_id по отношению к $friend_id 
			else {$string->mute_two=$mutes;}											// запись состояния звука у $friend_id по отношению к $author_id 
			R::store($string);
		}
	}

	// метод проверяет наличие собеседника перед его сохранением 
	public static function check_friends ($author_id, $friend_id)	{
		$friends = R::getAssocRow('SELECT * FROM friend WHERE (friend_one_id = ? AND friend_two_id = ?) 
		OR (friend_two_id = ? AND friend_one_id = ?)', [$author_id, $friend_id, $author_id, $friend_id]);
		return ($friends);
	}

	// метод получает все данные пользователя по id
	public static function get_user_by_id ($id)	{	 
		$user = R::findOne('user', 'id = ?', [$id]);
		// если указана несуществующая таблица, ошибки не возникнет, просто вернет, что нет такого пользователя
		if (is_null ($user)) return false;
		else {		
			$userarray ['picture'] = $user->picture;
			$userarray ['id'] = $user->id;
			if ($user->nick)	{	$userarray ['nick'] = $user->nick;}
				else $userarray ['nick'] = ' ';
			// если логин запрещен к просмотру берем nick, иначе берем login
			// если ник не задан, то запретить логин к просмотру нельзя
			if (($user->hide_email) == TRUE) {	$userarray ['login'] = ' ';}
			else {	$userarray ['login'] = $user->login;}
		$userarray ['hide_email'] = $user->hide_email;
		return $userarray;
		}
	}

	// метод удаляет собеседника при нажатии крестика
	public static function delete_friend ($author_id, $friend_id)	{	 
		$user = R::hunt('friend', '(friend_one_id = ? AND friend_two_id = ?) OR (friend_two_id = ? AND friend_one_id = ?)', 
		[$author_id, $friend_id, $author_id, $friend_id ]);
		}
	
		// метод поиска
		// условие поиска: login или nick содержит $findnick, email не скрыт(hide_email = FALSE) и id это не id автора, 
	public static function find_users ($id, $findnick)	{
		$findnick = '%'.$findnick.'%';
		$users = R::getAssocRow('SELECT id, nick, login, picture FROM user WHERE 
		(((login LIKE ?) AND (hide_email = FALSE)) OR nick LIKE ?) AND id !=?', [$findnick, $findnick, $id]);

		// проводим поиск друзей
		$friends = R::getAssocRow('SELECT friend_one_id AS id FROM friend WHERE 
		friend_two_id = ? UNION SELECT friend_two_id FROM friend WHERE friend_one_id = ?', [$id, $id]);

		// исключаем из результирующего массива друзей
		if ($friends) {		
			foreach ($friends as $friend) {
				foreach ($users as $index=>$value)	{	
						if ($friend['id'] == $value['id']) {	unset ($users[$index]);
						}
				}
			}
		}
		$users = array_values($users);								// перенумерация массива с 0 
		foreach ($users as &$value)	{								// замена null в nick на ' ',
				if ($value['nick'] == null) {$value['nick'] = ' ';}
		}	

		$i=0;$users_return =[];

		while($i< count($users)) {
			$user[$i]=array (
				'0' =>  $users[$i]['id'],
				'id' => $users[$i]['id'],
				'1' => $users[$i]['nick'],
				'nick' => $users[$i]['nick'],
				'2' => $users[$i]['picture'],
				'picture' => $users[$i]['picture'],
				'3' => $users[$i]['login'],
				'login' => $users[$i]['login']
			);
			array_push ($users_return, $user[$i]);
			$i++; 
		}
		return ($users_return);		
	}	

	public static function get_id ($login)	{					// получение аватара по id
		$user = R::findOne('user', 'login = ?', [$login]);
		$id = $user->id;
		return $id;
	}

	public static function save_picture($id, $filepath)	{		// сохранение аватара				
		$user = R::load('user', $id);
		$user->picture=$filepath;
		R::store($user);
	}

	public static function save_nick($id, $nick)	{			// сохранение nick
		$user = R::load('user', $id);
		$user->nick=$nick;
		R::store($user);
	}

	public static function inverse_email_status($id)	{		// инверсия e-mail скрыт/не скрыт			
		$user = R::load('user', $id);
		$user->hide_email=(!$user->hide_email);
		R::store($user);
	}

	// метод вставляет в таблицу 'user' строку с логином, паролью, солью, ролью, аватаром и ником
	public static function save_user( $login, $password, $sault, $role, $picture, $nick=null)	{	
		$create = R::dispense('user');          // создание таблицы 
		$create->login = $login;
		$create->password = $password;
		$create->sault= $sault;
		$create->role = $role;
		$create->picture = $picture;
		$create->nick = $nick;
		$create->hide_email = false;
		//Записываем объект в БД
		R::store($create);
	}

	// метод вставляет в таблицу 'friends' строку с id автора и id собеседника
	public static function save_friend( $author_id, $friend_id)	{	
		//может вставлять повторяющиеся строки
		// исключаем это через find_users
		$author = R::load('user', $author_id);
		$friend = R::load('user', $friend_id);
		$friend_bean = R::dispense( 'friend' );	
		$friend_bean->friendOne=$author;
		$friend_bean->friendTwo=$friend;
		$friend_bean->mute_one=true;				// состояние звука у $author_id по отношению к $friend_id
		$friend_bean->mute_two=true;				// состояние звука у $friend_id по отношению к $author_id
		R::store( $friend_bean);
		}

	//метод получает из таблицы user запись о пользователе и возвращает в виде массива
	public static function get_user_bylogin ($findUserName)	{	 
		$user = R::findOne('user', 'login = ?', [$findUserName]);
		// если указана несуществующая таблица, ошибки не возникнет, просто вернет нет такого пользователя
		if (is_null ($user)) return false;
		else {		
			$userarray ['password'] = $user->password;
			$userarray ['sault'] = $user->sault;
			$userarray ['id'] = $user->id;
			return $userarray;
		}
	}

	public static function load_message ($last_id, $friend_id, $author_id)	{	
		// сначала проверяем существование таблицы
		$isset = R::getAssocRow('SELECT * FROM messages');
		$messages = [];
		if (!$isset) {$last_id = 0 ;} else {
			$mes= R::getAssocRow('SELECT id, author_id, text, date, status_new FROM messages WHERE 
			((author_id =? AND recepient_id = ?) OR (recepient_id =? AND author_id = ?)) AND id > ?', [$author_id, $friend_id, $author_id, $friend_id, $last_id]);

			$i=0;
			while($i< count($mes)) {
				$temp[$i]=array (
					'0' =>  $mes[$i]['id'],
					'MessageId' => $mes[$i]['id'],
					'1' => $mes[$i]['author_id'],
					'MessageAuthorId' => $mes[$i]['author_id'],
					'2' => $mes[$i]['text'],
					'MessageText' => $mes[$i]['text'],
					'3' => \DateTime::createFromFormat('Y-m-d H:i:s', $mes[$i]['date'])->getTimestamp(),
					'UnixDate' => \DateTime::createFromFormat('Y-m-d H:i:s', $mes[$i]['date'])->getTimestamp(),
					'4' => $mes[$i]['status_new'],
					'Status' => $mes[$i]['status_new'],
				);
				array_push ($messages, $temp[$i]);
				// если один раз отправили сообщение пользователю c friend_id, то меняем его статус status_new с true на false
				// это нужно для звука на новых сообщениях
				if ($mes[$i]['author_id'] == $friend_id)	{	
						if ($mes[$i]['status_new'] == TRUE) {
						// если сообщение загружается из БД в первый раз, то меняется его статус (для звука нового сообщения)
						self::change_status($mes[$i]['id']);
					}
				}
				$last_id = $mes[$i]['id'];
				$i++; 
			}
		}

		$res['last_message_id'] = $last_id;
		$res['messages'] = $messages;
		return $res;
	}

	// замена статуса сообщения (для звука нового сообщения)
	public static function change_status ($message_id)	{
		$message= R::load('messages', $message_id);
		$message->status_new=FALSE;
		R::store($message);
	}

	// метод сохранения сообщения
	public static function save_message ($text, $friend_id, $author_id)	{
		$currentTime = new \DateTime();

		$author = R::load('user', $author_id);
		$friend = R::load('user', $friend_id);
		$create = R::dispense('messages');          // создание таблицы 
		$create->author = $author;
		$create->recepient= $friend;
		$create->text= $text;
		$create->date = $currentTime;
		$create->status_new = TRUE;					// флаг нового сообщения
		//Записываем объект в БД
		R::store($create);
		return TRUE;
	}

	// метод для выпадающего меню удаления сообщения
	public static function delete_message ($message_id)	{
		$message = R::hunt('messages', 'id = ?', [$message_id]);
	}

	// метод для выпадающего меню редактирования сообщения
	public static function edit_message ($text, $message_id)	{
		$message= R::load('messages', $message_id);
		$message->text=$text;
		R::store($message);
		return TRUE;
	}

	// метод для выпадающего меню переслать. Считываем текст сообщения
	public static function get_message_by_id ($message_id)	{	// 
		$message = R::findOne('messages', 'id = ?', [$message_id]);
		return $message->text ;
	}

	// метод для сохранения группового чата $author_id -автор чата, $group - группа автора чата, $friend_id - собеседники чата
	public static function save_friendsall( $author_id, $friend_id, $group)	{	
		//может вставлять повторяющиеся строки  исключаем это логикой в ControllerFriendsall
		$author = R::load('user', $author_id);
		$friend = R::load('user', $friend_id);
		$friendsall_bean = R::dispense( 'friendsall' );	
		$friendsall_bean->author=$author;
		$friendsall_bean->friend=$friend;
		$friendsall_bean->groupp=$group;
		$friendsall_bean->mute_author=true;		// состояние звука у $author_id в данной группе
		$friendsall_bean->mute_friend=true;		// состояние звука у $friend_id в данной группе

		 R::store( $friendsall_bean);
	}

	// метод возвращает количество созданных групп пользователя и подгруппы, в которые включён пользователь 
	public static function load_group_by_id ($author_id)	{	
		// сначала проверяем существование таблицы
		$isset = R::getAssocRow('SELECT * FROM friendsall');
		if (!$isset) return false;			// таблицы ещё не существует
		else {
			$return_arr = [
			 	'group2' =>  false,
				'group1' =>  false
			];
			$group = R::findOne('friendsall', 'author_id = ? AND groupp = 2', [$author_id]);
			if (isset ($group->groupp)) { $return_arr['group2']=true;  }
			$group = R::findOne('friendsall', 'author_id = ? AND groupp = 1', [$author_id]);
			if (isset ($group->groupp)) { $return_arr['group1']=true;}
			$subgroup = R::getAssocRow('SELECT groupp, author_id FROM friendsall WHERE friend_id = ?', [$author_id]);
			$return_arr['subgroup'] = $subgroup;
			return $return_arr;		
		}
	}

		// удаление своей группы пользователем
	public static function	delete_group ($author_id, $groupp)	{
		$delete = R::hunt('friendsall', 'author_id = ? AND groupp = ?', [$author_id, $groupp]);
	}	

		// метод считывания сообщений в групповых чатах
        // groupp - номер группы, chat_author_id - автор текущего чата ($last, $groupp, $chat_author_id, $id) 
		// id текущего пользователя
	public static function load_message_all ($last_id, $groupp, $chat_author_id, $id)	{
		// сначала проверяем существование таблицы
		$isset = R::getAssocRow('SELECT * FROM messagesall');
		$messages = [];
		if (!$isset) {$last_id = 0 ;} else {
			$mes= R::getAssocRow('SELECT id, mess_author_id, text, date FROM messagesall WHERE 
			(chat_author_id = ? AND groupp = ?) AND id > ?', [$chat_author_id, $groupp, $last_id]);
			$i=0;	
			$isset_messall_status = R::getAssocRow('SELECT * FROM messagesallstatus');

			while($i< count($mes)) {
				$status_new = TRUE;				// если таблицы ещё нет или сообщение отсутсвует в messagesallstatus, то сообщение новое
												// и еcли это сообщение текущего пользователя (отсекается в JS)
				if ($isset_messall_status) {
					$mes_status = R::findOne('messagesallstatus', 'user_read_id = ? AND mess_id = ?', [$id, $mes[$i]['id']]);
					if ($mes_status) $status_new = FALSE;
				}
				$temp[$i]=array (
					'0' =>  $mes[$i]['id'],
					'MessageId' => $mes[$i]['id'],
					'1' => $mes[$i]['mess_author_id'],
					'MessageAuthorId' => $mes[$i]['mess_author_id'],
					'2' => $mes[$i]['text'],
					'MessageText' => $mes[$i]['text'],
					'3' => \DateTime::createFromFormat('Y-m-d H:i:s', $mes[$i]['date'])->getTimestamp(),
					'UnixDate' => \DateTime::createFromFormat('Y-m-d H:i:s', $mes[$i]['date'])->getTimestamp(),
					'4' => self::get_user_by_id($mes[$i]['mess_author_id'])['picture'],
					'UserPic' => self::get_user_by_id($mes[$i]['mess_author_id'])['picture'],
					'5' => self::get_user_by_id($mes[$i]['mess_author_id'])['login'],
					'UserName' => self::get_user_by_id($mes[$i]['mess_author_id'])['login'],
					'6' => $status_new,
					'Status' => $status_new,
				);
				array_push ($messages, $temp[$i]);
				// если id текущего пользователя не равно автору сообщения, то создание таблицы, в которой сохраняем id пользователя
				// кому уже было выгружено сообщение в чат (для статуса нового сообщения для звука)
				if ($mes[$i]['mess_author_id'] != $id)	{		          																					
					if ($status_new) {
						$mes_id = R::load('messagesall', $mes[$i]['id']);				// получаем id сообщения
						$user_read_id = R::load('user', $id);							// получаем id пользователя кто прочитал сообщение 
						$status_bean = R::dispense( 'messagesallstatus');	
						$status_bean->mess=$mes_id;
						$status_bean->user_read=$user_read_id;
						R::store($status_bean);														
					}		
				}
				$last_id = $mes[$i]['id'];
				$i++; 
			}
		}
		$res['last_message_id'] = $last_id;
		$res['messages'] = $messages;
		return $res;
	}

	// метод сохранения сообщений группового чата
	public static function save_message_all ($text, $chat_author_id, $mes_author_id , $groupp)	{
		$currentTime = new \DateTime();

		$mes_author = R::load('user', $mes_author_id);			// извлекаем автора сообщения
		$chat_author = R::load('user', $chat_author_id);		// извлекаем автора текущего чата
		$create = R::dispense('messagesall');          			// создание таблицы сообщений
		$create->mess_author = $mes_author;						// автор сообщения
		$create->chat_author= $chat_author;						// автор текущего чата, в котором будет это сообщение
		$create->groupp=$groupp;								// группа текущего чата, сохраняем просто как перменную,
																// т.к. создавать дополнительный REFERENCE KEY на friendsall нерационально
		$create->text= $text;									// текст сообщения
		$create->date = $currentTime;
		R::store($create);										//Записываем объект в БД
		return TRUE;
	}

	// метод чтения или записи в БД переменной mute, отвечающей за звук в групповом чате
	// rec = false/true  чтение/запись
	public static function load_save_mute_all ($id, $chat_author_id, $groupp, $rec, $mutes=null)	{
		if ($id==$chat_author_id)	{
			$string = R::findOne('friendsall', 'author_id = ? AND  groupp = ?', [$chat_author_id, $groupp]);
			$mute = $string->mute_author;
		}	else {
			$string = R::findOne('friendsall', 'friend_id = ? AND groupp = ? ', [$id, $groupp]);
			$mute = $string->mute_friend;
		} 
	
		 if ($rec == false)	{											// без записи, только чтение mute
		 	return ($mute);
		 } else {														// запись нового значения mute
			if ($id==$chat_author_id) { $string->mute_author=$mutes;}	// запись состояния звука у $author_id по отношению к $friend_id 
			else {$string->mute_friend=$mutes;}							// запись состояния звука у $friend_id по отношению к $author_id 
			R::store($string);
		}
	}
}





