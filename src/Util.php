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
            'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
            'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
            'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
            'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
            'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', '/' => '-', ' ' => '-'
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
