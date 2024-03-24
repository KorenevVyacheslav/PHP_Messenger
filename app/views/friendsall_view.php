<link type="text/css" rel="stylesheet" href="/css/mystyle.css"> 
<?php if($data['auth'] == false): header("Location:". DIRECTORY_SEPARATOR. "input");  endif; ?>

<body>
    <header>
    <!-- переопределение стилей таблицы для отображения списка друзей (оформлены как таблица)   -->
        <style>
            table {
                border-collapse: collapse;
                width:25%;
                cursor: pointer;
                background-color: rgba(178, 223, 219, 0.9); 
                border-radius: 6px;
            }
            table td.shrink { white-space:nowrap;  }
        </style>
        <nav id="nav" class="teal darken-1">
            <div class="container">
                <span class="brand-logo" style="font-size:20px;">
                    <?php $str = htmlspecialchars($data['main_text']);                      //группа     
                        if (mb_strlen($str) > 40) {  $str = substr($str, 0, 40) . "&#8230;";  }
                        echo $str;?>
                </span>
                <ul class="right hide-on-med-and-down">
                    <li>
                        <a href='/logout'>
                            <img class="circle" style="height:35px;padding-top:15px;" src="/<?php echo $data['author_avatar_path'] ?>">
                            <?php    $str = htmlspecialchars($data['login']);       
                                if (mb_strlen($str) > 15) { $str = substr($str, 0, 15) . "&#8230;";  }
                            ?>
                            &ensp;<?php echo $str ?> &ensp;|&ensp;Выйти                                <!-- имя пользователя -->
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
        <!-- выводим список группповых чатов, в которые включен пользователь -->
        <?php if ($data['error'] == false): ?>          <!-- если нет ошибки выбора одного пользователя при создании группы -->
            <div id="friend_list" class="row teal lighten-5">                   
                <div class="col s12">
                    <form action="" class="col s12" method="post">
                        <?php if ($data['subgroup']) :  ?>
                            <div>
                                <span class="brand-logo" style="font-size:20px;">
                                    <?php echo 'Группы, в которые вы включены:' ?> 
                                </span> 
                            </div>
                        <?php foreach ($data['subgroup'] as $item): ?>
                            <a class="friend" href="/chatall/index/?groupp=<?php echo $item['groupp']?>&chat_author_id=<?php echo $item['author_id']?>">
                                <table>
                                    <tr>
                                        <td class="friend-name"> Группа <?php echo $item['groupp'] ?> пользователя <?php echo $item['nick'] . ' ' . $item['login']  ?> </td>
                                    </tr>
                                </table>
                            </a><div class="clearfix"></div>
                        <?php endforeach; ?> 
                </div>   
                        <?php  endif ?>
            </div> 
            <div> &nbsp </div> <div> &nbsp </div>

            <!-- выводим список созданных пользователем групп--->
            <?php if ($data['group1']): ?>
                <div>
                    <span class="brand-logo" style="font-size:20px;">
                    <?php echo 'Созданные вами групповые чаты (максимум 2):' ?>
                    </span>  
                </div>         
                <a class="friend" href="/chatall/index/?groupp=1&chat_author_id=<?php echo $_SESSION['id']?>">
                    <table>
                        <tr>
                            <td class="friend-name"> Ваша группа 1 </td>
                            <td class="friend-delete"><a class="button_delete hide-on-large-only" href="/friendsall/index/?groupp_delete=1">
                            <i class="material-icons krest">&#xE14c;</i></a></td>
                        </tr>
                    </table>
                </a><div class="clearfix"></div>
            <?php endif ?>       
            <?php if ($data['group2']): ?>  
                <a class="friend" href="/chatall/index/?groupp=2&chat_author_id=<?php echo $_SESSION['id']?>">
                    <table>
                        <tr>
                            <td class="friend-name"> Ваша группа 2 </td>
                            <td class="friend-delete"><a class="button_delete hide-on-large-only" href="/friendsall/index/?groupp_delete=2">
                            <i class="material-icons krest">&#xE14c;</i></a></td>
                        </tr>
                    </table>
                </a><div class="clearfix"></div>
            <?php endif ?>

            <!-- если группы уже две, то нет возможности создания новой группы -->
            <?php if ( !($data['group1'] && $data['group2'])): ?> 
                <span class="brand-logo" style="font-size:20px;">
                    <?php echo 'Выберите участников нового группового чата из ваших друзей:' ?>
                </span>
                    <?php foreach ($data['friends'] as $friend) { ?>        
                        <div class="chat-view cv-friend">
                            <table>
                                <tr>
                                    <td class="shrink"> 
                                        <div class="cv-avatar"> <img class="circle" src="/<?php echo $friend['picture'] ?>"></div>
                                        <div class="cv-text">  <b> <?php echo $friend['nick'] ?>     &nbsp &nbsp  <?php echo $friend['login'] ?> </div>
                                        <input id="id<?php echo $friend['id']?>" type="checkbox" name="user_include<?php echo $friend['id']?>" value="<?php echo $friend['id']?>">
                                        <label for="id<?php echo $friend['id']?>"></label> </td>      
                                </tr>
                            </table> <div> &nbsp </div>
                    <?php } ?> 
                        </div>
                <div class="row">
                    <div class="col s12 center-align">
                        <button class="btn waves-effect waves-light" type="submit" name="start">Начать групповой чат
                            <i class="material-icons right">message</i>
                        </button>
                    </div>
                </div>
            <?php endif?>
            </form>
        <?php else : ?>     <!-- if ($data['error'] == false) -->
            <div class="alert alert-danger">
                <h3 class="center"> Для создания группового чата вы должны выбрать не меньше двух участников</h3>
            </div>
            <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
        <?php endif; ?>
    </main>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</body>


