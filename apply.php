<?php
// 1. PASTE YOUR DISCORD WEBHOOK URL HERE
$discord_webhook_url = "https://discord.com/api/webhooks/1549740109492912228/fOsV6OKrAjF90C1Y_ynODPM9S59dN50tuS9uD7DDD1Bmh2gLcpW2yLz7iWp94jK9hWs5";

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form entries safely with empty string fallbacks
    $full_name = strip_tags(trim($_POST['full_name'] ?? ''));
    $discord_tag = strip_tags(trim($_POST['discord_tag'] ?? ''));
    $age = isset($_POST['age']) ? (int)$_POST['age'] : 0;
    $position = strip_tags(trim($_POST['position'] ?? ''));
    $timezone = strip_tags(trim($_POST['timezone'] ?? ''));
    $cover_letter = strip_tags(trim($_POST['cover_letter'] ?? ''));

                // 2. Single clear block of text for clean mobile copy-pasting
    $webhook_data = [
        "username" => "Staff Recruiter",
        "content" => "==================================
🎮 **NEW STAFF APPLICATION RECEIVED**
==================================

👤 **In-Game Name:** `$full_name`
📱 **Discord Username:** `$discord_tag`
🎂 **Applicant Age:** `$age`
🛡️ **Applied For Rank:** **$position**
🌐 **Timezone / Region:** `$timezone`

📄 **Why should we choose them?**
> $cover_letter

==================================
⚙️ **MANAGEMENT STATUS INSTRUCTIONS:**
• React with ✅ to **ACCEPT** and start onboarding
• React with 🟡 to put on **HOLD / INTERVIEW**
• React with ❌ to **DENY / REJECT**
=================================="
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
        $message = "🎉 Application sent! Keep an eye on your Discord DMs.";
        $messageClass = "success-msg";
    } else {
        $message = "❌ Error: Could not process submission. Check webhook config.";
        $messageClass = "error-msg";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Application Portal</title>
    <style>
        /* Minecraft Deep Slate Dark Theme */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #141419 0%, #1c1d24 50%, #111215 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #e2e8f0;
        }

        /* Clean Box Design with Cyan Diamond Border Accent */
        .form-container {
            background: #1f232c;
            border: 2px solid #555e70;
            border-top: 4px solid #55cdfc; /* Diamond Cyan top border */
            border-radius: 12px;
            padding: 35px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            box-sizing: border-box;
            margin: 20px;
        }

        h2 { 
            text-align: center; 
            margin-top: 0; 
            font-size: 26px; 
            font-weight: 700;
            color: #ffffff; 
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        p.subtitle { 
            text-align: center; 
            color: #94a3b8; 
            margin-bottom: 25px; 
            font-size: 14px; 
        }
        
        .form-group { 
            margin-bottom: 18px; 
        }
        
        .form-group label { 
            display: block; 
            margin-bottom: 6px; 
            font-size: 13px; 
            font-weight: 600;
            color: #cbd5e1; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
                .form-group input, .form-group textarea {
            width: 100%; 
            padding: 12px 14px; 
            background: #15181f;
            border: 1px solid #3f4756; 
            border-radius: 8px;
            color: #ffffff; 
            font-size: 15px; 
            outline: none; 
            transition: all 0.2s ease; 
            box-sizing: border-box;
            
            /* FORCES MOBILE PHONES TO SHOW TEXT OPTIONS CORRECTLY */
            color-scheme: dark;
        }


        .form-group input:focus, .form-group textarea:focus {
            border-color: #55cdfc; 
            box-shadow: 0 0 8px rgba(85, 205, 252, 0.3);
        }

        input::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.5;
            cursor: pointer;
        }

        textarea { 
            resize: vertical; 
            height: 120px; 
        }

        /* Minecraft Green Action Button */
        .submit-btn {
            width: 100%; 
            padding: 14px; 
            background: #22c55e;
            border: none; 
            border-radius: 8px; 
            color: #ffffff; 
            font-size: 16px; 
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer; 
            transition: all 0.2s ease;
            margin-top: 10px;
            box-shadow: 0 4px 0 #15803d;
        }

        .submit-btn:hover { 
            background: #16a34a;
            transform: translateY(-1px);
            box-shadow: 0 5px 0 #166534;
        }
        
        .submit-btn:active {
            transform: translateY(3px);
            box-shadow: 0 1px 0 #166534;
        }
        
        .alert { 
            padding: 12px 16px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            font-size: 14px; 
            text-align: center; 
            font-weight: 600;
        }
        
        .success-msg { 
            background: rgba(34, 197, 94, 0.15); 
            border: 1px solid #22c55e; 
            color: #4ade80; 
        }
        
        .error-msg { 
            background: rgba(239, 68, 68, 0.15); 
            border: 1px solid #ef4444; 
            color: #f87171; 
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Apply for Staff</h2>
    <p class="subtitle">Fill out the details to join our staff team.</p>

    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $messageClass; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
                <div class="form-group">
            <label for="discord_tag">Discord User ID (Hold profile to copy ID)</label>
            <input type="text" id="discord_tag" name="discord_tag" required placeholder="e.g. 48392019482019284">
        </div>


        <div class="form-group">
            <label for="discord_tag">Discord Username</label>
            <input type="text" id="discord_tag" name="discord_tag" required placeholder="username (or name#0000)">
        </div>

        <div class="form-group">
            <label for="age">Your Age</label>
            <input type="number" id="age" name="age" min="10" max="100" required placeholder="e.g. 16">
        </div>

                <div class="form-group">
            <label for="position">What do you want to apply for?</label>
            <select id="position" name="position" required style="color-scheme: dark;">
                <option value="" disabled selected>Select your rank / role...</option>
                <option value="Helper">Helper</option>
                <option value="Moderator (Mod)">Moderator (Mod)</option>
                <option value="Admin">Admin</option>
                <option value="Builder">Builder</option>
                <option value="Developer (Dev)">Developer (Dev)</option>
                <option value="Discord Staff">Discord Staff</option>
            </select>
        </div>


        <div class="form-group">
            <label for="timezone">Your Timezone / Country</label>
            <input type="text" id="timezone" name="timezone" required placeholder="e.g. GMT+5:30, EST, or India">
        </div>

        <div class="form-group">
            <label for="cover_letter">Why should we choose you over others?</label>
            <textarea id="cover_letter" name="cover_letter" required placeholder="Tell us about your previous experience, skills, and why you want to help our community..."></textarea>
        </div>

        <button type="submit" class="submit-btn">Submit Application</button>
    </form>
</div>

</body>
</html>
