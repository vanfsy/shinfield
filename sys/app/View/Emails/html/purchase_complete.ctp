いつも<?php echo Configure::read('info.siteName'); ?>をご利用いただきありがとうございます。<br>
下記の商品の購入が完了しました。<br><br>
<?php $pointTotal = null; ?>
<?php foreach ($datas as $value) : ?>
▼ 商品情報<br>
商品ID : <?php echo $value['item_id']; ?><br>
商品名 : <?php echo $value['item_name']; ?><br>
出品者 : <?php echo $value['nickname'] ?><br>
▼ 支払い金額<br>
決済方法 : ポイント<br>
決済ポイント : P <?php echo $value['total']; ?><br><br>
<?php $pointTotal += $value['total']; ?>
--------------------------<br>
<?php endforeach; ?>
支払い合計金額（ポイント） : ￥<?php echo $pointTotal; ?><br><br>
▼ お問い合わせ先<br>
<?php echo Configure::read('info.adminMail'); ?><br>
---------------------------------------------------------------------