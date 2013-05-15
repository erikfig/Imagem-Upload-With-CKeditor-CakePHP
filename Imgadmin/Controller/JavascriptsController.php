<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
class JavascriptsController extends ImgadminAppController {
	
	function admin(){
		$this->layout='ajax';
		$this->response->type(array('javascript' => 'text/javascript'));
		$this->response->type('javascript');
	}
	
}