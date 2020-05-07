<?php
App::uses('AppHelper', 'View/Helper');
 
class UtilityHelper extends AppHelper {
      
    public function deviceCheck(){

    }
     
    public  function day_diff($created) {
        // 日付をUNIXタイムスタンプに変換
        $createdTimestamp = strtotime($created);
        // 何秒離れているかを計算
        $diff = time() - $createdTimestamp;
        //一週間以上経過しているか確認
        return $diff < 604800;
                                                  
    }
}
