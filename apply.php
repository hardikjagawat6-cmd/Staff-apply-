<?php
// 1. PASTE YOUR DISCORD WEBHOOK URL HERE
$discord_webhook_url = "https://discord.com/api/webhooks/1549740109492912228/fOsV6OKrAjF90C1Y_ynODPM9S59dN50tuS9uD7DDD1Bmh2gLcpW2yLz7iWp94jK9hWs5";

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form entries
    $full_name = strip_tags(trim($_POST['full_name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = strip_tags(trim($_POST['phone']));
    $position = strip_tags(trim($_POST['position']));
    $experience = (int)$_POST['experience'];
    $cover_letter = strip_tags(trim($_POST['cover_letter']));

    // 2. Format a beautiful Discord Rich Embed card
    $webhook_data = [
        "username" => "Recruitment Bot",
        "avatar_url" => "https://imgur.com", // Avatar icon
        "embeds" => [[
            "title" => "📝 New Staff Application Received!",
            "color" => 11025911, // Elegant Purple border color
            "fields" => [
                ["name" => "👤 Full Name", "value" => $full_name, "inline" => true],
                ["name" => "💼 Target Position", "value" => $position, "inline" => true],
                ["name" => "📧 Email Address", "value" => $email, "inline" => false],
                ["name" => "📞 Phone Number", "value" => $phone, "inline" => true],
                ["name" => "⏳ Experience", "value" => $experience . " Years", "inline" => true],
                ["name" => "📄 Cover Letter / Notes", "value" => !empty($cover_letter) ? $cover_letter : "No notes provided.", "inline" => false]
            ],
            "footer" => [
                "text" => "Sent via Staff Portal • " . date("Y-m-d H:i:s")
            ]
        ]]
    ];

    // 3. Send the data to Discord via PHP cURL
    $ch = curl_init($discord_webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhook_data));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 4. Verify transmission status
    if ($http_code == 204 || $http_code == 200) {
        $message = "🎉 Application submitted successfully! Our HR team will review it.";
        $messageClass = "success-msg";
    } else {
        $message = "❌ Error: Could not process submission. Please check system configs.";
        $messageClass = "error-msg";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Our Team | Staff Application Portal</title>
    <style>
        /* Modern Abstract Mesh Gradient Background */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #121826 0%, #1e1b4b 40%, #311042 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #f3f4f6;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Glassmorphism Form Container */
        .form-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
            margin: 20px;
        }

        h2 { 
            text-align: center; 
            margin-top: 0; 
            font-size: 28px; 
            font-weight: 600;
            color: #ffffff; 
            letter-spacing: 0.5px;
        }
        
        p.subtitle { 
            text-align: center; 
            color: #9ca3af; 
            margin-bottom: 30px; 
            font-size: 14px; 
        }
        
        .form-group { 
            margin-bottom: 20px; 
        }
        
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-size: 14px; 
            font-weight: 500;
            color: #e5e7eb; 
        }
        
        .form-group input, .form-group textarea {
            width: 100%; 
            padding: 12px 16px; 
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15); 
            border-radius: 10px;
            color: #ffffff; 
            font-size: 15px; 
            outline: none; 
            transition: all 0.3s ease; 
            box-sizing: border-box;
        }

        .form-group input:focus, .form-group textarea:focus {
            background: rgba(255, 255, 255, 0.12); 
            border-color: #a855f7; 
            box-shadow: 0 0 10px rgba(168, 85, 247, 0.4);
        }

        /* styling for datalist options dropdown dropdown natively supported by OS */
        input::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.5;
            cursor: pointer;
        }

        textarea { 
            resize: vertical; 
            height: 110px; 
        }

        .submit-btn {
            width: 100%; 
            padding: 14px; 
            background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
            border: none; 
            border-radius: 10px; 
            color: #ffffff; 
            font-size: 16px; 
            font-weight: 600;
            cursor: pointer; 
            transition: transform 0.2s ease, box-shadow 0.2s ease; 
            margin-top: 10px;
        }

        .submit-btn:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 20px rgba(168, 85, 247, 0.4); 
        }
        
        .alert { 
            padding: 12px 16px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
            font-size: 14px; 
            text-align: center; 
        }
        
        .success-msg { 
            background: rgba(16, 185, 129, 0.2); 
            border: 1px solid #10b981; 
            color: #34d399; 
        }
        
        .error-msg { 
            background: rgba(239, 68, 68, 0.2); 
            border: 1px solid #ef4444; 
            color: #f87171; 
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Apply for Staff Position</h2>
    <p class="subtitle">Join our dynamic team and help shape the future.</p>

    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $messageClass; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required placeholder="John Doe">
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="johndoe@example.com">
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" required placeholder="+1 (555) 000-0000">
        </div>

        <div class="form-group">
            <label for="position">Target Position</label>
            <!-- Datalist integration allows typing custom string values OR selection -->
            <input type="text" id="position" name="position" list="positions-list" required placeholder="Select a role or type your own...">
            <datalist id="positions-list">
                <option value="Frontend Developer">
                <option value="Backend Developer">
                <option value="UI/UX Designer">
                <option value="Project Manager">
                <option value="HR Specialist">
                <option value="Data Analyst">
                <option value="Customer Support Specialist">
            </datalist>
        </div>

        <div class="form-group">
            <label for="experience">Years of Experience</label>
            <input type="number" id="experience" name="experience" min="0" max="50" required placeholder="e.g. 3">
        </div>

        <div class="form-group">
            <label for="cover_letter">Brief Cover Letter / Notes</label>
            <textarea id="cover_letter" name="cover_letter" placeholder="Tell us why you are a great fit..."></textarea>
        </div>

        <button type="submit" class="submit-btn">Submit Application</button>
    </form>
</div>

</body>
</html>
