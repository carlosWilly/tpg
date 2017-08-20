//FUNCIONES METRICES DE ACCION MULTIPLE PARA APLICACION VARIADA
  
  //-----------------------------------------------------------------
  /**
    *function global oculta y limpia modales
    *@return void. 
    *
  */



  function hidenModal(){
    $('#jcode-modal-multiple').modal('hide');
        $('#jcode-modal-multiple').on('hidden.bs.modal',function(){
          $('jcode-modal-content').html('');
        }); 
  }

 //-----------------------------------------------------------------
  /**
    *Function que simplifica ajax de envio de ficheros por lo general  imagenes
    *@param type string.
    *@param  form string include # external o this si se trata del mismo form en esencia. 
    *@param  route string de carga api o fichero segun corresponda. 
    *@return ajax;
    *
  */
function uploadSinglePhoto(type,form,route) {
        var formData = new FormData($(form)[0]);
        formData.append('p_type',type);
  return $.ajax({
        type: 'POST',
        url : 'ajcode/'+route,
        data:formData,
        contentType:false,
        processData:false,
       });
}
//-----------------------------------------------------------------
  /**
    *Function que simplifica ajax de envio de datos por lo general forms y data simple
    *@param  method GET,POST,MAP,DELETE
    *@param  bool valor boleano true si se carga un formulario, false si se enviaran datos individuales
    *@param  route ruta de api o fichero
    *@param  data array que contiene las opciones  si es true o false
    *@return ajax;
    *
  */
function dataSingleAjax(method,bool,route,data){
  //console.log(bool == true ? $(data.form).serialize() : data.params);
  return $.ajax({
      type:method,
      url:'ajcode/'+route,
      data:bool == true ? $(data.form).serialize() : data.params
  });
}

//-----------------------------------------------------------------
  /**
    *Function que simplifica ajax de envio aa rutas absolutas
    *@param  method GET,POST,MAP,DELETE
    *@param  bool valor boleano true si se carga un formulario, false si se enviaran datos individuales
    *@param  route ruta de api o fichero
    *@param  data array que contiene las opciones  si es true o false
    *@return ajax;
    *
  */
function absoluteDataAjax(method,bool,route,data){
  //console.log(bool == true ? $(data.form).serialize() : data.params);
  return $.ajax({
      type:method,
      url:route,
      data:bool == true ? $(data.form).serialize() : data.params
  });
}

//-----------------------------------------------------------------
  /**
    *Function sanea el campo para general modal al mismo tiempo que genera uno nuevo
    *@param  html codigo html que contendra el modal
    *@param  with ancho enterminos de bootstrap que tendra el modal modal-lg modal-sm, none indica que se aplica ancho por default
    *@param  background indica el color  que contandra el modal
    *@param  backdrop indica si el modal sera retractil a los lick fuera de el para minimizarse o estatico par aminimizar con data-dismiss
    *@return ajax;
    *
  */
function libereModal(html,width,background,backdrop){
  $('#modal-content').css('backgroundColor',background);
   var w = $('#modal-width');

  w.removeClass('modal-lg modal-sm')+w;
  width != 'none' ? w.addClass('modal-'+width) :'';
  $('#jcode-modal-content').html(html);
  $('#jcode-modal-multiple').modal({modal:'show',backdrop:backdrop});
}

function preloadAjaxModal(idClass,size='4x'){
   return $(idClass).html('<center><i class="fa fa-spinner fa-pulse fa-'+size+'" aria-hidden="true"></i><br>Wait, please</center>');
}

function messageAjaxModal(idClass,message,type,button=null){

   return $(idClass).html('<center><i  class="fa '+(type =='smile' ? 'fa-smile-o' : 'fa-frown-o')+' fa-4x" aria-hidden="true"></i><br>'+message+''+(button==null ? '<br><button class="btn btn-primary" data-dismiss="modal">Close</button>' : "" )+'</center>');
}


function addMessage(data) {
                      var control = $('#pone_sms').attr('data-token');
                      if(control!=data.tob) return false;
                      var html='';
                      if(toke_main==data.toa){
                          html+='<div class="media">';
                          html+='          <div class="media-left">';
                          html+='              <div style="width: 30px; height: 30px;background-image: url(views/assets/images/users/'+data.img+')" class="bg-img img-circle"></div>';
                          html+='          </div>';
                          html+='          <div class="media-body">';
                          html+='              <div class="mensaje b-blue white" style="padding:5px;border-radius:5px; word-wrap: break-word;width:auto">';
                          html+='                    <div class="contenido">'+data.txt+'</div>';
                          html+='                    <small style="font-size:11px;">Submitted '+data.date+'</small>';
                          html+='              </div>';
                          html+='          </div>';
                          html+='</div>';
                      }else{
                          html+='<div class="media">';
                          html+='          <div class="media-body">';
                          html+='              <div class="text-right b-silver black" style="padding:5px;border-radius:5px; word-wrap: break-word; ">';
                          html+='                    <div class="contenido">'+data.txt+'</div>';
                          html+='                    <small style="font-size:11px;">Submitted '+data.date+'</small>';
                          html+='              </div>';
                          html+='          </div>';
                          html+='          <div class="media-right">';
                          html+='              <div style="width: 30px; height: 30px;background-image: url(views/assets/images/users/'+data.img+')" class="bg-img img-circle"></div>';
                          html+='          </div>';
                          html+='</div>';
                      }
                     //$('#'+toke_main).next().children('div').removeClass('show').addClass('hide');
                     $('#pone_sms').append(html);
                     $( '#pone_sms').animate({scrollTop:$('#pone_sms')[0].scrollHeight},1000); 
             /*else{
               startFlashTitle('(1) '+msj.blmsg.name);
              if ($('#ch'+msj.blmsg.token).is(':empty')) {
                  $('#ch'+msj.blmsg.token).html('<i class="fa fa-envelope c-crimson"></i>');
                  $('#me'+msj.tokenYou).addClass('b-crimson');
              }
             }*/
  }
