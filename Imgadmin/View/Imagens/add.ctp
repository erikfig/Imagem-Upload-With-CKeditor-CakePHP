<?php 
	 /*** CSS DO TEMA ***/
	 echo $this->Html->css(array('/imgadmin/css/bootstrap.min','/imgadmin/css/bootstrap-responsive.min','/imgadmin/js/plupload/jquery.plupload.queue/css/jquery.plupload.queue','/imgadmin/css/ger-imagem'),null,array('inline'=>false));
	 
	 /*** JS DO TEMA ***/
	 echo $this->Html->script(array('http://bp.yahooapis.com/2.4.21/browserplus-min.js','/imgadmin/js/jquery','/imgadmin/js/plupload/plupload.full','/imgadmin/js/plupload/jquery.plupload.queue/jquery.plupload.queue','/imgadmin/js/bootstrap.min'),array('inline'=>false));
	 
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Gerenciador de Imagens</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php
        echo $this->Html->charset();
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->Html->meta('icon');
    ?>
    <style>
		.pull-left{
			margin-top:10px;
			position:relative;
		}
		.remove {
			position:absolute;
			top:-3px;
			right:-3px;
		}
	</style>
  </head>
  <body>
  <div class="media">
  <?php foreach($files as $file):?>
  <div class="pull-left">
  	<?php
		echo $this->Html->link(
			$this->Html->image($pasta.'/thumb/'.$file,
				array('class'=>'img-polaroid')
			)
			,'/img/'.$pasta.'/medio/'.$file,
			array('class'=>'img','escape'=>false,'data-url'=>$file)
		);
	?>
    <div class="remove"><a href="#" class="btn btn-mini ttip" data-placement="bottom" data-original-title="Apagar" data-url="<?php echo $file;?>"><i class="icon-remove"></i></a></div>
    </div>
  <?php endforeach; ?>
  </div>
  <div class="plupload">Carregando envio de imagens...</div>
  

 
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-body">
    <?php if(isset($_GET['CKEditor'])):?>
    <div class="btn-group" data-toggle="buttons-radio">
      <button type="button" class="btn btn-primary tamanho" data-pasta="">Original</button>
      <button type="button" class="btn btn-primary tamanho" data-pasta="thumb">50x50</button>
      <button type="button" class="btn btn-primary tamanho" data-pasta="pequeno">100x100</button>
      <button type="button" class="btn btn-primary tamanho" data-pasta="medio">250x250</button>
      <button type="button" class="btn btn-primary tamanho" data-pasta="grande">500x500</button>
      <button type="button" class="btn btn-primary tamanho" data-pasta="full">800x600</button>
    </div>
    <?php else: ?>
      <button type="button" class="btn btn-primary tamanho" data-pasta="full">Confirmar!</button>
	<?php endif; ?>
  </div>
