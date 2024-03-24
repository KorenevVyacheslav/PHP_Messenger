<link type="text/css" rel="stylesheet" href="/css/mystyle.css"/> 
<?php if($data['auth'] == false):  header("Location:". DIRECTORY_SEPARATOR. "input");  endif;  ?>

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
                    </ul>
            </div>
    </header>
 
<main>
    <div id="search_body">
        <div class="row" style="margin:0;">
            <div class="input-field card col s12">
                <i class="material-icons prefix" style="margin-top:10px;">&#xE8b6;</i>
                <input placeholder="Начните писать ник собеседника" type="text" id="search">
            </div>
        </div>
        <div id="find_list" class="row teal lighten-5">
            <div id="find_area" class="col s12"></div>
        </div>
    </div>
</main>
 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">

    $(document).ready(function ($) {
        var $searchBody = $("#search_body"),
        $nav = $("#nav"),
        $search = $("#search"),
        find_area = $("#find_area");
    
        var escapeHtml = function (unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        };
        var renderFinds = function (finds) {
            var html = '';
            // выпадающее окно с пользователями
            finds.forEach(function (item) {
                html += '<a class="friend" href="/Chat?friend_id=' + item.id + '">';
                html += '<table>';
                html += '<tr>';
                html += '<td class="friend-image"><img class="circle" src="' + item.picture + '"></td>';
                html += '<td class="friend-name">' + escapeHtml(item.nick) + '</td>';
                html += '<td class="friend-name">' + escapeHtml(item.login) + '</td>';
                html += '</tr>';
                html += '</table>';
                html += '</a><div class="clearfix"></div>';
            });
            find_area.html(html);       //задаёт содержание find_area innerHTML:
        };

        // получение пользователей через AJAX
        var Find = function () {    
            var id='<?php echo $_SESSION['id'];?>',
            value = $search.val();
            value = $.trim(value);

            if (value !== "") {
                $.ajax({
                    type: "POST",
                    url: '/app/ajax.php', 
                    data: {
                        act: "find",
                        id: id,
                        action: "methos",
                        val: value
                    },
                    success: function (data) {  renderFinds(data.finds);  }
                })
            }
        };

        var refreshHeight = function () {
            $searchBody.height(document.documentElement.clientHeight - $nav.height());
        };
        $(window).resize(refreshHeight);
        refreshHeight();
        $search.bind("keyup", function () {
            if ($search.val().trim() !== '') {
                Find();
            }
        });
    });
</script>
</body>

