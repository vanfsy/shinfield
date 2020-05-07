$(function(){
  $("#sp_menu_btn").click(function(){
    $("#menu").slideToggle();
    return false;
  });
  $(window).resize(function(){
    var win = $(window).width();
    var p = 990;
    if(win > p){
      $("#menu").show();
    } else {
      $("#menu").hide();
    }
  });
});
