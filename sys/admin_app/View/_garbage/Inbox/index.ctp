          <h1 class="page-header">受信トレイ</h1>

          <h2 class="sub-header">受信一覧</h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th width='20%'>件名</th>
                  <th width='65%'>差出人</th>
                  <th width='15%'>受信日時</th>
                </tr>
              </thead>
              <tbody>
<? foreach($arrInboxList as $row){ ?>
                <tr>
                  <td><a href="/inbox/inbox_mail/<?= $row['EmailData']['id'] ?>"><?= $row['EmailData']['subject'] ?></a></td>
                  <td><?= $row['EmailData']['name'] ?></td>
                  <td><?= date("Y.n.j H:i" , strtotime($row['EmailData']['send_date'])) ?></td>
                </tr>
<? } ?>
              </tbody>
            </table>
          </div>