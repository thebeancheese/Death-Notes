<?php
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$document_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($document_id <= 0) {
    http_response_code(400);
    exit("Invalid document request.");
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        rd.*,
        dr.applicant_user_id
     FROM record_documents rd
     JOIN death_records dr ON rd.record_id = dr.record_id
     WHERE rd.document_id = ? AND dr.deleted_at IS NULL
     LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $document_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$document = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);

if (!$document) {
    http_response_code(404);
    exit("Document not found.");
}

$role_id = (int)$_SESSION["role_id"];
$user_id = (int)$_SESSION["user_id"];
if (isUserRole($role_id) && (int)$document["applicant_user_id"] !== $user_id) {
    http_response_code(403);
    exit("You do not have access to this document.");
}

$absolute_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . str_replace("/", DIRECTORY_SEPARATOR, $document["file_path"]);
if (!is_file($absolute_path)) {
    http_response_code(404);
    exit("Stored file is missing.");
}

$mime_type = trim((string)$document["mime_type"]);
if ($mime_type === "") {
    $mime_type = "application/octet-stream";
}

header("Content-Type: " . $mime_type);
header("Content-Length: " . filesize($absolute_path));
header("Content-Disposition: inline; filename=\"" . basename((string)$document["original_file_name"]) . "\"");
readfile($absolute_path);
exit();
?>
