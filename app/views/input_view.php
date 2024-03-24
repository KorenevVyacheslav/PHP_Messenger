<?php 
use App\core\CSRF;
$token = CSRF::create_token(); ?>       <!-- проверка токена CSRF. Защита от брутфорса --->

<?php if($data['auth'] == true): header("Location:". DIRECTORY_SEPARATOR. "friends"); endif; ?>

<header>
    <style type="text/css">
        body { background-color: #3b70d1; }
        h1, h3 {color: #ffffff; }
    </style>
    <?php if (!$data['errors']): ?>             
        <h1 class="center hide-on-small-only">
            Добро пожаловать в Мессенджер! 
        </h1>
</header>
<main>
    <div class="row">
        <div class="col s0 m2 l3">
            <pre></pre>
        </div>
        <div class="col s12 m8 l6">
            <div class="card">
                <div class="card-content">
                    <div class="row center">
                        <div class="col s12">
                            <img src="ico/logo-dark.png">
                        </div>
                    </div>
                    <div class="row">
                        <form class="col s12" method="post">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="login_field" name="login" type="email" class="validate" length="40" maxlength="40" required>
                                    <label for="login_field">Ваш e-mail</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="password_field" name="password" type="password" class="validate" length="15" maxlength="15" required>    
                                    <input type="hidden" name="token1" value="<?php echo $token?>" > <br/>
                                    <label for="password_field">Пароль</label>
                                </div>
                            </div>
                            <div class="row center">
                                <div>
                                    <button class="btn waves-effect waves-light" type="submit" name="action">
                                        Войти
                                        <i class="material-icons right">&#xE163;</i> 
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col s12 center-align">
                            <a href="/register" class="btn waves-effect waves-light">Регистрация</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 center-align">
                            <a href="http://oauth.vk.com/authorize? <?php echo $data['params'] ?> " class="btn waves-effect waves-light">Войти через VK</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s0 m2 l3">
            <pre></pre>
        </div>
    </div>
    <?php else : ?>
    <body style="background-color:#3b70d1;">
        <?php foreach ($data['errors'] as $error): ?>
            <h3 style="color:#fff;" class="center"><?php echo $error ?></h3>
        <?php endforeach; ?>
            <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
    <?php endif;  ?>
    </body>


