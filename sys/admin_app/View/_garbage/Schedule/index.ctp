          <h1 class="page-header">求人情報公開設定</h1>

          <h2 class="sub-header">求人情報 一覧</h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>タイトル</th>
                  <th nowrap="nowrap">現在状況</th>
                  <th nowrap="nowrap">公開予定日</th>
                  <th nowrap="nowrap">公開終了予定日</th>
                  <th nowrap="nowrap">設定変更</th>
                </tr>
              </thead>
              <tbody>
<? foreach($arrDatas as $row){ ?>
                <tr>
                  <td><?= $row['JobData']['id'] ?></td>
                  <td><a href="/job/detail/<?= $row['JobData']['id'] ?>"><?= $row['JobData']['title'] ?></a></td>
<? if($row['JobData']['public_flg']){ ?>
                  <td>公開中</td>
<? }else{ ?>
                  <td>非公開</td>
<? } ?>
                  <td><? if(!empty($row['JobSchedule']['start'])){ echo date('Y.m.d',strtotime($row['JobSchedule']['start']));} ?></td>
                  <td><? if(!empty($row['JobSchedule']['end'])){ echo date('Y.m.d',strtotime($row['JobSchedule']['end']));} ?></td>
<? if(!is_null($row['JobSchedule']['edit_flg']) && $row['JobSchedule']['edit_flg'] == 0){ ?>
                  <td>設定変更</td>
<? }else{ ?>
                  <td><a href="/schedule/edit/<?= $row['JobData']['id'] ?>">設定変更</a></td>
<? } ?>
                </tr>
<? } ?>
              </tbody>
            </table>
          </div>