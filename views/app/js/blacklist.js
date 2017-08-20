(function(d){
  
  //EVENTO  lanza modal borrar de mi black list
  $('.jc-dlbl').click(function(){
     tmpBlackList('delete','jcode-deleteBlacklist',this.getAttribute('data-token'));
  });

  //EVENTO  agregar a black list
  $('#jcode-contentBoxChat, #content-data').on('click','#jc-adbl',function(){

     tmpBlackList('add','jc-addBlacklist',this.getAttribute('data-token'));
  });




  $('#jcode-modal-content').on('click','#jcode-deleteBlacklist',function(){
  	 preloadAjaxModal('#jcode-modal-content','2x');
     var token = this.getAttribute('data-token');
     var promise = $.post('ajcode/blackList/delete',{token:token});
     promise.done(function(json){
       var obj = $.parseJSON(json);
       var html ='<div id="jc-modalBlacklist"></div>';
       if (obj.success ==1) {
         //conn . send ( JSON . stringify ( {action:'notification',token:obj.data,type:1} )); 
         messageAjaxModal('#jcode-modal-content',obj.message,'smile');
         $('#jcode-modal-multiple').on('hidden.bs.modal', function (e) {
		  document.location.reload();
		 });
       }else{
         messageAjaxModal('#jcode-modal-content',obj.message,'sad');

       }
     });
  });

  $('#jcode-modal-content').on('click','#jc-addBlacklist',function(){
      var token = this.getAttribute('data-token');

      var promise = $.post('ajcode/blackList/add',{token:token});
      promise.done(function(json){
      	var obj = $.parseJSON(json);
      	if (obj.success==1) {
      	   messageAjaxModal('#jcode-modal-content',obj.message,'smile');

           //conn . send ( JSON . stringify ( {action:'notification',token:token,type:1} )); 
      	   $('#jcode-modal-multiple').on('hidden.bs.modal', function (e) {
			     document.location.reload();
		   });
      	}else{
      	   messageAjaxModal('#jcode-modal-content',obj.message,'sad');
      	}
      });
  });
})(document)

function tmpBlackList(pross,buttonId,data){
	 var title,message;
	if (pross=='add') {
		title = 'Add to my blacklist';
		message='When you add this user to your blacklist you can not have any contact with you; This means no messages, chats, requests, etc.';
	}else{
        title = 'Remove to my blacklist';
		message='When you remove this user from your blacklist, this can have all kinds of contact with you';
	}
	var html='<div class="media">\
	            <div class="media-left">\
	             <i class="fa fa-user-secret fa-4x" aria-hidden="true"></i>\
	            </div>\
	            <div class="media-body">\
	             <h4>'+title+'</h4>\
	             <p>'+message+'</p><br>\
                 <button class="btn btn-primary" id="'+buttonId+'" data-token="'+data+'">Confirm</button><button class="btn btn-default" data-dismiss="modal">Cancel</button>\
	            </div>\
	         </div>';
	libereModal(html,'sm','white','static');
}
