<?php
#echo str_repeat(' ', 1024)."<br>\n";
#ob_implicit_flush(true);
$fh = popen('ping -c 10 google.dk', 'r');
echo "<pre>\n";
#exit;
@ob_flush();
flush();
while (!feof($fh)) {
    echo fgets($fh)."<br>\n";
    @ob_flush();
    flush();
}
pclose($fh);

