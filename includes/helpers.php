<?php
// includes/helpers.php - reusable functions


function sanitize($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}


function getUserRole()
{
    return $_SESSION['role'] ?? null;
}

function showAlert($message, $type = 'danger')
{
    $message = htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8');
    $type = in_array($type, ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'], true)
        ? $type
        : 'danger';

    return "<div class='alert alert-$type alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}

function normalizePhone($phone)
{
    $phone = trim((string) $phone);

    if ($phone === '') {
        return '';
    }

    return preg_replace('/[\s().-]+/', '', $phone);
}

function isValidPhone($phone)
{
    $phone = normalizePhone($phone);

    return $phone === '' || preg_match('/^\+?[0-9]{7,15}$/', $phone);
}

function validateUserForm($data, $requirePassword = true, $validateRole = false)
{
    $errors = [];
    $fullname = trim($data['fullname'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $password = $data['password'] ?? '';
    $confirm = $data['confirm_password'] ?? '';
    $role = $data['role'] ?? '';

    if ($fullname === '') {
        $errors[] = 'Full name is required.';
    } elseif (strlen($fullname) < 2 || strlen($fullname) > 100) {
        $errors[] = 'Full name must be between 2 and 100 characters.';
    }

    if ($email === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if (!isValidPhone($phone)) {
        $errors[] = 'Phone number must contain 7 to 15 digits and may start with +.';
    }

    if ($requirePassword || $password !== '') {
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }

        if ($confirm !== '' && $password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }
    }

    if ($validateRole && !in_array($role, ['student', 'staff', 'admin'], true)) {
        $errors[] = 'Select a valid user role.';
    }

    return $errors;
}

function isValidDateValue($date)
{
    $date = trim((string) $date);
    $parsed = DateTime::createFromFormat('Y-m-d', $date);

    return $parsed && $parsed->format('Y-m-d') === $date;
}

function isFutureDate($date)
{
    if (!isValidDateValue($date)) {
        return false;
    }

    return new DateTime($date) > new DateTime('today');
}

function validateItemReportForm($data, $locationField, $dateField)
{
    $errors = [];
    $itemName = trim($data['item_name'] ?? '');
    $location = trim($data[$locationField] ?? '');
    $date = trim($data[$dateField] ?? '');
    $description = trim($data['description'] ?? '');
    $category = trim($data['category'] ?? '');
    $allowedCategories = ['', 'Phone', 'Laptop', 'Wallet', 'ID Card', 'Keys', 'Bag', 'Other'];

    if ($itemName === '' || strlen($itemName) < 2 || strlen($itemName) > 100) {
        $errors[] = 'Item name must be between 2 and 100 characters.';
    }

    if ($location === '' || strlen($location) < 2 || strlen($location) > 255) {
        $errors[] = 'Location must be between 2 and 255 characters.';
    }

    if (!isValidDateValue($date)) {
        $errors[] = 'Enter a valid date.';
    } elseif (isFutureDate($date)) {
        $errors[] = 'Date cannot be in the future.';
    }

    if (strlen($description) > 1000) {
        $errors[] = 'Description cannot exceed 1000 characters.';
    }

    if (!in_array($category, $allowedCategories, true)) {
        $errors[] = 'Select a valid category.';
    }

    return $errors;
}

function validateIncidentForm($data)
{
    $errors = [];
    $title = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? '');
    $type = trim($data['incident_type'] ?? '');
    $location = trim($data['location'] ?? '');
    $date = trim($data['incident_date'] ?? '');

    if ($title === '' || strlen($title) < 2 || strlen($title) > 100) {
        $errors[] = 'Title must be between 2 and 100 characters.';
    }

    if (!in_array($type, ['theft', 'safety', 'misconduct', 'other'], true)) {
        $errors[] = 'Select a valid incident type.';
    }

    if ($location === '' || strlen($location) < 2 || strlen($location) > 255) {
        $errors[] = 'Location must be between 2 and 255 characters.';
    }

    if ($description === '' || strlen($description) < 10 || strlen($description) > 1000) {
        $errors[] = 'Description must be between 10 and 1000 characters.';
    }

    if (!isValidDateValue($date)) {
        $errors[] = 'Enter a valid incident date.';
    } elseif (isFutureDate($date)) {
        $errors[] = 'Incident date cannot be in the future.';
    }

    return $errors;
}

function validateGpsCoordinates($latitude, $longitude)
{
    if ($latitude === null && $longitude === null) {
        return [];
    }

    if (!is_numeric($latitude) || !is_numeric($longitude)) {
        return ['GPS coordinates are invalid.'];
    }

    $latitude = (float) $latitude;
    $longitude = (float) $longitude;

    if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
        return ['GPS coordinates are out of range.'];
    }

    return [];
}

function validateImageUpload($file)
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['Image upload failed. Please try again.'];
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return ['Image must not exceed 5MB.'];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) || !@getimagesize($file['tmp_name'])) {
        return ['Upload a valid image file.'];
    }

    return [];
}

function paginate($currentPage, $totalRecords, $recordsPerPage, $baseUrl)
{
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));

    $html = '<nav><ul class="pagination justify-content-center">';
    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&p=' . ($currentPage - 1) . '">Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }

    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $currentPage) ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . '&p=' . $i . '">' . $i . '</a></li>';
    }

    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&p=' . ($currentPage + 1) . '">Next</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }
    $html .= '</ul></nav>';
    return $html;
}
?>
