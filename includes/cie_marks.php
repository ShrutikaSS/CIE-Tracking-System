<?php
/**
 * CIE Activity Marks System Logic
 * Single source of truth for all marks calculations and conversions.
 */

require_once __DIR__ . '/db.php';

/**
 * Calculate marks based on time decay and minimum marks rule.
 * 
 * - If submitted_time > end_time → marks = 0 (late submission)
 * - Else → linear decay: marks = max_marks * (1 - timeTaken/totalDuration)
 * - Minimum marks rule: if marks < 2 → marks = 2
 * - Early bonus: if submitted in first 10% of window → +1 bonus (capped at max_marks)
 * 
 * @param string|null $startTime
 * @param string|null $endTime
 * @param string $submittedTime
 * @param float $maxMarks
 * @return float
 */
function calculateMarks($startTime, $endTime, $submittedTime, $maxMarks = 10.0) {
    if (!$startTime || !$endTime) {
        return floatval($maxMarks);
    }
    
    $start = strtotime($startTime);
    $end = strtotime($endTime);
    $submit = strtotime($submittedTime);
    
    if ($start === false || $end === false || $submit === false || $start >= $end) {
        return floatval($maxMarks);
    }

    if ($submit > $end) {
        $secondsLate = $submit - $end;
        $daysLate = ceil($secondsLate / 86400);
        $marks = max(0.0, floatval($maxMarks) - $daysLate);
        return round($marks, 2);
    }

    if ($submit < $start) {
        return floatval($maxMarks);
    }

    $totalDuration = $end - $start;
    $timeTaken = $submit - $start;
    
    $decayFactor = 1.0 - ($timeTaken / $totalDuration);
    $marks = $maxMarks * $decayFactor;
    
    if ($timeTaken <= (0.1 * $totalDuration)) {
        $marks += 1.0;
    }
    
    if ($marks < 2.0 && $marks > 0) {
        $marks = 2.0;
    }
    
    if ($marks > $maxMarks) {
        $marks = $maxMarks;
    }
    
    return round($marks, 2);
}

/**
 * Get total marks awarded/obtained for a student in a subject.
 * Sums across all activities (units 1-6).
 * 
 * @param int $studentId
 * @param int $subjectId
 * @return float Total marks out of 60
 */
function getTotalActivityMarks($studentId, $subjectId) {
    $sql = "
        SELECT SUM(COALESCE(m.marks_obtained, s.marks_awarded, 0)) as total_marks
        FROM activities a
        LEFT JOIN marks m ON m.activity_id = a.id AND m.student_id = ?
        LEFT JOIN submissions s ON s.activity_id = a.id AND s.student_id = ?
        WHERE a.subject_id = ?
    ";
    $result = dbFetchOne($sql, 'iii', [$studentId, $studentId, $subjectId]);
    return $result ? floatval($result['total_marks']) : 0.0;
}

/**
 * Convert total activity marks (out of 60) to final CIE score (out of 20).
 * Formula: (TOTAL * 20) / 60
 * 
 * @param float $totalMarks
 * @return float
 */
function convertToCIE($totalMarks) {
    return round(($totalMarks * 20) / 60, 2);
}

/**
 * Determine activity status based on current time.
 * 
 * @param string|null $startTime
 * @param string|null $endTime
 * @param string $currentStatus
 * @return string 'NOT_STARTED' | 'ACTIVE' | 'CLOSED' | 'draft' | 'cancelled'
 */
function getActivityAutoStatus($startTime, $endTime, $currentStatus = 'draft') {
    if ($currentStatus === 'draft' || $currentStatus === 'cancelled') {
        return $currentStatus; 
    }
    
    if (!$startTime || !$endTime) {
        return $currentStatus;
    }
    
    $now = time();
    $start = strtotime($startTime);
    $end = strtotime($endTime);
    
    if ($now < $start) {
        return 'NOT_STARTED';
    } elseif ($now > $end) {
        return 'CLOSED';
    } else {
        return 'ACTIVE';
    }
}
