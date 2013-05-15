<?php

class ImgadminAppController extends AppController {
	public $components = array(
		'Imgadmin.ImgUpload'
    );
		
	public function beforeFilter() {
		
		$this->ImgUpload->criaPasta(WWW_ROOT.'img'.DS.'upload');
		$cms['base_url']=Router::url('/',true);
		$cms['base_url']= substr($cms['base_url'], 0, -1);
		$this->set('cms', $cms);
	}
}
