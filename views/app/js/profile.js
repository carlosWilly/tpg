(function(d){
    // carga procesos paraa la visualizacion de imagenes controlador Profile y Photos

	//------------------------------------------------------------------------------------
  //calculamos altura de pantalla para foto portada
  resizeCover();
  
  //lanzamodal para edicion de informaciond e ubication
   $('#jcode-edit-place').on('click', function(event) {
     event.preventDefault();
     var html='<div >\
               <div >\
                 <h3 class="text-center">Change your current location</<h3><br><br>\
                 <input id="jc-valMyLocation" type="text" class="form-control input-lg" placeholder="City, State, Country" /><br>\
                 <center><button id="jc-nowlocation-send" class="btn btn-default btn-lg">Update</button>&nbsp<button data-dismiss="modal" class="btn btn-danger btn-lg">Cancel</button></center>\
               </div>\
              </div>';
     libereModal(html,'sm','#fafafa','static');

     $('#jcode-modal-multiple').on('shown.bs.modal', function (e) {
      e.preventDefault();
        autocomplete = new google.maps.places.Autocomplete((document.getElementById('jc-valMyLocation')),{types: ['geocode']});
     })

     

   });

   $('#jcode-modal-content').on('click','#jc-nowlocation-send', function(event) {
     //event.preventDefault();
     var place = $('#jc-valMyLocation').val();
     preloadAjaxModal('#jcode-modal-content','3x');
     var promise = $.post('ajcode/user/nowubication',{nowUbication: place});
     promise.done(function(json){
       var obj = $.parseJSON(json);
       if (obj.success==1) {
         messageAjaxModal('#jcode-modal-content',obj.message,'smile');
         $('#jcode-modal-multiple').on('hidden.bs.modal', function (e) {
           location.reload();
         });  
       }else{
          messageAjaxModal('#jcode-modal-content',obj.message,'sad');
       }

     });


   });
  //--------------------------------------------------------


  //--------------------------------------------------------
    //--Lanza modal edicion informacion empleo controlador profile
	$('#content-data').on({

	     click: function(e) {
	         e.preventDefault();
             var empleo = dataSingleAjax('GET',false,'empleo/info',{'params':'foo=all'});
		     empleo.success(function(json){
                  var obj = $.parseJSON(json);
			      if(obj.success == 1) {
			      	tmpUserEmpleo(obj.data[0][0],obj.data[0][1],obj.data[0][2],obj.data[0][3]);
			      } 
		     });
		 }
	},'#jcode-edit-empleo');
	//------------------------------------------------------------------------------------

  //--Lanza modal edicion formacion profesional controlador profile
	$('#content-data').on({
     
	     click: function(e) {
	        e.preventDefault();
            var forma = dataSingleAjax('GET',false,'formacion/info',{'params':'foo=all'});
            forma.success(function(json){
	            var obj = $.parseJSON(json);
			    if(obj.success == 1) {
			     tmpUserFormation(obj.data[0][0],obj.data[0][1],obj.data[0][2],obj.data[0][3],obj.data[0][4]);
			    } 
		    });
		  }
	},'#jcode-edit-formacion');
  //---------------------------------------------------------------------------------------------------------

  //--Lanza modal edicion informacion personal controlador profile
	$('#content-data').on({
	     click: function(e) {
	        e.preventDefault();
	        var info = dataSingleAjax('GET',false,'descripcion/info',{'params':'foo=all'});
	        info.success(function(json){
	            var obj = $.parseJSON(json);
			    if(obj.success == 1) {
			     tmpUserDescription(obj.data[0][0]);
			    } 
		    });
		 }
	},'#jcode-edit-description');

  //--Lanza modal para realizar el matching en las opciones de usuario
  $('#content-data').on({
     
       click: function(e) {

        var html ='<div class="row">\
                        <div class="col-xs-12">\
                        <i data-dismiss="modal" class="fa fa-times pull-right fa-2x j-cursor" aria-hidden="true"></i><br>\
                        <h3>Hello <b class="c-crimson">'+name_main+'</b></h3>\
                        <p style="color:#767676"> let the universe, the stars and the cosmos as a whole guide you, and you can see the level of empathy you have with this person;\
                           And so you can find that friend, relationship, or adventure that tanto anhela.</p>\
                           <div id="jc-buttonMatch"  class="img-circle shadow-grall j-cursor"><i style="color:#222222" class="fa fa-heartbeat fa-4x"></i></div>\
                        </div>\
                  </div>';
        libereModal(html,'sm','#eee','static');

        /*  e.preventDefault();
            var forma = dataSingleAjax('GET',false,'formacion/info',{'params':'foo=all'});
            forma.success(function(json){
              var obj = $.parseJSON(json);
          if(obj.success == 1) {
           tmpUserFormation(obj.data[0][0],obj.data[0][1],obj.data[0][2],obj.data[0][3],obj.data[0][4]);
          } 
         });*/


      }
  },'#jcode-matchingProfile');
  //--------------------------------------------------------------------------------------------------------- 
	


//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::Conjunto de procedimientos para la barra medium menu de usuarios::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


    //--------------------------------------------------
    //Lanza modal para enviar solicitud de chat a los usuarios
	$('#content-data').on('click','#jcode-profile-chat',function(e){
       e.preventDefault();
       var require = $(this).attr('data-token'); 
       var user = $('#u_name').text();     
	     var html ='<div class="media">\
                    <div class="media-left">\
                        <div class="jcode-profile-button-single c-mostaza border-mostaza">\
                           <i class="fa-2x fa fa-comments fa-spin" aria-hidden="true"></i>\
                        </div>\
                    </div>\
                    <div class="media-body">\
                      <h4>Do you want to chat with <strong class="c-mostaza">'+user+'</strong>?</h4>\
                      <a id="jcode-init-chat" data="'+require+'" class="btn btn-info white">Yes</a>\
                      <button class="btn btn-default" data-dismiss="modal">Cancel</button>\
                    </div>\
                  </div>';
        libereModal(html,'sm','white','static');
	});
    //----------------------------------------------------

    //--------------------------------------------------
    //abre modal para incluir a esa persona a favoritos
	$('#content-data').on('click','#jcode-profile-favoritos',function(e){
        e.preventDefault();
        var require = $(this).attr('data-token');
        var user = $('#u_name').text();
	      var html ='<div class="media">\
                    <div class="media-left">\
                        <div class="jcode-profile-button-single c-blue border-blue">\
                           <i class="fa-2x fa fa-star fa-spin" aria-hidden="true"></i>\
                        </div>\
                    </div>\
                    <div class="media-body">\
                      <h4>Do you want to gregar a <strong class="c-blue">'+user+'</strong> your favorite list?</h4>\
                      <a id="jcode-init-fav" data="'+require+'" class="btn btn-info white">Yes</a>\
                      <button class="btn btn-default" data-dismiss="modal">Cancel</button>\
                    </div>\
                  </div>';
         libereModal(html,'sm','white','static');
	});
    //----------------------------------------------------
//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::



//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::Functions send info of user datos personales y photo de portada::::::::::::::
//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    //conjunto de funciones para  enviar los formulario de empleo
    $('#jcode-modal-content').on('submit','#jcode-empleo-form',function(e){
	    e.preventDefault();
	    procedureData(this,'empleo/edit','messageformsProfile');
	    //---------------------------------------------------------------
	});

	//conjunto de funciones para  enviar los formulario de formacion
    $('#jcode-modal-content').on('submit','#jcode-formation-form',function(e){
	    e.preventDefault();
	    procedureData(this,'formacion/edit','messageformsProfile');
	});

	//conjunto de funciones para  enviar los formulario de descripcion
    $('#jcode-modal-content').on('submit','#jcode-descrip-form',function(e){
	    e.preventDefault();
	    procedureData(this,'descripcion/edit','messageformsProfile');
	});

	//conjunto de funciones para  actualizar la photo de portada
    $('#jcode-modal-content').on('submit','#jcode-portada-form',function(e){
	    e.preventDefault();
	    procedureData(this,'portada/edit','messageformsProfile');
	});

	//Controlador profile  proceso interno para insertar persona a los favoritos
    $('#jcode-modal-content').on('click','#jcode-init-fav',function(e){
	    e.preventDefault();
	    preloadAjaxModal('#jcode-modal-content');
	    var promise = dataSingleAjax('POST',false,'favoritos/add',{'params':'jc-token='+this.getAttribute('data')});
	    promise.success(function(json){
                var obj = $.parseJSON(json);
                if(obj.success == 1) {
                	messageAjaxModal('#jcode-modal-content','This person has added to your favorites','smile');
			      } else {
                    messageAjaxModal('#jcode-modal-content',obj.message,'sad');
			      }
	    });
	});
	//conjunto de funciones para  mandar solicitudes de chat

//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

})(document)



