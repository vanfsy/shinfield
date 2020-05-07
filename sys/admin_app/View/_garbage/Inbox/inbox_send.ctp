          <h1 class="page-header">受信トレイ</h1>

          <h2 class="sub-header">メール返信</h2>
<?= $this->Form->create('EmailData',array('id' => 'SeekerEntryForm', 'url' => '/inbox/inbox_send/'.$intEmailDataId,'class' => 'form-horizontal','role'=>'form')); ?>
        <div class="panel panel-default">
          <div class="panel-heading">
<?= $this->Form->input('subject',array('label'=>'件名','class'=>'form-control','default' => $arrInboxMail['EmailData']['subject'])) ?>
            <div class="Rfloat">
              宛先人：<?= h($arrInboxMail['EmailData']['name']) ?>
            </div><br class="clear">
          </div>
          <div class="panel-body">
<?= $this->Form->input('body',array('label'=>'本文','class'=>'form-control','rows' => 20,'default' => $arrInboxMail['EmailData']['body'])) ?>
          </div>
        </div>
        <a href="/inbox"><button type="button" class="btn btn-primary btn-lg">キャンセル</button></a>
        <button type="submit" class="btn btn-primary btn-lg">送信確認</button>
<?php echo $this->Form->end(); ?>
      </div>
