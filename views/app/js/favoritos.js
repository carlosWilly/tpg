(function(d){
  $('.jc-deleteFav').on('click',function(){

        var html ='<div class="media">\
                      <div class="media-left">\
                        <i class="fa fa-star fa-4x" aria-hidden="true"></i>\
                      </div>\
                      <div class="media-body">\
                       <h4>Do you want to remove this person from your favorites?</h4><br>\
                       <center><button class="btn btn-primary" id="jc-removeFavorites" data-token="'+this.getAttribute('data-token')+'">Confirm</button><button class="btn btn-default" data-dismiss="modal">Cancel</button><center>\
                      </div>\
                    </div>';

          libereModal(html,'sm','white','static'); 
  });

  $('#jcode-modal-content').on('click','#jc-removeFavorites',function(e) {
  	  var promise = $.post('ajcode/favoritos/delete',{tokenUser:this.getAttribute('data-token')});
  	  promise.done(function(json){
  	  	var obj = $.parseJSON(json);
  	  	if (obj.success==1) {
            messageAjaxModal('#jcode-modal-content',obj.message,'smile');
            $('#jcode-modal-multiple').on('hidden.bs.modal', function (e) {
			 document.location.reload();
			})
  	  	}else{
            messageAjaxModal('#jcode-modal-content',obj.message,'sad');
  	  	}//falata configurar la api
  	  });
  });

  $('.jc-preferences').on('click', function(event) {
    event.preventDefault();
    var token = this.getAttribute('data-token');
    console.log(token);
    
    var promise = $.get('ajcode/search/userPreferences',{u_token:token});
    promise.done(function(json){
       var obj = $.parseJSON(json);
       if (obj.success==1) {
          var html='<div class="row">\
          <div class="col-xs-12">\
          <div class="media">\
              <div class="media-left">\
                 <div class="bg-img img-circle" style="width:70px;height:70px;background-color:red;background-image:url(views/assets/images/users/'+obj.data[0]['us_img']+')"></div>\
              </div>\
              <div class="media-body">\
                 <h4>Search Preferences for:</h4>\
                 <h5><b>'+obj.data[0]['us_name'].toUpperCase() +'</b></h5></div><hr>\
                          <div id="jcode-osearch-static"> \
                           <table class="table">';
                               html+='<tr><td class="text-center no-border"><i class="c-green-b fa-lg fa fa-search"></i></td><td class="no-border">'+obj.data[0]['us_options']+'</td></tr>';
                               html+='<tr><td class="text-center no-border"><i class="c-green-b fa-lg fa fa-male"></i><i class="c-green-b fa fa-female fa-lg"></i></td><td>'+obj.data[0]['us_preferences']+'</td></tr>';
                               html+='<tr><td class="text-center no-border"><i class="c-green-b fa-lg fa fa-circle-o-notch"></i></td><td class="no-border"> Intro '+obj.data[0]['us_eini']+'--'+obj.data[0]['us_eend']+' Ages</td></tr>';
                               html+='<tr><td class="text-center no-border"><i class="c-green-b fa-lg fa fa-balance-scale"></i></td><td class="no-border"> '+obj.data[0]['us_zodiacal']+'</td></tr>';
                               html+='<tr><td class="text-center no-border"><i class="c-green-b fa-lg fa fa-map-marker"></i></td><td class="no-border"> '+obj.data[0]['us_estado']+'</td></tr>';
                               html+='<tr><td class="text-center no-border"><i class="c-green-b fa-lg fa fa-street-view"></i></td><td class="no-border"> '+obj.data[0]['us_city']+'</td></tr>\
                           </table>\
                           </div>\
                 </div><br>\
                 <center><button class="btn btn-primary btn-sm" data-dismiss="modal">Close</button></center>\
          </div>\
          </div>';
          libereModal(html,'sm','#fafafa','none');
       }else{

       }
    });
  });

    //Lanza modal para enviar solicitud de chat a los usuarios
  $('#content-data').on('click','.jcode-profile-chat',function(e){
       e.preventDefault();
       var require = $(this).attr('data-token'); 
       var user = $(this).parents('ul').prev('h5').html();
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
})(document)
