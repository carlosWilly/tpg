(function(d){
	$('.jc-payment').click(function(){
      var token = this.getAttribute('data-token');
      
      var padre = $(this).parents('div.payments').siblings('div.si');
      var precio =padre.find('h2 >span > span').text();
      var plan =padre.find('h1').text();
      var color=padre.find('i').attr('data');
      var html='<div class="panel panel-default">\
      <div class="panel-body">\
          Payment details:<br>\
          <img src="views/assets/img/paypal.png" class="img-responsive" />\
          <div class="media">\
             <div class="media-left">\
                <i class="fa-5x fa fa-shield '+color+'" aria-hidden="true"></i>\
             </div>\
             <div class="media-body">\
                <h1>'+plan+'</h1>\
                <h2 style="font-size:50px;"><i class="fa fa-usd fa-lg" aria-hidden="true"></i> '+precio+' </h2><br />\
             </div>\
           </div>\
          <button class="btn btn-primary btn-block btn-lg" id="jc-confirm-pay" data-token="'+token+'">Confirm</button>\
          <button class="btn btn-default btn-block btn-lg" data-dismiss="modal">Cancel</button>\
      </div>\
      </div>';
      libereModal(html,'sm','white','static');
	});
	$('#jcode-modal-content').on('click','#jc-confirm-pay',function(){
      var token = this.getAttribute('data-token');
      preloadAjaxModal('#jcode-modal-content','3x'); 
      var promise = dataSingleAjax('POST',false,'payment/pay',{'params':'token='+token});
      promise.success(function(json){

         var obj = $.parseJSON(json);
         if (obj.success==1) {

            document.location.href=obj.url;
         }else{

            messageAjaxModal('#jcode-modal-content',obj.message,'sad');
         }
      });
	});

  $('.jc-removeTransaction').on('click',function(){
   
    libereModal('<center><i class="fa fa-spinner fa-pulse fa-4x" aria-hidden="true"></i><br>Going up, wait, please</center>','sm','white','static');

    var token = this.getAttribute('data-token');
    var promise = $.post('ajcode/payment/removeTransaction',{tokenTr:token});
    promise.done(function(json){
      var obj = $.parseJSON(json);
        var html='';
        if (obj.success==1) {
            messageAjaxModal('#jcode-modal-content',obj.message,'smile');
            $('#jcode-modal-multiple').on('hidden.bs.modal', function (e) {
              location.reload();
            });
        }
    });
  });

  $('#jc-fullPackOn').on('click', function(event) {
    event.preventDefault();
    libereModal('<center><i class="fa fa-spinner fa-4x fa-spin"></i></center>','sm','white','static');
    var promise = $.post('ajcode/payment/fullPack',{token:'all-full'});
    promise.done(function(json){
      var obj = $.parseJSON(json);
      if (obj.success==1) {
          messageAjaxModal('#jcode-modal-content',obj.message,'smile');
          $('#jcode-modal-multiple').on('hidden.bs.modal', function (e) {
            location.href='home';
          });
      }else{
          messageAjaxModal('#jcode-modal-content',obj.message,'sad');
      }
    });
  });

})(document)