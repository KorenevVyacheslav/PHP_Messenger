<link type="text/css" rel="stylesheet" href="/css/mystyle.css"> 
<?php if($data['auth'] == false): header("Location: /input"); endif; ?>
    
<body>
    <header>
        <nav id="nav" class="teal darken-1">
            <div class="container">
                <span class="brand-logo" style="font-size:20px;">
                    <?php  $str = htmlspecialchars($data['main_text']); 
                        if (mb_strlen($str) > 40) {  $str = substr($str, 0, 40) . "&#8230;";   }
                        echo $str; ?>
                </span>
                <ul class="right hide-on-med-and-down">
                    <li>
                        <a href='/logout'>
                            <img class="circle" style="height:35px;padding-top:15px;" src="/<?php echo $data['author_avatar_path'] ?>">
                                <?php  $str = htmlspecialchars($data['login']);       
                                    if (mb_strlen($str) > 15) { $str = substr($str, 0, 15) . "&#8230;"; }
                                ?>
                            &ensp;<?php echo $str ?> &ensp;|&ensp;Выйти                            <!-- имя пользователя -->
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
                                <div class="col s10">Общие чаты</div>
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
                        <a href="/logout">
                            <div class="row valign-wrapper">
                                <div class="col s2"><img class="valign" src="/ico/ico_quit.svg"></div>
                                <div class="col s10">Выйти из системы</div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <main>
        <div id="friend_list" class="row teal lighten-5">
            <div class="col s12">
           
                <!-- Выводим список друзей -->
                <?php foreach ($data['friends'] as $friend) {
                                        //должно быть "/chat/index/?friend_id=, но тогда браузер теряет стили при переходе на chat_view
                    echo '
                        <a class="friend" href="/?friend_id=' . $friend['id'] . '">           
                            <table>
                                <tr>
                                    <td class="friend-image"><img class="circle" src="/' . $friend['picture'] . '"></td>

                                
                                    <td class="friend-name">' . htmlspecialchars($friend['login']) . '</td>
                                    <td class="friend-name">' . htmlspecialchars($friend['nick']) . '</td>
                                    <td class="friend-delete"><a class="button_delete hide-on-large-only" href="/friends/index/?friend_id_delete=' . $friend['id'] . '">
                                    <i class="material-icons krest">&#xE14c;</i></a></td>
                                </tr>
                            </table>
                        </a><div class="clearfix"></div>';
                } ?>
            </div>
        </div>
    </main>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</body>

