<?php

$fname = $_POST["fname"];
$num = mb_strlen($fname, "UTF-8");
$font = $_POST["font"];
$hcolor = $_POST["color"];

// フォントの選択
if($font == "nikumaru"){
  $font = "./fonts/nikumaru.otf";
}elseif($font == "dasaji"){
  $font = "./fonts/dasaji.ttf";
}

// 姓の文字数
if($num == 1){
  $text1 = $fname;
  name1($font, $hcolor, $text1);
}elseif($num == 2){
  $text1 = mb_substr($fname, 0, 1, "UTF-8");
  $text2 = mb_substr($fname, 1, 1, "UTF-8");
  name2($font, $hcolor, $text1, $text2);
}elseif($num == 3){
  $text1 = mb_substr($fname, 0, 1, "UTF-8");
  $text2 = mb_substr($fname, 1, 1, "UTF-8");
  $text3 = mb_substr($fname, 2, 1, "UTF-8");
  name3($font, $hcolor, $text1, $text2, $text3);
}elseif($num == 4){
  $text1 = mb_substr($fname, 0, 1, "UTF-8");
  $text2 = mb_substr($fname, 1, 1, "UTF-8");
  $text3 = mb_substr($fname, 2, 1, "UTF-8");
  $text4 = mb_substr($fname, 3, 1, "UTF-8");
  name4($font, $hcolor, $text1, $text2, $text3, $text4);
}

$moji = imagecreatefrompng("temp.png");

// 色のチェック
if($hcolor == "aka"){
  $maru = imagecreatefrompng("r12.png");
}elseif($hcolor == "shu"){
  $maru = imagecreatefrompng("s12.png");
}elseif($hcolor == "beni"){
  $maru = imagecreatefrompng("b12.png");
}

// 完全なアルファチャネル情報を保存するフラグをonにする
imagesavealpha($moji, true);
imagesavealpha($maru, true);

imagecopy($maru, $moji, 0, 0, 0, 0, 34, 34);

// ファイル名の作成
$filename = makeRandStr(4);
$destination =  "hanko_" . $filename . ".png";

imagepng($maru, $destination);

imagedestroy($maru);
imagedestroy($moji);

// ダウンロードの処理
header('Content-Type: application/force-download');
header('Content-Length: ' . filesize($destination));
header('Content-disposition: attachment; filename="' . $destination . '"');
readfile($destination);

// ダウンロード後ファイルを削除する
unlink($destination);
unlink("temp.png");



/**
 * ランダム文字列生成 (英数字)
 * $length: 生成する文字数
 */
function makeRandStr($length) {
    static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ($i = 0; $i < $length; ++$i) {
        $str .= $chars[mt_rand(0, 61)];
    }
    return $str;
}

// 1文字用の関数
function name1($fontpath, $iro, $num1){

  // width of your image
  $imageWidth = 34;

  // height of your image
  $imageHeight = 34;

  // create Image
  $logoimg = imagecreatetruecolor($imageWidth, $imageHeight);

  // for transparent background
  imagealphablending($logoimg, false);
  imagesavealpha($logoimg, true);
  $col = imagecolorallocatealpha($logoimg, 255, 255, 255, 127);
  imagefill($logoimg, 0, 0, $col);

  // for font color
  $white = imagecolorallocate($logoimg, 255, 255, 255);
  $red = imagecolorallocate($logoimg, 255, 0, 0);
  $shu = imagecolorallocate($logoimg, 255, 102, 0);
  $beni = imagecolorallocate($logoimg, 204, 51, 51);

  // font path
  $font = $fontpath;

  // your text which you want to show in image
  $text1 = $num1;

  // size of your font
  $fontsize = 18;

  // x- position of your text
  $x = 5;

  // y- position of your text
  $y = 25;

  // angle of your text
  $angle = 0;

  // fill text in your image
  if($iro == "aka"){
    imagettftext($logoimg, $fontsize, $angle , $x, $y, $red, $font, $text1);
  }elseif($iro == "shu"){
    imagettftext($logoimg, $fontsize, $angle , $x, $y, $shu, $font, $text1);
  }elseif($iro == "beni"){
    imagettftext($logoimg, $fontsize, $angle , $x, $y, $beni, $font, $text1);
  }

  // path of target where you want to save image
  $target = "temp.png";

  // save your image at new location $target
  imagepng($logoimg, $target);

  imagedestroy($logoimg);

}

