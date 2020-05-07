<?php
class UtilityComponent extends Component {

/*
 * マスターデータ取得
 */
    function getMasterData() {
        // マスターデータ取得
        $DataLeaves      = ClassRegistry::init('DataLeave');
        $master_data = Cache::read("arrMasterData");
        // マスターデータ有効期限切れの場合
        if(empty($master_data)){
            $master_data = $DataLeaves->getArrMasterData();
            Cache::write("arrMasterData",$master_data);
        }
        return $master_data;
    }

/*
 * テンプレート生成
 */
    function createTemplate($id,$temp = null,$page_contents = null) {
        // テンプレート生成
        $PageContents      = ClassRegistry::init('PageContent');
        $PageLayouts = ClassRegistry::init('PageLayout');
        $PageContentParameters = ClassRegistry::init('PageContentParameter');

        $res = $PageContents->getContentById($id);
        if(count($res)==0){

            $layout_id = $page_contents["page_contents"]["layout_id"];
            $html = $PageLayouts->getOneFieldById($layout_id,"layout");

            $main_region_id = $PageLayouts->getOneFieldById($layout_id,"main_region_id");
            $PageRegions = ClassRegistry::init('PageRegion');
            $main_region = $PageRegions->getOneFieldById($main_region_id,"code");

        }else{
            $html = $res[0]['page_layouts']['layout'];
            $layout_id   = $res[0]['page_layouts']['id'];
            $main_region = $res[0]['page_regions']['code'];
            $content_id  = $res[0]['page_contents']['id'];
        }

        $content     = $page_contents['page_contents']['content'];
        $title       = $page_contents['page_contents']['meta_title'];
        $description = $page_contents['page_contents']['meta_description'];
        $keywords    = $page_contents['page_contents']['meta_keywords'];
        $slug        = $page_contents['page_contents']['slug'];
        $dir         = $page_contents['page_contents']['dir'];
        $block_id = null;
        if(isset($page_contents['page_content_blocks'])){
            $block_id    = $page_contents['page_content_blocks']['page_block_id'];
        }

        if(empty($page_contents) && count($res) > 0){
            $content     = $res[0]['page_contents']['content'];
            $title       = $res[0]['page_contents']['meta_title'];
            $description = $res[0]['page_contents']['meta_description'];
            $keywords    = $res[0]['page_contents']['meta_keywords'];
            $slug        = $res[0]['page_contents']['slug'];
            $dir         = $res[0]['page_contents']['dir'];
            $main_region_id = $res[0]['page_layouts']['main_region_id'];
        }
            $main_region_id = $PageLayouts->getOneFieldById($layout_id,"main_region_id");

        $html = str_replace("<!--@R_".$main_region."-->",$content,$html);

        // ブロック変換
        $res = @$PageContents->getBlocksById($layout_id,$content_id,$main_region_id);
        if(count($res) > 0){
            foreach($res as $key => $val){
                $html = str_replace("<!--@R_".$key."-->",$val,$html);
            }
        }

        // ページパラメータ変換
        $list = $PageContentParameters->getParametersById($content_id);
        if(count($list) > 0){
            foreach($list as $key => $val){
                $html = str_replace('{$P_'.$key.'}',$val,$html);
            }
        }

        if($dir == "/") $dir = "/publics";
        $view_dir = APP."views".$dir;
        // ディレクトリ確認・作成
        if(!is_dir($view_dir)){
            umask(0);
            $rc = mkdir($view_dir, 0777);
        }

        $filename = $view_dir."/".str_replace("/","",$dir)."_".$slug . $temp.".html";

        $handle = fopen( $filename, 'w');
        fwrite( $handle, $html);
        fclose( $handle );

        // コントローラ ファンクション作成
        $ctrl_path = ROOT."/app/controllers".$dir."_controller.php";
        $c_file = @file_get_contents($ctrl_path);
        $func_name = "function ".str_replace("/","",$dir)."_".$slug."(";

        // 該当するファンクションが存在しない場合
        if(strstr($c_file,$func_name) == false){

            $ds = $this->getDS();

            $str_end = "}$ds$ds?>$ds";
            $new_func = $ds."\t".$func_name."){".$ds;
            if($dir <> "/form") $new_func .= "\t\t\$this->render('".str_replace("/","",$dir)."_".$slug."');".$ds;
            $new_func .= "\t}".$ds;
            $new_func .= "}".$ds.$ds."?>".$ds;

            $c_file = str_replace($str_end,$new_func,$c_file);

            $handle = fopen( $ctrl_path, 'w');
            fwrite( $handle, $c_file);
            fclose( $handle );
        }

    }

