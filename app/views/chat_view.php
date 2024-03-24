<link type="text/css" rel="stylesheet" href="/css/mystyle.css">
<?php if($data['auth'] == false): header("Location:". DIRECTORY_SEPARATOR. "input"); endif; ?>
<link type="text/css" rel="stylesheet" href="/css/mystyle1.css">
<body>
    <header>
        <nav id="nav" class="teal darken-1">
            <div class="container">
                <span class="brand-logo" style="font-size:20px;">
                    <?php $str = htmlspecialchars($data['main_text']);              //'имя собеседника'
                        if (mb_strlen($str) > 40) {  $str = substr($str, 0, 40) . "&#8230;"; }
                    echo $str; ?>
                </span>
                <ul class="right hide-on-med-and-down">
                    <li>
                        <a href='/logout'>
                            <img class="circle" style="height:35px;padding-top:15px;" src="<?php echo $data['author_avatar_path'] ?>">
                            <?php $str = htmlspecialchars($data['login']);       
                                if (mb_strlen($str) > 15) { $str = substr($str, 0, 15) . "&#8230;";  } ?>
                            &ensp;<?php echo $str ?> &ensp;|&ensp;Выйти                           <!--// имя пользователя -->
                        </a>
                    </li>
                </ul>
                <ul id="slide-out" class="side-nav fixed">
                    <li id="li-friends" class="delete_default_padding">
                        <a href="/friends">
                            <div class="row valign-wrapper">
                                <div class="col s2"><img class="valign" src="ico/ico_chat.svg"></div>
                                <div class="col s10">Чаты</div>
                            </div>
                        </a>
                    </li>
                    <li id="li-im-all" class="delete_default_padding">
                        <a href="/friendsall">
                            <div class="row valign-wrapper">
                                <div class="col s2"><img class="valign" src="ico/ico_chat_all.svg"></div>
                                <div class="col s10">Общие чаты</div>
                            </div>
                        </a>
                    </li>
                    <li id="li-find-contacts" class="delete_default_padding">
                        <a href="/find">
                            <div class="row valign-wrapper">
                                <div class="col s2"><img class="valign" src="ico/ico_search.svg"></div>
                                <div class="col s10">Поиск собеседников</div>
                            </div>
                        </a>
                    </li>
                    <li id="li-profile" class="delete_default_padding">
                        <a href="/profile">
                            <div class="row valign-wrapper">
                                <div class="col s2"><img class="valign" src="ico/ico_avatar.svg"></div>
                                <div class="col s10">Мой профиль</div>
                            </div>
                        </a>
                    </li>
                    <li class="delete_default_padding">
                        <a href='/logout'>
                            <div class="row valign-wrapper">
                                <div class="col s2"><img class="valign" src="ico/ico_quit.svg"></div>
                                <div class="col s10">Выйти из системы</div>
                            </div>
                        </a>
                    </li>
                    <!-- отображение кнопки управления звуком-->
                    <li class="delete_default_padding">
                    <?php if ($data['mute']): ?>              
                        <form action="" method="post" id="post_form">
                            <a href=javascript:{} onclick="javascript:post_method();">
                                <div class="row valign-wrapper">
                                    <div class="col s2"><img class="valign" src="ico/mute_off.svg"></div>
                                    <div class="col s10">Выключить звук</div>
                                    <input id="form_input" type="hidden" name="mute" value = "off">
                                </div>
                            </a>
                        </form>
                        <?php else: ?> 
                            <form action="" method="post" id="post_form">
                                <a href=javascript:{} onclick="javascript:post_method();">
                                    <div class="row valign-wrapper">
                                        <div class="col s2"><img class="valign" src="ico/mute_on.svg"></div>
                                        <div class="col s10">Включить звук</div>
                                        <input id="form_input" type="hidden" name="mute" value = "on">
                                    </div>
                                </a>
                            </form>
                        <?php endif ?> 
                    </li>
                </ul>
            </div>
        </nav>
    </header>