// 2文字用の関数
function name2($fontpath, $iro, $num1, $num2){

  // width of your image
  $imageWidth = 34;

  // height of your image
  $imageHeight = 34;

  // create Image
  $logoimg = imagecreatetruecolor($imageWidth, $imageHeight);

  // for transparent background
  imagealphablending($logoimg, false);
  imagesavealpha($logoimg, true);
  $col = imagecolorallocatealpha($logoimg, 255, 255, 255, 127);
  imagefill($logoimg, 0, 0, $col);

  // for font color
  $white = imagecolorallocate($logoimg, 255, 255, 255);
  $red = imagecolorallocate($logoimg, 255, 0, 0);
  $shu = imagecolorallocate($logoimg, 255, 102, 0);
  $beni = imagecolorallocate($logoimg, 204, 51, 51);

  // font path
  $font = $fontpath;

  // your text which you want to show in image
  $text1 = $num1;
  $text2 = $num2;

  // size of your font
  $fontsize = 12;

  // x- position of your text
  $x = 9;

  // y- position of your text
  $y1 = 15;
  $y2 = 30;

  // angle of your text
  $angle = 0;

  // fill text in your image
  if($iro == "aka"){
    imagettftext($logoimg, $fontsize, $angle , $x, $y1, $red, $font, $text1);
    imagettftext($logoimg, $fontsize, $angle , $x, $y2, $red, $font, $text2);
  }elseif($iro == "shu"){
    imagettftext($logoimg, $fontsize, $angle , $x, $y1, $shu, $font, $text1);
    imagettftext($logoimg, $fontsize, $angle , $x, $y2, $shu, $font, $text2);
  }elseif($iro == "beni"){
    imagettftext($logoimg, $fontsize, $angle , $x, $y1, $beni, $font, $text1);
    imagettftext($logoimg, $fontsize, $angle , $x, $y2, $beni, $font, $text2);
  }

  // path of target where you want to save image
  $target = "temp.png";

  // save your image at new location $target
  imagepng($logoimg, $target);

  imagedestroy($logoimg);

}

// 3文字用の関数
function name3($fontpath, $iro, $num1, $num2, $num3){

  // width of your image
  $imageWidth = 34;

  // height of your image
  $imageHeight = 34;

  // create Image
  $logoimg = imagecreatetruecolor($imageWidth, $imageHeight);

  // for transparent background
  imagealphablending($logoimg, false);
  imagesavealpha($logoimg, true);
  $col = imagecolorallocatealpha($logoimg, 255, 255, 255, 127);
  imagefill($logoimg, 0, 0, $col);

  // for font color
  $white = imagecolorallocate($logoimg, 255, 255, 255);
  $red = imagecolorallocate($logoimg, 255, 0, 0);
  $shu = imagecolorallocate($logoimg, 255, 102, 0);
  $beni = imagecolorallocate($logoimg, 204, 51, 51);

  // font path
  $font = $fontpath;

  // your text which you want to show in image
  $text1 = $num1;
  $text2 = $num2;
  $text3 = $num3;

  // size of your font
  $fontsize = 9;

  // x- position of your text
  $x = 11;

  // y- position of your text
  $y1 = 12;
  $y2 = 22;
  $y3 = 32;

  // angle of your text
  $angle = 0;

  // fill text in your image
  if($iro == "aka"){
    imagettftext($logoimg, $fontsize, $angle , $x, $y1, $red, $font, $text1);
    imagettftext($logoimg, $fontsize, $angle , $x, $y2, $red, $font, $text2);
    imagettftext($logoimg, $fontsize, $angle , $x, $y3, $red, $font, $text3);
  }elseif($iro == "shu"){
    imagettftext($logoimg, $fontsize, $angle , $x, $y1, $shu, $font, $text1);
    imagettftext($logoimg, $fontsize, $angle , $x, $y2, $shu, $font, $text2);
    imagettftext($logoimg, $fontsize, $angle , $x, $y3, $shu, $font, $text3);
  }elseif($iro == "beni"){
    imagettftext($logoimg, $fontsize, $angle , $x, $y1, $beni, $font, $text1);
    imagettftext($logoimg, $fontsize, $angle , $x, $y2, $beni, $font, $text2);
    imagettftext($logoimg, $fontsize, $angle , $x, $y3, $beni, $font, $text3);
  }

  // path of target where you want to save image
  $target = "temp.png";

  // save your image at new location $target
  imagepng($logoimg, $target);

  imagedestroy($logoimg);

}

