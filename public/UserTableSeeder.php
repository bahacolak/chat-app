<?php

class UserTableSeeder
{
    private $dbFile = 'chatapptest.sqlite';
    private $db;

    public function __construct()
    {
        
        $this->db = new SQLite3($this->dbFile);

       
        $this->createUsersTable();
    }

    private function createUsersTable()
    {
        $query = 'CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    uuid TEXT NOT NULL,
                    hashPassword TEXT NOT NULL,
                    firstName TEXT NOT NULL,
                    lastName TEXT NOT NULL,
                    email TEXT NOT NULL,
                    dateOfBirth DATE,
                    registrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                  )';

        
        if ($this->db->exec($query)) {
            echo "Users table created or already exist";
        } else {
            echo "Error occured: " . $this->db->lastErrorMsg();
        }
    }

    public function addDummyUsers()
    {
        
        $names = array('Bahadir', 'Jane', 'Michael', 'Emily', 'David');
        $surnames = array('Colak', 'Smith', 'Johnson', 'Brown', 'Williams');
        $emails = array('bahadircolak.dev@gmail.com', 'jane@example.com', 'michael@example.com', 'emily@example.com', 'david@example.com');
        $datesOfBirth = array('1990-01-15', '1988-07-22', '1995-04-10', '1992-11-30', '1985-09-18');

        
        foreach ($names as $index => $name) {
            $uuid = uniqid();
            $hashPassword = password_hash('123456', PASSWORD_DEFAULT); 
            $lastName = $surnames[$index];
            $email = $emails[$index];
            $dateOfBirth = $datesOfBirth[$index];

            $query = "INSERT INTO users (uuid, hashPassword, firstName, lastName, email, dateOfBirth)
                      VALUES ('$uuid', '$hashPassword', '$name', '$lastName', '$email', '$dateOfBirth')";

            if ($this->db->exec($query)) {
                echo "\nDummy users added.";
            } else {
                echo "\nError occured: " . $this->db->lastErrorMsg();
            }
        }

    
        $this->db->close();
    }
}


$seeder = new UserTableSeeder();
$seeder->addDummyUsers();

?>
