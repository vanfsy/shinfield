パスワードの再発行を以下の内容で行いました。<br><br>
下記よりログインをお願いします。<br><br>
▼「<?php echo Configure::read('info.siteName'); ?>」ログインページ<br>
http://<?php echo Configure::read('info.domain'); ?>/mypage/login<br><br>
・メールアドレス： <?php echo $mailAddress; ?><br>
・パスワード： <?php echo $password; ?><br><br>
ご本人様確認のため、下記URLへアクセスし<br>
アカウントの本登録を完了させて下さい。<br><br>
<?php echo $tmpLink; ?><br><br>
▼ お問い合わせ先<br>
<?php echo Configure::read('info.adminMail'); ?><br>
---------------------------------------------------------------------
