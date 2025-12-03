<?php

/**
 * User Model
 * Handles all user-related database operations
 */

require_once __DIR__ . '/../config/database.php';

class User
{
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $middle_name;
    public $phone;
    public $address;
    public $profile_picture;
    public $role;
    public $is_active;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Create new user
     */
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " 
                  SET username=:username, email=:email, password=:password, 
                      first_name=:first_name, last_name=:last_name, middle_name=:middle_name,
                      phone=:phone, address=:address, role=:role";

        $stmt = $this->conn->prepare($query);

        // Hash password
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":middle_name", $this->middle_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":role", $this->role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Read all users
     */
    public function read()
    {
        $query = "SELECT id, username, email, first_name, last_name, middle_name, 
                  phone, address, role, is_active, created_at 
                  FROM " . $this->table . " 
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Read single user
     */
    public function readOne()
    {
        $query = "SELECT id, username, email, first_name, last_name, middle_name, 
                  phone, address, profile_picture, role, is_active, created_at 
                  FROM " . $this->table . " 
                  WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->middle_name = $row['middle_name'];
            $this->profile_picture = $row['profile_picture'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->role = $row['role'];
            $this->is_active = $row['is_active'];
            return true;
        }
        return false;
    }

    /**
     * Update user
     */
    public function update()
    {
        $query = "UPDATE " . $this->table . " 
                  SET username=:username, email=:email, first_name=:first_name, 
                      last_name=:last_name, middle_name=:middle_name, phone=:phone, 
                      address=:address, role=:role, is_active=:is_active 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":middle_name", $this->middle_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":is_active", $this->is_active);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Delete user
     */
    public function delete()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Login user
     */
    public function login($username, $password)
    {
        $query = "SELECT id, username, email, password, first_name, last_name, role, is_active 
                  FROM " . $this->table . " 
                  WHERE (username = :username OR email = :username) AND is_active = 1 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password'])) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->role = $row['role'];
            return true;
        }
        return false;
    }

    /**
     * Check if email exists
     */
    public function emailExists()
    {
        $query = "SELECT id FROM " . $this->table . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Check if username exists
     */
    public function usernameExists()
    {
        $query = "SELECT id FROM " . $this->table . " WHERE username = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