<main>
    <div id="chat_list" class="row teal lighten-5">
        <div id="chat_area" class="col s12">
            <!-- Поле сообщений -->
        </div>
        <div id="loader" class="col s12 center valign-wrapper">                 <!-- кружочек отправки   -->  
            <div class="preloader-wrapper small active valign center-block">
                <div class="spinner-layer spinner-green-only">
                     <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div> 
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--невидимая форма для снятия флага редактирования сообщения- -->
    <form action="" method="post" id="post_form3">
        <input type="hidden" name="edit_message" value = "off">
    </form>
    <!--текст, появляющийся при редактировании сообщения-->                     
    <?php if ($data['edit_message'] == true): ?>
        <p class="col s12"> введите новый текст редактируемого сообщения: </p>
    <?php endif ?>
                    
    <div id="input_row" class="row teal lighten-4">
        <form id="chat_form" class="col s12">
            <div class="row">
                <div class="col s1">
                    <pre></pre>
                </div>
                <div id="textarea_field" class="input-field col s9">
                    <i class="material-icons prefix">&#xE254;</i>
                    <textarea id="textarea" class="materialize-textarea"></textarea>
                </div>
                <div id="button_container" class="col s2 center-align">
                    <!-- Кнопка "отправить" без режима редактирования сообщения-->
                    <?php if ($data['edit_message'] == false): ?>
                        <a id="send_button" class="btn-floating btn waves-effect waves-light teal lighten-1">
                            <i class="material-icons">&#xE163;</i>
                        </a>
                    <?php else: ?>
                        <!-- Кнопка отправить в режиме редактирования --->
                        <a id="edit_button" class="btn-floating btn waves-effect waves-light teal lighten-1">
                            <i class="material-icons">&#xE163;</i>
                        </a>
                    <?php endif ?>
                </div>
            </div>
        </form>
    </div>
