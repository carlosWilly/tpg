(function(d){

$('#pone_eprofile').on('click','.jc-setup',function(){
  
  var option = $(this).attr('data');
   var html='';
   html+='<form id="jc-setup-user" role="form">';
  switch(option){
     case '1':
        var u_name = $('#u_name').text();
        var u_last = $('#u_lastname').text();
        html+=' <div class="col-xs-12">\
	               <h3>Upload your name and Lastnames</h3><hr>\
	                Your Name\
	                <input type="text" name="u_name" class="form-control" value="'+u_name+'">\
	                <br>\
	                Your Lastname\
	                <input type="text" name="u_lastname" class="form-control" value="'+u_last+'">\
	                <br>\
	                <div id="jc-setup-message"></div>\
	                <button class="btn btn-primary btn-block">Upload</button>\
	                <a class="btn btn-warning btn-block" data-dismiss="modal">Cancel</a>\
	             </div>\
	             <div class="clearfix"></div>';
      break;
      case '2':
        var u_email = $('#u_email').text();
        html+=' <div class="col-xs-12">\
	               <h3>Upload your Email</h3><hr>\
	                Your Email\
	                <input type="text" name="u_email" class="form-control" value="'+u_email+'">\
	                <br>\
	                <div id="jc-setup-message"></div>\
	                <button class="btn btn-primary btn-block">Upload</button>\
	                <a class="btn btn-warning btn-block" data-dismiss="modal">Cancel</a>\
	                <br>\
	                <p>Make sure you enter a valid email, remember that you will start session with Him.</p>\
	             </div>\
	             <div class="clearfix"></div>';
      break;
       case '3':
        html+=' <div class="col-xs-12">\
	               <h3>Upload your password</h3><hr>\
	                <br>\
	                Enter your new password\
	                <input type="password" name="u_npass" class="form-control">\
	                <br>\
	                Repeat your new password\
	                <input type="password" name="u_cpass" class="form-control">\
	                <br>\
	                <div id="jc-setup-message"></div>\
	                <button class="btn btn-primary btn-block">Upload</button>\
	                <a class="btn btn-warning btn-block" data-dismiss="modal">Cancel</a>\
	                <br>\
	                <p>Make sure your password contains numbers and letters in addition to at least 6 characters</p>\
	             </div>\
	             <div class="clearfix"></div>';
      break;
      case '4':
        var u_sex = $('#u_sexo').attr('data');
        html+=' <div class="col-xs-12">\
	               <h3>Upload your Sex</h3><hr>\
	                Your current key\
	                <select class="form-control" name="u_sexo"><option value="1" '+(u_sex ==1 ? "selected" :'')+'>Male</option><option value="2" '+(u_sex ==2 ? "selected" :'')+'>Female</option></select>\
	                <br>\
	                <div id="jc-setup-message"></div>\
	                <button class="btn btn-primary btn-block">Upload</button>\
	                <a class="btn btn-warning btn-block" data-dismiss="modal">Cancel</a>\
	                <br>\
	                <p></p>\
	             </div>\
	             <div class="clearfix"></div>';
      break;
      case '5':
        var u_fecnac = $('#u_fecnac').text();
        var m = u_fecnac.split('-');//substr(u_fecnac.indexOf('/')+1);
        var di = m[2];var mo = m[1];var ye = m[0];  
        
        var u_hora = $('#u_fecnac').attr('data');
        html+=' <div class="col-xs-12">\
	               <h3>Upload your birthday</h3><hr>\
	                Your birthday\
			                 <select class="form-control input-lg" name="u_day">\
		                         <option value="0">Day</option>';
		         for (var i = 1; i < 31; i++) {
			    html+=          '<option value="'+i+'" '+( m[2] == i ? "selected" :"")+'>'+i+'</option>';         	
			         }
				html+=       '</select>\
				             <select class="form-control input-lg" name="u_mes">\
				                  <option value="0">Month</option>';
				         for (var i = 1; i <= 12; i++) {
				html+=            '<option value="'+i+'" '+( m[1] == i ? "selected" :"")+'>'+i+'</option>';         	
				         }
				html+=       '</select>\
				              <select class="form-control input-lg" name="u_year">\
				                    <option value="0">Year</option>';
				         for (var i = 1960; i <= 2016; i++) {
				html+=              '<option value="'+i+'" '+( m[0] == i ? "selected" :"")+'>'+i+'</option>';         	
				         }
				html+=       '</select>\
	                <br>Birth hour\
	                <input type="text" name="u_hora" class="form-control" value="'+u_hora+'" />\
	                <br>\
	                <div id="jc-setup-message"></div>\
	                <button class="btn btn-primary btn-block">Upload</button>\
	                <a class="btn btn-warning btn-block" data-dismiss="modal">Cancel</a>\
	                <br>\
	                <p></p>\
	             </div>\
	             <div class="clearfix"></div>';
      break;
  }
  
 html+='</form>'
$('#modal-width').removeClass('modal-lg').addClass('modal-sm');
$('#modal-content').css('backgroundColor','white');
$('#jcode-modal-content').html(html);
$('#jcode-modal-multiple').modal({modal:'show',backdrop:'static'});
});

$('#jcode-modal-content').on('submit','#jc-setup-user',function(e){
    e.preventDefault();


    $.ajax({
       type:'POST',
       url:'ajcode/user/updateProcess',
       data: $(this).serialize(),
       beforeSend:function(){
         $('#jc-setup-message').html('<center><i class="fa-2x fa fa-spinner fa-spin" aria-hidden="true"></i></center>');
       },
       success:function(json){
        var d = $.parseJSON(json);
      
        if (d.success==1) {
            $('#jc-setup-message').removeClass('alert-danger').addClass('alert alert-success').html('<center>'+d.message+'</center>');
            setTimeout(function(){ hidenModal();document.location.reload()},2000);
        }else{
        	$('#jc-setup-message').removeClass('alert-success').addClass('alert alert-danger').html('<center>'+d.message+'</center>');
        }
       }
    });
});

//lanzamiento template principal
$('#jcode-modal-setup').click(function(){
 var html='<div class="panel panel-default">\
        <div class="panel-heading">\
          <h2><strong>'+user.u_name+'</strong> configura you account</h2>\
        </div>\
        <div class="panel-body">\
         <div class="col-xs-12">\
            <table class="table table-striped">\
               <thead>\
                 <tr>\
                   <th colspan="3">General account settings</th>\
                 </tr>\
               </thead>\
               <tbody>\
                 <tr><td>Names:</td><td><span id="u_name">'+user.u_name+'</span> <span id="u_lastname">'+user.u_lastname+'</span></td><td><button class="jc-setup btn-sm btn btn-info" data="1">Editar</button></td></tr>\
                 <tr><td>Email:</td><td><span id="u_email">'+user.u_email+'</span></td><td><button class="jc-setup btn-sm btn btn-info" data="2">Editar</button></td></tr>\
                 <tr><td>Password:</td><td>************</td><td><button class="jc-setup btn-sm btn btn-info" data="3">Editar</button></td></tr>\
                 <tr><td>Gender:</td><td><span id="u_sexo" data="'+user.u_sexo+'">'+user.u_gename+'</span></td><td><button class="jc-setup btn-sm btn btn-info" data="4">Editar</button></td></tr>\
                 <tr><td>Birthday:</td><td><span id="u_fecnac" data="'+user.u_horan+'">'+user.u_fecnac+'</span></td><td><button class="jc-setup btn-sm btn btn-info" data="5">Editar</button></td></tr>\
               </tbody>\
             </table>\
         </div>\
        </div>\
      </div>\
      <center><button class="btn btn-primary btn-lg" data-dismiss="modal">Cancel</butto></center>';
 libereModal(html,'none','white','static');
});


})(document)