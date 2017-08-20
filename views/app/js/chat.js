(function(d){
  
  CHA={
    init:function(){
        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        //:::::::::::::::::::::::::::::.CREA BOX DE CHAT::::::::::::::::::::::::::::::::::::::::::

          $('#jcode-load-chat').on('click','.jcode-token-chat',function(){
             //pedidmos datos para rellenar las cajas de chat
             console.log($(this).attr('data'));
             var token = $(this).attr('data');
             var name = $(this).children('li:eq(1)').text();
             CHA.createBoxChat(token,name);
             
          });

          $('#jcode-movil-chat').on('click','.jcode-token-chat',function(){
             //pedidmos datos para rellenar las cajas de chat
             var token = $(this).attr('data');
             var tokenUnique = $(this).attr('data-unique');
             CHA.createBoxChat(token,tokenUnique);
          });
        //-------------------------------------------------------------------------------------

        //:::::::::::::::::::::::::::::::::::ENVIA MENSAJES DE CHAT::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
          $('#ch_text').on('keyup',function(e){
                    e.preventDefault();
                    if (conn == undefined) return false;
                    if (e.keyCode != 13) return false;
                        var u_message = $(this);
                        if (u_message.val().length<=1) return false;
                        $('#write').removeClass('show').addClass('hide');
                        var tob = this.getAttribute('data-token');
                        var toa = $('#pone_sms').attr('data-token');
                        var msg ={txt:$.trim(u_message.val()),date: moment().format('hh:mm a')}
                        addMessageMe(msg); 
                        u_message.val(' ');
                        $.post('ajcode/chat/sendMessage',{token:toa,string:msg.txt}).done(function(json){
                           var data = $.parseJSON(json);
                           if(data.success == 1) {
                                var pmw ={action:0,toa : toa,tob : tob,txt : msg.txt,date: msg.date,name:name_main,img:data.img}
                                conn.send(JSON.stringify(pmw)); 
                           }else alert(data.message);
                        });
          });
        //:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        //::::::::::::::::::::::::::::::::::::Envia solicitud de chat a un usuario en particular::::::::::::::
          $('#jcode-modal-content').on('click','#jcode-init-chat',function(e){
                e.preventDefault();
                var user = this.getAttribute('data');
                
                $.post('ajcode/chat/solicite',{'token-user':user}).done(function(json){
                       var obj = $.parseJSON(json);
                       if(obj.success == 1){
                              conn.send(JSON.stringify({action:1,toa:user,type:1} )); 
                              messageAjaxModal('#jcode-modal-content',obj.message,'smile');
                       }else messageAjaxModal('#jcode-modal-content',obj.message,'sad');
                });
          });
        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        //::::::::::::::::::::::::::BORRA LOS BOXES  DE CHATS
          $('#ch_panel').on('click','.jcode-chat-remove',function(){
              $('#ch_panel').addClass('hidden');
          });
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    },loadChat:function(){
      $('#jcode-movil-chat').html('');
        preloadAjaxModal('#jcode-load-chat','2x');
        var html='';
        $.get('ajcode/loadChat',{'params':'foo=all'}).done(function(json){
               var obj = $.parseJSON(json);
               
                  if(obj.success ==  1) {
                     for(var i=0; i< obj.data.length; i++){
                          html+='<ul class="jcode-token-chat jc-appTopMenu" data="'+obj.data[i]['u_id']+'" style="cursor:pointer;">';
                          html+='<li>';
                          html+='<div style="width: 35px; height: 35px;background-image: url(views/assets/images/users/'+(obj.data[i]['u_img'] == null ? 'no-img.jpg' : obj.data[i]['u_img'] )+')" class="bg-img img-circle '+(obj.data[i]['u_estado'] ==1 ? 'border-green' :'border-white')+'">\
                                     <div id="ch'+obj.data[i]['u_id']+'" style="margin-top:15px;margin-left:20px;">'+obj.data[i]['u_mysms']+'</div>\
                                 </div>';
                          html+='</li>';
                          html+= '<li style="font-size:12px;" class="margin-lateral">'+obj.data[i]['u_nombre']+'</li>';
                          html+='</ul>';
                       };
                        $('#jcode-load-chat').html(html);
                  } else {
                        $('#jcode-load-chat').html(obj.message);
                  }
        });
    },
    createBoxChat:function(token,name){
       var html ='';
       $('#ch'+token+'>i').remove();
       $('#pone_sms').attr('data-token',token);
       $('#ch_text').attr('data-token',toke_main);
       $('#ch_name').text(name)
  
       //----------------------------------------------------------
              $.get('ajcode/chat/content',{token:token}).done(function(json){
               var data=$.parseJSON(json);
               if (data.success =1) {   
                     for(var i=0; i < data.data.length; i++){
                      if(toke_main==data.data[i]['msj_id_user']){
                          html+='<div class="media">';
                          html+='          <div class="media-left">';
                          html+='              <div style="width: 30px; height: 30px;background-image: url(views/assets/images/users/'+data.data[i]['msj_img']+')" class="bg-img img-circle"></div>';
                          html+='          </div>';
                          html+='          <div class="media-body">';
                          html+='              <div class="mensaje b-blue white" style="padding:5px;border-radius:5px; word-wrap: break-word;width:auto">';
                          html+='                    <div class="contenido">'+data.data[i]['msj_message']+'</div>';
                          html+='                    <small style="font-size:11px;">Submitted '+data.data[i]['msj_date']+'</small>';
                          html+='              </div>';
                          html+='          </div>';
                          html+='</div>';
                      }else{
                          html+='<div class="media">';
                          html+='          <div class="media-body">';
                          html+='              <div class="text-right b-silver black" style="padding:5px;border-radius:5px; word-wrap: break-word; ">';
                          html+='                    <div class="contenido">'+data.data[i]['msj_message']+'</div>';
                          html+='                    <small style="font-size:11px;">Submitted '+data.data[i]['msj_date']+'</small>';
                          html+='              </div>';
                          html+='          </div>';
                          html+='          <div class="media-right">';
                          html+='              <div style="width: 30px; height: 30px;background-image: url(views/assets/images/users/'+data.data[i]['msj_img']+')" class="bg-img img-circle"></div>';
                          html+='          </div>';
                          html+='</div>';
                      }
                     }          
                }
               
            $('#pone_sms').html(html); 
            $('#ch_sms').focus();
            $('#pone_sms'). animate ({  scrollTop :  $ ( '#pone_sms')[ 0 ]. scrollHeight },   1000 ); 
            $('#ch_panel').removeClass('hidden');
            //------------------------------------------------
          });
    }
  }
  //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

CHA.init();
CHA.loadChat();
//-------------------------------------------------------------------------------------
})(document)

