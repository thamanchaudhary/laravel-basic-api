<?php
if (!function_exists('p')) {
    function p($data)
    {
        echo "<pre>";
        printf($data);
        echo "</pre>";
    }
}
