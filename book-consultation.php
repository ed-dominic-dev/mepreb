<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "mepreb";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("DB connection failed: " . $conn->connect_error);

// ------------------------
// reCAPTCHA verification
// ------------------------
$recaptcha_secret = '6LdbcRYsAAAAAKbaagEFDmeZh1RYmhvoY50DaOKa';
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

$verify = file_get_contents(
    "https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}"
);
$captcha_success = json_decode($verify);

if (!$captcha_success->success) {
    die("reCAPTCHA verification failed. Please check the box and try again.");
}

// ------------------------
// Get POST data
// ------------------------
$fullName = $_POST['fullName'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$company = $_POST['company'] ?? '';
$transaction = $_POST['transaction'] ?? '';
$meetingType = $_POST['meetingType'] ?? '';
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$comments = $_POST['comments'] ?? '';
$propertyTypeArr = $_POST['propertyType'] ?? [];
$propertyTypeStr = implode(", ", $propertyTypeArr);

// Insert into database
$sql = "INSERT INTO consultation_bookings 
(full_name,email,phone,company,transaction_type,property_type,preferred_date,preferred_time,meeting_type,additional_comments)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssssss",
    $fullName,$email,$phone,$company,$transaction,$propertyTypeStr,$date,$time,$meetingType,$comments
);

if ($stmt->execute()) {
    header("Location: book-a-consultation.html");
    exit;
} else {
    die("Insert failed: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
   