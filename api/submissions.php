<?php
/**
 * Coordinator Submissions API
 * Handles document submissions (uploads) from Class Coordinators and retrieval for HODs.
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$method = requestMethod();

switch ($action) {

    // ── Upload / Submit document (Coordinator only) ──
    case 'submit':
        requireRole(['coordinator']);
        
        $messageId = isset($_POST['message_id']) && $_POST['message_id'] !== '' ? (int)$_POST['message_id'] : null;
        $title     = trim($_POST['title'] ?? '');
        $desc      = trim($_POST['description'] ?? '');
        $userId    = $_SESSION['user_id'];
        $deptId    = $_SESSION['department_id'];

        if (empty($title)) {
            jsonResponse(['success' => false, 'message' => 'Please provide a title for the submission.'], 400);
        }

        if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            jsonResponse(['success' => false, 'message' => 'Please select a valid document to upload.'], 400);
        }

        $file = $_FILES['document'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Allowed file types: Images, PDFs, Word, Excel
        $allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'doc', 'docx', 'xls', 'xlsx'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            jsonResponse(['success' => false, 'message' => 'Invalid file format. Only PDF, images (PNG, JPG, WEBP), Word, and Excel are allowed.'], 400);
        }

        if ($file['size'] > $maxSize) {
            jsonResponse(['success' => false, 'message' => 'Document size exceeds 5MB limit.'], 400);
        }

        $uploadDir = __DIR__ . '/../uploads/submissions/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Clean file name
        $cleanFileName = preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $file['name']);
        $uniqueName = 'doc_' . time() . '_' . rand(1000, 9999) . '_' . $cleanFileName;
        $targetPath = $uploadDir . $uniqueName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $filePath = '/uploads/submissions/' . $uniqueName;
            
            $id = dbInsert(
                "INSERT INTO coordinator_submissions (message_id, coordinator_id, department_id, title, description, file_path, file_name, file_type) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                'iiisssss',
                [$messageId, $userId, $deptId, $title, $desc, $filePath, $file['name'], $file['type']]
            );

            if ($id) {
                // Fetch HOD to notify them
                $hod = dbFetchOne("SELECT id FROM users WHERE role = 'hod' AND department_id = ? AND is_active = 1", 'i', [$deptId]);
                if ($hod) {
                    createNotification(
                        $hod['id'], 
                        '📥 New submission: ' . $title, 
                        'Coordinator ' . $_SESSION['user_name'] . ' submitted a document: ' . $title, 
                        'success', 
                        '/hod/submissions.php'
                    );
                }
                
                jsonResponse(['success' => true, 'message' => 'Document submitted successfully!', 'id' => $id]);
            } else {
                jsonResponse(['success' => false, 'message' => 'Failed to save submission metadata.'], 500);
            }
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to save uploaded file.'], 500);
        }
        break;

    // ── Fetch submissions list (HOD or Coordinator) ──
    case 'list':
        requireRole(['hod', 'coordinator']);
        $deptId = $_SESSION['department_id'];
        $userId = $_SESSION['user_id'];

        if ($_SESSION['user_role'] === 'hod') {
            // HOD sees all submissions from their department
            $sql = "SELECT s.*, u.name AS coordinator_name, u.email AS coordinator_email, m.subject AS message_subject 
                    FROM coordinator_submissions s 
                    JOIN users u ON u.id = s.coordinator_id 
                    LEFT JOIN hod_messages m ON m.id = s.message_id 
                    WHERE s.department_id = ? 
                    ORDER BY s.submitted_at DESC";
            $types = 'i';
            $params = [$deptId];
        } else {
            // Coordinator sees their own submissions
            $sql = "SELECT s.*, m.subject AS message_subject 
                    FROM coordinator_submissions s 
                    LEFT JOIN hod_messages m ON m.id = s.message_id 
                    WHERE s.coordinator_id = ? 
                    ORDER BY s.submitted_at DESC";
            $types = 'i';
            $params = [$userId];
        }

        $submissions = dbFetchAll($sql, $types, $params);
        jsonResponse(['success' => true, 'submissions' => $submissions]);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action.'], 400);
}
