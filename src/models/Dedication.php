<?php
class Dedication {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($song_id, $message) {
        $this->conn->query("UPDATE dedications SET is_active = 0");

        $stmt = $this->conn->prepare("
            INSERT INTO dedications (song_id, message, is_active)
            VALUES (?, ?, 1)
        ");
        $stmt->bind_param("is", $song_id, $message);
        return $stmt->execute();
    }

    public function getActive() {
        $result = $this->conn->query("
            SELECT d.message, s.youtube_link
            FROM dedications d
            JOIN songs s ON d.song_id = s.id
            WHERE d.is_active = 1
            ORDER BY d.created_at DESC
            LIMIT 1
        ");
        return $result->fetch_assoc();
    }

    public function getAll() {
        return $this->conn->query("
            SELECT d.message, s.youtube_link, d.created_at
            FROM dedications d
            JOIN songs s ON d.song_id = s.id
            ORDER BY d.created_at DESC
        ");
    }
}