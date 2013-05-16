Administração de imagens com upload multiplo e integração com CKeditor
===================================

Você envia sua imagem (ou suas imagens) e apenas clica em cima para adicionar o nome (url) dela no campo do formulário, em uma janela a parte para você não perder seus dados.

Integrado ao CKeditor!

O sistema já redimensiona para 5 formatos diferentes de imagens para você usar.

INSTALAÇÃO
===================================

Clone ou desconpacte no diretório app/Plugin do seu CakePHP e renomeio a pasta parra Imgadmin

Em app/Config/boostrap.php adicione:

CakePlugin::load('Imgadmin',array('routes'=>true));

ou

CakePlugin::loadAll(array('Imgadmin' => array('routes' => true)));

No seu form, ou layout, ou tema... bem, na sua view, adicione os javascripts:

echo $this->Html->script(array('//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js','imgadmin/admin.js','/imgadmin/js/ckeditor/ckeditor.js'));

Altera a permissão da pasta app/webroot/img para leitura e gravação (777).

Pronto, está tudo configurado

Usando
===================================

Para usar é fácil, apenas coloque um field hidden com class img-select, ex:

echo $this->Form->hidden('minhaimagem',array('class'=>'img-select'));

E para usar o ckeditor adicione a class ckeditor em um textarea

echo $this->Form->textarea('texto',array('class'=>'ckeditor'));

O CKeditor funciona normalmente, ou seja, com todas as funções e configurações que você precisar...

Configuração opcional
===================================

Você ainda pode opcionalmente configurar o Plugin para ajustar suas imagem ao seu gosto adicionando esse código ao seu app/Config/core.php:

Configure::write('Imgadmin', array(
  'full'=>array(
		'largura'=>800,
		'altura'=>600,
		'redimensiona'=>'outside'
	),
	'thumb'=>array(
		'largura'=>50,
		'altura'=>50,
		'redimensiona'=>'outside'
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

Sendo que os valores para redimensiona são:

  	 * resize  - Redimensiona a imagem sem se preocupar com as proporções (pode distorcer)
		 * inside	- Redimensiona a imagem dentro do espaço informado, mantendo a largura e largura dentro dos limites, sem tirar a imagem da proporção
		 * outside	- Redimensiona a imagem ocupando o espaço maximo informado, mantendo a largura e altura ocupando o máximo possível, sem tirar da proporção
		 * crop		- Redimensiona a imagem como outside (acima) e corta os excessos (default) mantendo apenas o centro

Bom uso!!!

Att. Erik Figueiredo
www.erikfigueiredo.com.br
