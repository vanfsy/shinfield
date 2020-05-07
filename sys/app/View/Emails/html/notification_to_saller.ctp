いつも<?php echo Configure::read('info.siteName'); ?>をご利用いただきありがとうございます。<br>
出品者に気になるを通知しました。<br><br>
▼ 商品情報<br>
商品ID : <?php echo $value['item_id']; ?><br>
商品名 : <?php echo $value['item_name']; ?><br>
出品者 : <?php echo $value['company']; ?>&nbsp;<?php echo $value['nickname']; ?><br>
<br>
商品情報はこちらを参照下さい。<br>
http://<?php echo Configure::read('info.domain'); ?>/item/detail/<?php echo $value['item_id'] ?><br><br>
--------------------------<br><br>
▼ お問い合わせ先<br>
<?php echo Configure::read('info.adminMail'); ?><br>
---------------------------------------------------------------------
