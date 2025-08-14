<?php 
if($argc == 1){
        echo "Aucune option ou dossier ne correspond à votre demande.\n";
}else{
        $r = $n = $s = $p = $o = $c = null;
        foreach ($args = $argv as $key => $value) {
                if(preg_match('/(^\-r.?)|(\-\-recursive.?)/', $value)){
                        $r = 0;
                }
                if (preg_match('/(^\-i.?)/', $value)) {
                        $n = $args[$key+1];
                }
                if(preg_match('/(^(\-\-output\-image=).?)/', $value)){
                        $n = trim(substr($value, strpos($value, '=')+1));
                }
                if (preg_match('/(^\-s.?)/', $value)) {
                        $s = $args[$key+1];
                }
                if(preg_match('/(^(\-\-output\-style=).?)/', $value)){
                        $s = trim(substr($value, strpos($value, '=')+1));
                }
                if (preg_match('/(^\-p.?)/', $value)) {
                        $p = $args[$key+1];
                }
                if(preg_match('/(^(\-\-padding=).?)/', $value)){
                        $p = trim(substr($value, strpos($value, '=')+1));
                }
                if (preg_match('/(^\-o.?)/', $value)) {
                        $o = $args[$key+1];
                }
                if(preg_match('/(^(\-\-override-size=).?)/', $value)){
                        $o = trim(substr($value, strpos($value, '=')+1));
                }
                if (preg_match('/(^\-c.?)/', $value)) {
                        $c = $args[$key+1];
                }
                if(preg_match('/(^(\-\-columns_number=).?)/', $value)){
                        $c = trim(substr($value, strpos($value, '=')+1));
                }
        }
        $dirname = (preg_match('/^-./',$args[$argc-1])?null:$args[$argc-1]); 
        scan($dirname, $r ,$n, $s, $p, $o, $c);
}
function scan($dirname = null, $r = null, $n = null, $s =null, $p = null, $o = null, $c = null, $files = null, $dir_path = null){
        static $files = [];
        $dir_path = ($dirname == substr(getcwd(), strrpos(getcwd(), '/')+1)?getcwd():(is_null($dirname)?getcwd():(is_null($dir_path)?getcwd()."/$dirname":$dir_path)));
        if (is_dir($dir_path) && $handle = opendir($dir_path)) {
                while (false !== ($entry = readdir($handle))) {
                        if(preg_match('/^(\.*)$/', $entry)){
                                continue;
                        }
                        elseif(is_file("$dir_path/$entry") && preg_match('/.\.png/', $entry)){
                                $img = imagecreatefrompng("$dir_path/$entry");
                                if (is_numeric($o) && $o>0) {
                                        $width = (imagesx($img) < imagesy($img)?(($o/imagesy($img))*imagesx($img)):$o);
                                        $height = (imagesy($img) < imagesx($img)?(($o/imagesx($img))*imagesy($img)):$o);
                                        $new_img = imagecreatetruecolor($width, $height);
                                        imagesavealpha($new_img, true);
                                        imagefill($new_img,0, 0, imagecolorallocatealpha($new_img, 0, 0, 0, 127));
                                        imagecopyresampled($new_img, $img, 0, 0, 0, 0, $width, $height, getimagesize("$dir_path/$entry")[0], getimagesize("$dir_path/$entry")[1]);
                                        $img = $new_img;
                                }
                                array_push($files, ["img" => $img, "width" => strval(imagesx($img)),  "height" => strval(imagesy($img)),
                                "name" => pathinfo("$dir_path/$entry")['filename'],
                                "path" => "$dir_path/$entry"]);
                        }
                        elseif (is_dir($dir_path = "$dir_path/$entry") && isset($r)) {
                                $r++;
                                scan($entry, $r, $n, $s, $p, $o, $c, $files, $dir_path);
                                concatenate($files, $s, $n, $p, $c);
                        }else{
                                continue;
                        }
                }
                closedir($handle);
                if(is_null($r)){
                        concatenate($files, $s, $n, $p, $c);
                }
        }else{
                echo "Ce n'est pas un dossier valide.\n";
        }
}

