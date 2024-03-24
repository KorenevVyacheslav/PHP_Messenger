<?php

define('URL', __DIR__ . DIRECTORY_SEPARATOR); 
define ('APP', URL . 'app' . DIRECTORY_SEPARATOR);                 //    директория   /app/
define ('VIEWS', APP . 'views' . DIRECTORY_SEPARATOR);             //    директория   /app/views/
define ('CONTROLLERS', APP . 'controllers' . DIRECTORY_SEPARATOR); //    директория   /app/controllers/
define ('MODELS', APP . 'models' . DIRECTORY_SEPARATOR);           //    директория   /app/models/
define ('IMAGES', 'userpics' . DIRECTORY_SEPARATOR);                //   директория   /userpics/ 
define ('CONTROLLERS_NAMESPACE', 'App\\controllers\\' );

define('UPLOAD_MAX_SIZE', 2097152);                                 // максимальный размер изображения пользователя
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif','image/jpg']);  // допустимые форматы

// введите ваши нстройки БД:
define ('HOST', 'localhost');                                       // определите наименование хоста
define ('USER', 'homestead');                                       // определите имя пользователя БД
define ('PASSWORD', 'secret');                                      // определите пароль к БД
define ('DB_NAME', 'homestead');                                    // определите наименование БД

// настройки для OSP
// define ('HOST', 'localhost');                                       // определите  наименование хоста
// define ('USER', 'root');                                            // определите имя пользователя БД
// define ('PASSWORD', '');                                            // определите пароль к БД
// define ('DB_NAME', 'test');                                         // определите наименование БД