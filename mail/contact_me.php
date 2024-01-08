<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Fungsi untuk membersihkan dan menyaring input
function clean_input($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Mengecek CSRF token
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    // Membersihkan dan menyaring input
    $name = clean_input($_POST["name"]);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $message = clean_input($_POST["message"]);

    // Validasi input
    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Harap isi semua kolom formulir dengan benar.";
        exit;
    }

    $subject = "Pesan dari $name";
    $to = "warungbucening174@gmail.com";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'warungbucening174@gmail.com';
        $mail->Password   = 'tumvhqardfvsxchs';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom($email, $name);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = "Nama: $name<br>Email: $email<br><br>Pesan:<br>$message";

        $mail->send();

        http_response_code(200);
        echo "Pesan berhasil dikirim!";
    } catch (Exception $e) {
        http_response_code(500);
        // Catat kesalahan daripada menampilkannya ke pengguna
        error_log("Terjadi kesalahan saat mengirim pesan. Error: {$mail->ErrorInfo}");
        echo "Terjadi kesalahan saat mengirim pesan. Silahkan coba lagi.";
    }
} else {
    http_response_code(403);
    echo "Akses ditolak.";
}
?>
