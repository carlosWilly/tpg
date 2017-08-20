function preloadAjaxModal(idClass,size='4x'){
   return $(idClass).html('<center><i class="fa fa-spinner fa-pulse fa-'+size+'" aria-hidden="true"></i><br>Wait, please</center>');
}