    /*
     * テンプレート存在確認
     */
    function tempExists(){

        global $slug;
        global $url_dir;

        $dir = str_replace("/","",$url_dir);
        if(empty($dir)) $dir = "publics";
        $temp_path = $dir."_".$slug;
        $temp_path = APP."views".DS.$dir.DS.$temp_path.".html";
        if(!file_exists($temp_path)){
            return false;
        }else{
            return true;
        }
    }

    function moveFilesTemp($data){
        if(isset($data["temp_image"])){
            foreach($data["temp_image"] as $key => $val){
                $temp_image = WWW_ROOT.'images/items/temp/'.$val;
                $save_image = WWW_ROOT.'images/items/'.$val;
                if(file_exists($temp_image)){
                    @rename($temp_image,$save_image);
                }
            }
        }
    }

    /*
     * ファイルアップ処理
     */
    function fileUpTempSave($files,$size = null){

        $app_dir = ROOT.'/app/tmp/';

        $flg = false;
        $this->file_name = null;
        $this->file_path = null;
        $this->file_extension = null;
        $this->file_type = null;
        $this->file_size = null;

        if(isset($files["name"])){
            $path_parts = pathinfo($files["name"]);
            $this->file_extension = $path_parts['extension'];
            $this->file_type = $files['type'];
            $this->file_size = $files['size'];
            $this->file_name = time().'_'.$this->getGenesCode(8).'.'.$this->file_extension;
            $dir = substr($this->file_name,0,3);

            if(!file_exists($app_dir.'files')) {
            // ディレクトリの場合
                umask(0);
                mkdir($app_dir.'files', 0777);
            }

            $dir_path = $app_dir.'files'.DS.$dir;
            if(!file_exists($dir_path)) {
            // ディレクトリの場合
                umask(0);
                mkdir($dir_path, 0777);
            }

            $this->file_path = $app_dir.'files'.DS.$dir.DS.$this->file_name;
            move_uploaded_file($files["tmp_name"], $this->file_path);
            $flg = true;
        }
        return $flg;
    }

    function getFileName(){
        return $this->file_name;
    }
    function getFilePath(){
        return $this->file_path;
    }
    function getFileType(){
        return $this->file_type;
    }
    function getFileSize(){
        return $this->file_size;
    }
    function getFileExtension(){
        return $this->file_extension;
    }

    /*
     * ファイルアップ処理
     * コントローラより取得して一時フォルダに保持する
     * サイズの指定がない場合、原寸をアップする
     * このバージョンを正規とする
     */
    function fileUpTempImages($files,$size = null){
        if(isset($files["file"]["name"])){
            foreach($files["file"]["name"] as $name => $val){

                $w = null;
                $h = null;
                if(isset($size[$name])){
                    $file_size = explode(",",$size[$name]);
                    $w = $file_size[0];
                    $h = $file_size[1];
                }
                $arrFile["name"]     = $files["file"]["name"][$name];
                $arrFile["type"]     = $files["file"]["type"][$name];
                $arrFile["tmp_name"] = $files["file"]["tmp_name"][$name];
                $arrFile["error"]    = $files["file"]["error"][$name];
                $arrFile["size"]     = $files["file"]["size"][$name];
                $this->fileTempSave($arrFile,$name,$w,$h);
            }
        }

        $flg = false;
        if(count($this->arrImages) > 0) $flg = true;

        return $flg;
    }

