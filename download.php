<?php

$type = $_POST["type"] ?? "maru";
$fontKey = $_POST["font"] ?? "nikumaru";
$hcolor = $_POST["color"] ?? "aka";

// フォントパスの設定
$fontPath = "./fonts/nikumaru.otf";
if ($fontKey == "dasaji") {
    $fontPath = "./fonts/dasaji.ttf";
} elseif ($fontKey == "genomon") {
    $fontPath = "./fonts/SourceHanSerif-Regular.otf";
}

// 出力用ファイル名
$destination = "hanko_" . makeRandStr(6) . ".png";

// タイプ別の生成処理
if ($type === "kaku") {
    $cname = trim($_POST["cname"] ?? "株式会社サンプル");
    createKakuIn($fontPath, $hcolor, $cname, $destination);
} elseif ($type === "date") {
    $d_top = trim($_POST["d_top"] ?? "承認");
    $d_date = trim($_POST["d_date"] ?? date('Y.m.d'));
    $d_bottom = trim($_POST["d_bottom"] ?? "山田");
    createDateIn($fontPath, $hcolor, $d_top, $d_date, $d_bottom, $destination);
} else {
    // 丸印
    $fname = trim($_POST["fname"] ?? "山田");
    createMaruIn($fontPath, $hcolor, $fname, $destination);
}

// ダウンロード処理
if (file_exists($destination)) {
    header('Content-Type: image/png');
    header('Content-Length: ' . filesize($destination));
    header('Content-Disposition: attachment; filename="' . $destination . '"');
    readfile($destination);

    // 一時ファイル削除
    @unlink($destination);
    exit;
}

/**
 * ランダム文字列生成
 */
function makeRandStr($length = 6) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, $length);
}

/**
 * カラー定義取得
 */
function getColorRGB($iro) {
    if ($iro === "shu") {
        return [255, 102, 0];   // 朱
    } elseif ($iro === "beni") {
        return [204, 51, 51];   // 紅
    }
    return [255, 0, 0];         // 赤
}

/**
 * 1. 丸印（認印）生成関数 - 枠線もPHPで描画
 */
function createMaruIn($fontpath, $iro, $fname, $target) {
    $size = 120; // 120x120px
    $img = imagecreatetruecolor($size, $size);
    imagealphablending($img, false);
    imagesavealpha($img, true);

    $trans = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $trans);

    list($r, $g, $b) = getColorRGB($iro);
    $color = imagecolorallocate($img, $r, $g, $b);

    // 外枠（円）を描画
    imagesetthickness($img, 5);
    imageellipse($img, $size / 2, $size / 2, $size - 10, $size - 10, $color);

    // 文字の描画配置
    $num = mb_strlen($fname, "UTF-8");
    if ($num === 1) {
        imagettftext($img, 50, 0, 32, 85, $color, $fontpath, $fname);
    } elseif ($num === 2) {
        $t1 = mb_substr($fname, 0, 1, "UTF-8");
        $t2 = mb_substr($fname, 1, 1, "UTF-8");
        imagettftext($img, 36, 0, 38, 52, $color, $fontpath, $t1);
        imagettftext($img, 36, 0, 38, 100, $color, $fontpath, $t2);
    } elseif ($num === 3) {
        $t1 = mb_substr($fname, 0, 1, "UTF-8");
        $t2 = mb_substr($fname, 1, 1, "UTF-8");
        $t3 = mb_substr($fname, 2, 1, "UTF-8");
        imagettftext($img, 26, 0, 44, 40, $color, $fontpath, $t1);
        imagettftext($img, 26, 0, 44, 73, $color, $fontpath, $t2);
        imagettftext($img, 26, 0, 44, 106, $color, $fontpath, $t3);
    } else {
        // 4文字（2行2列）
        $t1 = mb_substr($fname, 0, 1, "UTF-8");
        $t2 = mb_substr($fname, 1, 1, "UTF-8");
        $t3 = mb_substr($fname, 2, 1, "UTF-8");
        $t4 = mb_substr($fname, 3, 1, "UTF-8");
        imagettftext($img, 28, 0, 62, 52, $color, $fontpath, $t1);
        imagettftext($img, 28, 0, 62, 98, $color, $fontpath, $t2);
        imagettftext($img, 28, 0, 20, 52, $color, $fontpath, $t3);
        imagettftext($img, 28, 0, 20, 98, $color, $fontpath, $t4);
    }

    imagepng($img, $target);
    imagedestroy($img);
}

