(function(d){

  $(d).ready(function(){
    //lanza modal upload photo perfil
    $('#content-data').on({
          click:function(){
            tmpUploadImage('upload photo Profile','profile');
          }
    },'#jcode-photoPerfil');
    //------------------------------------------------------------------
    //Lanza modal para cargar fotos al catalogo personal de imagenes
	  $('#content-data').on({
          click:function(){
            tmpUploadImage('upload photo catalogo','catalogo');
          }
	  },'.btn-load-photos');
    //-------------------------------------------------------------------------

    //Lanza modal que carga imagenes personales del catalogo para que selecciona una imagen para que remplace la de portada
    $('#content-data').on({
          click:function(e){
            e.preventDefault();
            tmpUploadImage('Upload a cover image','portada');
          }
    },'#jc-portadaUploadPhoto');
    //-------------------------------------------------------------------------

    //Lanza modal para eliminar fotos de catalogo personal del usuario
    $('#content-data').on({
          click:function(e){
            e.preventDefault();
            var token = this.getAttribute('data');
            var html ='<div class="media">';
                html+='       <div class="media-left"><i class="fa fa-trash-o fa-4x" aria-hidden="true"></i></div>';
                html+='       <div class="media-body">';
                html+='         <p>Are you sure you want to delete this photo?</p>';
                html+='         <button class="btn btn-primary btn-sm" data="'+token+'" id="jc-finalDeleteImg">Confirm</button> <button class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>';    
                html+='       </div>';
                html+='  </div>';
            libereModal(html,'sm','white','static');
          }
    },'#jc-delete-photo');
    //-------------------------------------------------------------------------

    //Contolador photo lanza modal para actualizar foto de perfil seleccionando fotos de catalogo personal
    $('#content-data').on({
          click:function(e){
            e.preventDefault();
            var token = this.getAttribute('data');
            var html ='<div class="media">';
                html+='       <div class="media-left"><i class="fa fa-camera fa-4x" aria-hidden="true"></i></div>';
                html+='       <div> Are you sure to change your profile photo?</p>';
                html+='         <button class="btn btn-primary btn-sm" data="'+token+'" id="jc-finalPerfilPhoto">Confirm</button> <button class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>';    
                html+='       </div>';
                html+='  </div>';
            libereModal(html,'sm','white','static');
          }
    },'#jc-selecPhotoPerfil');
    //-------------------------------------------------------------------------

     //controlador photos lanza modal para actualizar foto de portada seleccionando fotos del catalogo personal
    $('#content-data').on({
          click:function(e){
            e.preventDefault();
            var token = this.getAttribute('data');
            var html ='<div class="media">';
                html+='       <div class="media-left"><i class="fa fa-picture-o fa-4x" aria-hidden="true"></i></div>';
                html+='       <div> Are you sure to change your cover photo?</p>';
                html+='         <button class="btn btn-primary btn-sm" data="'+token+'" id="jc-finalPortadaPhoto">Confirm</button> <button class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>';    
                html+='       </div>';
                html+='  </div>';
            libereModal(html,'sm','white','static');
          }
    },'#jc-selecPhotoPortada');
    //--------------------------------------------------------------------------------

    //Lanza modal cargadno las imagenes del usuario para poder ser seleccionadas y usadas como portada
    $('#content-data').on({
          click:function(e){
            e.preventDefault();
            //----------------------------------------------------------------
            $('#jcode-modal-content').html('<center><i class="fa-2x fa fa-spinner fa-spin" aria-hidden="true"></i></center>');
            var html ='';
            var params = {'params':'foo=all'};
            var result = dataSingleAjax('GET',false,'photos/myPictures',params);
            result.success(function(json){
                    var obj = $.parseJSON(json);

                    html='<div class="row">';
                    html+='<div class="panel panel-default">';
                    html+='<div class="panel-heading"><h3>Choose one of my photos <i style="margin-top:-15px;" data-dismiss="modal" class="pull-right fa-2x fa fa-times" aria-hidden="true"></i></h3></div>';
                    html+='<div class="panel-body" style="overflow-y:auto;height:500px;">';
                    if (obj.success ==1 ) {
                        for( var i=0; i<obj.data.length; i++){

                            html+='<div class="col-xs-12 col-sm-6 col-md-3">\
                                     <a href="#" class="jc-photos-user" data="'+obj.data[i]['iu_id']+'">\
                                        <div class="bg-img thumbnail" style="width: 100%;height: 150px;background-image: url(\'views/assets/images/users/'+obj.data[i]['iu_img']+'\')"></div>\
                                     </a>\
                                    </div>';
                        };
                    }else{
                             html+='<div class="media">\
                                      <div class="media-left media-middle">\
                                        <i class="fa fa-picture-o fa-5x" aria-hidden="true"></i>\
                                      </div>\
                                      <div class="media-body">\
                                        <br>\
                                        <h4 class="media-heading">'+obj.message+'</h4>\
                                      </div>\
                                    </div>';
                    }
                    html+='</div>';
                    html+='<div class="panel-footer"><button class="btn btn-primary" data-dismiss="modal">Cancel</button></div>';
                    html+='</div>';
                    html+='</div>';
                    $('#jcode-modal-content').html(html);

            });
            libereModal(html,'lg','transparent','static');
            //----------------------------------------------------------------
        }
    },'#jc-portadaSelectPhoto');
//-----------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------


    //Controlador Photos proceso interno de eliminacion de imagen del catalogo
    $('#jcode-modal-content').on({
          click:function(e){
              e.preventDefault();
              var token = this.getAttribute('data');
              $('#jcode-modal-content').html('<center><i class="fa fa-spinner fa-pulse fa-4x" aria-hidden="true"></i><br>Going up, wait, please</center>');
              var promise = dataSingleAjax('post',false,'photo/deletePhoto',{'params':'token-user='+token});
              promise.success(function(json){
                  var obj = $.parseJSON(json);
                  if (obj.success ==1) {
                     $('#jcode-modal-content').html('<center><i class="fa fa-smile-o fa-4x" aria-hidden="true"></i><br>'+obj.message+'</center>');
                      setTimeout(function(){
                      document.location.reload();
                    },2000);
                  }else{
                   $('#jcode-modal-content').html('<center><i class="fa fa-frown-o fa-4x" aria-hidden="true"></i><br>'+obj.message+'</center>');
                  }
               });
          }
    },'#jc-finalDeleteImg');
    //-------------------------------------------------------------------------

    //Controlador photos proceso interno actualizacion foto perfil por seleccion
    $('#jcode-modal-content').on({
          click:function(e){
              e.preventDefault();
              var token = this.getAttribute('data');
              updatePhotoSelection(token, 'perfil');
              
          }
    },'#jc-finalPerfilPhoto');
     //-------------------------------------------------------------------------
    
     //Controlador photos proceso interno de actualizacion foto portada por seleccion
    $('#jcode-modal-content').on({
          click:function(e){
              e.preventDefault();
              var token = this.getAttribute('data');
              updatePhotoSelection(token, 'portada');
              
          }
    },'#jc-finalPortadaPhoto');
     //-------------------------------------------------------------------------


    //Controlador profile y photos sube imagenes provinientes de los modales  cuando se tiene que subir una imagen nueva
	  $('#jcode-modal-content').on({
          submit:function(e){
              e.preventDefault();
              var type = this.getAttribute('data');
              $('#photo-message').html('<center><i class="fa fa-spinner fa-pulse fa-4x" aria-hidden="true"></i><br>Going up, wait, please</center>');
              var promise = uploadSinglePhoto(type,this,'photosProfile');
              promise.success(function(json){
                  var obj = $.parseJSON(json);
                  if (obj.success ==1) {
                     $('#modal-width').addClass('modal-sm');
                     $('#photo-message').removeClass('alert alert-danger').addClass('alert alert-success').html('<center><i class="fa fa-smile-o fa-4x" aria-hidden="true"></i><br>Uploaded successfully</center>');
                     setTimeout(function(){
                      document.location.reload();
                     },500);
                  }else{
                    $('#photo-message').removeClass('alert alert-success').addClass('alert alert-danger').html('<center>'+obj.message+'</center>');
                  }
               });
          }
	  },'#upload-img');
	   //-------------------------------------------------------------------------
	   
  });

//Controlador profile actualiza fotos por seleccion de modal de las imagenes de catalogo
   $('#jcode-modal-content').on('click','.jc-photos-user',function(e){
      e.preventDefault();
      var token = this.getAttribute('data');
      updatePhotoSelection(token, 'portada');
   });	
	
//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


//--------------------------------------------------------------
$('#jcode-modal-content').on('click','#file-select', function(e) {
		     e.preventDefault();
		    $('#photoProfile').click();
});
//---------------------------------------------------------------
$('#jcode-modal-content').on('change','input[type=file]',function() {
    var file = (this.files[0].name).toString();
    var reader = new FileReader();
    
    $('#file-info').text('');
    $('#file-info').text(file);
    
     reader.onload = function (e) {
         $('#file-select img').attr('src', e.target.result);
	 }
     reader.readAsDataURL(this.files[0]);
});

//show and hide bottom edit images-----------------------------
$('.jc-photo-user-p').mouseover(function() {
    $(this).children('div').show();
   
});
$('.jc-photo-user-p').mouseout(function() {
    $(this).children('div').hide();
    
});
//-------------------------------------------------------------
})(document)

