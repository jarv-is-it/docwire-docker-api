<?php

$path = __DIR__ . '/../config/laravel';

$content = file_get_contents($path);
$content = str_replace("\r", '', $content);
file_put_contents($path, $content);
