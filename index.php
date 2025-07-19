<?php
require_once __DIR__ . '/vendor/autoload.php'; // Load mPDF
include 'db.php';

$candidate = null;
$error = null;

// 🧾 Handle PDF export request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['download_pdf'])) {
$name = $_POST['name'];
$status = $_POST['status'];
$seat_no = $_POST['seat_no'] ?? '';
$reporting_date = $_POST['reporting_date'] ?? '';
$venue = $_POST['venue'] ?? '';

$mpdf = new \Mpdf\Mpdf([
    'margin_top' => 10,
    'margin_bottom' => 10,
    'margin_left' => 10,
    'margin_right' => 10,
    'default_font' => 'dejavusans'
]);

// 🟦 Stylish Header with Logo & Contact Info
$header = '
<div style="border: 2px solid #3b82f6; padding: 20px; border-radius: 10px; font-family: sans-serif;">
    <div style="text-align: center;">
        <img src="JBIMS_logo.webp" style="height: 80px; margin-bottom: 10px;"><br>
        <span style="font-size: 20px; font-weight: bold; color: #111827;">Jamnalal Bajaj Institute of Management Studies</span><br>
        <span style="font-size: 13px; color: #374151;">🌐 www.jbims.edu | ✉️ director@jbims.edu | ☎️ 9326234746</span>
    </div>
    <hr style="margin-top: 15px; margin-bottom: 15px;">
    <div style="text-align:center; font-size:16px; font-weight:bold; color:#2563eb;">📄 MHRD Admission Status Report</div>
</div>
<br>
';

$mpdf->WriteHTML($header);

// 🟩 Table with full styling
$html = '
<div style="border: 2px solid #d1d5db; padding: 25px; border-radius: 12px; font-size: 14px; font-family: sans-serif;">

    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
        <tr style="background-color: #f3f4f6;">
            <td style="padding: 10px; border: 1px solid #d1d5db; width: 35%; font-weight: bold;">Name</td>
            <td style="padding: 10px; border: 1px solid #d1d5db;">' . htmlspecialchars($name) . '</td>
        </tr>
        <tr style="background-color: #fef3c7;">
            <td style="padding: 10px; border: 1px solid #d1d5db; font-weight: bold;">Status</td>
            <td style="padding: 10px; border: 1px solid #d1d5db;">' . htmlspecialchars($status) . '</td>
        </tr>';

if ($status === 'Shortlisted') {
    $html .= '
        <tr>
            <td style="padding: 10px; border: 1px solid #d1d5db; font-weight: bold;">Seat No</td>
            <td style="padding: 10px; border: 1px solid #d1d5db;">' . htmlspecialchars($seat_no) . '</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #d1d5db; font-weight: bold;">Reporting Date</td>
            <td style="padding: 10px; border: 1px solid #d1d5db;">' . htmlspecialchars($reporting_date) . '</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #d1d5db; font-weight: bold;">Venue</td>
            <td style="padding: 10px; border: 1px solid #d1d5db;">' . htmlspecialchars($venue) . '</td>
        </tr>';
}

$html .= '
    </table>

    <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #6b7280;">
        This is a system-generated report. Please contact the institute for any clarifications.
    </div>
</div>
';

$mpdf->WriteHTML($html);

// 📆 Footer with timestamp
$mpdf->SetHTMLFooter('
    <div style="text-align: center; font-size: 10px; color: #888; padding-top: 5px;">
        © ' . date('Y') . ' JBIMS | Generated on ' . date('d M Y, h:i A') . '
    </div>
');

$mpdf->Output('Admission_Status.pdf', 'D');
exit;


}

