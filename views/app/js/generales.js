
function scale(){
  var w = $(window).width()-16;
  var left = $('#jc_barLeft').width();
  var right = $('#jc_barRight').width();
  var center = $('#jc_barCenter').width();
  result = w - (left+right);
  $('#jc_barCenter').width(result+'px'); 
}

function resizeCover(){
     var screen = $(window).height();
     var topmenu = $('#zone-topmenu').height();
     var abosluto = screen - topmenu;
     $('#jc-cover').height(abosluto);
}

 scale();
$(window).resize(function() {
  scale();
  resizeCover()
});




(function(d){

  

var id;
$(window).resize(function() {

    clearTimeout(id);
    id = setTimeout(function(){
         resizeCover();
         doneResizing();  
    }, 200);
});


  //--------------------------------------------------------------------
  //define la altura de la barra del chat
  var alto = $(window).height() - $('#zone-topmenu').height() -1;
  $('#jcode-right-chat').height(alto+'px');
  $('#jcode-container-left').height(alto+'px');

  //---------------------------------------------------------------------
  //Desloguea
  $('#jc-logout').click(function(){
       var logout = $.post('ajcode/salir',{'foo':'all'});
       logout.done(function(json){
       console.log(json);
       if (json ==1) {
         conn.send ( JSON . stringify ( {action:'notification',token:toke_main,type:2} )); 
         document.location.href='home';

       }
       });
  });


  //--inicializamos para elizar los scroll con nicescroll
   $(d).ready(function(){

     $("#search-options").niceScroll();
     $("#jcode-load-chat").niceScroll(); 
     $('[data-toggle="tooltip"]').tooltip();
     //creacion de cuenta

       $('html').click(function(){
       $('#jcode-append-search').hide().html('');
     });
     
   });

})(document);
 
  //------------------------------------------------------------------------------------
  
