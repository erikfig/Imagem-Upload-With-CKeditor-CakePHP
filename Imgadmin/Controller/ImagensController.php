<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
class ImagensController extends ImgadminAppController {
	
	function add(){
		$this->layout = 'ajax';
		//$myFolder=WWW_ROOT.'img'.DS.'myImg'.DS;
		$pasta ='upload';
		$myFolder=WWW_ROOT.'img'.DS.$pasta.DS;
		if($this->request->is('post')):
			$arquivo=$this->ImgUpload->salvaImage($_FILES['file'],false,$myFolder);
			die('{"jsonrpc" : "2.0", "result" : null, "id" : "id", "cleanFileName" : "'.$arquivo['nomebase'].'"}');
		endif;
		$dir = new Folder($myFolder);
		$files = $dir->find('.*', true);
		$this->set('pasta',$pasta);
		$this->set('files',$files);
		$this->set('max_size',preg_replace('/(M)/','',ini_get('upload_max_filesize')));
	}
	
	function remove($arquivo){
		$this->layout = 'ajax';
		$pasta = $this->Auth->user('id');
		$myFolder=WWW_ROOT.'img'.DS.$pasta.DS;
		$this->ImgUpload->removeImageCMS($arquivo,$myFolder);
	}
	
}