    /*
     * ファイルアップ情報
     */
    function fileTempSave($file,$name,$w=null,$h=null){

        $dir = WWW_ROOT.'images/items/';

        $image_file = null;

        if($file["size"] > 0){

            $temp = $file["tmp_name"];
            $file = $file["name"];
            $image_name = date("YmdHis")."_".$name;

            //アップロードするファイルの場所
            $uploaddir = WWW_ROOT.'images/items/temp/';
            $ext = explode(".",$file);
            $image_name = basename($image_name.".".$ext[1]);
            $uploadfile = $uploaddir . $image_name;

            //画像をテンポラリーの場所から、上記で設定したアップロードファイルの置き場所へ移動
            move_uploaded_file($temp, $uploadfile);
            $size=getImageSize($uploadfile);

            if($w == null && $h == null){
                $this->arrImages[$name] = $image_name;
                 return;
            }

            //縦幅より横幅が大きければ横幅を固定、縦幅が大きければ縦幅を固定
            if($w >= $h) $length = $w;
            if($w < $h) $length = $h;
            if($size[0] >= $size[1]){$width = $length; $high = $size[1] * $length / $size[0];}
            else{$width = $size[0] * $length / $size[1]; $high = $length;}
            if($size[0] < $w && $size[1] < $h){
                $width = $size[0];
                $high  = $size[1];
            }

            //元画像を生成
            if ($ext[1] == "jpg") $img_in=ImageCreateFromJPEG($uploadfile);
            if ($ext[1] == "gif") $img_in=ImageCreateFromGIF($uploadfile);
            if ($ext[1] == "png") $img_in = ImageCreateFromPNG($uploadfile);
            if ($ext[1] == "bmp") $img_in=ImageCreateFromJPEG($uploadfile);

            // GD2.01以降必須 フルカラーで滑らかなサイズ変更ができる
            $img_out=ImageCreateTruecolor($width,$high);

            if ($ext[1] == "png"){

                imagealphablending($img_out, false);
                $fillcolor = imagecolorallocatealpha($img_out, 0, 0, 0, 127);
                imagefill($img_out, 0, 0, $fillcolor);
                imageSaveAlpha($img_out,true);

            }
            ImageCopyResampled($img_out,$img_in,0,0,0,0,$width,$high,$size[0],$size[1]);

            //画像ファイルの書き出し
            if ($ext[1] == "jpg") ImageJPEG($img_out,$uploadfile,100);
            if ($ext[1] == "gif") ImageGIF($img_out,$uploadfile,100);
            if ($ext[1] == "png") ImagePNG($img_out,$uploadfile,100);
            if ($ext[1] == "bmp") ImageJPEG($img_out,$uploadfile,100);
            ImageDestroy($img_in);
            ImageDestroy($img_out);

            $this->arrImages[$name] = $image_name;

        }

    }

    var $arrImages = array();

