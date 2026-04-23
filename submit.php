<?php
// ─────────────────────────────────────────────
//  IRA Insignia – Contact Form Handler
//  Place this file in the SAME folder as contact.html
// ─────────────────────────────────────────────

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.html');
    exit;
}

// ── Config ──────────────────────────────────
$to      = 'nithulcr@gmail.com';
$from    = 'no-reply@irainsignia.com';   // must be a valid domain on your server
$siteName = 'IRA Insignia';
// ────────────────────────────────────────────

// Sanitise inputs
function clean($val) {
    return htmlspecialchars(strip_tags(trim($val)));
}

$firstName   = clean($_POST['firstName']   ?? '');
$lastName    = clean($_POST['lastName']    ?? '');
$email       = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$countryCode = clean($_POST['countryCode'] ?? '+91');
$phone       = clean($_POST['phone']       ?? '');
$interest    = clean($_POST['interest']    ?? 'Not specified');
$budget      = clean($_POST['budget']      ?? 'Not specified');
$message     = clean($_POST['message']     ?? '');

// Basic server-side validation
$errors = [];
if (empty($firstName))                        $errors[] = 'First name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
if (empty($phone))                            $errors[] = 'Phone number is required.';

if (!empty($errors)) {
    // Redirect back with an error flag (you can expand this)
    header('Location: contact.html?error=1');
    exit;
}

// ── Build email ──────────────────────────────
$subject = "$siteName Enquiry – $firstName $lastName";

$body  = "You have received a new enquiry from the $siteName website.\n";
$body .= str_repeat("─", 50) . "\n\n";
$body .= "Name     : $firstName $lastName\n";
$body .= "Email    : $email\n";
$body .= "Phone    : $countryCode $phone\n";
$body .= "Interest : $interest\n";
$body .= "Budget   : $budget\n\n";
$body .= "Message:\n$message\n\n";
$body .= str_repeat("─", 50) . "\n";
$body .= "Submitted: " . date('d M Y, H:i:s') . " IST\n";

$headers  = "From: $siteName <$from>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// ── Send ─────────────────────────────────────
$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    header('Location: contact.html?sent=1');
} else {
    header('Location: contact.html?error=1');
}
exit;
?>
