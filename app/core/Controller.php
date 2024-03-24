<?php
namespace App\core;

class Controller {
	
	public $model;
	public $view;

	function __construct()	{						
		$this->view = new View();				
	}
	
	public function action_index()	{}				//определим в дочерних классах

	// метод генерации строки из случайных символов
	public function generateCode ($length=10) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;
		while (strlen($code) < $length) {
			$code .= $chars[mt_rand(0,$clen)];
		}
		return $code;                                          
	} 
}






