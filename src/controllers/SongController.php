<?php
require_once "../models/Song.php";

class SongController {
    private $songModel;

    public function __construct($db) {
        $this->songModel = new Song($db);
    }

    public function create($link, $title) {
        return $this->songModel->create($link, $title);
    }

    public function getAll() {
        return $this->songModel->getAll();
    }
}