</main>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="/js/chat.js"></script>
<script type="text/javascript">

    // функция раскрывающегося списка, если кликнули по сообщению
    function popup (id) {
    document.getElementById(id).classList.toggle("showw");
    }

    // функция управления звуком    
    function post_method()  {
        document.getElementById('post_form').submit();                          // отправляем форму серверу
    };

    // функция устанавливаем флаг редактирования сообщения  
    function post_method2(id)   {
            document.getElementById('post_form_edit' + id + '').submit();      // отправляем форму серверу
    };

    // функция закрытия раскрывающегося списка, если кликнули по любой другой области страницы
    window.onclick = function(e) {          
        // чтобы выпадающее меню само закрывалось  при клике в любом месте страницы кроме своих ссобщений
        if (!e.target.matches('.cv-text')) {                    // срабатывает на любом месте страницы кроме кнопки
            var dropdowns = document.getElementsByClassName("dropdownn-content");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];                         // <div id="drop37" class="dropdownn-content"
                if (openDropdown.classList.contains('showw')) { openDropdown.classList.toggle("showw"); }
            }
        }
    }

    var last_message_id = 0, // номер последнего сообщения, что получил пользователь
    f_id = <?php echo $data['friend_id'] ?>,            // id собеседника
    friend_avatar_path = "<?php echo $data['friend_avatar_path'] ?>",
    author_avatar_path = "<?php echo $data['author_avatar_path'] ?>",
    friend_name = "<?php echo htmlspecialchars($data['main_text']) ?>", // имя собеседника
    first_load = false,                                             // чтобы звук отработал один раз на несколько новых сообщений
    sound_enable = "<?php echo $data['mute'] ?>",                   // разрешение звука через кнопку на странице
    audio = new Audio('sms.mp3'),
    $sendButton = $("#send_button"),
    $editButton = $("#edit_button");

    var friends = new Array();                                      // формируем массив друзей для кнопки переслать
    friends = <?php echo json_encode($data['friends']);?>;  

    $(document).ready(function () {                                 //запускаем, когда документ загрузится
        var renderMessages = function (messages) {
            var html = ''; 
            messages.forEach(function (message) {
                var avatar, viewClass;
                var id = 'drop'+ message.MessageId; 
                var html_edit = '';
                // загрузка в html сообщений собеседника
                if (parseInt(message.MessageAuthorId) == f_id) {
                    viewClass = "cv-friend";
                    avatar = friend_avatar_path;
                    html += '<div class="chat-view ' + viewClass + '">';
                    html += '<div class="cv-avatar"><img class="circle" src="' + avatar + '"></div>';
                    html += '<div class="cv-text">' + escapeHtml(message.MessageText) + '<br><span class="date">' 
                    + formatUnixTimestamp(message.UnixDate) + '</span></div>';
                    html += '</div>';       //chat-view 
                } else {    // загрузка в html сообщений пользователя
                    viewClass = "cv-host";
                    avatar = author_avatar_path;
                    html += '<a onclick="popup(\''+ id +'\')" class="dropbtnn">';       // функция обработки при клике
                    html += '<div class="chat-view ' + viewClass + '">';
                    html += '<div class="cv-avatar"><img class="circle" src="' + avatar + '"></div>';
                    html += '<div class="cv-text">' + escapeHtml(message.MessageText) + '<br><span class="date">' 
                    + formatUnixTimestamp(message.UnixDate) + '</span></div>';

                    html += '<div class="cv-text">';
                    html += '<div id="' + id + '" class="dropdownn-content">';  // присваиваем id для выпадающего меню
                                                                                // чтобы открылось на самом сообщении
                    //обработка пункта меню "Удалить"                                                            
                    html +=  '<a href="/?friend_id='+f_id+'&message_delete=' + message.MessageId + '"> Удалить</a>';

                    // обработка пункта меню "Редактировать" 
                    html += '<form action="" method="post" id="post_form_edit' + message.MessageId + '">';
                    html+= '<input type="hidden" name="edit_message" value = "on">';
                    html+= '<input type="hidden" name="id_message" value ="' + message.MessageId + '">';
                    html += '<a href=javascript:{} onclick="javascript:post_method2(' + message.MessageId + ');">Редактировать</a>';
                    html += '</form>';

                    // обработка пункта меню "Переслать" 
                    html +=  '<div> Переслать: &nbsp</div>';       // добавляем пробел                 
                    html +=  '<ul>';
                        html += '<li>';                         
                            friends.forEach((item) => 
                            html +='<a href="/Chat?friend_id='+ item.id +'&message_recent='+ message.MessageId +'">' 
                            + item.nick +' '+ item.login + '</a>');
                        html += '</li>';                                                                           
                    html += '</ul>';
                    html += '</div>';
                    html += '</div>';  
                    html += '</div>';       //chat-view 
                    html += '</a>';
                }
                //  звуковое уведомление
                // если первая загрузка нового сообщения
                if((first_load == false) && parseInt(message.MessageAuthorId) == f_id) {        
                    // если у сообщения статус нового и звук разрешен на пиктограмме звука 
                    if ((parseInt(message.Status) == 1) && (sound_enable == true))    {
                        audio.play();
                        first_load = true;      // если новых сообщений больше 1, то звук больше не отработает
                    }
                }
            });
            $loader.addClass("hide");
            $chat.append(html);                 // добавляем html к chat 
        };      //renderMessages = function (messages)

        // получение сообщения через AJAX
        var Load = function () {
            var id='<?php echo $_SESSION['id'];?>';
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: '/app/ajax.php',
                data: {
                    act: "load",
                    last: last_message_id,
                    friend_id: f_id,
                    id: id
                },
                success: function (data) {
                    last_message_id = data.last_message_id;
                    renderMessages(data.messages);
                    if (data.messages.length > 0)
                        scrollToBottom();
                },
                complete: function () { setTimeout(Load, 1000); }
            });
        };

        // отправка сообщения через AJAX
        var Send = function () {
            var text = $textarea.val(),
            id='<?php echo $_SESSION['id'];?>';
            text = $.trim(text);
            if (text !== "") {
                $loader.removeClass("hide");
                scrollToBottom();
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: '/app/ajax.php',
                    data: {
                        act: "send",
                        friend_id: f_id,
                        text: text,
                        id: id
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            $textarea.val("");              // очистим поле ввода сообщения
                            $textarea.height(22);
                        } else {
                            console.log("Ошибка сервера при отправке сообщения");
                        }
                    },
                    error: function () {  console.log("Ошибка AJAX-запроса при отправке сообщения");  }
                });
            }
        };

        // отправка редактируемого сообщения через AJAX
        var Edit = function () {
            var text = $textarea.val(),
            message_id='<?php echo (int) $_SESSION ['id_message'];?>';
            text = $.trim(text);
            if (text !== "") {
                $loader.removeClass("hide");
                scrollToBottom();
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: '/app/ajax.php',
                    data: {
                        act: "edit",
                        //friend_id: f_id,
                        text: text,
                        message_id: message_id
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            $textarea.val("");              // очистим поле ввода сообщения
                            $textarea.height(22);
                            // выключаем флаг редактирования сообщения
                            document.getElementById('post_form3').submit();                          
                        } else { console.log("Ошибка сервера при отправке сообщения"); }
                    },
                    error: function () { console.log("Ошибка AJAX-запроса при отправке сообщения");  }
                });
            }
        };

        $sendButton.click(Send);            // нажатие кнопки "Отправить"
        $editButton.click(Edit);            // нажатие кнопки "Отправить" в режиме редактирования

        $chatForm.submit(function (e) {
            e.preventDefault();
            Send();
        });
        $textarea.focus();
        Load();
    });
</script>
</body>
<link type="text/css" rel="stylesheet" href="/css/mystyle.css">