    /*
     * ファイルアップ情報
     */
/*
    function fileUpImage($files,$entity,$w=200,$h=200){
//echo print_r($files);
        $flg = false;
        foreach($entity as $i => $row){
            $arrRes[$i] = $row;

            $ori_name = $row["field_name"];
            $dir = WWW_ROOT.'images/items/';

            $image_file = null;
            if($row["field_type"] == "file" && !empty($ori_name)){
                $field_name = "file_".$ori_name;
                $image_file = $dir.$ori_name;
            }

            if($row["field_type"] == "file" && @$files[$field_name]["size"] > 0){

                $temp = $files[$field_name]["tmp_name"];
                $file = $files[$field_name]["name"];
                $image_name = date("YmdHis")."_".$row["field_name"];

                //アップロードするファイルの場所
                $uploaddir = WWW_ROOT.'images/items/temp/';
                $ext = explode(".",$file);
                $name = explode(".",$image_name);
                $image_name = basename($name[0].".".$ext[1]);
                $uploadfile = $uploaddir . $image_name;

                //画像をテンポラリーの場所から、上記で設定したアップロードファイルの置き場所へ移動
                move_uploaded_file($temp, $uploadfile);
                $size=getImageSize($uploadfile);

                //縦幅より横幅が大きければ横幅を固定、縦幅が大きければ縦幅を固定
                if($w >= $h) $length = $w;
                if($w < $h) $length = $h;
                if($size[0] >= $size[1]){$width = $length; $high = $size[1] * $length / $size[0];}
                else{$width = $size[0] * $length / $size[1]; $high = $length;}

                //元画像を生成
                if ($ext[1] == "jpg") $img_in=ImageCreateFromJPEG($uploadfile);
                if ($ext[1] == "gif") $img_in=ImageCreateFromGIF($uploadfile);
                if ($ext[1] == "png") $img_in=ImageCreateFromPNG($uploadfile);
                if ($ext[1] == "bmp") $img_in=ImageCreateFromJPEG($uploadfile);

                // GD2.01以降必須 フルカラーで滑らかなサイズ変更ができる
                $img_out=ImageCreateTruecolor($width,$high);
                ImageCopyResampled($img_out,$img_in,0,0,0,0,$width,$high,$size[0],$size[1]);

                //画像ファイルの書き出し
                if ($ext[1] == "jpg") ImageJPEG($img_out,$uploadfile);
                if ($ext[1] == "gif") ImageGIF($img_out,$uploadfile);
                if ($ext[1] == "png") ImagePNG($img_out,$uploadfile);
                if ($ext[1] == "bmp") ImageJPEG($img_out,$uploadfile);
                ImageDestroy($img_in);
                ImageDestroy($img_out);

                $this->arrImages[$ori_name] = $image_name;
                $flg = true;
            }
        }

        return $flg;
    }
*/

    function getFileNames(){
        return $this->arrImages;
    }

    function addFileNames($entity){

        $arrRes = array();
        foreach($entity as $i => $row){
            $arrRes[$i] = $row;
            foreach($this->arrImages as $name => $val){
                if($name == $i){
                    $arrRes[$i] = $val;
                }
            }
        }
        return $arrRes;
    }

    /*
     * ファイルアップ情報
     */
/*
    function resizImage($image_name,$length=200){

        $uploaddir = WWW_ROOT.'images/items/';
        if( empty($image_name) || !file_exists($uploaddir.$image_name)){
            $arrRes["width"] = "";
            $arrRes["height"] = "";
            $arrRes["image_name"] = "";
            return $arrRes;
        }

        $size=getImageSize($uploaddir.$image_name);
        //縦幅より横幅が大きければ横幅を固定、縦幅が大きければ縦幅を固定
        if($size[0] >= $size[1]){$width = $length; $high = $size[1] * $length / $size[0];}
        else{$width = $size[0] * $length / $size[1]; $high = $length;}

        $arrRes["width"] = $width;
        $arrRes["height"] = $high;
        $arrRes["image_name"] = $image_name;

        return $arrRes;
    }
*/

    function moveFiles($file_name){
        $temp_image = WWW_ROOT.'images/items/temp/'.$file_name;
        $save_image = WWW_ROOT.'images/items/'.$file_name;
        if(file_exists($temp_image)){
            rename($temp_image,$save_image);
        }
    }

