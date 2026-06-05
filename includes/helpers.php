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
    return "<div class='alert alert-$type alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
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