// 📱 Regular phone check form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['phone'])) {
    $phone = $conn->real_escape_string($_POST['phone']);
    $sql = "SELECT * FROM candidates WHERE phone = '$phone'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $candidate = $result->fetch_assoc();
    } else {
        $error = "No candidate found with this phone number.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MHRD Admission Checker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #4f46e5;
            --success: #16a34a;
            --danger: #dc2626;
            --gray: #6b7280;
            --light: #f9fafb;
            --dark: #111827;
            --radius: 12px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to right, #dbeafe, #eff6ff);
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #fff;
            max-width: 540px;
            width: 100%;
            padding: 35px;
            border-radius: var(--radius);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            height: 200px;
            object-fit: contain;
        }

        h1 {
            text-align: center;
            color: var(--primary);
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            color: var(--gray);
            margin-bottom: 25px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
        }

        input[type="tel"] {
            width: 100%;
            padding: 12px 14px;
            font-size: 16px;
            border: 2px solid #e5e7eb;
            border-radius: var(--radius);
            outline: none;
            transition: 0.3s;
        }

        input[type="tel"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border: none;
            border-radius: var(--radius);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            opacity: 0.95;
        }

        .error {
            margin-top: 20px;
            background: #fee2e2;
            border-left: 5px solid var(--danger);
            padding: 12px;
            color: var(--danger);
            border-radius: var(--radius);
        }

        .result {
            margin-top: 30px;
            border: 1px solid #e5e7eb;
            border-radius: var(--radius);
            background: #fff;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.04);
        }

        .result .header {
            background: var(--primary);
            color: white;
            padding: 15px 20px;
            border-top-left-radius: var(--radius);
            border-top-right-radius: var(--radius);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 20px;
            text-align: left;
        }

        th {
            color: var(--gray);
            width: 40%;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            display: inline-block;
        }

        .shortlisted {
            background: #dcfce7;
            color: var(--success);
        }

        .not-shortlisted {
            background: #fee2e2;
            color: var(--danger);
        }

        .pdf-button {
            margin-top: 20px;
            text-align: center;
        }

        .pdf-button button {
            background: #ef4444;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
        }

        .check-again {
            margin-top: 20px;
            text-align: center;
        }

        .check-again a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .check-again a:hover {
            text-decoration: underline;
        }
        .header-banner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    text-align: left;
}

.header-banner img {
    height: 150px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.header-title h1 {
    margin: 0;
    font-size: 1.5rem;
    color: var(--primary);
}

.header-title p {
    margin: 5px 0 0;
    font-size: 0.9rem;
    color: var(--gray);
}

    </style>
</head>
<body>
    <div class="card">
        <div class="header-banner">
    <img src="JBIMS_logo.webp" alt="Institute Logo">
    <div class="header-title">
        <h1>MHRD Admission Status</h1>
        <p>Jamnalal Bajaj Institute of Management Studies <br> (Autonomous)</p>
    </div>
</div>
<p>Enter your registered phone number</p>
        <form method="POST">
            <div class="input-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" placeholder="10-digit number" required>
            </div>
            <button type="submit"><i class="fas fa-search"></i> Check Status</button>
        </form>

        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($candidate): ?>
            <div class="result">
                <div class="header">
                    <i class="fas fa-user-check"></i> Application Details
                </div>
                <table>
                    <tr>
                        <th>Name</th>
                        <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="status-badge <?php echo ($candidate['status'] === 'Shortlisted') ? 'shortlisted' : 'not-shortlisted'; ?>">
                                <?php echo $candidate['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php if ($candidate['status'] === 'Shortlisted'): ?>
                        <tr>
                            <th>Seat No</th>
                            <td><?php echo htmlspecialchars($candidate['seat_no']); ?></td>
                        </tr>
                        <tr>
                            <th>Reporting Date</th>
                            <td><?php echo htmlspecialchars($candidate['reporting_date']); ?></td>
                        </tr>
                        <tr>
                            <th>Venue</th>
                            <td><?php echo htmlspecialchars($candidate['venue']); ?></td>
                        </tr>
                    <?php endif; ?>
                </table>

                <form method="POST" class="pdf-button">
                    <input type="hidden" name="download_pdf" value="1">
                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($candidate['name']); ?>">
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($candidate['status']); ?>">
                    <?php if ($candidate['status'] === 'Shortlisted'): ?>
                        <input type="hidden" name="seat_no" value="<?php echo htmlspecialchars($candidate['seat_no']); ?>">
                        <input type="hidden" name="reporting_date" value="<?php echo htmlspecialchars($candidate['reporting_date']); ?>">
                        <input type="hidden" name="venue" value="<?php echo htmlspecialchars($candidate['venue']); ?>">
                    <?php endif; ?>
                    <button type="submit"><i class="fas fa-file-pdf"></i> Download PDF</button>
                </form>
            </div>

            <div class="check-again">
                <a href=""><i class="fas fa-arrow-left"></i> Check another number</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