function procedureData(form,route,div){
       
       preloadAjaxModal('#'+div);
       var promise = dataSingleAjax('POST',true,route,{'form':form});
       promise.success(function(json){
              var obj = $.parseJSON(json);
              if(obj.success == 1) {
		      	
                 messageAjaxModal('#jcode-modal-content',obj.message,'smile');
                 setTimeout(function(){ hidenModal(); document.location.reload();},2000);
		      } else {
		        messageAjaxModal('#'+div,obj.message,'sad');
		      } 
       });
}


//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::::::::::templates Profile::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//template empleo user
function tmpUserEmpleo(param0,param1,param2,param3){
	
	var html ='\
                <div class="row">\
                  <div class="col-xs-12">\
                    <center><h2>Job Information</h2></center>\
                    <hr>\
                    <form  role="form" id="jcode-empleo-form">\
                      <label for="">Company Name</label>\
                      <input type="text"  name="jc-emp-empresa" class="form-control" value="'+param0+'"><br>\
                      <label for="">Position you play</label>\
                      <input type="text" name="jc-emp-cargo" class="form-control" value="'+param1+'"><br>\
                      <label for="">What city?</label>\
                      <input type="text"  name="jc-emp-ciudad" class="form-control" value="'+param2+'"><br>\
                      <label for="">Tell us about your job</label>\
                      <textarea id="jc-emp-descrip" name="jc-emp-descrip" rows="5" class="form-control">'+param3+'</textarea><br>\
                      <button class="btn btn-primary">Save changes</button>\
                      <a class="btn btn-default" data-dismiss="modal">Cancel</a>\
                      <div id="messageformsProfile"></div>\
                    </form>\
                  </div>\
                </div>\
		    ';
    libereModal(html,'sm','#fafafa','static');
}

