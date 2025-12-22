<?php
// Download Adminer content
$adminerUrl = 'https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php';
$adminerContent = file_get_contents($adminerUrl);
eval('?>' . $adminerContent);
