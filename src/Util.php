<?php

function proper($text)
{
    $strings = explode('-', $text);
    $retorno = "";

    foreach($strings as $s)
    {
        $retorno .= ucwords($s);
    }
    return $retorno;
}   

function toArray($data)
{
    $retorno = [];

    if(!empty($data)) 
    {
        if(is_array($data)) 
        {
            foreach($data as $item) 
            {
                if(is_object($item)) 
                {
                    if(get_class($item) == "stdClass") 
                    {
                        array_push($retorno, json_decode(json_encode($item), true));
                    } 
                    else 
                    {
                        array_push($retorno, $item->toArray());
                    }
                } 
                else 
                {
                    array_push($retorno, json_decode(json_encode($item), true));                        
                }
            }                
        } 
        else 
        {
            if(is_object($data)) 
            {
                if(get_class($data) == "stdClass") 
                {
                    $retorno = json_decode(json_encode($data), true);
                } 
                else 
                {
                    $retorno = $data->toArray();
                }
            } 
            else 
            {
                $retorno = json_decode(json_encode($data), true);
            }
        }
    }

    return $retorno;
}

function Json($data)
{
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function toUSD($brl, $decimals = null)
{
    $source = array('.', ',');
    $replace = array('', '.');
    $brl = str_replace($source, $replace, $brl); 
    $brl = (float)$brl;

    return number_format($brl, (!empty($decimals) ? $decimals : 2), '.', '');
}

function toBRL($float, $decimals = null)
{
    return number_format($float, (!empty($decimals) ? $decimals : 2), ',', '.');
}

function toDateBRL($date, $format = null)
{
    return date($format ?? "d/m/Y", strtotime($date));
}

function toDateUS($date, $format = null)
{
    return (DateTime::createFromFormat('d/m/Y', $date))->format($format ?? "Y-m-d");
}

function random($len = null) 
{
    return strtoupper(substr(md5(rand()), 0, $len ?? 0));
}

function truncate($string, $maxlenght) 
{
    $len = strlen($string);
    return substr($string, 0, ($len > $maxlenght ? $maxlenght : $len) ) . "...";
}

function dd($content)
{
    print "<pre>";
    print_r($content);
    print "</pre>";        
}

function csrf()
{
    return \Estartar\Core\NoCSRF::generate('csrf_token');
}

function loremipsum($text, $rounds)
{
    if(empty($rounds)) {
        return $text;        
    }
    
    $count = 1;
    while($count < $rounds) {
        $text .= " {$text}";
        $count += 1;
    }

    return $text;
}

function slugify($string) 
{

    $table = array(
            '??'=>'S', '??'=>'s', '??'=>'Dj', '??'=>'dj', '??'=>'Z', '??'=>'z', '??'=>'C', '??'=>'c', '??'=>'C', '??'=>'c',
            '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'A', '??'=>'C', '??'=>'E', '??'=>'E',
            '??'=>'E', '??'=>'E', '??'=>'I', '??'=>'I', '??'=>'I', '??'=>'I', '??'=>'N', '??'=>'O', '??'=>'O', '??'=>'O',
            '??'=>'O', '??'=>'O', '??'=>'O', '??'=>'U', '??'=>'U', '??'=>'U', '??'=>'U', '??'=>'Y', '??'=>'B', '??'=>'Ss',
            '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'a', '??'=>'c', '??'=>'e', '??'=>'e',
            '??'=>'e', '??'=>'e', '??'=>'i', '??'=>'i', '??'=>'i', '??'=>'i', '??'=>'o', '??'=>'n', '??'=>'o', '??'=>'o',
            '??'=>'o', '??'=>'o', '??'=>'o', '??'=>'o', '??'=>'u', '??'=>'u', '??'=>'u', '??'=>'y', '??'=>'y', '??'=>'b',
            '??'=>'y', '??'=>'R', '??'=>'r', '/' => '-', ' ' => '-'
    );

    // -- Remove duplicated spaces
    $stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);

    // -- Returns the slug
    return strtolower(strtr($string, $table));
}

function hexa2rgb($hex, $factor = null) 
{
    list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
    $factor = $factor ?? 1;
    return strval(intval($r * $factor) . "," . intval($g * $factor) . "," . intval($b * $factor));  
}

function rotate($file, $deg) 
{

    $retorno = [
        "status" => "success",
        "message" => ""
    ];

    try {

        $path_parts = pathinfo($file);
        $ext = $path_parts['extension'];
    
        if ($deg) {
    
            // If png
            if ($ext == "png") {
                $img_new = imagecreatefrompng($file);
                $img_new = imagerotate($img_new, $deg, 0);
    
                // Save rotated image
                imagepng($img_new,$file);
            }else {
                $img_new = imagecreatefromjpeg($file);
                $img_new = imagerotate($img_new, $deg, 0);
    
                // Save rotated image
                imagejpeg($img_new,$file,80);
            }
        }  

    } catch (\Throwable $t) {
        $retorno["status"] = "error";
        $retorno["message"] = $t->getMessage();        
    } catch (\Exception $e) {
        $retorno["status"] = "error";
        $retorno["message"] = $e->getMessage();        
    }

    return $retorno;

  }  

  function thumb($file, $width, $height)
  {
      $array_file = explode("/", $file);
      $count = count($array_file) - 1;
      $filename = $array_file[$count]; 
      $array_file_new = explode(".", $filename);
      $filename_new = $array_file_new[0] . "_" . strval($width) . "x" . strval($height) . "." . $array_file_new[1];
  
      $source_file = CONFIG["BASEDIR"] . $file;
      $dst_dir = CONFIG["BASEDIR"] . str_replace($filename, $filename_new, $file);
      $dst_url = CONFIG["BASEURL"] . str_replace($filename, $filename_new, $file);
      $max_width = intval($width);
      $max_height = intval($height);    
  
      $imgsize = getimagesize($source_file);
      $width = $imgsize[0];
      $height = $imgsize[1];
      $mime = $imgsize['mime'];
      
      switch($mime){
          case 'image/gif':
              $image_create = "imagecreatefromgif";
              $image = "imagegif";
              break;
      
          case 'image/png':
              $image_create = "imagecreatefrompng";
              $image = "imagepng";
              $quality = 7;
              break;
      
          case 'image/jpeg':
              $image_create = "imagecreatefromjpeg";
              $image = "imagejpeg";
              $quality = 80;
              break;
      
          default:
              return false;
              break;
      }
          
      $dst_img = imagecreatetruecolor($max_width, $max_height);
      $src_img = $image_create($source_file);
          
      $width_new = $height * $max_width / $max_height;
      $height_new = $width * $max_height / $max_width;
  
      //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
      if($width_new > $width){
          //cut point by height
          $h_point = (($height - $height_new) / 2);
          //copy image
          imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
      }else{
          //cut point by width
          $w_point = (($width - $width_new) / 2);
          imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
      }
          
      $image($dst_img, $dst_dir, $quality);
      
      if($dst_img)imagedestroy($dst_img);
      if($src_img)imagedestroy($src_img);
  
      // header('Content-Type: image/jpeg');
      // readfile($dst_dir);
      // exit;
  
      return $dst_url;
      exit;
  }