// 4文字用の関数
function name4($fontpath, $iro, $num1, $num2, $num3, $num4){

  // width of your image
  $imageWidth = 34;

  // height of your image
  $imageHeight = 34;

  // create Image
  $logoimg = imagecreatetruecolor($imageWidth, $imageHeight);

  // for transparent background
  imagealphablending($logoimg, false);
  imagesavealpha($logoimg, true);
  $col = imagecolorallocatealpha($logoimg, 255, 255, 255, 127);
  imagefill($logoimg, 0, 0, $col);

  // for font color
  $white = imagecolorallocate($logoimg, 255, 255, 255);
  $red = imagecolorallocate($logoimg, 255, 0, 0);
  $shu = imagecolorallocate($logoimg, 255, 102, 0);
  $beni = imagecolorallocate($logoimg, 204, 51, 51);

  // font path
  $font = $fontpath;

  // your text which you want to show in image
  $text1 = $num1;
  $text2 = $num2;
  $text3 = $num3;
  $text4 = $num4;

  // size of your font
  $fontsize = 8;

  // x- position of your text
  $x1 = 6;
  $x2 = 16;

  // y- position of your text
  $y1 = 15;
  $y2 = 27;

  // angle of your text
  $angle = 0;

  // fill text in your image
  if($iro == "aka"){
    imagettftext($logoimg, $fontsize, $angle , $x2, $y1, $red, $font, $text1);
    imagettftext($logoimg, $fontsize, $angle , $x2, $y2, $red, $font, $text2);
    imagettftext($logoimg, $fontsize, $angle , $x1, $y1, $red, $font, $text3);
    imagettftext($logoimg, $fontsize, $angle , $x1, $y2, $red, $font, $text4);
  }elseif($iro == "shu"){
    imagettftext($logoimg, $fontsize, $angle , $x2, $y1, $shu, $font, $text1);
    imagettftext($logoimg, $fontsize, $angle , $x2, $y2, $shu, $font, $text2);
    imagettftext($logoimg, $fontsize, $angle , $x1, $y1, $shu, $font, $text3);
    imagettftext($logoimg, $fontsize, $angle , $x1, $y2, $shu, $font, $text4);
  }elseif($iro == "beni"){
    imagettftext($logoimg, $fontsize, $angle , $x2, $y1, $beni, $font, $text1);
    imagettftext($logoimg, $fontsize, $angle , $x2, $y2, $beni, $font, $text2);
    imagettftext($logoimg, $fontsize, $angle , $x1, $y1, $beni, $font, $text3);
    imagettftext($logoimg, $fontsize, $angle , $x1, $y2, $beni, $font, $text4);
  }

  // path of target where you want to save image
  $target = "temp.png";

  // save your image at new location $target
  imagepng($logoimg, $target);

  imagedestroy($logoimg);

}

?>
