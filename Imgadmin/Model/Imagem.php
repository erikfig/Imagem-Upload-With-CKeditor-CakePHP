<?php
class Imagem extends ImgadminAppModel {
	public $name = 'Imagem';
	
	public $actsAs = array(
        'Upload.Upload' => array(
            'arquivo' => array(
                'fields' => array(
                    'dir' => 'photo_dir'
                ),
                'thumbnailSizes' => array(
					'full' => '800x600',
					'grande' => '500x500',
                    'medio' => '250x250',
                    'pequeno' => '100x100',
                    'thumb' => '50x50'
                )
            )
        )
    );
}