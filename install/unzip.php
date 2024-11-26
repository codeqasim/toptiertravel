<?php

$zip = new ZipArchive;
$res = $zip->open('v9.zip');
if ($res === TRUE) {
  $zip->extractTo('../');
  $zip->close();
  echo 'done';
} else {
  echo 'error';
}
