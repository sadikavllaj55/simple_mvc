<?php

namespace App\Model;

use PDO;
class Tasks
{
    private $db;

    public function __construct()
    {
        $db = DatabaseConnection::getInstance();
        $db_config = CONFIG['database'];
        $db::connect($db_config['host'], $db_config['dbname'], $db_config['username'], $db_config['password']);
        $this->db = $db->getConnection();
    }

    public function insert($title, $note, $time)
    {
        $user_id = $this->getUserId();

        if ($user_id === false) {
            return false;
        }

        $query = $this->db->prepare('
            INSERT INTO task (user_id, title, note, time) 
            VALUES (:user, :title, :note, :time)'
        );

        if ($time == '') {
            $time = null;
        }

        $query->bindParam(':user', $user_id);
        $query->bindParam(':title', $title);
        $query->bindParam(':note', $note);
        $query->bindParam(':time', $time);

        return $query->execute();
    }

    public function getById($id) {
        $query = $this->db->prepare('
            SELECT *
            FROM task 
            WHERE task.id=:id
        ');

        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function myTasks()
    {
        $query = $this->db->prepare('
            SELECT *
            FROM task    
            WHERE user_id=:user
         ');

        $user_id = $this->getUserId();

        $query->bindParam(':user', $user_id);

        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $note, $time)
    {
        $query = $this->db->prepare('
            UPDATE task 
            SET title = :title, note = :note, time = :time
            WHERE id=:id'
        );

        if ($time == '') {
            $time = null;
        }

        $query->bindParam(':id', $id);
        $query->bindParam(':title', $title);
        $query->bindParam(':note', $note);
        $query->bindParam(':time', $time);
        return $query->execute();
    }

    public function delete($id)
    {
        $query = $this->db->prepare('
            DELETE  FROM task 
            WHERE id = :id'
        );
        $query->bindParam(':id', $id);
        return $query->execute();
    }

    private function getUserId()
    {
        $auth = new Auth();

        if ($auth->isLoggedIn()) {
            return $auth->getCurrentUser()['id'];
        }

        return false;
    }
}