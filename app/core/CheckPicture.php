<?php
namespace App\core;

// контроллер проверки изображения на размер, формат и привидения его к формату аватара
class CheckPicture {

   	public static function check_picture($login_in)  {                         
    	$fileName = $_FILES['avatar']['name'];			
      	$data = [
			'register' => false,						// запрет регистрации по умолчанию
		];
      
		//Проверяем размер
		if ($_FILES['avatar']['size'] > UPLOAD_MAX_SIZE) {
			$data['error'] = 'Недопустимый размер файла ' . $fileName;; 
         	return $data;
		}

		//Проверяем формат
		if (!in_array($_FILES['avatar']['type'], ALLOWED_TYPES)) {
			$data ['error'] = 'Недопустимый формат файла ' . $fileName;
			return $data;
		}
        // форматируем изображение
		if ($_FILES['avatar']['error'] == 0) {
			$file_tmp = $_FILES['avatar']['tmp_name'];
			$finfo = new \finfo;
			$type = $finfo->file($file_tmp, FILEINFO_MIME_TYPE);

			if ($type == "image/x-ms-bmp" || $type == "image/bmp" || $type == "image/x-windows-bmp" || $type == "image/gif" 
			|| $type == "image/jpeg" || $type == "image/pjpeg" || $type == "image/png") {
				$image = new \Imagick($file_tmp);
                $width = $image->getImageWidth();			// ширина изображения в пикселях
                $height = $image->getImageHeight();			// высота изображения в пикселях
                if ($width > $height) {
                    $a = $width - $height;
                    $b = intval ($a / 2);
                    $image->cropImage($height, $height, $b, 0);		// обрезаем изображение 
                } else {
                    $a = $height - $width;
                    $b = intval ($a / 2);
                    $image->cropImage($width, $width, 0, $b);		// обрезаем изображение 
                }

				$image->thumbnailImage(50, 50);						// изменяем размер до  50х50 пикселей
                $image->setImageFormat('jpeg');						// изменяем формат на jpeg
				$login = str_replace('@', '_', $login_in);			// чтобы сохранить имя файла уникальным
				$login_corrected = str_replace('.', 'x', $login);	// заменяем "@" на "_" и "." на "x"

				$filePath = IMAGES . $login_corrected . ".jpg";		// сохраняем аватар
				file_put_contents($filePath, $image);

				if (file_exists($filePath)) {
					$data ['register'] = true;
					$data ['filePath'] = $filePath ;
					return $data;
				}
			}		//if ($type == "image/x-ms-bmp" ...
		} else $data['error'] = 'Во время загрузки файла ' .$fileName .' произошла ошибка';
		return $data;
  	}
}
