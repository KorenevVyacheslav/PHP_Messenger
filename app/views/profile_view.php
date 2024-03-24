<link type="text/css" rel="stylesheet" href="/css/mystyle.css"/> 
<?php if($data['auth'] == false): header("Location:". DIRECTORY_SEPARATOR. "input");  endif; ?>

<body>
<header>
    <nav id="nav" class="teal darken-1">
        <div class="container">
            <span class="brand-logo" style="font-size:20px;">
                <?php  $str = htmlspecialchars($data['main_text']);
                    if (mb_strlen($str) > 40) { $str = substr($str, 0, 40) . "&#8230;";   }
                echo $str; ?>
            </span>
            <ul class="right hide-on-med-and-down">
                <li>
                    <a href='/logout'>
                        <img class="circle" style="height:35px;padding-top:15px;" src="<?php echo $data['author_avatar_path'] ?>">
                        <?php $str = htmlspecialchars($data['login']);   
                        if (mb_strlen($str) > 15) {  $str = substr($str, 0, 15) . "&#8230;"; }
                        ?>
                        &ensp;<?php echo $str ?>&ensp;|&ensp;Выйти
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
                    <a href="/logout">
                        <div class="row valign-wrapper">
                            <div class="col s2"><img class="valign" src="ico/ico_quit.svg"></div>
                            <div class="col s10">Выйти из системы</div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<?php if (!$data['errors']) : ?> 
<main>
    <div id="change_avatar_div" class="row">
        <div class="col s12 center">
            <h4><?php echo htmlspecialchars($data['nick']) ?></h4>
            <img class="circle" src="<?php echo $data['author_avatar_path'] ?>">
        </div>
        <form enctype="multipart/form-data" class="col s12" method="post" action="">
            <h5 class="center">Изменить аватар</h5>
            <div class="row">
                <div class="file-field input-field col s12 m5 l5">
                    <div class="btn" style="height:100px;line-height:102px;width:100%;">
                        <span>Выбрать аватар<i class="material-icons right">&#xE2c8;</i></span>
                        <input name="avatar" type="file" accept="image/*" required>
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                    </div>
                </div>
                <div class="col s0 m7 l7 hide-on-small-only valign-wrapper" style="height:100px;margin-top:1rem;">
                    <p class="valign bubble" style="margin:0;"><i class="material-icons left">&#xE317;</i>Нажмите, чтобы
                        выбрать изображение. Оно должно иметь формат JPEG,&nbsp;JPG,&nbsp;PNG,&nbsp;GIF&nbsp;или&nbsp;PNG (до&nbsp;2&nbsp;МБ)
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m5 l5">
                    <button class="btn waves-effect waves-light" style="height:100px;line-height:102px;width:100%;"
                            type="submit" name="action">
                        Установить аватар
                        <i class="material-icons right">&#xE163;</i>
                    </button>
                </div>
                <div class="col s0 m7 l7 hide-on-small-only valign-wrapper" style="height:100px;">
                    <p class="valign bubble" style="margin:0;"><i class="material-icons left">&#xE317;</i>Нажмите, чтобы
                        установить выбранное изображение</p>
                </div>
            </div>
        </form>
        <form class="col s12" method="post" action="">
            <div class="row">
                <div class="col s12 m5 l5">
                    <button class="btn waves-effect waves-light red darken-3"
                        style="height:100px;line-height:102px;width:100%;"  type="submit" name="delete">
                            Удалить аватар
                            <i class="material-icons right">&#xE14c;</i>
                    </button>
                </div>
                <div class="col s0 m7 l7 hide-on-small-only valign-wrapper" style="height:100px;">
                    <p class="valign bubble" style="margin:0;"><i class="material-icons left">&#xE317;</i>Нажмите, чтобы
                     далить свой аватар</p>
                </div>
            </div>
        </form>
        <form action="" class="col s12" method="post">
           <h5 class="center">Изменить никнейм</h5>
            <div class="row">
                <div class="input-field col s12">
                    <input id="nick" name="login" type="text" class="validate" length="10" maxlength="10" required>
                    <label for="nick">Введите никнейм (максимум 10 символов без пробелов)</label>
                </div>
            </div>
            <div class="row">
                <div class="col s12 center-align">
                    <button class="btn waves-effect waves-light" type="submit" name="action">Изменить никнейм
                        <i class="material-icons right">person</i>
                    </button>
                </div>
            </div>
        </form>   
        <form action="" class="col s12" method="post">
           <h5 class="center">Поставьте галочку, чтобы скрыть/открыть свой e-mail для поиска</h5>
           <h6 class="center">Сейчас ваш e-mail <?php if (!$data['hide_email']) echo 'не' ?> скрыт </h6>
           <div class="row">
                <div class="center">
                    <input id="hide" type="checkbox" name="hide_email">
                    <label for="hide"></label>
                </div>
            </div>
            <div class="row">
                <div class="col s12 center-align">
                    <button class="btn waves-effect waves-light" type="submit" name="action">Скрыть/открыть e-mail
                        <i class="material-icons right">mail</i>
                    </button>
                </div>
            </div>
        </form>   
    </div>
<?php else : ?>
    <body style="background-color:#3b70d1;">
        <?php foreach ($data['errors'] as $error): ?>
            <div class="alert alert-danger">
            <h3 style="color:#fff;" class="center"><?php echo $error; ?> </h3>
            </div>
        <?php endforeach; ?>
        <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
<?php endif;  ?>
</main>

<script type="text/javascript">
    $(document).ready(function () {                 //запускаем, когда документ загрузится
        var refreshHeight = function () {
            $("#change_avatar_div").height(document.documentElement.clientHeight - $("#nav").height());
        };
        $(window).resize(refreshHeight);
        refreshHeight();
    });
</script>
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
</body>
