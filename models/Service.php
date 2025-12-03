<?php

/**
 * Service Model
 * Handles all service-related database operations
 */

require_once __DIR__ . '/../config/database.php';

class Service
{
    private $conn;
    private $table = 'services';

    public $id;
    public $service_name;
    public $description;
    public $requirements;
    public $processing_time;
    public $fee;
    public $is_active;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Create new service
     */
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " 
                  SET service_name=:service_name, description=:description, 
                      requirements=:requirements, processing_time=:processing_time, 
                      fee=:fee, is_active=:is_active";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":service_name", $this->service_name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":requirements", $this->requirements);
        $stmt->bindParam(":processing_time", $this->processing_time);
        $stmt->bindParam(":fee", $this->fee);
        $stmt->bindParam(":is_active", $this->is_active);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Read all services
     */
    public function read()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY service_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Read active services only
     */
    public function readActive()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE is_active = 1 ORDER BY service_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Read single service
     */
    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->service_name = $row['service_name'];
            $this->description = $row['description'];
            $this->requirements = $row['requirements'];
            $this->processing_time = $row['processing_time'];
            $this->fee = $row['fee'];
            $this->is_active = $row['is_active'];
            return true;
        }
        return false;
    }

    /**
     * Update service
     */
    public function update()
    {
        $query = "UPDATE " . $this->table . " 
                  SET service_name=:service_name, description=:description, 
                      requirements=:requirements, processing_time=:processing_time, 
                      fee=:fee, is_active=:is_active 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":service_name", $this->service_name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":requirements", $this->requirements);
        $stmt->bindParam(":processing_time", $this->processing_time);
        $stmt->bindParam(":fee", $this->fee);
        $stmt->bindParam(":is_active", $this->is_active);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Delete service
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
}