    function getUrlParam($param_name,$init_id = 0){
        $url = explode("/",$_GET["url"]);
        $param = $init_id;
        foreach($url as $row){
            $array = explode(".",$row);
            foreach($array as $name){
                if(strstr($name, $param_name."_") == false){
                }else{
                    $param = str_replace($param_name."_","",$name);
                }
            }
        }
        return $param;
    }

/*
 * メールテンプレート送信処理
 */
    function sendMailTemplate($body,$add_body = null,$to_mail = null, $template_code = null){
        $flg = true;
        $EmailTemplates = ClassRegistry::init('EmailTemplate');
        $EmailDatas     = ClassRegistry::init('EmailData');

        $obj = $EmailTemplates->getTemplateByCode($template_code);

        $obj_body = $this->convOrderTemplate($body,$template_code);
        $obj_body = mb_convert_kana($obj_body,'KV');

// テスト
        $admin_mail = $obj["admin_mail"];
//        $admin_mail = 'info@mallento.com';

        // メール送信 カスタマー宛
        mb_language('ja');
/*
        $Qdmail->errorDisplay(false);
        $param = array('host'=> SMTP_HOST,'port'=> SMTP_PORT,'from'=> SMTP_FROM ,'protocol'=> SMTP_PROTOCOL ,'user'=> SMTP_USER ,'pass' => SMTP_PASS ,);
        $Qdmail->smtp(true);
        $Qdmail->smtpServer($param);
        $Qdmail->unitedCharset( 'UTF-8' );

        $Qdmail->to($to_mail);
        $Qdmail->bcc('admin@mallento.com');
        $Qdmail->subject();
        $Qdmail->from();
        $Qdmail->text( $obj_body );
*/
        $email = new CakeEmail('smtp');                        // インスタンス化
        $email->from($admin_mail);  // 送信元
        $email->to($to_mail);                      // 送信先
        $email->subject($obj["subject"]);                      // メールタイトル
        $email->bcc('admin@mallento.com');

        if($email->send($obj_body)){
        // メール送信 ショップオーナー宛
            $email->from($to_mail);  // 送信元
            $email->to($admin_mail);                      // 送信先
            $email->subject($obj["name"]);                      // メールタイトル
            $email->bcc('admin@mallento.com');
            $email->send( $add_body."------------------------------------------\n\n".$obj_body );
        }else{

            $user_data["email_datas"]["id"] = null;
            $user_data["email_datas"]["name"] = "遅延メール クライアント";
            $user_data["email_datas"]["email"] = $to_mail;
            $user_data["email_datas"]["from_mail"] = $admin_mail;
            $user_data["email_datas"]["from_name"] = "";
            $user_data["email_datas"]["subject"] = $obj["subject"];
            $user_data["email_datas"]["body"] = $obj_body;
            $user_data["email_datas"]["send_flg"] = 0;
            $EmailDatas->isSave($user_data);

            $owner_data["email_datas"]["id"] = null;
            $owner_data["email_datas"]["name"] = "遅延メール エスグラフィック";
            $owner_data["email_datas"]["email"] = $admin_mail;
            $owner_data["email_datas"]["from_mail"] = $to_mail;
            $owner_data["email_datas"]["from_name"] = "";
            $owner_data["email_datas"]["subject"] = $obj["name"];
            $owner_data["email_datas"]["body"] = $add_body."------------------------------------------\n\n".$obj_body;
            $owner_data["email_datas"]["send_flg"] = 0;
            $EmailDatas->isSave($owner_data);

            $admin_data["email_datas"]["id"] = null;
            $admin_data["email_datas"]["name"] = "遅延メール 管理者";
            $admin_data["email_datas"]["email"] = "admin@mallento.com";
            $admin_data["email_datas"]["from_mail"] = $to_mail;
            $admin_data["email_datas"]["from_name"] = "";
            $admin_data["email_datas"]["subject"] = $obj["name"];
            $admin_data["email_datas"]["body"] = $add_body."------------------------------------------\n\n".$obj_body;
            $admin_data["email_datas"]["send_flg"] = 0;
            $EmailDatas->isSave($admin_data);

            $flg = false;
        }

        return $flg;

    }

/*
 * 受注情報テンプレート変換処理
 */
    function convOrderTemplate($body,$template_code){

        $EmailTemplates = ClassRegistry::init('EmailTemplate');

        $obj = $EmailTemplates->getTemplateByCode($template_code);

        if(is_array($body)){
            $obj_body = $obj["body"];
            foreach($body as $key => $val){
                if(!is_array($val)){
                    $serch_word = '<!--{$'.$key.'}-->';
                    $obj_body = str_replace($serch_word,$val,$obj_body);
                }
            }
        }else{
            $obj_body = str_replace('<!--{$order}-->',$body,$obj["body"]);
        }

        if(isset($body["detail"])){
            // 商品詳細変換
            $arr1 = explode('<!--{loop_dateil}-->',$obj_body);
            $top_body = $arr1[0];
            $str = $arr1[1];
            $arr2 = explode('<!--{/loop}-->',$str);
            $str_line = $arr2[0];
            $bottom_body = $arr2[1];

            $trans_line = null;
            foreach($body["detail"] as $val){
                $temp_body = $str_line;
                $temp_body = str_replace('<!--{$item_code}-->',$val["item_code"],$temp_body);
                $temp_body = str_replace('<!--{$item_name}-->',$val["item_name"],$temp_body);
                $temp_body = str_replace('<!--{$options}-->',$val["options"],$temp_body);
                $temp_body = str_replace('<!--{$price}-->',$val["price"],$temp_body);
                $temp_body = str_replace('<!--{$quantity}-->',$val["quantity"],$temp_body);
                $temp_body = str_replace('<!--{$sum_total}-->',$val["sum_total"],$temp_body);
                $trans_line .= $temp_body;
            }
            $obj_body = $top_body.$trans_line.$bottom_body;
        }

        return $obj_body;

    }

/*
 * メール送信処理
 */
    function sendMail($body,$subject,$from_mail,$to_mail,$add_body = null){

        // テスト
//        $to_mail = 'info@mallento.com';

        $flg = true;
        $EmailDatas     = ClassRegistry::init('EmailData');
        $body = mb_convert_kana($body,'KV');

        // メール送信 カスタマー宛
        mb_language('ja');

        $email = new CakeEmail('smtp');                        // インスタンス化
        $email->from($from_mail);  // 送信元
        $email->to($to_mail);                      // 送信先
        $email->subject($subject);                      // メールタイトル

        if($email->send($body)){
            // メール送信 ショップオーナー宛
            $email->to($from_mail);
            $email->bcc('admin@mallento.com');
            $email->subject($subject);
            $email->from($to_mail);
            $email->send( $add_body."以下の内容で送信しました。------------------------------------------\n\n".$body );
        }else{

            $user_data["email_datas"]["id"] = null;
            $user_data["email_datas"]["name"] = "遅延メール クライアント";
            $user_data["email_datas"]["email"] = $to_mail;
            $user_data["email_datas"]["from_mail"] = $from_mail;
            $user_data["email_datas"]["from_name"] = "";
            $user_data["email_datas"]["subject"] = $subject;
            $user_data["email_datas"]["body"] = $body;
            $user_data["email_datas"]["send_flg"] = 0;
            $EmailDatas->isSave($user_data);

            $owner_data["email_datas"]["id"] = null;
            $owner_data["email_datas"]["name"] = "遅延メール エスグラフィック";
            $owner_data["email_datas"]["email"] = $from_mail;
            $owner_data["email_datas"]["from_mail"] = $to_mail;
            $owner_data["email_datas"]["from_name"] = "";
            $owner_data["email_datas"]["subject"] = $subject;
            $owner_data["email_datas"]["body"] = "以下の内容で送信しました。------------------------------------------\n\n".$body;
            $owner_data["email_datas"]["send_flg"] = 0;
            $EmailDatas->isSave($owner_data);

            $admin_data["email_datas"]["id"] = null;
            $admin_data["email_datas"]["name"] = "遅延メール 管理者";
            $admin_data["email_datas"]["email"] = "admin@mallento.com";
            $admin_data["email_datas"]["from_mail"] = $to_mail;
            $admin_data["email_datas"]["from_name"] = "";
            $admin_data["email_datas"]["subject"] = $subject;
            $admin_data["email_datas"]["body"] = "以下の内容で送信しました。------------------------------------------\n\n".$body;
            $admin_data["email_datas"]["send_flg"] = 0;
            $EmailDatas->isSave($admin_data);

            $flg = false;
        }

        return $flg;

    }

/*
 * MySqlの日付型から年齢を取得
 */
    function calcAge($dob){
        $now = date("Ymd");
        $birthday = str_replace("-","", substr($dob,0,10));
        return floor(($now-$birthday)/10000);
    }

/*
 * コントローラ作成
 */
    function createController($data){
        $dir_path = APP."controllers".DS;
        $controller_name = $data["page_directories"]["controller_name"];
        $controller_name = strtolower($controller_name);
        $dir = dir($dir_path);

        // 該当するコントローラーが存在するか確認
        $flg = false;
        while($file = $dir->read()) {
            $f = explode(".",$file);
            if (@$f[1] == "php"){
                $c = str_replace("_controller","",$f[0]);
                if($c == $controller_name){
                    $flg = true;
                }
            }
        }
        $dir->close();

        if(!$flg){
            $l = strlen($controller_name);
            $s = substr($controller_name,0,1);
            $b = substr($controller_name,1,($l-1));
            $name = strtoupper($s) . $b;

            // 改行の設定
            $ds = $this->getDS();

            $content = "<?php\n";
            $content .= "class ".$name."Controller extends PageAppController {".$ds;
            $content .= "\tpublic function ".$controller_name."_(){".$ds;
            if($name <> "Form") $content .= "\t\t\$this->redirect('/');".$ds;
            $content .= "\t}".$ds;

            $content .= "\tpublic function ".$controller_name."_top(){".$ds;
            $content .= "\t}".$ds;

            $content .= "}".$ds.$ds."?>".$ds;

            $filename = $dir_path.$controller_name . "_controller.php";
            $handle = fopen( $filename, 'w');
            fwrite( $handle, $content);
            fclose( $handle );
            // viewディクトリ作成
            @mkdir( APP."views".DS.$controller_name, 0777);

        }

        // 不要なコントローラの削除
        $PageDirectories = ClassRegistry::init('PageDirectory');
        $PageDirectories->delController();

    }

