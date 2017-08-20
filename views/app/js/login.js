var autocomplete;
var componentForm = {
  locality: 'long_name',
  administrative_area_level_1: 'short_name',
  country: 'short_name',
};

(function(d){
	'use strict';





//:::::::::::::::::::inicia acciones al enviar el formulario de logueo :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  var html='      <form id="jcode-login">\
	                  <div style="padding:10px;margin-top:10px;">\
	                      <a href="'+oauthFace+'" class="white btn btn-primary btn-block shadow-grall"><i class="fa fa-facebook-official fa-lg"></i> | Login with Facebook</a><br>\
		                  <a href="'+oauthIn+'" class="white aouth-linkedin btn btn-info btn-block shadow-grall"><i class="fa fa-linkedin-square fa-lg"></i> | Login with LinkEdin</a><br>\
		                  <input type="email" name="u-email" class="form-control" placeholder="You e-mail" required=""><br />\
		                  <input type="password" name="u-pass" class="form-control" placeholder="Password"><br />\
		                   <p class="pull-right j-cursor" id="jc-lostpass"><i class="fa fa-lock" aria-hidden="true"></i> Forgot your account?</p><br />\
	                  </div>\
		              <div style="background-color:#eee;padding:10px;border-radius:0px 0px 4px 4px">\
		                  <div class="col-xs-12">\
		                     <p id="load-message"></p></div><div class="clearfix"></div>\
		                     <a id="jcode-login-send" class="btn btn-sm btn-default btn-block shadow-grall">Log in</a>\
	                  </div>\
                  </form>';

    var reg='      <form id="jcode-reg">\
	                  <div style="padding:10px;margin-top:10px;">\
	                      <a href="'+oauthFace+'" class="white btn btn-primary btn-block shadow-grall"><i class="fa fa-facebook-official fa-lg"></i> | Login with Facebook</a><br>\
	                      <a href="'+oauthIn+'" class=" white aouth-linkedin btn btn-info btn-block shadow-grall"><i class="fa fa-linkedin-square fa-lg"></i> | Login with LinkEdin</a><br>\
		                  <input type="email"    id="u_name" name="u_name" class="form-control" placeholder="First name" required=""><br />\
		                  <input type="text"     id="u_lastname" name="u_lastname" class="form-control" placeholder="Last Name"><br />\
		                  <input type="text"     id="u_email"    name="u_email" class="form-control" placeholder="Email"><br />\
		                  <input type="password" id="u_pass" name="u_pass" class="form-control" placeholder="Create a password"><br />\
	                  </div>\
		              <div style="background-color:#eee;padding:10px;border-radius:0px 0px 4px 4px">\
		                  <div class="col-xs-12">\
		                     <p id="jcode-reg-message"></p></div><div class="clearfix"></div>\
		                     <a id="jcode-reg-send" class="btn btn-sm btn-default btn-block shadow-grall">Create Account</a>\
	                  </div>\
                  </form>';

       
   
   $('[rel="loginpopover"]').popover({
           
           trigger:'click focus',
       	   container:'body',
           html:true,
           placement:'bottom',
           content: html,
           template:'<div class="popover" role="tooltip" style="width:300px;border: 2px solid #d9d9d9;">\
                           <div class="arrow"></div>\
                            <div class="popover-content poper-login"></div>\
                    </div>'
	 
	});

   $('[rel="regpopover"]').popover({
           
           trigger:'click focus',
       	   container:'body',
           html:true,
           placement:'bottom',
           content: reg,
           template:'<div class="popover" role="tooltip" style="width:300px;border: 2px solid #d9d9d9;">\
                           <div class="arrow"></div>\
                            <div class="popover-content poper-login"></div>\
                    </div>'
	 
	});

    $('[rel="loginpopover"]').on('shown.bs.popover', function () {
	    
	     $('#jcode-login-send').on('click',function(evt){
            evt.preventDefault();
             loginMethod();
         });

         $('#jcode-login').on('keypress',function(evt){
          if (evt.keyCode != 13)
              return;
             evt.preventDefault();
             loginMethod();
         });

         $('#jc-lostpass').on('click',function(e) {

		  	 $('#jcode-lostModal').modal('show');
		  	 $('[rel="loginpopover"]').popover('hide');

		  });
        $('#jcode-top-creates').popover('hide');
	});


	$('[rel="regpopover"]').on('shown.bs.popover', function () {
	    
	     $('#jcode-reg-send').on('click',function(evt){
            evt.preventDefault();
             register('#jcode-reg');
         });

         $('#jcode-login-intro').popover('hide');
	});

//lost pass model
$('#lostpass_form').on('submit',function(e){
   e.preventDefault();
    
    $('#ajax_lostpass').removeClass('hide alert-dager').addClass('alert-success').html('<center><i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i><br> Sending wait please</center>');

    var promise = $.post('ajcode/lostpass',$(this).serialize());
    promise.done(function(json){
       var obj = $.parseJSON(json);

       if (obj.success==1) {
           var html ='<div class="media">\
                      <div class="media-left">\
                            <i class="fa fa-smile-o fa-4x" aria-hidden="true"></i>\
                       </div>\
                       <div class="media-body">\
                        <h4>'+obj.message+'</h4>\
                       </div>\
                   </div>';
            $('#modal-body').html(html);
        }else{
            $('#ajax_lostpass').removeClass('hide alert-success').addClass('alert-danger').html('<center>'+obj.message+'</center>');
        }
       

    })

})

  
    

  

   
//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::  
   
})(document);



