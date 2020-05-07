<?php
class PageContent extends AppModel {

//    public $name = 'PageContent';
//    var $error_message = null;
    var $pgnum = 0;

    /*
     * 入力値検証
     */

    function isValid(){

        $validate["name"]["isUnique"]  = array("rule" => "isUnique", "message" => "既に同名のページ名があります。");
        $validate["slug"]["isSlug"]  = array("rule" => "isSlug", "message" => "スラッグは半角英数字と_（アンダーバー）だけです。");
        $validate["slug"]["isEqControllerName"]  = array("rule" => "isEqControllerName", "message" => "同名のディレクトリがあります。ディレクトリと同じスラッグ名は登録出来ません。");

        $validate["name"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
        $validate["slug"]["notEmpty"]  = array("rule" => "notEmpty", "message" => "必須項目です。");
        $this->validate = $validate;

    }

    /*
     * エラーチェック
     *
    function isError($data){
        if(parent::isError($data,'page_contents')){
            if(!$this->isUniqueSlug($data)){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
     * */

    /*
     * フィールド
    function getFields($table = 'page_contents'){
        return parent::getFields($table);
    }
    */

    /*
     * オプションリスト
    function getOptions($table = 'page_contents'){

        return parent::getOptions($table);
    }
    */

    /*
     * オプションリスト
    function getTreeOptions($table = 'page_contents'){

        return parent::getTreeOptions($table);
    }
    */

    /*
     * リスト
    function getOneFieldById($id,$field){

        // SQL処理
        $sql =  "SELECT `$field` FROM page_contents WHERE id = '$id' AND del_flg = 0";
        $array = $this->query($sql);
        $res = "親ページなし";
        if(count($array) > 0) $res = $array[0]["page_contents"][$field];
        return $res;
    }
     */

    /*
     * リスト
     */
    function getOneEntityById($id){

        // SQL処理
        $sql =  "SELECT * FROM page_contents WHERE id = '$id' AND del_flg <= 0";
        $array = $this->query($sql);
        $arrRes = null;
        if(count($array) > 0){
            $arrRes = $array[0];
            $dir = $array[0]["page_contents"]["dir"];
            $arrRes["page_contents"]["dir"] = null;
            $arrRes["page_contents"]["dir_url"] = null;
            if($dir != "/"){
                $dir = str_replace("/","",$dir);
                $arrRes["page_contents"]["dir"] = "/".$dir;
                $arrRes["page_contents"]["dir_url"] = $dir;
            }
        }

        return $arrRes;
    }

    /*
     * リスト
     */
    function getOneEntityBySlug($slug){
        // SQL処理
        if($slug == "root") $slug = "top";
//        $sql =  "SELECT * FROM page_contents WHERE slug = '$slug' AND status = 'public' AND del_flg <= 0";
        $sql =  "SELECT * FROM page_contents WHERE slug = '$slug' AND del_flg = 0";
        $array = $this->query($sql);
        if(count($array) > 0){
            return $array[0]["page_contents"];
        }else{
            return null;
        }
    }

    /*
     * 登録更新処理
    function isSave($data){
        return parent::isSave($data['page_contents']);
    }
     */

    /*
     * カテゴリリスト
     */
    function getListCategoryAdmin(){

        // SQL処理
        $sql =  "SELECT * FROM article_categories WHERE parent_id = 0 AND del_flg = 0 ORDER BY rank";
        $array = $this->query($sql);

        $arrRes = array();
        foreach($array as $row){
            $p_id = $row["article_categories"]["id"];
            $sql =  "SELECT * FROM article_categories WHERE parent_id = '$p_id'";
            $arrCat = $this->query($sql);
            foreach($arrCat as $row_c){
                $val = $row_c["article_categories"];
                $arrRes[$val["id"]] = $row["article_categories"]["name"]." > ".$val["name"];
            }
        }

        return $arrRes;
    }

    /*
     * ページ内容取得
     */
    function getContentById($id){

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM page_contents ";
        $sql .=  " INNER JOIN page_layouts ";
        $sql .=  "         ON page_contents.layout_id = page_layouts.id ";
        $sql .=  " INNER JOIN page_regions ";
        $sql .=  "         ON page_layouts.main_region_id = page_regions.id ";
        $sql .=  "      WHERE page_contents.del_flg = 0 ";
        $sql .=  "        AND page_layouts.del_flg = 0 ";
        $sql .=  "        AND  page_contents.id = '$id' ";
        $array = $this->query($sql);
        return $array;
    }

    /*
     * ブロック内容取得
     */
    function getBlocksById($layout_id,$content_id,$main_region_id){

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM page_regions ";
        $sql .=  "      WHERE page_regions.del_flg = 0 ";
        $sql .=  "        AND page_regions.id <> '$main_region_id' ";
        $sql .=  "        AND page_regions.layout_id = '$layout_id' ";
        $array = $this->query($sql);

        $arrRes = array();
        foreach($array as $key => $val){

            $region_id = $val["page_regions"]["id"];
            $sql =  "";
            $sql .=  "     SELECT * ";
            $sql .=  "       FROM page_blocks ";
            $sql .=  " INNER JOIN page_content_blocks ";
            $sql .=  "         ON page_blocks.id = page_content_blocks.page_block_id ";
            $sql .=  "      WHERE page_content_blocks.del_flg <= 0 ";
            $sql .=  "        AND page_blocks.del_flg <= 0 ";
            $sql .=  "        AND page_content_blocks.page_region_id = '$region_id' ";
            $sql .=  "        AND page_content_blocks.page_content_id = '$content_id' ";
            $sql .=  "        ORDER BY page_content_blocks.rank ";
            $arrBlocks = $this->query($sql);
            $contents = null;
            foreach($arrBlocks as $row){
                $contents .= $row["page_blocks"]["content"];
            }

            $code = $val["page_regions"]["code"];
            $arrRes[$code] = $contents;
        }

        return $arrRes;
    }

    /*
     * ブロック内容取得
     */
    function getBlocksByBlockId($layout_id,$block_id,$main_region_id){

        // SQL処理
        $sql =  "";
        $sql .=  "     SELECT * ";
        $sql .=  "       FROM page_regions ";
        $sql .=  "  LEFT JOIN page_blocks ";
        $sql .=  "         ON page_regions.id = page_blocks.region_id ";
        $sql .=  "      WHERE page_regions.del_flg = 0 ";
        $sql .=  "        AND page_regions.id <> '$main_region_id' ";
        $sql .=  "        AND page_regions.layout_id = '$layout_id' ";
        $sql .=  "        AND page_blocks.del_flg = 0 ";
        $sql .=  "        AND page_blocks.id = '$block_id' ";
        $array = $this->query($sql);

        $arrRes = array();
        $arrRes["code"] = $array[0]["page_regions"]["code"];
        foreach($array as $key => $val){
            $arrRes["content"] .= $val["page_blocks"]["content"];
        }

        return $arrRes;
    }

    var $pdir = null;

    /*
     * ページング
     */
    function setWhereDir($id){
        $this->pdir = $id;
    }

    /*
     * ページング
     */
    function getPagingEntity($disp_num,$pgnum){

        // ページング用パラメータ設定
        $this->setDispNum($disp_num);
        $this->setPgNum($pgnum);

        // SQL処理
        $sql =  "";
        $sql .=  "   SELECT * ";
        $sql .=  "     FROM page_contents ";
        $sql .=  "    WHERE del_flg <= 0 ";
        if(!empty($this->pdir)){
            $sql .=  "        AND dir = '".$this->pdir."'";
        }
        $sql .=  "    ORDER BY rank , modified desc";

        // ページング用SQL文字列
        $pg_sql = $this->getPagingString($sql);
        $array = $this->query($pg_sql);

        // ランク再処理
        $array = $this->reSort($array,$pgnum,$disp_num);

        $arrRes["list"] = array();
        foreach($array as $key => $row){

            $layout_id = $row["page_contents"]["layout_id"];
            $sql =  "SELECT * FROM page_layouts WHERE id = ".$layout_id;
            $arr = $this->query($sql);

            $arrRes["list"][$key] = $row["page_contents"];
            $arrRes["list"][$key]["layout"]        = $arr[0]["page_layouts"]["name"];

            $dir = $row["page_contents"]["dir"];
            $arrRes["list"][$key]["dir"]           = null;
            if($dir != "/"){
                $dir = str_replace("/","",$dir);
                $arrRes["list"][$key]["dir"] = $dir."/";
            }

            $parent_id = $row["page_contents"]["parent_id"];
            $sql =  "SELECT * FROM page_contents WHERE id = ".$parent_id;
            $arr = $this->query($sql);
            $arrRes["list"][$key]["parent_name"] = @$arr[0]["page_contents"]["name"];

        }

        // ページング用
        $arrRes["current_pg"] = $this->getCurrentPg();

        $where = null;
        if(!empty($this->pdir)){
            $where = " dir = '".$this->pdir."'";
        }
        $arrRes["pg_list"] = $this->getArrPgList($sql);
        $arrRes["prev"] = $this->getPgPrev();
        $arrRes["next"] = $this->getPgNext();

        return $arrRes;
    }

    /*
     * パンくず
     */
    function getTopicPath($slug,$ds){
        // SQL処理
        $sql =  "SELECT * FROM page_contents WHERE slug = '$slug' AND del_flg <= 0";
        $array = $this->query($sql);
        if(count($array) == 0) return null;

        $arrRes = $array[0];
        $name = $array[0]["page_contents"]["name"];
        $parent_id = $array[0]["page_contents"]["parent_id"];
        $dir = HOME_URL.$slug;
        if($slug == "top") $dir = HOME_URL;

        $topic_path = $name;
        $id = $parent_id;
        while( $id > 0 ){

            // SQL処理
            $sql =  "SELECT * FROM page_contents WHERE id = '$id' AND del_flg <= 0";
            $array = $this->query($sql);
            $arrRes = $array[0];
            $name = $array[0]["page_contents"]["name"];
            $parent_id = $array[0]["page_contents"]["parent_id"];
            $slug = $array[0]["page_contents"]["slug"];
            $dir = HOME_URL.$slug;
            if(!empty($topic_path)) $topic_path = $ds . $topic_path;
            $topic_path = "<a href='$dir'>".$name."</a>".$topic_path;
            $id = $parent_id;
        }
        return $topic_path;
    }

    /*
     * 存在判定
     */
    function isExistBySlug($slug,$url_dir){
        if($slug === 'top') return true;
        // SQL処理
        $sql =  "SELECT * FROM page_contents WHERE slug = '$slug' AND del_flg <= 0";
        $array = $this->query($sql);
        $flg = false;
        if(count($array) > 0){
            $flg = true;
            $dir = $array[0]["page_contents"]["dir"];
            if($url_dir != $dir) $flg = false;
        }
        return $flg;
    }

    /*
     * 公開判定
     */
    function isPublicBySlug($slug){

//        if($slug === 'top') return true;
        // SQL処理
        $sql =  "SELECT count(*) as cnt FROM page_contents WHERE slug = '$slug' AND status = 'public' AND del_flg <= 0";
        $array = $this->query($sql);
        $flg = false;
        if($array[0][0]["cnt"] > 0) $flg = true;
        return $flg;
    }

    /*
     * ヘッダ画像取得
     */
    function getFieldBySlug($slug,$field_name){

        $field = null;
        // SQL処理
        $sql =  "SELECT * FROM page_contents WHERE slug = '$slug' AND status = 'public' AND del_flg <= 0";
        $array = $this->query($sql);
        if(count($field) > 0){
            $field = $array[0]["page_contents"][$field_name];
            $parent_id = $array[0]["page_contents"]["parent_id"];

//        echo $parent_id."-".$field."/";
            while($parent_id > 1){
                if(!empty($field)){
                    break;
                }
                    $sql =  "SELECT * FROM page_contents WHERE id = '$parent_id' AND status = 'public' AND del_flg <= 0";
                    $array = $this->query($sql);
                    $field = $array[0]["page_contents"][$field_name];
                    $parent_id = $array[0]["page_contents"]["parent_id"];
            }
        }

        return $field;
    }

    /*
     * ヘッダ画像削除
     */
    function isDeleImageItem($id,$field_name){

        $data["page_contents"]["id"] = $id;
        $data["page_contents"][$field_name] = "null";

        return $this->isSave($data);
    }

    /*
     * パンくず
     */
    function getTopicPathArray($slug){
        // SQL処理
        $sql =  "SELECT * FROM page_contents WHERE slug = '$slug' AND del_flg <= 0";
        $array = $this->query($sql);
        if(count($array) == 0) return null;

        $name = $array[0]["page_contents"]["name"];
        $parent_id = $array[0]["page_contents"]["parent_id"];
        $dir = $array[0]["page_contents"]["dir"];
//        $dir = HOME_URL.$slug;
        if($slug == "top") $dir = HOME_URL;

        $home_url = substr(HOME_URL, 0, (strlen(HOME_URL)-1));
        $topic_path[0]["name"] = $name;
        $topic_path[0]["url"]  = null;

        $i = 1;
        $id = $parent_id;
        while( $id > 0 ){

            // SQL処理
            $sql =  "SELECT * FROM page_contents WHERE id = '$id' AND del_flg <= 0";
            $array = $this->query($sql);
            $name = $array[0]["page_contents"]["name"];
            $parent_id = $array[0]["page_contents"]["parent_id"];
            $slug = $array[0]["page_contents"]["slug"];
            $dir  = $array[0]["page_contents"]["dir"];
            $topic_path[$i]["name"] = $name;
            $topic_path[$i]["url"]  = $home_url.$dir."/".$slug;
            if($slug === "top"){
                $topic_path[$i]["url"]  = $home_url;
            }
            $id = $parent_id;
            ++$i;
        }

        $max = count($topic_path);
        $cnt = $max - 1;
        $arrRes = array();
        for($i=0;$i<$max;++$i){
            $arrRes[$i] = $topic_path[$cnt];
            --$cnt;
        }

        return $arrRes;
    }

    /*
     * サイトツリー
     */
    function getSiteTree(){
        // SQL処理
        $sql =  "SELECT * FROM page_directories WHERE del_flg <= 0";
        $arrDir = $this->query($sql);

        $arrRes = array();
        foreach($arrDir as $row){
            $dir_name = $row["page_directories"]["name"];
            $dir_name = "/".$dir_name;

            $sql =  "SELECT * FROM page_contents WHERE dir = '$dir_name' AND del_flg <= 0 ORDER BY rank";
            $arrPg = $this->query($sql);
            foreach($arrPg as $val){
                $pg_name = $val["page_contents"]["name"];
                $id = $val["page_contents"]["id"];
                $arrRes[$dir_name][$pg_name] = $id;
            }
        }

        return $arrRes;
    }


    /*
     * スラッグ正当性判定
     */
    function isUniqueSlug($data){
        $id   = $data["page_contents"]["id"];
        $slug = $data["page_contents"]["slug"];
        $dir  = $data["page_contents"]["dir"];
        // SQL処理
        $sql = "";
        $sql .= " SELECT * ";
        $sql .= "   FROM page_contents ";
        $sql .= "  WHERE slug = '$slug' ";
        $sql .= "    AND dir = '$dir' ";
        $sql .= "    AND id <> '$id' ";
        $sql .= "    AND del_flg <= 0";
        $array = $this->query($sql);
        if(count($array) > 0){
            $this->error_messages["slug"] = "既に同名のスラッグがあります。";
            return false;
        }else{
            return true;
        }
    }

}
