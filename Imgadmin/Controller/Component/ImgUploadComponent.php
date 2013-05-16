<?php

/**
 * Image Upload
 *
 * By Erik Alves de Figueiredo (erik.figueiredo@gmail.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Erik Alves de Figueiredo
 * @link          erik.figueiredo@gmail.com
 * @package       gapi_cake
 * @subpackage    controllers.components
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 *  
 *  Modo de usar
 *  
 *  Cole o código abaixo no seu Controller (ou AppController para estar disponível sempre)
 *  
	public $components = array('ImgUpload');
 *  
 */

App::import('Utility', 'Folder');
App::import('File', 'Folder');

class ImgUploadComponent extends Component {
	
	// DEFINE SE A PASTA A SER USADA DENTRO DE WEBROOT/IMG
	// ATENÇÃO: A FUNÇÃO JÁ ADICIONA A ULTIMA BARRA '/' NO FIM DO CAMINHO, NÃO É NECESSÁRIO ADICIONAR ISSO EM LUGAR NENHUM
	public $destino='upload';
	
	public $_defaults = array(
		'path' => null
	);
	
	//Configura a variável de configurações e cria as pastas de upload caso não existam!
	public function __construct(ComponentCollection $collection, $settings=array()){
		$settings = array_merge($this->_defaults, $settings);
		$settings['path'] =  ($settings['path']==null)?WWW_ROOT.'img'.DS.$this->destino.DS:$settings['path'];
    	$this->settings = $settings;
		$this->criaPasta($settings['path']);
	}
	
	//Cria as pastas caso não existam
	
	function criaPasta($pasta){
		if(!is_dir($pasta)):
			$folder = new Folder();
			$pasta = $pasta.DS;
			if($folder->create($pasta)):
				$folder->create($pasta.'thumb');
				$folder->create($pasta.'pequeno');
				$folder->create($pasta.'medio');
				$folder->create($pasta.'grande');
				$folder->create($pasta.'full');
				$folder->chmod($pasta, 0755, true);
			else:
				throw new NotFoundException(
					__('Não foi possível criar a pasta de upload (Padrão em APP/webroot/myImg) verifique se o caminho está correto e as permissões!')
				);
			endif;
		endif;
	}
	
	//Verifica se a abiblioteca GD está ativa
	function getGD($gd_info=false){
		$retorno=(extension_loaded('gd') && function_exists('gd_info'))? true : false;
		if($gd_info){
			return var_dump(gd_info());
		}else{
			return $retorno;
		}
	}
	
	//Remove um arquivo

	function removeImage($imagem){
		$aquivo = new File($this->settings['path'].$imagem,false);
		if($aquivo->readable()):
			$aquivo->delete();
			return true;
		else:
			throw new NotFoundException(
					__('Não foi possível apagar o arquivo '.$this->settings['path'].$imagem. ', verifique as permissões da pasta e o caminho completo!')
				);
		endif;
	}
	
	/* Salva o arquivo no diretório ou retorna um binário dos dados
	 * 
	 * Retorna:
	 * 
	 * $retorno['nomebase']		- O nome completo da imagem (Ex: 'erik.png')
	 * $retorno['nome']			- O nome da imagem sem extensão (Ex: 'erik')
	 * $retorno['extensao']		- Só a extensãoa (Ex: 'png')
	 * $retorno['diretorio']	- O diretório de salvamento (ou que poderá ser salvo se solicitou modo binário)
	 * $retorno['binario']		- Se você setar o segundo parametro para true, aqui terá todo o conteúdo do arquivo enviado, mesmo que seja imagem
	 * 
	 * Se você setar o modo binário, poderá, por exemplo, pegar o $retorno['binario'] salvar em
	 * seção e salvar isto posteriormente, excelente para envios de imagem antes de salvar o formulário
	 * 
	 */
	