//template modal subir imagenes de pc
function tmpUploadImage(title,type){
  var html='\
                 <div class="row">\
                 <div class="col-xs-12">\
                   <h3>'+title+'<i style="float:right" class="fa fa-times" data-dismiss="modal" aria-hidden="true"></i></h3>\
                   <div id="photo-message"></div>\
                    <form method="post" role ="form" id="upload-img" data="'+type+'" enctype="multipart/form-data">\
                      <center>\
                        <div id="file-select" style="cursor:pointer">\
                          <img src="views/assets/img/picture.jpg" class="img-responsive">\
                          <div id="file-info"></div>\
                        </div>\
                        <br>\
                        <input type="file" id="photoProfile" name="jc-picture"  class="hidden form-control">\
                        <button id="upload-button" class="btn btn-primary btn-lg">To post</button>\
                      </center>\
                    </form>\
                 </div>\
               </div>\
            ';
            /*$('#jcode-modal-content').html(html);
            $('#jcode-modal-multiple').modal({modal:'show',backdrop:'static'});*/
            libereModal(html,'sm','#fafafa','static');
            $('#upload-img')[0].reset();
            $('#file-select img').attr('src', 'views/assets/img/picture.jpg');
            $('#file-info').text('');
}

//funcion que actualiza las imagenes seleccionadas del catalogo tanto en controlador Profile y photos
function updatePhotoSelection(token, type){

      preloadAjaxModal('#jcode-modal-content');
      var promise = dataSingleAjax('POST',false,'photo/updateSelecion',{'params':'token-type='+type+'&token-user='+token});
      promise.success(function(json){
        var obj = $.parseJSON(json);
        if (obj.success ==1) {
          
         $('#modal-width').removeClass('modal-lg').addClass('modal-sm');
         $('#modal-content').css('backgroundColor','white');
         
        /* if (type =='portada' && typeof(portada) == "undefined") {
             messageAjaxModal('#jcode-modal-content',obj.message,'smile');
             setTimeout(function(){
               hidenModal();
             },2000);
         }else{*/
            messageAjaxModal('#jcode-modal-content',obj.message,'smile');
            setTimeout(function(){
               document.location.reload();
             },700);
        // }
        }else{
           messageAjaxModal('#jcode-modal-content',obj.message,'sad');
        }
    });
}