function addMessageMe(data) {
                  var html='';
                      html+='<div class="media">';
                      html+='          <div class="media-left">';
                      html+='              <div style="width: 30px; height: 30px;background-image: url(views/assets/images/users/'+mage_main+')" class="bg-img img-circle"></div>';
                      html+='          </div>';
                      html+='          <div class="media-body">';
                      html+='              <div class="mensaje b-blue white" style="padding:5px;border-radius:5px; word-wrap: break-word;width:auto">';
                      html+='                    <div class="contenido">'+data.txt+'</div>';
                      html+='                    <small style="font-size:11px;">Submitted '+data.date+'</small>';
                      html+='              </div>';
                      html+='          </div>';
                      html+='</div>';
                 $('#pone_sms').append(html);
                 $( '#pone_sms'). animate ({scrollTop:$( '#pone_sms')[0].scrollHeight},1000); 
  }

function addNotification(msj){
//-----------------------------------------------------
          switch(msj.type){
            case 1:
               $('#jcode_notification').addClass('b-crimson');//notificaciones top user con la campana roja
               if(typeof msj.function != "undefined"){
                 switch(msj.function){
                    case 0:
                    CHA.loadChat();
                    break;
                 }
               }
              break;

            case 2:// online user
               $('#ch'+msj.token).parent('div').removeClass('border-green').addClass('border-white'); 
              break;
            case 3:// outline user
               $('#ch'+msj.token).parent('div').removeClass('border-white').addClass('border-green'); 
              break;
            case 4:
               var control = $('#jcode-load-chat').attr('data-conextifly');
               if ('cxtfly'+msj.token==control) {loadChat();}
              break;
            case 5://aparece y desaparece icono mensaje en el box chat lateral derecho cuando la persona envia un mensaje
               $('#'+msj.token).next().children('div').removeClass('hide').addClass('show');
              break;

          }
//------------------------------------------------------ 
}



function onLine(){
  return  navigator.onLine ? true :false;
}

//funcon parpadea titulo
(function () {
   var titleOriginal = document.title;   
   var intervalBlinkTitle = 0;
 
   // Inicia el parpadeo del título la página
   window.startFlashTitle = function (newTitle) {       
       if(intervalBlinkTitle == 0){
        document.title = 
         (document.title == titleOriginal) ? newTitle : titleOriginal;           
 
        intervalBlinkTitle = setInterval(function (){
         document.title = 
          (document.title == titleOriginal) ? newTitle : titleOriginal;           
        }, 1000);
    } 
   };
 
   // Para el parpadeo del título de la página   
   // Restablece el título
   window.stopFlashTitle = function () {       
       clearInterval(intervalBlinkTitle);
       intervalBlinkTitle = 0;
       document.title = titleOriginal;
   };
  }());
//-----------------------------------------------------------------------------

// Get Browser-Specifc Prefix
function getBrowserPrefix() {
   
  // Chequear por la propiedad sin prefijos.
  if ('hidden' in document) {
    return null;
  }
 
  // Todos los prefijos posibles.
  var browserPrefixes = ['moz', 'ms', 'o', 'webkit'];
 
  for (var i = 0; i < browserPrefixes.length; i++) {
    var prefix = browserPrefixes[i] + 'Hidden';
    if (prefix in document) {
      return browserPrefixes[i];
    }
  }
 
  // La API nos es soportada por el navegador.
  return null;
}
 
// Obtener la propiedad specífica oculta del navegador
function hiddenProperty(prefix) {
  if (prefix) {
    return prefix + 'Hidden';
  } else {
    return 'hidden';
  }
}
 
// Obtener la propiedad de estado visible del navegador
function visibilityState(prefix) {
  if (prefix) {
    return prefix + 'VisibilityState';
  } else {
    return 'visibilityState';
  }
}
 
// Obtener el evento specífico del navegador
function visibilityEvent(prefix) {
  if (prefix) {
    return prefix + 'visibilitychange';
  } else {
    return 'visibilitychange';
  }
}

// Obtener el prefijo del navegador
var prefix = getBrowserPrefix();
var hidden = hiddenProperty(prefix);
var visibilityState = visibilityState(prefix);
var visibilityEvent = visibilityEvent(prefix);
 
document.addEventListener(visibilityEvent, function(event) {
  if (!document[hidden]) {
    stopFlashTitle();
  } else {
   stopFlashTitle();
  }
});
