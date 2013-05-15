// JavaScript Document

$(function(){
			
     base_url='<?php echo $this->Html->url('/');?>';
	//Se existir algum lcampo que quer uma imagem
	if($('.img-select').length>0){
	
	//Adiciona o Botão Para Selecionar a imagem
		$('.img-select').each(function(){
		$(this).before('<button type="button" id="'+$(this).attr('id')+'-bnt" class="btn btn-img-select" data-id="'+$(this).attr('id')+'" >Selecionar uma imagem</button>');
		});
		
		//Escolhe a foto
		$('.btn-img-select').click(function(){
		window.open(base_url+'imgadmin/Imagens/add?elementid='+$(this).data('id'),'_blank','height=400,width=800');
		return false;
		});
	
	}
});