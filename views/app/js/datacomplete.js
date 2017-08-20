
(function(d){
     var DT = {
         init:function(e){
             $('#jc-complete-send').on('click', function(e) {
                e.preventDefault();
                libereModal('<center><i class="fa fa-spinner fa-spin fa-3x"></i><br><p><b>Processing</b></p></center>','none','#fafafa','static');
                $.post('ajcode/register/datacomplete',$('#jc-datacomplete').serialize()).done(function(json){
                  var obj = $.parseJSON(json);
                  if (obj.success==1) {
                      messageAjaxModal('#jcode-modal-content',obj.message,'smile');
                      $('#jcode-modal-multiple').on('hidden.bs.modal', function (e) {
                        location.reload();
                      });
                  }else messageAjaxModal('#jcode-modal-content',obj.message,'sad');
                });
            });
            //-------------
            $('[rel="popover"]').popover({
                trigger:'hover',
                html:true,
                delay:200,
            });
         },
         loadGoogle:function(){
          var autocomplete;
            autocomplete = new google.maps.places.Autocomplete((document.getElementById('jc-reg-location_1')),{types: ['geocode']});
            autocomplete = new google.maps.places.Autocomplete((document.getElementById('jc-reg-location_2')),{types: ['geocode']});
         }
     }
DT.init();
DT.loadGoogle();
})(document)


//-------------------------------------------------------------------------------------------------------------------