function concatenate ($files, $s = null, $n = null, $p = null, $c = null){
        if(isset($c) && $c > 1){
                $a = 0;
                $dst_y=0;
                $lines = count($files)/$c;
                $arr = [];
                $max_height = [];
                $total_width = [];
                for ($i=0; $i < (is_float($lines)?ceil($lines):$lines); $i++) {
                        $arr_slice =  array_slice($files,$a, $c);
                        array_push($max_height, max(array_column($arr_slice, "height")));
                        array_push($total_width, array_sum(array_column($arr_slice, "width")));
                        $arr[$i] = $arr_slice;
                        $a+= $c;
                }
                if(is_numeric($p) && isset($p)){
                        $count = count($files)-1;
                        $padding = $p / $count;
                        $padding = (is_float($padding)?floor($padding):$padding);
                }
                $img_bg = imagecreatetruecolor((is_numeric($p) && isset($p)?max($total_width)+$p:max($total_width)), array_sum($max_height));
                imagesavealpha($img_bg, true);
                imagefill($img_bg,0, 0, imagecolorallocatealpha($img_bg, 0, 0, 0, 127));
                $html_data = "<!DOCTYPE html>\n<html lang=\"fr\">\n<head>\n      <meta charset=\"UTF-8\">\n      <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n      <title>Document</title>\n      <link rel=\"stylesheet\" href=\"".(is_null($s)?'style.css':"$s.css")."\"></head>\n<body>";
                $css_data = "*{\n    margin: 0;\n    padding: 0;\n}\n\nbody{\n    display: inline-flex;\n}\n\n";
                foreach ($arr as $key => $value) {
                        $dst_x=0;
                        for ($i=0; $i < count($value); $i++) { 
                                imagecopy($img_bg, $value[$i]["img"], (is_numeric($p) && isset($p) && $i>0?$dst_x+$padding:$dst_x), $dst_y, 0, 0, $value[$i]["width"], $value[$i]["height"]);
                                $css_data .= ".".strtolower(str_replace(" ", "-", $value[$i]["name"]))."{\n    height: ".$value[$i]['height']."px;\n    width: ".$value[$i]['width']."px;\n    background: no-repeat url(./".(is_null($n)?"sprite.png":"$n.png").");\n    background-position-x: ".-(is_numeric($p) && isset($p) && $i>0?$dst_x+$padding:$dst_x)."px;\n    background-position-y: ".-($dst_y)."px;\n}\n\n";
                                
                                $html_data .= "\n      <div class=\"".strtolower(str_replace(" ", "-", $value[$i]["name"]))."\"></div>";
                                
                                $dst_x += (is_numeric($p) && isset($p)?$value[$i]["width"]+$padding:$value[$i]["width"]);
                        }
                        $dst_y += $max_height[$key];
                }
        }else{
                if(is_numeric($p) && isset($p)){
                        $count = count($files)-1;
                        $padding = $p / $count;
                        $padding = (is_float($padding)?floor($padding):$padding);
                }
                if (isset($c) && $c == 1) {
                        $img_bg = imagecreatetruecolor( $max_width = (is_numeric($p) && isset($p))?max(array_column($files, "width"))+$p:max(array_column($files, "width")),array_sum(array_column($files, "height")));
                        $dst_y=0;
                }else {
                        $img_bg = imagecreatetruecolor((is_numeric($p) && isset($p))?array_sum(array_column($files, "width"))+$p:array_sum(array_column($files, "width")), $max_height = max(array_column($files, "height")));
                }
                imagesavealpha($img_bg, true);
                imagefill($img_bg,0, 0, imagecolorallocatealpha($img_bg, 0, 0, 0, 127));
                $dst_x=0;
                $html_data = "<!DOCTYPE html>\n<html lang=\"fr\">\n<head>\n      <meta charset=\"UTF-8\">\n      <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n      <title>Document</title>\n      <link rel=\"stylesheet\" href=\"".(is_null($s)?'style.css':"$s.css")."\"></head>\n<body>";
                $css_data = "*{\n    margin: 0;\n    padding: 0;\n}\n\nbody{\n    display: inline-flex;\n}\n\n";
                for ($i=0; $i < count($files) ; $i++) {
                        imagecopy($img_bg, $files[$i]["img"], (is_numeric($p) && isset($p) && $i>0?$dst_x+$padding:$dst_x), (isset($c)?$dst_y:($max_height-$files[$i]["height"])), 0, 0, $files[$i]["width"], $files[$i]["height"]);
                        $css_data .= ".".strtolower(str_replace(" ", "-", $files[$i]["name"]))."{\n    height: ".$files[$i]['height']."px;\n    width: ".$files[$i]['width']."px;\n    background: no-repeat url(./".(is_null($n)?"sprite.png":"$n.png").");\n    background-position-x: ".-(is_numeric($p) && isset($p) && $i>0?$dst_x+$padding:$dst_x)."px;\n    background-position-y: ".-(isset($c)?$dst_y:($max_height)-$files[$i]["height"])."px;\n}\n\n";
                        $html_data .= "\n      <div id=\"img $i\" class=\"".strtolower(str_replace(" ", "-", $files[$i]["name"]))."\"></div>";
                        if(isset($c) && $c == 1){
                                $dst_y += $files[$i]["height"];
                        }else{
                                $dst_x += (is_numeric($p) && isset($p)?$files[$i]["width"]+$padding:$files[$i]["width"]);
                        }
                }
        }
        $html_data .= "\n</body>\n</html>";
        fwrite($html_file = fopen("index.html", 'w+'), $html_data);
        fclose($html_file);
        fwrite($css_file = fopen((is_null($s)?"style.css":"$s.css"), "w+"), $css_data);
        fclose($css_file);
        imagepng($img_bg, (is_null($n)?"spritesheet.png":"$n.png"));
        imageDestroy($img_bg);
}