</div>

  <?php echo $this->fetch('script');?>
  <script>
  $(function(){
  if($('.plupload').length > 0){
	 plupload.addI18n({
			'Select files' : 'Selecione arquivos',
			'Add files to the upload queue and click the start button.' : 'Adicione os arquivos e clique em enviar',
			'Filename' : 'Nome do arquivo',
			'Status' : 'Status',
			'Size' : 'Tamanho',
			'Add files' : 'Adicionar arquivos',
			'Start upload':'Enviar',
			'Stop current upload' : 'Parar envio',
			'Start uploading queue' : 'Enviar',
			'Drag files here.' : 'Arraste suas imagens aqui'
	});
	$('.plupload').pluploadQueue({
		// General settings
		runtimes : 'gears,flash,silverlight,browserplus,html5',
		url : '<?php echo $this->Html->url(array('controller'=>'Imagens','action'=>'add'));?>',
		max_file_size : '<?php echo $max_size;?>mb',
		unique_names : false,

		// Specify what files to browse for
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"}//,
			//{title : "Zip files", extensions : "zip"}
		],

		// Flash settings
		flash_swf_url : '<?php echo $this->Html->url('/imgadmin/js');?>/plupload/plupload.flash.swf',

		// Silverlight settings
		silverlight_xap_url : '<?php echo $this->Html->url('/imgadmin/js');?>/plupload/plupload.silverlight.xap',
		
		preinit : {
            Init: function(up, info) {
                bootstrap();
            },
			FileUploaded: function(up, file, info) {
				var obj = JSON.parse(info.response);
				var html = '<a href="<?php echo $this->Html->url('/img');?>/<?php echo $pasta;?>/medio/'+obj.cleanFileName+'" class="img" data-url="'+obj.cleanFileName+'"><img src="<?php echo $this->Html->url('/img');?>/<?php echo $pasta;?>/thumb/'+obj.cleanFileName+'" class="img-polaroid" alt=""></a>';
				$('.media').append(html);
			},
        }

	});
	function bootstrap(){
		$(".plupload_header").remove();
		$(".plupload_progress_container").addClass("progress").addClass('progress-striped');
		$(".plupload_progress_bar").addClass("bar");
		$(".plupload_button").each(function(e){
			if($(this).hasClass("plupload_add")){
				$(this).attr("class", 'btn btn-primary pl_add btn-small');
			} else {
				$(this).attr("class", 'btn btn-success pl_start btn-small');
			}
		});
	}
	$('a.img').live('mouseover mouseout mousemove click',function(e){
		if(e.type == 'mouseover'){
			$('body').append('<div id="image_preview" style="position: absolute" class="img-polaroid"><img src="'+$(this).attr('href')+'"/></div>');
			$("#image_preview").fadeIn();
		} else if(e.type == 'mouseout') {
			$("#image_preview").remove();
		} else if(e.type == 'mousemove'){
			$("#image_preview").css({
				top:e.pageY+10+"px",
				left:e.pageX+10+"px"
			});
		} else if(e.type == 'click'){
			$("#image_preview").remove();
			seleciona($(this).data('url'));
			return false;
		}
	});
  }
	  function seleciona(url){
		<?php if(isset($_GET['CKEditor'])||isset($_GET['elementid'])):?>
		$('#myModal').modal();
		$('#myModal .tamanho').click(function(){
			<?php if(isset($_GET['CKEditor'])):?>
				url = '<?php echo $cms['base_url'];?>/img/<?php echo $pasta;?>/'+$(this).data('pasta')+'/'+url;
				funcNum = GetUrlParam('CKEditorFuncNum') ;
				window.top.opener.CKEDITOR.tools.callFunction( funcNum, url);
				window.top.close() ;
				window.top.opener.focus() ;
				function GetUrlParam( paramName )
				{
					var oRegex = new RegExp( '[\?&]' + paramName + '=([^&]+)', 'i' ) ;
					var oMatch = oRegex.exec( window.top.location.search ) ;
				 
					if ( oMatch && oMatch.length > 1 ){
						return decodeURIComponent( oMatch[1] ) ;
					}else{
							return '' ;
					}
				
					return false;
				}
			<?php elseif(isset($_GET['elementid'])):?>
			
				window.opener.$('#<?php echo $_GET['elementid'];?>').val(url);
				window.opener.$('#<?php echo $_GET['elementid'];?>-bnt').addClass('btn-success').text('Imagem selecionada');
				window.close();
			<?php endif;?>
			return false;
		});
		<?php endif;?>
	  }
	  $('.remove a').live('click',function(){
		  var este = $(this);
		  if(confirm('Tem certeza que quer apagar esta imagem?')){
			$.ajax({
				url:'<?php echo $cms['base_url']?>'+'/Imagens/remove/'+este.data('url'),
				success:function(){
					este.parent().parent().hide('slow',function(){
						este.parent().parent().remove();
					});
				}
			});
	  	  }
		  return false;
	  });
	  if($('.ttip').length>0){
			$('.ttip').tooltip();
		}
  });
  </script>
  <div id="debug"></div>
  </body>
</html>