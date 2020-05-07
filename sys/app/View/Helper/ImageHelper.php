<?php
class ImageHelper extends Helper {

    var $cacheDir = 'imagecache';

/**
 * Automatically resizes an image and returns formatted IMG tag
 *
 * @param string $path Path to the image file, relative to the webroot/img/ directory.
 * @param integer $width Image of returned image
 * @param integer $height Height of returned image
 * @param boolean $aspect Maintain aspect ratio (default: true)
 * @access public
 */
    function resize($path, $width=null, $height=null, $aspect = true) {

        $types = array(1 => "gif", "jpeg", "png", "swf", "psd", "wbmp");

        $fullpath = WWW_ROOT."images".DS."items".DS;
        $url = $fullpath.$path;
        $temp_dir = null;

        if(!file_exists($url)){
            $path = "temp".DS.$path;
            $url = $fullpath.$path;
            $temp_dir = "temp";
        }

        if(!file_exists($url)){
//            $path = "temp".DS.$path["field_value"];
            $path = "temp".DS.$path;
            $url = $fullpath.$path;
            $temp_dir = "temp";
        }

        if(!file_exists($url) || empty($path)){
//            return  HOME_URL."images".DS."adviser".DS."noimage_l.jpg";
            $url = WWW_ROOT."images".DS."common".DS."no_image.png";
        }

        // サイズ指定がない場合は原寸で表示
        if($width == null && $height == null){
//            $cachefile = WWW_ROOT."images".DS.$this->cacheDir.DS.$width.'x'.$height.'_'.basename($path);
            if(!empty($temp_dir)) $temp_dir .= "/";
            $cachepath = HOME_URL."images/items/".$temp_dir.basename($path);
            return $cachepath;
        }

        if (!($size = getimagesize($url))) return;

        if ($aspect) {
            if (($size[1]/$height) > ($size[0]/$width))
                $width = ceil(($size[0]/$size[1]) * $height);
            else
                $height = ceil($width / ($size[0]/$size[1]));
        }

        $cachefile = WWW_ROOT."images".DS.$this->cacheDir.DS.$width.'x'.$height.'_'.basename($path);
        $cachepath = HOME_URL."images/".$this->cacheDir."/".$width.'x'.$height.'_'.basename($path);

        if (file_exists($cachefile)) {
            $csize = getimagesize($cachefile);
            $cached = ($csize[0] == $width && $csize[1] == $height);
            if (@filemtime($cachefile) < @filemtime($url))
                $cached = false;
        } else {
            $cached = false;
        }

        if (!$cached) {
            $resize = true;
        } else {
            $resize = false;
        }

        if ($resize) {
            $image = call_user_func('imagecreatefrom'.$types[$size[2]], $url);

            if (function_exists("imagecreatetruecolor") && ($temp = imagecreatetruecolor ($width, $height))) {

                if ($types[$size[2]] == "png"){
                    imagealphablending($temp, false);
                    $fillcolor = imagecolorallocatealpha($temp, 0, 0, 0, 127);
                    imagefill($temp, 0, 0, $fillcolor);
                    imageSaveAlpha($temp,true);
                }
                imagecopyresampled ($temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
            } else {
                $temp = imagecreate ($width, $height);

                if ($types[$size[2]] == "png"){
                    imagealphablending($temp, false);
                    $fillcolor = imagecolorallocatealpha($temp, 0, 0, 0, 127);
                    imagefill($temp, 0, 0, $fillcolor);
                    imageSaveAlpha($temp,true);
                }
                imagecopyresized ($temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
            }

            if($types[$size[2]] == "png"){
                call_user_func("image".$types[$size[2]], $temp, $cachefile,0);
            }else if($types[$size[2]] == "jpeg"){
                call_user_func("image".$types[$size[2]], $temp, $cachefile,100);
            }else{
                call_user_func("image".$types[$size[2]], $temp, $cachefile);
            }

            imagedestroy ($image);
            imagedestroy ($temp);
        }

        return $cachepath;
    }

    function resizeImgTag($path, $width=null, $height=null,$alt=null,$id=null ) {

        $fullpath = WWW_ROOT."images".DS."items".DS;
        $url = $fullpath.$path;

        $imagepath = HOME_URL."images/items/".$path;
        if(!file_exists($url)){
            $imagepath = HOME_URL."images/common/no_image.png";
            $url = WWW_ROOT."images".DS."common".DS."no_image.png";
        }
        $size = @getimagesize($url);

        $w = $size[0];
        $h = $size[1];

        if($width < $w or $height < $h){
            if($w > $h){
                $per = $h / $w;
                $h = $width * $per;
                $w = $width;
            }else{
                $per = $w / $h;
                $w = $height * $per;
                $h = $height;
            }
        }else if($width > $w and $height > $h){
            $w = $size[0];
            $h = $size[1];
        }
        return "<img alt='$alt' src='$imagepath' width='$w' height='$h' id='$id' onerror='onImgErr(this)' />";

    }

    function upload_resize($table, $id, $filename, $type) {

        $scr = null;
        $parent_path = WWW_ROOT.'upload/';
        $file = pathinfo($parent_path.$table.'/'.$id.'/'.$filename);
        if(!isset($file['extension'])){
            return null;
        }
        $scr = $file['dirname'].'/'.$file['filename'].'_'.$type.'.'.$file['extension'];

        $data = @file_get_contents($scr);
        if(!$data){
            $scr = $file['dirname'].'/'.$file['filename'].'_thumb.'.$file['extension'];
        }

        return $scr;
    }

}
?>
