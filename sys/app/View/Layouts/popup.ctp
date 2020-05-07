<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Language" content="ja" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>ログイン</title>
<link rel="stylesheet" type="text/css" href="<?= FULL_BASE_URL ?>/admin/css/style.css" />
<link type="text/css" href="<?= FULL_BASE_URL ?>/admin/css/jquery-ui.css" rel="stylesheet" />
<script type="text/javascript" src="<?= FULL_BASE_URL ?>/admin/js/fckeditor/fckeditor.js"></script>
<script type="text/javascript" src="<?= FULL_BASE_URL ?>/admin/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?= FULL_BASE_URL ?>/admin/js/jquery-ui-1.8.custom.min.js"></script>
<script type="text/javascript" src="<?= FULL_BASE_URL ?>/admin/js/interface_1.2/interface.js"></script>
<script type="text/javascript" src="<?= FULL_BASE_URL ?>/admin/js/admin.js"></script>
<script type="text/javascript" src="<?= FULL_BASE_URL ?>/admin/js/edit_area/edit_area_full.js"></script>

</head>
<body style="background:none;padding:15px 15px 15px 15px;">
<div id="top"></div>
<?= $content_for_layout ?>

	<script type="text/javascript">
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
</body>
</html>