    function convDbDate($date){
        if(!empty($date)){
            $date = substr(str_replace("/","-",$date),0,10);
        }else if("0000-00-00"){
            $date = "";
        }else if(empty($date)){
            $date = "1900-01-01";
        }
        return $date;
    }

/*
 * MySqlの日付型から年月日を配列で返す
*/
    function convDbArrYmd($date,$delimiter = "-"){
        $arr_date = null;
        if(!empty($date)){
            $arr_date = explode($delimiter,substr($date,0,10));
        }
        return $arr_date;
    }

/*
 * MySQL日付型に年加算
 */
    function addYearDbDate($date,$year){

        $arrYmd = $this->convDbArrYmd($date);
        $y = $arrYmd[0] + $year;
        $sdate = mktime (0, 0, 0, $arrYmd[1], $arrYmd[2], $y);

        return date("Y-m-d",$sdate);
    }

/*
 *  File Get CSV
 */
    function fgetcsv( &$fh, $test = false ) {
        if ( feof( $fh ) ) return false ;

        $csv = '' ;

        while ( ! feof( $fh ) ) {
            $csv .= mb_convert_encoding( fgets( $fh ), 'UTF-8', 'SJIS-win' ) ;
            if ( ( ( preg_match_all( '/"/', $csv, $matches ) ) % 2 ) == 0 ) break ;
        }

        $values = array() ;

        $temp = preg_replace( '/(?:¥x0D¥x0A|[¥x0D¥x0A])?$/', ',', $csv, 1 ) ;

        preg_match_all( '/("[^"]*(?:""[^"]*)*"|[^,]*),/', $temp, $matches ) ;

        for ( $i = 0 ; $i < count( $matches[ 1 ] ) ; $i++ ) {
            if ( preg_match( '/^"(.*)"$/s', $matches[ 1 ][ $i ], $m ) ) {
                $matches[ 1 ][ $i ] = preg_replace( '/""/', '"', $m[ 1 ] ) ;
            }

            $values[] = $matches[ 1 ][ $i ] ;
        }
        return $values ;
    }

/*
 *  改行の設定
 */
    function getDS() {
        // 改行の設定
        $ds = "\n";
        if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {
            $ds = "\r\n";
        }
        return $ds;
    }

