<link rel="stylesheet" type="text/css" href="<?= FULL_BASE_URL ?>/admin/css/style.css" />
<link type="text/css" href="<?= FULL_BASE_URL ?>/admin/css/jquery-ui.css" rel="stylesheet" />
<script type="text/javascript" src="<?= FULL_BASE_URL ?>/admin/js/fckeditor/fckeditor.js"></script>
<script type="text/javascript" src="<?= FULL_BASE_URL ?>/admin/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?= FULL_BASE_URL ?>/admin/js/jquery-ui-1.8.custom.min.js"></script>
<script type="text/javascript" src="<?= FULL_BASE_URL ?>/admin/js/interface_1.2/interface.js"></script>
<script type="text/javascript" src="<?= FULL_BASE_URL ?>/admin/js/admin.js"></script>

<?= $content_for_layout ?>

<script type="text/javascript">
	$('.menu_elm').click(function(event){
		divid = $(this).text() + "_menu_elm";
		$("#" + divid).toggle();
	});

	$("form").submit(function() {
		url = $("form").attr("action");
		var params = $('form').serialize();
		$.post(url, params,
				function(rs) {
					$("#main_contents").html(rs);
				});

		return false;
	});

	$("a").click(function() {
		url = this.href;
		$.post(url, null,
				function(rs) {
					$("#main_contents").html(rs);
				});
		return false;
	});

	$('#content_h1', parent.document).text('<?= $param.title ?>');
	$('#content_h2', parent.document).text('<?= $param.h2 ?>');
	h = document.body.clientHeight + 48;
	$('#main', parent.document).css('height',h);

	setTimeout(function(){
		$('#head_message').fadeOut(3000);
	}, 10000);
	$('form').click(function(event){
		$('#head_message').text("");
		$('#head_message').css("border","none");
	});

</script>

