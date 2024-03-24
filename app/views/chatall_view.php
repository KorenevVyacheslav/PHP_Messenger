<link type="text/css" rel="stylesheet" href="/css/mystyle.css"/> 
<?php if($data['auth'] == false): header("Location:". DIRECTORY_SEPARATOR. "input");  endif; ?>
    
<body>
    <header>
        <nav id="nav" class="teal darken-1">
            <div class="container">
                <span class="brand-logo" style="font-size:20px;">
                    <?php $str = htmlspecialchars($data['main_text']);                       //'имя собеседника'       
                        if (mb_strlen($str) > 40) { $str = substr($str, 0, 40) . "&#8230;"; }
                        echo $str; ?>
                </span>
                <ul class="right hide-on-med-and-down">
                    <li>
                        <a href='/logout'>
                            <img class="circle" style="height:35px;padding-top:15px;" src="/<?php echo $data['author_avatar_path'] ?>">
                                <?php   $str = htmlspecialchars($data['login']);       
                                if (mb_strlen($str) > 15) { $str = substr($str, 0, 15) . "&#8230;";  }
                                ?>
                            &ensp;<?php echo $str ?> &ensp;|&ensp;Выйти                     <!--// имя пользователя -->
                        </a>
                    </li>
                </ul>
                <ul id="slide-out" class="side-nav fixed">
                    <li id="li-friends" class="delete_default_padding">
                        <a href="/friends">
                            <div class="row valign-wrapper">
                                <div class="col s2"><img class="valign" src="/ico/ico_chat.svg"></div>
                                <div class="col s10">Чаты</div>
                            </div>
                        </a>
                    </li>
                    <li id="li-im-all" class="delete_default_padding">
                        <a href="/friendsall">
                            <div class="row valign-wrapper">
                                <div class="col s2"><img class="valign" src="/ico/ico_chat_all.svg"></div>
                                <div class="col s10">Общий чат</div>
                            </div>
                        </a>
                    </li>
                    <li id="li-find-contacts" class="delete_default_padding">
                        <a href="/find">
                            <div class="row valign-wrapper">
                                <div class="col s2"><img class="valign" src="/ico/ico_search.svg"></div>
                                <div class="col s10">Поиск собеседников</div>
                            </div>
                        </a>
                    </li>
                    <li id="li-profile" class="delete_default_padding">
                        <a href="/profile">
                            <div class="row valign-wrapper">
                                <div class="col s2"><img class="valign" src="/ico/ico_avatar.svg"></div>
                                <div class="col s10">Мой профиль</div>
                            </div>
                        </a>
                    </li>
                    <li class="delete_default_padding">
                        <a href='/logout'>
                            <div class="row valign-wrapper">
                                <div class="col s2"><img class="valign" src="/ico/ico_quit.svg"></div>
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
                                        <div class="col s2"><img class="valign" src="/ico/mute_off.svg"></div>
                                        <div class="col s10">Выключить звук</div>
                                        <input id="form_input" type="hidden" name="mute" value = "off">
                                    </div>
                                </a>
                            </form>
                        <?php else: ?> 
                            <form action="" method="post" id="post_form">
                                <a href=javascript:{} onclick="javascript:post_method();">
                                <div class="row valign-wrapper">
                                    <div class="col s2"><img class="valign" src="/ico/mute_on.svg"></div>
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
            <div id="loader" class="col s12 center valign-wrapper">         <!-- кружочек отправки   -->  
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
        <div id="input_row" class="row teal lighten-4">
            <form id="chat_form" class="col s12">
                <div class="row">
                    <div class="col s1">
                        <pre></pre>
                        <!-- Отступ -->
                    </div>
                    <div id="textarea_field" class="input-field col s9">
                        <i class="material-icons prefix">&#xE254;</i>
                        <textarea id="textarea" class="materialize-textarea"></textarea>
                    </div>
                    <div id="button_container" class="col s2 center-align">
                        <!-- Кнопка -->
                        <a id="send_button" class="btn-floating btn waves-effect waves-light teal lighten-1">
                            <i class="material-icons">&#xE163;</i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </main>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="/js/chat.js"></script>
<script type="text/javascript">

    // функция отправки метода POST для управления звуком
    function post_method()  {
            document.getElementById('post_form').submit()
        };

        var last_message_id = 0,                                // номер последнего сообщения, что получил пользователь
        author_avatar_path = "<?php echo $data['author_avatar_path'] ?>",
        author_id = "<?php echo $_SESSION['id'] ?>",
        first_load = false,                                     // чтобы звук отработал один раз на несколько новых сообщений
        sound_enable = "<?php echo $data['mute'] ?>",           // разрешение звука через кнопку на странице
        $sendButton = $("#send_button"),
        audio = new Audio('/sms.mp3');

        $(document).ready(function () {                         //запускаем, когда документ загрузится
        var renderMessages = function (messages) {
            var html = '';         
            messages.forEach(function (message) {
                var viewClass; 
                if (parseInt(message.MessageAuthorId) == author_id) { viewClass = "cv-host"; } 
                else {  viewClass = "cv-friend";  }
                html += '<div class="chat-view ' + viewClass + '">';
                html += '<div class="cv-avatar"><img class="circle" src="' + '/'+ message.UserPic + '"></div>';                      
                html += '<div class="cv-text">' + escapeHtml(message.MessageText) + '<br><span class="date"><strong>' + escapeHtml(message.UserName) + 
				'</strong>&nbsp;@&nbsp;' + formatUnixTimestamp(message.UnixDate) + '</span></div>'; 
                html += '</div>';       //chat-view 
                //  звуковое уведомление
                // если первая загрузка нового сообщения
                if((first_load == false) && (parseInt(message.MessageAuthorId) != author_id)) {    
                    // если у сообщения статус нового и звук разрешен на кнопке звука 
                    if ((message.Status == 1) && (sound_enable == true))    {
                        audio.play();
                        first_load = true;      // если новых сообщений больше 1, то звук больше не отработает
                    }            
                }
            });
            $loader.addClass("hide");
            $chat.append(html);                 // добавляет html к chat 
            first_load = true;                  // если новых сообщений больше 1, то звук больше не отработает
        };

        // получение сообщений через AJAX
        var Load = function () {
            var id='<?php echo $_SESSION['id'];?>',
            groupp = '<?php echo $data['groupp']; ?>', 
            chat_author_id = '<?php echo $data['chat_author_id']; ?>';                         
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: '/app/ajax.php',
                data: {
                    act: "loadall",
                    last_id: last_message_id,
                    groupp: groupp,
                    chat_author_id: chat_author_id,
                    id: id
                },
                success: function (data) {
                    last_message_id = data.last_message_id;
                    renderMessages(data.messages);
                    if (data.messages.length > 0)  scrollToBottom();
                },
                complete: function () { setTimeout(Load, 1000);  }
            });
        };

        // отправка сообщений через AJAX
        var Send = function () {
            var text = $textarea.val(),
            groupp ='<?php echo $data['groupp'];?>',
            chat_author_id ='<?php echo $data['chat_author_id'] ;?>',
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
                        act: "sendall",
                        chat_author_id: chat_author_id,
                        groupp: groupp,
                        text: text,
                        mes_author_id: id
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

        $sendButton.click(Send);
        $chatForm.submit(function (e) {
            e.preventDefault();
            Send();
        });
        $textarea.focus();
        Load();
    });

</script>
</body>