    function getPgParam($param,$param_name){
        $url = explode(".",$param);
        $param = null;
        foreach($url as $row){
            if(strstr($row,$param_name)){
                $param = $row;
            }
        }
        return $param;
    }

/*
* ファイルアップ処理
*/
    function fileSave($file,$size_limit=1) {

        // ファイルアップ
        $dir = session_id().time();
        // ディレクトリを作る場所
        $save_dir = WWW_ROOT.'images/files/'.$dir;
        // 作成するディレクトリ名
        // ディレクトリが存在するかチェックし、なければ作成
        if(!is_dir($save_dir)){
            umask(0);
            mkdir($save_dir, 0777);
        }

        // ファイルサイズチェック
        $size = 0;
        foreach($file["file"]["size"] as $val){
            $size = $size + $val;
        }

        if($size == 0){
            return true;
        }

        $maxsize = $size_limit * 1048576;
        if($size > $maxsize) {
            $this->file_errors = $size_limit."Mサイズを超えています。";
            return false;
        }

        foreach($file["file"]["tmp_name"] as $name => $temp){
            $arr_name = explode(".",$file["file"]["name"][$name]);
            $file_name = mb_convert_encoding($file["file"]["name"][$name],"SJIS", "auto");
            $uploadfile = $save_dir .DS. $file_name;
            move_uploaded_file($temp, $uploadfile);
            $uploadfile_attach = $save_dir .DS. $name.".".$arr_name[1];
            copy($uploadfile, $uploadfile_attach);
            $this->file_paths[$name]["name"] = $file["file"]["name"][$name];
            $this->file_paths[$name]["path"] = $dir."/". $name.".".$arr_name[1];
        }
        return true;

    }

