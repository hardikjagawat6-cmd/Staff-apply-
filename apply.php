<?php
// Initialize message variables
$message = "";
$message_type = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect and sanitize text inputs safely
    $full_name  = htmlspecialchars(trim($_POST['full_name']));
    $email      = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone      = htmlspecialchars(trim($_POST['phone']));
    $position   = htmlspecialchars(trim($_POST['position']));
    $experience = htmlspecialchars(trim($_POST['experience']));

    // 2. Simple validation check
    if (empty($full_name) || empty($email) || empty($position)) {
        $message = "Please fill in all required fields.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $message_type = "error";
    } else {
        
        /* 
        ========================================================================
        DATABASE INTEGRATION (OPTIONAL)
        To save data permanently, uncomment this block and configure your MySQL database.
        ========================================================================
        $conn = new mysqli("localhost", "DB_USERNAME", "DB_PASSWORD", "DB_NAME");
        if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
        
        $stmt = $conn->prepare("INSERT INTO applicants (name, email, phone, position, experience) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $full_name, $email, $phone, $position, $experience);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        */

        // For this demo, we mock a successful submission
        $message = "Success! Thank you, $full_name. Your application for the $position role has been submitted.";
        $message_type = "success";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Application Form</title>
    <style>
        /* Modern reset and font alignment */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Beautiful interactive animated gradient background */
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            padding: 20px;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Glassmorphism Container UI */
        .form-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
            color: #ffffff;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        p.subtitle {
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
            color: #f0f0f0;
        }

        /* Input fields styling */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        input[type="text"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            font-size: 15px;
            color: #fff;
            outline: none;
            transition: all 0.3s ease;
        }

        /* Style options inside select element for legibility */
        select option {
            background: #23a6d5;
            color: #fff;
        }

        input:focus, select:focus, textarea:focus {
            background: rgba(255, 255, 255, 0.3);
            border-color: #fff;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        /* Button hover animations */
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: #ffffff;
            border: none;
            border-radius: 8px;
            color: #333;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .submit-btn:hover {
            background: #23a6d5;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        /* Success & Error Notifications */
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        .alert.success {
            background: rgba(46, 204, 113, 0.3);
            border: 1px solid #2ecc71;
            color: #fff;
        }
        .alert.error {
            background: rgba(231, 76, 60, 0.3);
            border: 1px solid #e74c3c;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Join Our Team</h2>
    <p class="subtitle">Submit your details to apply for an open position</p>

    <!-- PHP Alert Messages -->
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="form-group">
            <label for="full_name">Full Name *</label>
            <input type="text" id="full_name" name="full_name" required placeholder="John Doe">
        </div>

        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required placeholder="johndoe@example.com">
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" placeholder="+1 (555) 000-0000">
        </div>

        <div class="form-group">
            <label for="position">Position Applying For *</label>
            <select id="position" name="position" required>
                <option value="" disabled selected>Select a role...</option>
                <option value="Manager">Manager</option>
                <option value="Developer">Developer</option>
                <option value="Designer">Designer</option>
                <option value="Support Staff">Support Staff</option>
            </select>
        </div>

        <div class="form-group">
            <label for="experience">Brief Work Experience</label>
            <textarea id="experience" name="experience" placeholder="Tell us briefly about your relevant background..."></textarea>
        </div>

        <button type="submit" class="submit-btn">Submit Application</button>
    </form>
</div>

</body>
</html>
