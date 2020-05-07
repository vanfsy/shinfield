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

</head>
<body style="background:none;">

<?= $content_for_layout ?>
	<script type="text/javascript">
		if(parent.document.URL != '<?= FULL_BASE_URL ?>/admin/member/login'){
			parent.location.href = "<?= FULL_BASE_URL ?>/admin/member/login";
		}
	</script>

</body>
</html>
