いつも<?php echo Configure::read('info.siteName'); ?>をご利用いただきありがとうございます。<br>
あなたが出品した下記の商品が購入されました。<br><br>
▼ 商品情報<br>
商品ID : <?php echo $datas['id']; ?><br>
商品名 : <?php echo $datas['title']; ?><br><br>
▼ お問い合わせ先<br>
<?php echo Configure::read('info.adminMail'); ?><br>
---------------------------------------------------------------------
