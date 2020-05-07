// JavaScript Document
jQuery(function($){
    $(".pd_main_img").magnificPopup({ 
        type: 'image'
    });
	$('#pd_sub_img').magnificPopup({
	  delegate: 'a', 
	  type: 'image',
	  gallery: { 
		enabled:true
	  }
	});
});