<?php
class Song {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        return $this->conn->query("SELECT * FROM songs");
    }

    public function create($link, $title) {
        $stmt = $this->conn->prepare("INSERT INTO songs (youtube_link, title) VALUES (?, ?)");
        $stmt->bind_param("ss", $link, $title);
        return $stmt->execute();
    }
}