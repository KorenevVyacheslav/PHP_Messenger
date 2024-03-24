<style type="text/css">
    body { background-color: #3b70d1; }
    h1, h3 {color: #ffffff; }
</style>

<header>
<?php if (!$data['errors'] && $data['auth'] == false): ?> 
    <h1 class="center hide-on-small-only">
        Регистрация
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
                        <form enctype="multipart/form-data" class="col s12" method="post">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="login_field" name="login" type="text" class="validate" length="40"
                                           maxlength="40" required>
                                    <label for="login_field">Введите ваш e-mail</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="password_field" name="password" type="password" class="validate"
                                           length="15" maxlength="15" required>
                                    <label for="password_field">Придумайте пароль </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="password_repeat_field" name="password_repeat" type="password"
                                           class="validate" length="15" maxlength="15" required>
                                    <label for="password_repeat_field">Введите пароль еще раз</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12">
                                    <p class="center-align" style="color:#00b0ff">Пароль должен состоять только из букв английского/русского алфавита, цифр и спецсимволов non-word (кроме пробела), от 4-15 символов</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="file-field input-field col s12">
                                    <div class="btn">
                                        <span>Выбрать аватар (необязательно)</span>
                                        <input name="avatar" type="file" accept="image/*">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12">
                                    <p class="center-align" style="color:#777">Изображение должно иметь формат JPEG, JPG,
                                        GIF или PNG (до 2 МБ)</p>
                                </div>
                            </div>
                            <div class="row center">
                                <div>
                                    <button class="btn waves-effect waves-light" type="submit" name="action">
                                        Зарегистрироваться
                                        <i class="material-icons right">&#xE163;</i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s0 m2 l3">
            <pre></pre>
        </div>
    </div>
    <?php else : ?>
        <body style="background-color:#00897B;">
        <?php foreach ($data['errors'] as $error): ?>
            <div class="alert alert-danger">
            <h3 style="color:#fff;" class="center"><?php echo $error; ?> </h3>
            </div>
        <?php endforeach; 
     endif;  


    if($data['auth'] == true): ?>
        <h3 style="color:#fff;" class="center">Регистрация пройдена!</h3>
            <div class="center-align"><a href="/friends" class="btn waves-effect waves-light">Вперёд &raquo;</a></div> 
    <?php elseif ($data['errors']): ?>
        <div class="center-align"><a href="javascript:history.back()" class="btn waves-effect waves-light">&laquo; Назад</a></div>
    <?php endif;  
    