// template  formation user
function tmpUserFormation(param0,param1,param2,param3,param4){
	
	var html ='\
                 <div class="row">\
                          <div class="col-xs-12">\
                            <center><h2>Formation Profesional</h2></center>\
                            <hr>\
                            <form action="" role="form" id="jcode-formation-form">\
                              <label for="">In which school did you study?</label>\
                              <input type="text" id="jc-fm-ename" name="jc-fm-ename" class="form-control" value="'+param0+'"><br>\
                              <label for="">Study period</label>\
                              <label for="">since</label>\
                              <select class="form-control" id="jc-fm-since" name="jc-fm-since">';
                              for(var i=1960; i<2016; i++){
               html+=         '<option value="'+i+'"  '+(param1 == i ? 'selected' : '')+'   >'+i+'</option>'; 
                              };
               html+=         '</select>\
                              <label for="">Until</label>\
                              <select class="form-control" id="jc-fm-until" name="jc-fm-until">';
                              for(var i=1960; i<2016; i++){
               html+=         '<option value="'+i+'"  '+(param2 == i ? 'selected' : '')+'>'+i+'</option>'; 
                              };
               html+=         '</select><br>\
                              <label for="">Studies culminated?</label><br>\
                              <input type="radio" name="jc-fm-cl" value="1" '+(param3 == 1 ? 'checked' : '')+' > Studing\
                              <input type="radio" name="jc-fm-cl" value="2" '+(param3 == 2 ? 'checked' : 'checked')+'> culminated<br><br>\
                              <label for="">What course did you study?</label>\
                              <input type="text" id="jc-fm-carrera" name="jc-fm-carrera" class="form-control"  value="'+param4+'"><br>\
                              <button class="btn btn-primary">Save changes</button>\
                              <a class="btn btn-default" data-dismiss="modal">Cancel</a>\
                              <div id="messageformsProfile"></div>\
                            </form>\
                          </div>\
                        </div>\
		    ';

		     libereModal(html,'sm','#fafafa','static');
}

//template descripcion usuario
function tmpUserDescription(param0){
	   var html ='\
                <div class="row">\
                  <div class="col-xs-12">\
                    <center><h2>'+name_main+'</h2></center>\
                    <hr>\
                    <form action="" role="form" id="jcode-descrip-form">\
                      <label for="">Tell us about yourself...</label>\
                      <textarea id="jc-u-descrip" name="jc-u-descrip" rows="5" class="form-control">'+param0+'</textarea><br>\
                      <button class="btn btn-primary">Save changes</button>\
                      <a class="btn btn-default" data-dismiss="modal">Cancel</a>\
                      <div id="messageformsProfile"></div>\
                    </form>\
                  </div>\
                </div>\
		    ';
		 libereModal(html,'sm','#fafafa','static');
}
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::