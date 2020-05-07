いつも<?php echo Configure::read('info.siteName'); ?>をご利用いただきありがとうございます。<br>
下記の決済が完了しました。<br><br>
▼ 購入ポイント情報<br>
今回購入ポイント：<?php echo $purchasePoint; ?><br>
お支払い金額：<?php echo $paymentAmount; ?><br><br>
▼ お問い合わせ先<br>
<?php echo Configure::read('info.adminMail'); ?><br>
---------------------------------------------------------------------