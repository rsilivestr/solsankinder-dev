<?php

include_once "check-in/conn.php";

function createTable($table_name, $table_schema) {
  if ($GLOBALS['conn']->query("DESCRIBE $table_name")) {
    echo "Table \"$table_name\" already exists";
  } else {
    $sql = "CREATE TABLE " . $table_name . $table_schema;

    if ($GLOBALS['conn']->query($sql) === TRUE) {
      echo "Table \"$table_name\" was created";
    } else {
      echo "Something went wrong, table \"$table_name\" was not created";
    }
  }
}

function dropTable($table_name) {
  if (!$GLOBALS['conn']->query("DESCRIBE $table_name")) {
    echo "Table \"$table_name\" does not exist";
  } else {
    $sql = "DROP TABLE $table_name";
    $GLOBALS['conn']->query($sql);
    echo "Table \"$table_name\" was dropped";
  }
}

$districtSchema = "(
  id INT PRIMARY KEY AUTO_INCREMENT,
  district_name VARCHAR(30) NOT NULL
)";

$clinicSchema = "(
  id INT PRIMARY KEY AUTO_INCREMENT,
  clinic_name VARCHAR(255) NOT NULL,
  district_id INT NOT NULL
)";

$unitSchema = "(
  id INT PRIMARY KEY AUTO_INCREMENT,
  unit_name VARCHAR(100) NOT NULL,
  date_id INT DEFAULT 0
)";

$dateSchema = "(
  id INT PRIMARY KEY AUTO_INCREMENT,
  ci_date DATE NOT NULL,
  unit_ids VARCHAR(100) NOT NULL,
  is_active TINYINT(1)
)";

$intervalSchema = "(
  id INT PRIMARY KEY AUTO_INCREMENT,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  is_active TINYINT(1)
)";

$spotSchema = "(
  id INT PRIMARY KEY AUTO_INCREMENT,
  date_id INT NOT NULL,
  interval_id INT NOT NULL,
  total INT DEFAULT 40,
  occupied INT DEFAULT 0,
  available INT DEFAULT 40
)";

$patientSchema = "(
  id INT PRIMARY KEY AUTO_INCREMENT,
  fio VARCHAR(128) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  dob DATE NOT NULL
)";

$eventSchema = "(
  event_id INT PRIMARY KEY AUTO_INCREMENT,
  patient_id INT NOT NULL,
  date_id INT NOT NULL,
  interval_id INT NOT NULL,
  unit_id INT NOT NULL,
  clinic_id INT NOT NULL
)";
