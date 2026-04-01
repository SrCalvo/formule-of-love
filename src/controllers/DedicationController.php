<?php
require_once "../models/Dedication.php";

class DedicationController {
    private $model;

    public function __construct($db) {
        $this->model = new Dedication($db);
    }

    public function create($song_id, $message) {
        return $this->model->create($song_id, $message);
    }

    public function getActive() {
        return $this->model->getActive();
    }

    public function getAll() {
        return $this->model->getAll();
    }
}