/**
 * 2. 角印生成関数（二重枠＋複数行対応）
 */
function createKakuIn($fontpath, $iro, $cname, $target) {
    $width = 160;
    $height = 160;
    $img = imagecreatetruecolor($width, $height);
    imagealphablending($img, false);
    imagesavealpha($img, true);

    $trans = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $trans);

    list($r, $g, $b) = getColorRGB($iro);
    $color = imagecolorallocate($img, $r, $g, $b);

    // 外枠と内枠（二重線）の描画
    imagesetthickness($img, 4);
    imagerectangle($img, 6, 6, $width - 7, $height - 7, $color); // 外枠
    imagesetthickness($img, 2);
    imagerectangle($img, 13, 13, $width - 14, $height - 14, $color); // 内枠

    // 送り字（〜之印）を自動付与する調整
    if (!preg_match('/(印|之印)$/u', $cname)) {
        $cname .= "之印";
    }

    // 文字列を縦書き2列〜3列に並べる計算
    $chars = [];
    $len = mb_strlen($cname, "UTF-8");
    for ($i = 0; $i < $len; $i++) {
        $chars[] = mb_substr($cname, $i, 1, "UTF-8");
    }

    // 縦書きレイアウト簡易化（2列構成）
    $half = ceil($len / 2);
    $col1 = array_slice($chars, 0, $half); // 右列
    $col2 = array_slice($chars, $half);    // 左列

    $fontSize = 18;
    // 右列の描画
    $y = 42;
    foreach ($col1 as $ch) {
        imagettftext($img, $fontSize, 0, 95, $y, $color, $fontpath, $ch);
        $y += 28;
    }
    // 左列の描画
    $y = 42;
    foreach ($col2 as $ch) {
        imagettftext($img, $fontSize, 0, 42, $y, $color, $fontpath, $ch);
        $y += 28;
    }

    imagepng($img, $target);
    imagedestroy($img);
}

/**
 * 3. 日付印（デイト印）生成関数
 */
function createDateIn($fontpath, $iro, $topText, $dateText, $bottomText, $target) {
    $size = 150;
    $img = imagecreatetruecolor($size, $size);
    imagealphablending($img, false);
    imagesavealpha($img, true);

    $trans = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $trans);

    list($r, $g, $b) = getColorRGB($iro);
    $color = imagecolorallocate($img, $r, $g, $b);

    // 外枠（円）の描画
    imagesetthickness($img, 4);
    imageellipse($img, $size / 2, $size / 2, $size - 10, $size - 10, $color);

    // 中心の仕切り線（横2本）
    imagesetthickness($img, 2);
    imageline($img, 15, 52, $size - 15, 52, $color);
    imageline($img, 15, 98, $size - 15, 98, $color);

    // フォーマット調整（ハイフンをドットに統一など）
    $dateText = str_replace('-', '.', $dateText);

    // 文字の描画（各段）
    // 上段（例：承認）
    imagettftext($img, 16, 0, 48, 40, $color, $fontpath, $topText);
    // 中段（日付：例：2026.08.12）
    imagettftext($img, 11, 0, 22, 80, $color, $fontpath, $dateText);
    // 下段（例：山田）
    imagettftext($img, 16, 0, 48, 132, $color, $fontpath, $bottomText);

    imagepng($img, $target);
    imagedestroy($img);
}
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
