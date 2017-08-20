(function(d){
	$('#jcode-append-search').hide();

	$('#jcode-top-search').keyup(function(){
     //-------------------------------------------

     if ($('#jcode-top-search').val()=='') {

     	   $('#jcode-append-search').hide();

     }else{

         $('#jcode-append-search').show();
     }

    //preloadAjaxModal('#jcode-append-search','2x');
    var busqueda = $(this).val();
    var promise = $.post('ajcode/topSearch','data-search='+busqueda);
    var data ='';
    promise.done(function(json){
       var obj = $.parseJSON(json);
       if(obj.success == 1) {
        
         for(var i=0; i < obj.data.length; i++){
             data+='<a  href="profile/'+obj.data[i]['b_id']+'" class="jc-a profile" data="'+(obj.data[i]['b_id'])+'">\
                       <div class="media" style="margin-bottom:5px;">\
                           <div class="media-left" style="padding:10px;">\
                               <div class="bg-img img-circle img-responsive" style="width:50px;height:50px;background-image:url(views/assets/images/users/'+(obj.data[i]['b_image'] == null ?'no-img.jpg' : obj.data[i]['b_image'])+');"></div>\
                           </div>\
                           <div class="media-body" >\
                                <h4 class="text-left" style="font-size:13px;">'+obj.data[i]['b_name']+'</h4>\
                                <p class="text-left" style="color:#2a2a2a;font-size:12px;">'+obj.data[i]['b_zodiacal']+' '+obj.data[i]['b_edad']+' Age</p>\
                            </div>\
                        </div>\
                     </a>';
          };
            data+='<div id="afterSearch"></div>';
          $('#jcode-append-search').html(data);

         
      } else {
             messageAjaxModal('#jcode-append-search',obj.message,'sad',true);
      }
      //scroll dinamico
      if($('#jcode-append-search').height() >= 350) {

               $('#jcode-append-search').removeClass('jtopsearchStatic').addClass('jtopsearchDinamic');
               //deteccion de los procesos de scroll
               //DETENCCION DE scoll para carga de datos
                var pagina=0;
                var win = $('#jcode-append-search');
                  
                 win.scroll(function(){
                  
                   if (win.scrollTop() >= 45){
                   
                    pagina++;

                    var nuevo = $.post('ajcode/topSearch','data-search='+busqueda+'&page='+pagina);
                    nuevo.success(function(json){
                        var ne = $.parseJSON(json);
                       //-----------------------------------------------------------------------------
                          if (ne.success == 1) { 
                              if (ne.data.length >0) {   
                                 var extra='';  
                                 for(var i=0; i < ne.data.length; i++){
                                     extra+='<a  href="profile/'+ne.data[i]['b_id']+'" class="jc-a profile" data="'+(ne.data[i]['b_id'])+'">\
                                               <div class="media" style="margin-bottom:5px;">\
                                                   <div class="media-left" style="padding:10px;">\
                                                       <div class="bg-img img-circle img-responsive" style="width:50px;height:50px;background-image:url(views/assets/images/users/'+(ne.data[i]['b_image'] == null ?'no-img.jpg' : ne.data[i]['b_image'])+');"></div>\
                                                   </div>\
                                                   <div class="media-body" >\
                                                        <h4 class="text-left" style="font-size:13px;">'+ne.data[i]['b_name']+'</h4>\
                                                        <p class="text-left" style="color:#2a2a2a;font-size:12px;">'+ne.data[i]['b_zodiacal']+' '+ne.data[i]['b_edad']+' Age</p>\
                                                    </div>\
                                                </div>\
                                             </a>';
                                  };  
                                $('#jcode-append-search').scrollTop(35);
                                $('#afterSearch').append(extra); 
                              }

                            }
                       //-----------------------------------------------------------------------------
                    })
                   }          
                  });
               //-------------------------------------------------------------------------------------- 
      }else{
               $('#jcode-append-search').removeClass('jtopsearchDinamic').addClass('jtopsearchStatic'); 
      }
            //scroll dinamico
        });
	});

 //input tipo select al actualizar las opciones de busca cambia  al seleccionar stados este verifica las coudades
 $('#jcode-modal-content').on('change','#jc-state',function(){
     var states = dataSingleAjax('get',false,'search/cityState',{'params':'token='+this.value});
     states.success(function(json){
         var obj = $.parseJSON(json);
         var html='<option val="0">All citys</option>';
         if (obj.success == 1) {

             if (obj.data.length >0) {
              for(var i=0;i<obj.data.length;i++){
                 html +='<option val="'+obj.data[i]['ci_id']+'">'+obj.data[i]['ci_name']+'</option>';
              }
              
             }
           
         }
         $('#jc-city').html(html);
     });
 });
})(document)
