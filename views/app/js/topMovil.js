(function(d){
  
  $('#me'+toke_main).click(function(e){
  	 $(this).removeClass('b-crimson');
  	 $('#jcode-load-chat').html('');
  	 var html ='';
  	    //-----------------------------------------------------------------------------------------------------------
            
            var promise = dataSingleAjax('POST',false,'loadChat',{'params':'foo=all'}); 
	        preloadAjaxModal('#jcode-movil-chat','2x')

	         promise.done(function(json){
               var obj = $.parseJSON(json);
                  if(obj.success ==  1) {
                     for(var i=0; i< obj.data.length; i++){
                          html+='<ul class="jcode-token-chat jc-appTopMenu" data="'+obj.data[i]['u_token']+'" data-unique="'+obj.data[i]['ch_code']+'" style="cursor:pointer;">';
                          html+='<li>';
                          html+='<div style="width: 35px; height: 35px;background-image: url(views/assets/images/users/'+(obj.data[i]['u_img'] == null ? 'no-img.jpg' : obj.data[i]['u_img'] )+')" class="bg-img img-circle '+(obj.data[i]['u_estado'] ==1 ? 'border-green' :'border-white')+'">\
                                     <div id="ch'+obj.data[i]['u_token']+'" style="margin-top:15px;margin-left:20px;">'+obj.data[i]['u_mysms']+'</div>\
                                 </div>';
                          html+='</li>';
                          html+= '<li style="font-size:12px;" class="margin-lateral">'+obj.data[i]['u_nombre']+'</li>';
                          html+='</ul>';
                       };
                         $('#jcode-movil-chat').html(html);
                  } else {
                         $('#jcode-movil-chat').html(obj.message);
                  }
            }); 
	     //------------------------------------------------------------------------------------------------------------  
    
  });
  //----------------------------------------------------------------
  //toogle menu user
  $('#jc-toggle-menuUser').click(function(){
    $('.jc-mu').toggle('fast',function(){
         $(this).removeClass('hidden-xs');
    });
  });
})(document)