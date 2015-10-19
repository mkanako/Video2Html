<?php
if (PHP_SAPI !== 'cli') die();

$ffmpeg='';

$video=$argv[1];

if (!file_exists($video)) die();

$path=dirname($video);
$name=basename($video);
$dir=$path.DIRECTORY_SEPARATOR.explode('.', $name)[0].DIRECTORY_SEPARATOR;
if (file_exists($dir.'screenshot'.DIRECTORY_SEPARATOR)) {
  foreach( glob($dir.'screenshot'.DIRECTORY_SEPARATOR.'*.jpg') as $file ) @unlink($file);
}else{
  mkdirs($dir.'screenshot'.DIRECTORY_SEPARATOR);
}

exec($ffmpeg.' -ss 00:00 -i \''.$video.'\' -f image2 -r 4  -filter:v "lutyuv=\'u=128:v=128\',scale=640:-1"  \''.$dir.'screenshot'.DIRECTORY_SEPARATOR.'%3d.jpg\'');
exec('echo y|'.$ffmpeg.' -ss 00:00 -i \''.$video.'\' -ab 128k  \''.$dir.'audio.mp3\'');
foreach( glob('*.js') as $file ) copy($file, $dir.basename($file));



$js='';
$n=0;
$zip_n=0;

@unlink($dir.'data.js');

foreach (glob( $dir.'screenshot'.DIRECTORY_SEPARATOR.'*.jpg' , GLOB_BRACE ) as $index=>$filename)
{
  $js.=generator($filename);
  if ($n>200) {
    file_put_contents($dir.'data.js', $js,FILE_APPEND);
    $js='';
    $n=0;
  }
  $n++;
  clearstatcache();
  if (file_exists($dir.'data.js')&&filesize($dir.'data.js')>10000000) {
    $zip = new ZipArchive();
    if ($zip->open($dir.'data'.$zip_n.'.zip', ZIPARCHIVE::CREATE)!==TRUE) {
        exit("cannot open file\n");
    }
    $zip->addFile($dir.'data.js');
    $zip->close();
    @unlink($dir.'data.js');
    $zip_n++;
  }
}

if ($n<=200) file_put_contents($dir.'data.js', $js,FILE_APPEND);
if (file_exists($dir.'data.js')) {
  $zip = new ZipArchive();
  if ($zip->open($dir.'data'.$zip_n.'.zip', ZIPARCHIVE::CREATE)!==TRUE) {
      exit("cannot open file\n");
  }
  $zip->addFile($dir.'data.js');
  $zip->close();
  @unlink($dir.'data.js');
}
ob_start();
include 'index.html';
$html=ob_get_contents();
ob_end_clean();
file_put_contents($dir.'index.html', $html);

die();


function generator($f){
  $img     = imagecreatefromjpeg($f);
  $imgSize = getimagesize($f);
  $imgX    = $imgSize[0];
  $imgY    = $imgSize[1];
  $text    = '';
  $reply=1;
  $last_color=imagecolorat($img, 0, 0);
  for ($j = 0; $j < $imgY; $j += 4) {
      for ($i = 0; $i < $imgX; $i += 4) {
          if ($i==0&&$j==0) continue;
          $color = imagecolorat($img, $i, $j);
          if ($last_color==$color) {
            $reply++;
          }else{
            if ($reply>1) {
              $text.=hexcolor($last_color).'*'.$reply.',';
            }else{
              $text.=hexcolor($last_color).',';
            }
            $last_color=$color;
            $reply=1;
          }
      }
  }
  if ($reply>1) {
    $text.=hexcolor($last_color).'*'.$reply;
  }else{
    $text.=hexcolor($last_color);
  }
  return $text.';';
}

function hexcolor($c) {
    $r = ($c >> 16) & 0xFF;
    return dechex($r);
    $g = ($c >> 8) & 0xFF;
    $b = $c & 0xFF;
    if ($r==$g&&$g==$b) return dechex($r).dechex($r).dechex($r);
    return '' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

function mkdirs($path) {
  if (!is_dir($path)) {
    mkdirs(dirname($path));
    mkdir($path);
  }
  return is_dir($path);
}
