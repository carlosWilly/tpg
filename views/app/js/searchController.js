(function(d){
   SR = {
     init:function(e){
        SR.pb();

        $('#jc-loadSearchController').on('click',function(){
               var html ='';
                pagina ++;
                //console.log(pagina);
                var promise = $.post('ajcode/search/scrollsearch','page='+pagina+'');
                promise.done(function(jcode){
                      var jc = $.parseJSON(jcode);
                      if (jc.success==1) {//--success
                        //-----------------------section one
                        if (jc.data.length >0) {
                          for(var i=0;i<jc.data.length;i++){
                            html+='<div class="col-xs-12 col-sm-6">\
                                 <div class="media" style="border-bottom: 1px dashed silver;border-radius:5px;padding:10px;overflow:hidden;">\
                                    <div class="col-xs-12 col-sm-3">\
                                      <a href="profile/'+jc.data[i]['u_id']+'">\
                                        <div style="width: 80px; height: 80px;background-image: url(views/assets/images/users/'+(jc.data[i]['iu_img'] == null ? 'no-img.jpg' : jc.data[i]['iu_img'])+');border: 4px solid #f73b3a;" class="bg-img img-circle">\
                                        </div>\
                                      </a>\
                                    </div>\
                                    <div class="col-xs-12 col-sm-9">\
                                      <h5 class="media-heading" style="color:#202020"><strong>'+jc.data[i]['u_nombre']+'</strong></h5>\
                                      <p class="">'+jc.data[i]['pa_iso']+' - '+jc.data[i]['st_name']+' - '+jc.data[i]['ci_name']+'</p>\
                                      <p><img  src="views/assets/images/zodiacal/'+jc.data[i]['sz_icon']+'" width="30">'+jc.data[i]['sz_name']+'<i class="c-mostaza fa fa-circle-o-notch fa-lg"></i> <span style="font-size: 15px;">'+jc.data[i]['edad']+'</span> age</p>\
                                    </div>\
                                  </div><br>\
                              </div>'; 
                          }

                          $('#after-search').append(html);
                          $('#jc-loadSearchController').show();  
                        }

                        //------------------------section one
                      }else{
                          $('#jc-loadSearchController').hide();  
                      }//--success
                }); 

        });//limit event         
        //-----------------------------------------------------------------------------------------
        $('#s_name').on('keyup', function(e) {
          e.preventDefault();
          if(e.keyCode!=13) return false;
          SR.search($('#s_foption').serialize()+'&s_name='+$('#s_name').val());
        });

        $('#s_option,#s_with,#s_szinc,#s_location').on('change', function(e) {
          e.preventDefault();
          SR.search($('#s_foption').serialize()+'&s_name='+$('#s_name').val());
        });
        //-----------------------------------------------------------------------------------------

        $('#jc-searchByName').on({

          keyup:function(e){
            
            e.preventDefault();
            var key = e.keyCode;
            if (key == 13) return;
            var html='';
            var promise = $.post('ajcode/search/scrollsearch',{searchByName:this.value});
            promise.done(function(jcode){
            var jc = $.parseJSON(jcode);
                if (jc.success==1) {//--success


                  //-----------------------section one
                  if (jc.data.length >0) {
                    for(var i=0;i<jc.data.length;i++){
                      html+='<div class="col-xs-12 col-sm-6">\
                           <div class="media" style="border-bottom: 1px dashed silver;border-radius:5px;padding:10px;overflow:hidden;">\
                              <div class="col-xs-12 col-sm-3">\
                                <a href="profile/'+jc.data[i]['u_id']+'">\
                                  <div style="width: 80px; height: 80px;background-image: url(views/assets/images/users/'+(jc.data[i]['iu_img'] == null ? 'no-img.jpg' : jc.data[i]['iu_img'])+');border: 4px solid #f73b3a;" class="bg-img img-circle">\
                                  </div>\
                                </a>\
                              </div>\
                              <div class="col-xs-12 col-sm-9">\
                                <h5 class="media-heading" style="color:#202020"><strong>'+jc.data[i]['u_nombre']+'</strong></h5>\
                                <p class="">'+jc.data[i]['pa_iso']+' - '+jc.data[i]['st_name']+' - '+jc.data[i]['ci_name']+'</p>\
                                <p><img  src="views/assets/images/zodiacal/'+jc.data[i]['sz_icon']+'" width="30">'+jc.data[i]['sz_name']+'<i class="c-mostaza fa fa-circle-o-notch fa-lg"></i> <span style="font-size: 15px;">'+jc.data[i]['edad']+'</span> age</p>\
                              </div>\
                            </div><br>\
                        </div>'; 
                    }

                    $('#loadOptionsSearch').html(html);
                    
                  }

                  $('#jc-loadSearchController').show();  
                  //------------------------section one
                }else{
                    html+='<div class="media">\
                            <div class="media-left media-middle">\
                              <i class="fa fa-meh-o fa-5x" aria-hidden="true"></i>\
                            </div>\
                            <div class="media-body">\
                              <br>\
                              <h4 class="media-heading">We could not find people according to your search preferences, try changing them <button id="jcode-searchOptions-csetup" class="btn btn-default">My search options</button></h4>\
                            </div>\
                          </div>';
                    $('#loadOptionsSearch').html(html);
                    $('#after-search').html(' ');
                    $('#jc-loadSearchController').hide();  
                }

            });
          }

        });//limit event    
     },
     search:function(data){
            $.post('ajcode/search/scrollsearch',data).done(function(jcode){
                console.log(jcode);
                var jc = $.parseJSON(jcode);
                var html='';
                if (jc.success==1) {//--success
                  //-----------------------section one
                    for(var i=0;i<jc.data.length;i++){
                      html+='<div class="col-xs-12 col-sm-6">\
                           <div class="media" style="border-bottom: 1px dashed silver;border-radius:5px;padding:10px;overflow:hidden;">\
                              <div class="col-xs-12 col-sm-3">\
                                <a href="profile/'+jc.data[i]['u_id']+'">\
                                  <div style="width: 80px; height: 80px;background-image: url(views/assets/images/users/'+(jc.data[i]['u_img'])+');border: 4px solid #f73b3a;" class="bg-img img-circle">\
                                  </div>\
                                </a>\
                              </div>\
                              <div class="col-xs-12 col-sm-9">\
                                <h5 class="media-heading" style="color:#202020"><strong>'+jc.data[i]['u_name']+'</strong></h5>\
                                <p class="">'+jc.data[i]['u_estado']+'</p>\
                                <p><img  src="views/assets/images/zodiacal/'+jc.data[i]['u_zicon']+'" width="30">'+jc.data[i]['u_signo']+'<i class="c-mostaza fa fa-circle-o-notch fa-lg"></i> <span style="font-size: 15px;">'+jc.data[i]['u_edad']+'</span> age</p>\
                              </div>\
                            </div><br>\
                        </div>'; 
                    }
                    $('#loadOptionsSearch').html(html);
                }
            });
     },
     pb:function(){
        $.get('ajcode/search/PB',{pb_roken:toke_main}).done(function(string){
          
          var r = $.parseJSON(string);
          if(r.success==1){
            $('#s_option').val(r.data[0]['pb_option']);
            $('#s_with').val(r.data[0]['pb_preference']);
            $('#s_szinc').val(r.data[0]['pb_szodiacal']);
            $('#s_location').val(r.data[0]['pb_state']);
          }
         SR.search($('#s_foption').serialize()+'&s_name='+$('#s_name').val());
        });
     }

   }
SR.init();
})(document)
