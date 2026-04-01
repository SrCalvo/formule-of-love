<?php
function getYouTubeId($url) {
    if (preg_match('/youtu\.be\/([^\?]+)/', $url, $match)) {
        return $match[1];
    }
    if (preg_match('/v=([^&]+)/', $url, $match)) {
        return $match[1];
    }
    if (preg_match('/embed\/([^\?]+)/', $url, $match)) {
        return $match[1];
    }
    return "";
}