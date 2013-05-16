<?php

class ImgadminAppController extends AppController {
	public $components = array(
		'Imgadmin.ImgUpload'
    );
		
	public function beforeFilter() {
		/*
		 * POSSÍVEIS VALORES PARA O RESIZE:
		 * resize	- Redimensiona a imagem sem se preocupar com as proporções
		 * inside	- Redimensiona a imagem dentro do espaço informado, mantendo a largura e largura dentro dos limites, sem tirar a imagem da proporção
		 * outside	- Redimensiona a imagem ocupando o espaço maximo informado, mantendo a largura e altura ocupando o máximo possível, sem tirar da proporção
		 * crop		- Redimensiona a imagem como outside (acima) e corta os excessos (default)
		 */
			if(!Configure::check('Imgadmin')){
				Configure::write('Imgadmin', array(
					'full'=>array(
						'largura'=>800,
						'altura'=>600,
						'redimensiona'=>'outside'
					),
					'thumb'=>array(
						'largura'=>50,
						'altura'=>50,
						'redimensiona'=>'crop'
					),
					'pequeno'=>array(
						'largura'=>100,
						'altura'=>100,
						'redimensiona'=>'crop'
					),
					'medio'=>array(
						'largura'=>250,
						'altura'=>250,
						'redimensiona'=>'crop'
					),
					'grande'=>array(
						'largura'=>500,
						'altura'=>500,
						'redimensiona'=>'crop'
					)
				));
			}
		
		$this->ImgUpload->criaPasta(WWW_ROOT.'img'.DS.'upload');
		$cms['base_url']=Router::url('/',true);
		$cms['base_url']= substr($cms['base_url'], 0, -1);
		$this->set('cms', $cms);
	}
}