	function salvaImage($file=null,$binario=false,$pasta=null){
		
		if($pasta!=null) $this->settings['path'] = $pasta;
		
		//verifica se o $file é um array de upload válido
		if(!is_array($file)):
			throw new NotFoundException(
					__('O parametro $file precisa ser um array!')
				);
		else:
			if((!array_key_exists('name',$file))and(!array_key_exists('type',$file))and(!array_key_exists('size',$file))and(!array_key_exists('tmp_name',$file))and(!array_key_exists('error',$file))):
				throw new NotFoundException(
					__('O array $file não é um arquivo de upload válido!')
				);
			endif;
		endif;
		
		//Le e armazena o conteudo do arquivo
		$temp_arquivo = new File($file['tmp_name'],false);
		$dados = $temp_arquivo->read();
		$temp_arquivo->close();
		
		//Cria o arquivo tomando cuidado para não sobrescrever, por exemplo, se existir a imagem erik.png, ele salvará erik2.png
		$arquivo=$this->verificaNome($file);
		
		//gera o array de saída
		$pathinfo = pathinfo($arquivo->name);
		$retorno['nomebase']=	$pathinfo['basename'];
		$retorno['nome']=		$pathinfo['filename'];
		$retorno['extensao']=	$pathinfo['extension'];
		$retorno['diretorio']=	$this->settings['path'];
		
		//se o binário estiver ligado
		if($binario==true):
			//Grava o conteúdo do arquivo no array de retorno
			$retorno['binario']=$dados;
		else:
			//Se não estiver ligado, salva o arquivo na pasta
			if($arquivo->create()) :
				$arquivo->write($dados);
				$arquivo->close();
			else:
				throw new NotFoundException(
					__('O arquivo não pode ser salvo, verifique as permissões da pasta e o caminho completo!')
				);
			endif;
		endif;
		
		$this->redimensionaCMS($retorno);
		
		return $retorno;
		
	}
	
	//Cria o arquivo tomando cuidado para não sobrescrever, por exemplo, se existir a imagem erik.png, ele salvará erik2.png
	function verificaNome($file){

		$arquivo = new File($this->settings['path'].$file['name'],false,0644);
		$marcador = 2;
		$pathinfo = pathinfo($file['name']);
		
		while($arquivo->exists()):
			$novo=$this->settings['path'].$pathinfo['filename'].$marcador.'.'.$pathinfo['extension'];
			$arquivo->close();
			$arquivo = new File($novo,false,0644);
			$marcador++;
		endwhile;
		return $arquivo;
	}
	
	/* Redimensiona a imagem para o tamanho informado, e com o tipo de corte informado
	 * 
	 * $file(array) deve ser no padrão de retorno da função salvaImagem(), com exceção do $file['binario'] que a função não oferece suporte para cortar diretamente antes de salvar e $file['novoDiretorio'] que é um novo parametro opcional (veja abaixo), exemplo
	 * 
	 * $file['nomebase']		- O nome completo da imagem (Ex: 'erik.png')
	 * $file['nome']			- O nome da imagem sem extensão (Ex: 'erik')
	 * $file['extensao']		- Só a extensãoa (Ex: 'png')
	 * $file['diretorio']		- O diretório em que a imagem está
	 * $file['novoDiretorio']	- O diretório de salvamento (não informe este parametro em branco se que sobrescrever
	 * 							  o original)
	 * 
	 * $tipo(string) pode ser:
	 * resize	- Redimensiona a imagem sem se preocupar com as proporções
	 * inside	- Redimensiona a imagem dentro do espaço informado, mantendo a largura e largura dentro dos limites, sem tirar a imagem da proporção
	 * outside	- Redimensiona a imagem ocupando o espaço maximo informado, mantendo a largura e altura ocupando o máximo possível, sem tirar da proporção
	 * crop		- Redimensiona a imagem como outside (acima) e corta os excessos (default)
	 * 
	 * $largura(int) é a largura da imagem (o padrão é 40px)
	 * 
	 * $altura é a altura da imagem(int) é a largura da imagem (o padrão é 40px)
	 * 
	 * seguindo as informações acima, se salvar uama imagem apenas informando o parametro obrigatório ($file),
	 * teremos como resultado uma imagem de 40x40px exatos, retirando todas as sobras
	 *
	 */
	function redimensiona($file,$largura=40,$altura=40,$tipo='crop'){
		
		
		$salva=(isset($file['novoDiretorio']))?$file['novoDiretorio']:$file['diretorio'];
		
		$size = @getimagesize($file['diretorio'].$file['nomebase']); 
		
		switch ($size['mime']) {
			case "image/gif":
				$original = imagecreatefromgif($file['diretorio'].$file['nomebase']);
				break;
			case "image/jpeg":
				$original = imagecreatefromjpeg($file['diretorio'].$file['nomebase']);
				break;
			case "image/png":
				$original = imagecreatefrompng($file['diretorio'].$file['nomebase']);
				break;	
			default:
				$original = false;
				break;
		}
		
		if($original){
			$largOrig	=	imagesx($original);
			$altOrig	=	imagesy($original);
			
			//não mantem proporção
			if($tipo=='resize'){
				
					$img = imagecreatetruecolor($largura,$altura);
					imagecopyresized($img,$original,0,0,0,0,$largura,$altura,$largOrig,$altOrig);
			}
			
			//mantem proporção mas a imagem não será maior que o definido
			if($tipo=='inside'){
					$largRes = ($largOrig*$altura)/$altOrig;
					if($largRes>=$largura){
						$largRes=$largura;
						$altRes = ($altOrig*$largura)/$largOrig;
					}else{
						$altRes=$altura;
					}
					
					$img = imagecreatetruecolor($largRes,$altRes);
					imagecopyresized($img,$original,0,0,0,0,$largRes,$altRes,$largOrig,$altOrig);
			}
			
			//prepara o tamanho da imagem para osutside e crop
			if($tipo=='outside' || $tipo=='crop'){
					$largRes = ($largOrig*$altura)/$altOrig;
					if($largRes>=$largura){
						$altRes=$altura;
					}else{
						$largRes=$largura;
						$altRes = ($altOrig*$largura)/$largOrig;
					}
			}
			
			if($tipo=='outside'){
				$img = imagecreatetruecolor($largRes,$altRes);
				imagecopyresized($img,$original,0,0,0,0,$largRes,$altRes,$largOrig,$altOrig);
			}
			
			//corta os esseços
			if($tipo=='crop'){
				
				$imgForCrop = imagecreatetruecolor($largRes,$altRes);
				imagecopyresampled($imgForCrop,$original,0,0,0,0,$largRes,$altRes,$largOrig,$altOrig);
				
				$posx1		=	($largRes/2)-($largura/2);
				$posy1		=	($altRes/2)-($altura/2);
				
				$img = imagecreatetruecolor($largura,$altura);
				imagecopy($img,$imgForCrop,0,0,$posx1,$posy1,$largura,$altura);
			}
			
			switch ($size['mime']) {
				case "image/gif":
					imagegif($img,$salva.$file['nomebase']);
					break;
				case "image/jpeg":
					imagejpeg($img,$salva.$file['nomebase']);
					break;
				case "image/png":
					imagepng($img,$salva.$file['nomebase']);
					break;	
				default:
					throw new NotFoundException(__('Imagem inválida'));
					break;
			}
		}
	}
	