    var $file_errors;

    function getFileErrors() {
        return $this->file_errors;
    }

    var $file_paths;

    function getFilePaths() {
        return $this->file_paths;
    }

/*
* キャッシュ全削除処理
*/
    function chachAllClear() {

        $dir_path = APP."tmp".DS."cache";
        $dir = dir(APP."tmp".DS."cache");
        $flg = false;
        while($file = $dir->read()) {
            $arr_file = explode("_",$file);
            if($arr_file[0] == "cake" && $arr_file[1] != "arr"){
                unlink($dir_path.DS.$file);
            }
        }
        $dir->close();

    }

/*
 *  ID・パスワード ランダム生成
 */
    function getGenesCode($nLengthRequired = 8) {
        $sCharList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
        mt_srand();
        $sRes = "";
        for($i = 0; $i < $nLengthRequired; $i++){
            $sRes .= $sCharList{mt_rand(0, strlen($sCharList) - 1)};
        }
         return $sRes;
    }

/********* 以下拡張 *********/
    function convFormType($form_type){

        if(is_array($form_type)){
            $line = null;
            foreach($form_type as $row){
                if(!empty($line)) $line .= ",";
                $line .= $row;
            }
        }
        return $line;
    }

    function getHolidays($start_date,$end_date){

        $url = "http://www.google.com/calendar/feeds/japanese__ja@holiday.calendar.google.com/public/full";

        $query["start-min"]  = $start_date;
        $query["start-max"]  = $end_date;

        App::uses( 'HttpSocket', 'Network/Http');
        $socket = new HttpSocket( array( 'ssl_verify_host' => false));
//        App::import('Core', 'HttpSocket');
        App::import('Xml');
//        $socket = new HttpSocket();
        $res_content = $socket->get($url, $query);
//print_r($res_content);
        $xml = new Xml($res_content);
        $res = Set::reverse($xml);

        if(!isset($res["Feed"]["Entry"])) return null;

        $arrRes = array();
        if(isset($res["Feed"]["Entry"][0])){
            foreach($res["Feed"]["Entry"] as $row){
                $key = $row["When"]["startTime"];
                $val = $row["title"]["value"];
                $arrRes[$key] = $val;
            }
        }else{
            $key = $res["Feed"]["Entry"]["When"]["startTime"];
            $val = $res["Feed"]["Entry"]["title"]["value"];
            $arrRes[$key] = $val;
        }

        return $arrRes;
    }

    public function calculateReviewAve($reviews) {
        $count = count($reviews);
        if ($count > 0) {
            $total = '';
            foreach($reviews as $row) {
                $total += $row['Review']['rating'];
            }
            return $total/$count;
        } else {
            return null;
        }
    }
}
?>
