          <h1 class="page-header">受信トレイ</h1>

          <h2 class="sub-header">メール詳細</h2>

        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><?= h($arrInboxMail['EmailData']['subject']) ?></h3>
            <div class="Rfloat">
              差出人：<?= h($arrInboxMail['EmailData']['name']) ?>
              日時：<?= h($arrInboxMail['EmailData']['send_date']) ?>
            </div><br class="clear">
          </div>
          <div class="panel-body">
            <?= nl2br(h($arrInboxMail['EmailData']['body'])) ?>
            <div class="Rfloat"><a href="/inbox/inbox_send/<?= $arrInboxMail['EmailData']['id'] ?>"><button type="button" class="btn btn-danger">返信</button></a></div>
          </div>
        </div>
        <a href="/inbox"><button type="button" class="btn btn-primary btn-lg">一覧へ戻る</button></a>
      </div>