	/* Redimensiona a imagem para os formatos padrões do CMS
	 * 
	 * $file(array) é o padrão normal do redimensiona() sem o $file['novoDiretorio']
	 * 
	 * $tipo(array) são os formatos de redimensionamento para a imagem original e miniatura, exemplo
	 * $tipo['miniatura']='inside';
	 * $tipo['original']='ouside';
	 * 
	 * $largura(array) é a largura da imagem para a imagem original e miniatura, exemplo
	 * $largura['miniatura']=40;
	 * $largura['original']=400;
	 * 
	 * $altura(array) é a altura da imagem para a imagem original e miniatura, exemplo
	 * $altura['miniatura']=40;
	 * $altura['original']=400;
	 *
	 */
	function redimensionaCMS($file){
		
		$config = Configure::read('Imgadmin');
		
		// imagem 50x50px
		$file['novoDiretorio']= $file['diretorio'].'thumb'.DS;
		$this->redimensiona($file,$config['thumb']['largura'],$config['thumb']['altura'],$config['thumb']['redimensiona']);
		
		// imagem 100x100px
		$file['novoDiretorio']= $file['diretorio'].'pequeno'.DS;
		$this->redimensiona($file,$config['pequeno']['largura'],$config['pequeno']['altura'],$config['pequeno']['redimensiona']);
		
		// imagem 250x250px
		$file['novoDiretorio']= $file['diretorio'].'medio'.DS;
		$this->redimensiona($file,$config['medio']['largura'],$config['medio']['altura'],$config['medio']['redimensiona']);
		
		// imagem 500x500px
		$file['novoDiretorio']= $file['diretorio'].'grande'.DS;
		$this->redimensiona($file,$config['grande']['largura'],$config['grande']['altura'],$config['grande']['redimensiona']);
		
		// imagem 800x600px
		$file['novoDiretorio']= $file['diretorio'].'full'.DS;
		$this->redimensiona($file,$config['full']['largura'],$config['full']['altura'],$config['full']['redimensiona']);
		
	}
	
	
	/* Apaga as imagens do CMS
	 * 
	 */
	function removeImageCMS($imagem,$pasta=null){
		
		if($pasta!=null) $this->settings['path'] = $pasta;
		
		// imagem original
		$this->removeImage($imagem);
		
		// imagem 50x50
		$this->removeImage('thumb'.DS.$imagem);
		
		// imagem 100x100
		$this->removeImage('pequeno'.DS.$imagem);
		
		// imagem 250x250
		$this->removeImage('medio'.DS.$imagem);
		
		// imagem 500x500
		$this->removeImage('grande'.DS.$imagem);
		
		// imagem 800x600
		$this->removeImage('full'.DS.$imagem);
				
		return true;
	}
		
}