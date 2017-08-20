(function(d,cha){
  //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  //::::::::::::::::::::::FUNCIONES INFORMAACION ACERCA DE LAS OPCIONES DE BUSQUEDA:::::::::::
  $('#jcode-left-osearch, #jcode-searchOptions-csetup').click(function(){
     $.ajax({
       type: 'GET',
       url:'ajcode/search/osearchInfo',
       data:'foo=all',
       beforeSend:function(){
         $('#jcode-modal-content').html('<center><i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i></center>');
       },
       success:function(data){
        var obj = $.parseJSON(data);
        var html='';
          html+='<form id="jcode-search-osearchSend">';
          html+='<h2>My search Options</h2><hr>';
          html+='            <div class="col-xs-12">';
          html+='              Me';
          html+='            <select class="form-control" name="jc-option" id="jc-option"> <option value="1" '+(obj.dsingle.option == 1 ? 'selected=""' : '' )+'>looking for a friendship</option><option value="2" '+(obj.dsingle.option == 2 ? 'selected=""' : '' )+'>looking for a relationship</option><option value="3" '+(obj.dsingle.option == 3 ? 'selected=""' : '' )+'>looking for something casual</option> </select>';
          html+='              <br>';
          html+='            </div>';
          html+='            <div class="col-xs-12">';
          html+='             Whit a:';
          html+='            <select class="form-control" name="jc-preference" id="jc-preference"><option value="1" '+(obj.dsingle.preference == 1 ? 'selected=""' : '' )+'>with a man</option><option value="2" '+(obj.dsingle.preference == 2 ? 'selected=""' : '' )+'>with a woman</option><option value="3" '+(obj.dsingle.preference == 3 ? 'selected=""' : '' )+'>with both</option></select>';
          html+='            <br>';
          html+='            In the age range:';
          html+='            </div>';
          html+='            <div class="col-xs-12 col-md-6"><input type="number" name="jc-eini" id="jc-eini"  class="form-control" value="'+obj.dsingle.eini+'"></div>';
          html+='            <div class="col-xs-12 col-md-6"><input type="number" name="jc-eend" id="jc-eend" class="form-control" value="'+obj.dsingle.eend+'"></div>';
          html+='            <div class="col-xs-12">';
          html+='              <br>';
          html+='               Zodiac sign:';
          html+='             <select name="jc-zodiacal" id="jc-zodiacal" class="form-control">';
          html+='              <option value="0">All Zodiacal</option>';
                              $.each(obj.signs,function(key,val){
          html+='              <option value="'+val['sz_id']+'" '+(obj.dsingle.signo == val['sz_id'] ? 'selected=""' : '' )+'>'+val['sz_name']+'</option>';
                              });
          html+='             </select>';
          html+='            </div>';
          html+='            <div class="col-xs-12">';
          html+='             <br>';
          html+='             State:';
          html+='             <select name="jc-state" id="jc-state" class="form-control">';
          html+='              <option value="0">All states</option>';
                              $.each(obj.aestate,function(key,val){
          html+='              <option value="'+val['st_id']+'" '+(obj.dsingle.state == val['st_id'] ? 'selected=""' : '' )+'>'+val['st_name']+'</option>';
                              });
          html+='             </select>';
          html+='             </div>';
          html+='             <div class="col-xs-12">';
          html+='             <br>';
          html+='             City:';
          html+='             <select name="jc-city" id="jc-city" class="form-control">';
          html+='             <option value="0">All citys</option>';
                              $.each(obj.acity,function(key,val){
          html+='              <option value="'+val['ci_id']+'" '+(obj.dsingle.city == val['ci_id'] ? 'selected=""' : '' )+'>'+val['ci_name']+'</option>';
                              });
          html+='             </select>';
          html+='             </div>';
          html+='             <div class="clearfix"></div>';
          html+='             <br>';
          html+='             <center>'; 
          html+='             <div id="jcode-osearch-message"></div>'; 
          html+='             <button class="btn btn-primary">SEND OPTIONS</button>';
          html+='             <button class="btn btn-default" data-dismiss="modal">CANCEL</button>';
          html+='             </center>';
          html+='         </form>';
          $('#jcode-modal-content').html(html);
       }
     });

     $('#modal-width').removeClass('modal-lg').addClass('modal-sm');
     $('#modal-content').css('backgroundColor','white');
     $('#jcode-modal-multiple').modal({modal:'show',backdrop:'static'});
  });
  //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

   //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  //::::::::::::::::::::::UPDATE OPCIONES DE BUSQUEDA POR USUARIO::::::::::::::::::::::::::::::
  $('#modal-content').on('submit','#jcode-search-osearchSend',function(e){
     e.preventDefault();
     
     $.ajax({
       type: 'POST',
       url:'ajcode/search/updateSearchOption',
       data:$(this).serialize(),
       beforeSend:function(){
         $('#jcode-osearch-message').html('<center><i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i></center>');
       },
       success:function(data){
         var data = $.parseJSON(data);
         if(data.success == 1){
           $('#jcode-osearch-message').addClass('alert alert-success').html(data.message);
           setTimeout(function(){
             hidenModal();

             if (typeof(controlOseach) != 'undefined') {
              document.location.reload();
             }else{
              var iconOption;
              var control = $('#jc-preference option:selected').text();
              switch(control){
                case 'with a woman':
                  iconOption ='<i class="c-green-b font-xs fa fa-male""></i>';  
                break;
                  
                case 'with a man':
                  iconOption ='<i class="c-green-b font-xs fa fa-female""></i>';
                break;

                case 'with both':
                   iconOption ='<i class="c-green-b font-xs fa fa-male""></i> <i class="c-green-b font-xs fa fa-female""></i>';  
                break;
              }
              //reload static osearch
              var html='<table class="table table-condensed">\
                        <tr><td class="text-center no-border"><i class="c-green-b font-xs fa fa-search" aria-hidden="true"></i></td><td class="no-border"> '+$('#jc-option option:selected').text()+'</td></tr>\
                        <tr><td class="text-center no-border">'+iconOption+'</td><td class="no-border">'+$('#jc-preference option:selected').text()+'</td></tr>\
                        <tr><td class="text-center no-border"><i class="c-green-b font-xs fa fa-circle-o-notch" aria-hidden="true"></i></td><td class="no-border"> Intro '+$('#jc-eini').val()+' and  '+$('#jc-eend').val()+' Ages</td></tr>\
                        <tr><td class="text-center no-border"><i class="c-green-b font-xs fa fa-balance-scale" aria-hidden="true"></i></td><td class="no-border"> '+$('#jc-zodiacal option:selected').text()+'</td></tr>\
                        <tr><td class="text-center no-border"><i class="c-green-b font-xs fa fa-map-marker" aria-hidden="true"></i></td><td class="no-border"> '+$('#jc-state option:selected').text()+'</td></tr>\
                        <tr><td class="text-center no-border"><i class="c-green-b font-xs fa fa-street-view" aria-hidden="true"></i></td><td class="no-border"> '+$('#jc-city option:selected').text()+'</td></tr></table>';
              $('#jcode-osearch-static').html(html);
             }

           },2500);
         
         }else{
          $('#jcode-osearch-message').removeClass('alert-success').addClass('alert-danger').html(data.message);
         }
       }
     });
  });
  //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

  //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  //::::::::::::::::::::::FUNCIONES PARA CONFIRMAR NOTIFICACION:::::::::::::::::::::::::::::::
        $('#modal_notifications').on('click','.ch_acept',function(){

               $.post('ajcode/chat/chatConfirm',{n_token:$(this).attr('data-token'),n_mode:$(this).attr('data-mode')}).done(function(string){
                    var data = $.parseJSON(string);
                    if(data.success == 1){
                    cha.loadChat();
                    conn.send(JSON.stringify({action:1,toa:data.toa,type:1,function:0} ));
                   }else alert(data.message);
               });
        });
  //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

  //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  //::::::::::::::::::::::FUNCIONES PARA CANCELAR NOTIFICAION:::::::::::::::::::::::::::::::::
  $('#modal-content').on('click','.jcode-cancelNoti',function(){
     preloadAjaxModal('#jcode-modal-content','3x');
      var promise = $.post('ajcode/chat/chatCancel',{token:this.getAttribute('data')});
      promise.done(function(json){
          var obj = $.parseJSON(json);
          if (obj.success ==1) {
            conn . send ( JSON . stringify ( {action:'notification',token:obj.data,type:1} )); 
            messageAjaxModal('#jcode-modal-content',obj.message,'smile');
          }else{
            messageAjaxModal('#jcode-modal-content',obj.message,'sad');
          }
     });
  });
  //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

  //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  //::::::::::::::::::::::DESPLIEGA MODALES FAVORITOS Y NOTIFICACIONES::::::::::::::::::::::::
 

     //--Despliega el box de personas de mi interes----------------------------
        $('#jcode_favoritos').click(function(e){
              $.get('ajcode/top/myinteres',{foo:'all'}).done(function(json){
                 var obj = $.parseJSON(json);
                  var html ='';
                    if (obj.success == 1) {
                      for(var i=0;i<obj.data.length;i++){
                          html+='<div class="media">\
                                  <div class="media-left">\
                                    <a href="profile/'+obj.data[i]['u_id']+'">\
                                      <img class="media-object img-circle" src="views/assets/images/users/'+(obj.data[i]['u_img']!='' ? obj.data[i]['u_img'] : 'no-img.jpg' )+'" alt="..." width="55">\
                                    </a>\
                                  </div>\
                                  <div class="media-body">\
                                    <a href="profile/'+obj.data[i]['u_id']+'"><h4 class="media-heading"></b>'+obj.data[i]['u_nombre']+'</b></h4></a>\
                                    <ol class="breadcrumb">\
                                      <li>'+obj.data[i]['u_fecnac']+' years</li>\
                                      <li>'+obj.data[i]['f_estado']+'</li>\
                                      <li class="redb">'+obj.data[i]['f_zodiacal']+'</li>\
                                    </ol>\
                                  </div>\
                                </div>';
                      }
                    }else html+='<center> <i class="fa fa-bell-slash fa-4x" aria-hidden="true" style="color:#77777"></i><p>'+obj.message+'</p></center>';
                    $('[data-toggle="tooltip"]').tooltip();
                    $('#pone_favoritos').html(html);
              });
              $('#modal_favoritos').modal('show');
        });
     //------------------------------------------------------------------------

     //--Despliega el box notificaciones----------------------------
        $('#jcode_notification').click(function(e){
          //limpiamos box de notificaciones
           $(this).removeClass('b-crimson');
          //----------------------------------------
          $.get('ajcode/top/notifications',{foo:'all'}).done(function(json){
             var obj = $.parseJSON(json);
             var html ='';
                if (obj.success == 1) {
                  for(var i=0;i<obj.data.length;i++){
                      html+='<div class="media">\
                              <div class="media-left">\
                                <a href="#">\
                                  <img class="media-object img-circle" src="views/assets/images/users/'+(obj.data[i]['u_img']!='' ? obj.data[i]['u_img'] : 'no-img.jpg' )+'" alt="..." width="55">\
                                </a>\
                              </div>\
                              <div class="media-body">\
                                <h4 class="media-heading"></b>'+obj.data[i]['n_remite']+'</b> <small>'+obj.data[i]['n_message']+'</small></h4>';
                               if(obj.data[i]['sl_tipo']==0 && obj.data[i]['sl_status']==0){
                      html+='      <button data-token="'+obj.data[i]['n_id']+'" data-mode="0" class="ch_acept btn btn-xs btn-info">Agree</button>&nbsp<button data-token="'+obj.data[i]['n_id']+'" data-mode="1" class="ch_cancel btn btn-xs btn-danger">Remove</button>';
                               }
                               if(obj.data[i]['sl_tipo']==0 && obj.data[i]['sl_status']==1){
                      html+='<p>Petition accepted</p>';
                               }
                      html+='   </div>\
                             </div>';
                  }
                }else html+='<center> <i class="fa fa-bell-slash fa-4x" aria-hidden="true" style="color:#77777"></i><p>'+obj.message+'</p></center>';
                $('[data-toggle="tooltip"]').tooltip();
                $('#pone_notifications').html(html);
          });
          $('#modal_notifications').modal('show');
        });
     //-------------------------------------------------------------------------
  //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
})(document,CHA)

