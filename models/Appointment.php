<?php

/**
 * Appointment Model
 * Handles all appointment-related database operations
 */

require_once __DIR__ . '/../config/database.php';

class Appointment
{
    private $conn;
    private $table = 'appointments';

    public $id;
    public $user_id;
    public $service_id;
    public $appointment_date;
    public $appointment_time;
    public $status;
    public $purpose;
    public $notes;
    public $admin_notes;
    public $queue_number;
    public $payment_method;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Create new appointment
     */
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " 
                  SET user_id=:user_id, service_id=:service_id, 
                      appointment_date=:appointment_date, appointment_time=:appointment_time, 
                      purpose=:purpose, notes=:notes, status=:status, payment_method=:payment_method";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":service_id", $this->service_id);
        $stmt->bindParam(":appointment_date", $this->appointment_date);
        $stmt->bindParam(":appointment_time", $this->appointment_time);
        $stmt->bindParam(":purpose", $this->purpose);
        $stmt->bindParam(":notes", $this->notes);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":payment_method", $this->payment_method);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Read all appointments
     */
    public function read()
    {
        $query = "SELECT a.*, u.first_name, u.last_name, u.email, u.phone, 
                  s.service_name, s.fee 
                  FROM " . $this->table . " a
                  LEFT JOIN users u ON a.user_id = u.id
                  LEFT JOIN services s ON a.service_id = s.id
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Read appointments by user
     */
    public function readByUser()
    {
        $query = "SELECT a.*, s.service_name, s.fee, a.payment_method 
                  FROM " . $this->table . " a
                  LEFT JOIN services s ON a.service_id = s.id
                  WHERE a.user_id = ?
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Read appointments by date
     */
    public function readByDate($date)
    {
        $query = "SELECT a.*, u.first_name, u.last_name, u.email, u.phone, 
                  s.service_name 
                  FROM " . $this->table . " a
                  LEFT JOIN users u ON a.user_id = u.id
                  LEFT JOIN services s ON a.service_id = s.id
                  WHERE a.appointment_date = ?
                  ORDER BY a.appointment_time ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $date);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Read single appointment
     */
    public function readOne()
    {
        $query = "SELECT a.*, u.first_name, u.last_name, u.email, u.phone, u.address,
                  s.service_name, s.description, s.requirements, s.fee 
                  FROM " . $this->table . " a
                  LEFT JOIN users u ON a.user_id = u.id
                  LEFT JOIN services s ON a.service_id = s.id
                  WHERE a.id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->user_id = $row['user_id'];
            $this->service_id = $row['service_id'];
            $this->appointment_date = $row['appointment_date'];
            $this->appointment_time = $row['appointment_time'];
            $this->status = $row['status'];
            $this->purpose = $row['purpose'];
            $this->notes = $row['notes'];
            $this->admin_notes = $row['admin_notes'];
            $this->queue_number = $row['queue_number'];
            return $row;
        }
        return false;
    }

    /**
     * Update appointment
     */
    public function update()
    {
        $query = "UPDATE " . $this->table . " 
                  SET appointment_date=:appointment_date, appointment_time=:appointment_time, 
                      status=:status, admin_notes=:admin_notes, queue_number=:queue_number 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":appointment_date", $this->appointment_date);
        $stmt->bindParam(":appointment_time", $this->appointment_time);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":admin_notes", $this->admin_notes);
        $stmt->bindParam(":queue_number", $this->queue_number);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Update status
     */
    public function updateStatus()
    {
        $query = "UPDATE " . $this->table . " 
                  SET status=:status, admin_notes=:admin_notes 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":admin_notes", $this->admin_notes);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Delete appointment
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
     * Check if time slot is available
     */
    public function isTimeSlotAvailable($date, $time, $exclude_id = null)
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE appointment_date = :date 
                  AND appointment_time = :time 
                  AND status NOT IN ('cancelled', 'rejected')";

        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":time", $time);

        if ($exclude_id) {
            $stmt->bindParam(":exclude_id", $exclude_id);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['count'] == 0;
    }

    /**
     * Get appointment statistics
     */
    public function getStatistics()
    {
        $query = "SELECT 
                  COUNT(*) as total,
                  SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                  SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                  SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                  SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                  FROM " . $this->table;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
