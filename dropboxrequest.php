<?php
  // Build headers for request auth
error_reporting(E_ALL); ini_set('display_errors', 1); 
GetFiles('/dog');
function GetFiles($a){
  $headers = [
      'Authorization: Bearer ',//Place Key In Here
      'Content-Type: application/json'
  ];
  $fields = array(
      'path' => $a, // relative path from app folder
      'recursive' => false, // dig down into folders or not
      'include_media_info' => false, // extra file meta (not looks at this object, so not sure if it's useful)
      'include_has_explicit_shared_members' => false // I think this is to do with who it is shared with - but can't recall the logic
    );
    $data_string = json_encode($fields);
    $ch = curl_init();
    // API domain + version + resource area + command (something like that)
    curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/2/files/list_folder");
    // Build the actual request - headers + number of params + payload
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    // Return the transfer as a string - so it can be outputted to console
    // Might want to send it to a log, or just remove it for production
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $output contains the response string
    $output = curl_exec($ch);
    curl_close($ch);
    // This stuff is me trying to figure out how to get useful / concide information
    // It's mostly junk, I'm sure you'll figure something better - especally given that I've not finished

    $outputJSON = json_decode($output);
    //echo $output;
    //echo "sdgfdgdfg";
    $entries = $outputJSON->entries;

  foreach($entries as $key=>$value) {
    $data=array();
    foreach($value as $key2=>$value2) {
      array_push($data,$value2);
    }
    if($data[0]=='folder')
      {
        echo "[FOLDER]";
        GetFiles($data[2]);
      }
    else
      {
          echo "[FILE]";
      }
      GetPhotos($data[2],$data[3]);
    }
  }

function GetPhotos($b,$Id){
  $headers = [
      'Authorization: Bearer iUYi3JacHKsAAAAAAABYEsfX7sB2cgSxFWA813LgTWBeHYeR-83avDJskX-RIdfG',
      'Content-Type: application/json'
  ];
  $fields = array(
      'path' => $b, // relative path from app folder
      //'recursive' => false, // dig down into folders or not
      //'include_media_info' => false, // extra file meta (not looks at this object, so not sure if it's useful)
      //'include_has_explicit_shared_members' => false // I think this is to do with who it is shared with - but can't recall the logic
    );
    $data_string = json_encode($fields);
    $ch = curl_init();
    // API domain + version + resource area + command (something like that)
    curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/2/files/get_temporary_link");
    // Build the actual request - headers + number of params + payload
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    // Return the transfer as a string - so it can be outputted to console
    // Might want to send it to a log, or just remove it for production
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $output contains the response string
    $output = curl_exec($ch);
    curl_close($ch);
    // This stuff is me trying to figure out how to get useful / concide information
    // It's mostly junk, I'm sure you'll figure something better - especally given that I've not finished
    $outputJSON = json_decode($output,true);
    $entries = $outputJSON->entries;
    $imgSrc = $outputJSON[link];
    jpegImgCrop($imgSrc);
}
function jpegImgCrop($target_url) 
{
$im = imagecreatetruecolor(500, 500);
$stamp = imagecreatefromjpeg($target_url);

$red = imagecolorallocate($im, 209, 231, 244);
imagefill($im, 0, 0, $red);
$sx = imagesx($stamp);
$sy = imagesy($stamp);

imagecopy($im, $stamp, imagesx($im) - $sx, imagesy($im) 
- $sy, 0, 0, imagesx($stamp), imagesy($stamp));

header('Content-type: image/jpg');
imagejpeg($im,"testing.png",100);
imagedestroy($im); 
}
?>
