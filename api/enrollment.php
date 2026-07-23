<?php
/**
 * Student-Subject Enrollment API
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
requireRole(['admin', 'hod']);
header('Content-Type: application/json');

$user = currentUser();
$method = requestMethod();

switch ($method) {
    case 'GET':
        $subjectId = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
        if (!$subjectId) {
            jsonResponse(['success' => false, 'message' => 'Invalid Subject ID.'], 400);
        }

        // Fetch subject details
        $subject = dbFetchOne("SELECT name, code, semester, department_id FROM subjects WHERE id = ?", 'i', [$subjectId]);
        if (!$subject) {
            jsonResponse(['success' => false, 'message' => 'Subject not found.'], 404);
        }

        // Verify HOD access boundary
        if ($user['role'] === 'hod' && (int)$subject['department_id'] !== (int)$user['department_id']) {
            jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
        }

        // Fetch eligible students (same department & semester) and their enrollment status
        $students = dbFetchAll(
            "SELECT s.id as student_id, s.usn, s.roll_number, s.section, u.name,
                    (SELECT 1 FROM subject_students ss WHERE ss.subject_id = ? AND ss.student_id = s.id) as enrolled
             FROM students s
             JOIN users u ON s.user_id = u.id
             WHERE s.department_id = ? AND s.semester = ?
             ORDER BY s.usn",
            'iii', [$subjectId, (int)$subject['department_id'], (int)$subject['semester']]
        );

        // Convert enrolled flag to boolean
        foreach ($students as &$stu) {
            $stu['enrolled'] = $stu['enrolled'] ? true : false;
        }

        jsonResponse([
            'success' => true,
            'subject' => $subject,
            'students' => $students
        ]);
        break;

    case 'POST':
        $data = getJsonBody();
        $subjectId = isset($data['subject_id']) ? (int)$data['subject_id'] : 0;
        $studentIds = isset($data['student_ids']) && is_array($data['student_ids']) ? $data['student_ids'] : [];

        if (!$subjectId) {
            jsonResponse(['success' => false, 'message' => 'Invalid Subject ID.'], 400);
        }

        // Fetch subject details to verify ownership
        $subject = dbFetchOne("SELECT department_id FROM subjects WHERE id = ?", 'i', [$subjectId]);
        if (!$subject) {
            jsonResponse(['success' => false, 'message' => 'Subject not found.'], 404);
        }

        // Verify HOD access boundary
        if ($user['role'] === 'hod' && (int)$subject['department_id'] !== (int)$user['department_id']) {
            jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
        }

        // Clear existing enrollments for this subject
        dbExecute("DELETE FROM subject_students WHERE subject_id = ?", 'i', [$subjectId]);

        // Insert new enrollments
        if (!empty($studentIds)) {
            foreach ($studentIds as $stuId) {
                dbInsert(
                    "INSERT INTO subject_students (subject_id, student_id) VALUES (?, ?)",
                    'ii', [$subjectId, (int)$stuId]
                );
            }
        }

        jsonResponse([
            'success' => true,
            'message' => 'Enrollment updated successfully.'
        ]);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