//:::::::::::::::::::Bloque de funciones utiles:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
function loginMethod(){//funtion de logueo
            preloadAjaxModal('#load-message');
            var promise = dataSingleAjax('POST',true,'login',{'form':'#jcode-login'});
            promise.success(function(json){
                   var obj = $.parseJSON(json);
                     if(obj.success == 1) {
                         $('#load-message').html('<center><i class="fa fa-smile-o fa-3x"></i><br /><b>'+obj.message,'</b></center>');
		                 conn.send ( JSON . stringify ( {action:'notification',token:obj.token,type:3} )); 
		                 setTimeout(function(){
		                 document.location.reload();
		                },2500);
				      }else {
				      	$('#load-message').html('<center><i class="fa fa-frown-o fa-3x"></i><br><b>'+obj.message,'</b></center>');
				      } 
            });
}
//---------------------------------------------------------------------------------------------------------------




function initAutocomplete() {//function load autocomplete form api google
  autocomplete = new google.maps.places.Autocomplete((document.getElementById('jcode-reg-location_1')),{types: ['geocode']});
  autocomplete = new google.maps.places.Autocomplete((document.getElementById('jcode-reg-location_2')),{types: ['geocode']});
}
//-------------------------------------------------------------------------------------------------------------------



function tmpReg(){//function template form register
	var html;
	html='<div class="hidden-xs col-md-3"></div>\
     <div class="col-xs-12 col-md-6 shadow-intense" style="background-color:#222222;color:#bcbcbc;padding-bottom:100px;">\
        <div class="section-50-50">\
         <form action="" id="jcode-reg-form">\
         <div class="col-xs-12">\
         <center><h2>Join us at the playground</h2></center>\
         <strong>Personal Dates</strong>\
         </div>\
         <div class="col-xs-12 col-md-5">\
           <input type="text" class="form-control input-lg" placeholder="Fisrt name" name="jcode-reg-nombre">\
         </div>\
         <div class="col-xs-12 col-md-7">\
           <input type="text" class="form-control input-lg" placeholder="Last Name" name="jcode-reg-apellido">\
         </div>\
         <div class="clearfix"></div><br>\
         <div class="col-xs-12 col-md-7">\
           <input type="text" class="form-control input-lg" placeholder="Email" name="jcode-reg-email">\
         </div>\
         <div class="col-xs-12 col-md-5">\
           <input type="password" class="form-control input-lg" placeholder="Password" name="jcode-reg-pass">\
         </div>\
         <div class="clearfix"></div><br>\
         <div class="col-xs-12"><strong>Birthday</strong></div>\
         <div class="col-xs-12 col-md-3">\
	         <select class="form-control input-lg" name="jcode-reg-mes">\
	              <option value="0">Month</option>';
	         for (var i = 1; i <= 12; i++) {
	html+=      '<option value="'+i+'">'+i+'</option>';         	
	         }
	html+=  '</select>\
	         </div>\
         <div class="col-xs-12 col-md-3">\
         <select class="form-control input-lg" name="jcode-reg-dia">\
              <option value="0">Day</option>';
         for (var i = 1; i < 31; i++) {
	html+=      '<option value="'+i+'">'+i+'</option>';         	
	         }
	html+=  '</select>\
	         </div>\
	         <div class="col-xs-12 col-md-3">\
	        <select class="form-control input-lg" name="jcode-reg-anio">\
	              <option value="0">Year</option>';
	         for (var i = 1960; i <= 2016; i++) {
	html+=      '<option value="'+i+'">'+i+'</option>';         	
	         }
	html+=  '</select>\
	         </div>\
	         <div class="col-xs-12 col-md-3"><input type="text" class="form-control input-lg" placeholder="hh:mm" name="jcode-reg-hora" value="06:00" rel="popover" data-container="body" data-toggle="popover" data-placement="bottom" title="Time of your birth" data-content="Please enter the time of your birth in 24 hours format, do not forget to include the two points, example 14:35"></div>\
	         <div class="clearfix"></div><br>\
	         <div class="col-xs-12"><strong>Location of birth</strong><input type="text" class="form-control input-lg" id="jcode-reg-location_1" name="jcode-reg-location_1" placeholder="city,estate,country" value="Manhattan, Nueva York, Estados Unidos" rel="popover" data-container="body" data-toggle="popover" data-placement="top" title="Where were you born?" data-content="Enter the city first followed by the state and the country, do not worry we will help you with the location"></div>\
	         <div class="col-xs-12"><br /><strong>Place where you live</strong><input type="text" class="form-control input-lg" id="jcode-reg-location_2" name="jcode-reg-location_2" placeholder="city,estate,country" value="Manhattan, Nueva York, Estados Unidos" rel="popover" data-container="body" data-toggle="popover" data-placement="top" title="Where you live now?" data-content="Indicating the place where you live now allows us to find people who are in your same area; So you will have more chances to find what you are looking for"></div>\
	         <div class="col-xs-12"><br><strong>Gender</strong>\
	         <h3>\
	           <input type="radio" name="jcode-reg-sexo" value="1" checked> Man\
	           <input type="radio" name="jcode-reg-sexo" value="2"> Woman\
	         </h3>\
	         </div>\
	         <div class="col-xs-12 col-md-6">\
	          <select class="form-control input-lg" name="jcode-reg-option">\
	            <option value="1">looking for a friendship</option>\
	            <option value="2">looking for a relationship</option>\
	            <option value="3">looking for something casual</option>\
	          </select>\
	         </div>\
	         <div class="col-xs-12 col-md-6">\
	          <select class="form-control input-lg" name="jcode-reg-preference">\
	            <option value="1">with a Men</option>\
	            <option value="2">with a Woman</option>\
	            <option value="3">with a Both</option>\
	          </select>\
	         </div>\
	         <div class="col-xs-12">\
	         <br>\
	         <div id="jcode-reg-message"></div>\
	         <br>\
	         <center><button class="btn btn-default btn-lg" style="background-color:#d90007;color:white">Create Account</button></center><br>\
	         <p>by registering you agree to our terms of service and our privacy policy playground does not post or share your information. Privacy policy</p>\
	         </div>\
	         </form>\
	        </div>\
	     </div>\
	     <div class="hidden-xs col-md-3"></div>\
	     <div class="clearfix"></div>\
	     <br><br>';
             
	     $('#jcode-container-front').html(html);

	     $('[rel="popover"]').popover({
				trigger:'hover',
				html:true,
				delay:200,
			});
}
//-----------------------------------------------------------------------------------------------------


function register(form){ 

            var check= true;
                $(form).each(function(){
				   if( $('#u_name').val().length == 0 || $('#u_lastname').val().length == 0 || $('#u_email').val().length == 0 || $('#u_pass').val().length == 0 ){  

				   	check = false; $(this).addClass('focus');
				   }
				});
            //-----------------------------------------------------------
            if (check == false) { messageAjaxModal('#jcode-reg-message','Complete all fields(1)','sad',true); return; }

					
			    //-----------------------------------------------------------
			    preloadAjaxModal('#jcode-reg-message','3x');
                //------------------------------------------------------------------------------------
                var dataOne = $(form).serialize();    
                var promise = $.post('ajcode/register',$.trim(dataOne));
                promise.done(function(data){
	        	     var obj = $.parseJSON(data);
	        	     if(obj.success == 1) {
		                messageAjaxModal('#jcode-reg-message',obj.message,'smile',true);
		                setTimeout(function(){document.location.reload();},1000);
				      } else {
				        messageAjaxModal('#jcode-reg-message',obj.message,'sad',true);
				      }
                });
        
	};

