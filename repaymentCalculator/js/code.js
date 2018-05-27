$(document).ready(function(){
  $('#selectallboxes').cleck(function(event){

    if(this.checked){
      $('.checkboxes').each(function(){
        this.checked = true;
      });
    } else {
      $('.checkboxes').each(function(){
        this.checked = false;
      });
    }
  });
});
