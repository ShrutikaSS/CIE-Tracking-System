<?php
/**
 * Database Connection & Helper Functions
 * CIE Activity Marks Tracking System
 */

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cie_tracking');
define('DB_PORT', 3307);

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Set charset
$conn->set_charset('utf8mb4');

/**
 * Execute a prepared statement and return results
 * @param string $sql   SQL query with ? placeholders
 * @param string $types Parameter types (s=string, i=int, d=double)
 * @param array  $params Parameters to bind
 * @return mysqli_result|bool
 */
function dbQuery($sql, $types = '', $params = [])
{
    global $conn;

    if (empty($types)) {
        $result = $conn->query($sql);
        if ($result === false) {
            error_log("SQL Error: " . $conn->error . " | Query: " . $sql);
        }
        return $result;
    }

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare Error: " . $conn->error . " | Query: " . $sql);
        return false;
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result === false && $stmt->affected_rows >= 0) {
        // INSERT/UPDATE/DELETE — return statement for affected_rows / insert_id
        return $stmt;
    }

    return $result;
}

/**
 * Fetch all rows as associative array
 */
function dbFetchAll($sql, $types = '', $params = [])
{
    $result = dbQuery($sql, $types, $params);
    if ($result === false)
        return [];

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

/**
 * Fetch single row
 */
function dbFetchOne($sql, $types = '', $params = [])
{
    $result = dbQuery($sql, $types, $params);
    if ($result === false)
        return null;
    return $result->fetch_assoc();
}

/**
 * Insert and return insert ID
 */
function dbInsert($sql, $types = '', $params = [])
{
    global $conn;
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare Error: " . $conn->error);
        return false;
    }

    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    return $id;
}

/**
 * Update/Delete and return affected rows
 */
function dbExecute($sql, $types = '', $params = [])
{
    global $conn;
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare Error: " . $conn->error);
        return false;
    }

    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();
    return $affected;
}

/**
 * Get last error
 */
function dbError()
{
    global $conn;
    return $conn->error;
}
