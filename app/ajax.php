<?php
    namespace App;
    use App\models\DB;

    // при AJAX-запросе компилятор теряет все классы, поэтому надо их загружать по новому
    // загружаем все классы:
    require_once dirname(__DIR__, 1) . DIRECTORY_SEPARATOR. 'vendor' . DIRECTORY_SEPARATOR. 'autoload.php';
    require_once dirname(__DIR__, 1) . DIRECTORY_SEPARATOR.'config.php';
    require_once 'models' . DIRECTORY_SEPARATOR .'DB.php';

    if (isset($_POST['act'])) { 
        header("Content-Type: application/json; charset=utf-8");
        switch ($_POST['act']) {
            case "find" :
                // поиск пользователя по findnick, исключая id автора ($id, $findnick)              
                $res['finds'] = DB::find_users ($_POST['id'], $_POST['val']);                
                echo json_encode($res);
                break;
            case "load" : 
                // загрузка сообщения где last - номер последнего сообщения, id автора ($last, $friend_id, $id)              
                $arr= DB::load_message ($_POST['last'], $_POST['friend_id'], $_POST['id']);
                echo json_encode($arr);
                break;
            case "send" : 
                // сохранение сообщения в БД  ($text, $friend_id, $author_id);             
                $status = DB::save_message ($_POST['text'], $_POST['friend_id'], $_POST['id']);         
                if ($status==TRUE)  $response['status'] = 'ok';
                    else $response['status'] = 'error';
                echo json_encode($response);
                break;      
            case "edit" : 
                // замена сообщения в БД  ($text, $message_id);             
                $status = DB::edit_message ($_POST['text'], $_POST['message_id']);         
                if ($status==TRUE)  $response['status'] = 'ok';
                else $response['status'] = 'error';
                echo json_encode($response);
                break;  
            case "loadall" :
            // загрузка сообщения где last - номер последнего сообщения, id автора, groupp - номер группы,
            // groupp - номер группы, chat_author_id - автор текущего чата ($last, $groupp, $chat_author_id) 
            $arr= DB::load_message_all ($_POST['last_id'], $_POST['groupp'], $_POST['chat_author_id'], $_POST['id']);
            echo json_encode($arr);
            break;
            case "sendall" :
                // сохранение сообщения в БД save_message_all ($text, $chat_author_id, $mes_author_id , $groupp);             
                $status = DB::save_message_all ($_POST['text'], $_POST['chat_author_id'], $_POST['mes_author_id'], $_POST['groupp']);         
                if ($status==TRUE)  $response['status'] = 'ok';
                    else $response['status'] = 'error';
                echo json_encode($response);
                break; 
            default :
            exit();
        }
    }








