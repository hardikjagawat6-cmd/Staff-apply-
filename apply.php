<?php
// 1. CONFIGURATION SETUP
$discord_webhook_url = "YOUR_DISCORD_WEBHOOK_URL_HERE";
$bot_token           = "YOUR_BOT_TOKEN_HERE"; // Paste your new bot token here

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form entries safely with empty string fallbacks
    $full_name    = strip_tags(trim($_POST['full_name'] ?? ''));
    $discord_tag   = strip_tags(trim($_POST['discord_tag'] ?? '')); // This is the numerical User ID now
    $age          = isset($_POST['age']) ? (int)$_POST['age'] : 0;
    $position     = strip_tags(trim($_POST['position'] ?? ''));
    $timezone     = strip_tags(trim($_POST['timezone'] ?? ''));
    $cover_letter = strip_tags(trim($_POST['cover_letter'] ?? ''));

    // 2. SEND ALERT TO YOUR PRIVATE STAFF REVIEW CHANNEL
    $webhook_data = [
        "username" => "Staff Recruiter",
        "content" => "==================================\n" .
                     "🎮 **NEW STAFF APPLICATION RECEIVED**\n" .
                     "==================================\n\n" .
                     "👤 **In-Game Name:** `$full_name` \n" .
                     "📱 **Discord User:** <@$discord_tag> (ID: `$discord_tag`) \n" .
                     "🎂 **Applicant Age:** `$age` \n" .
                     "🛡️ **Applied For Rank:** **$position** \n" .
                     "🌐 **Timezone / Region:** `$timezone` \n\n" .
                     "📄 **Why should we choose them?** \n" .
                     "> $cover_letter \n\n" .
                     "==================================\n" .
                     "⚙️ **MANAGEMENT STATUS INSTRUCTIONS:**\n" .
                     "• React with ✅ to ACCEPT and start onboarding\n" .
                     "• React with ❌ to DENY / REJECT\n" .
                     "=================================="
    ];

    // Send the alert card via standard Webhook connection
    $ch = curl_init($discord_webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhook_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($ch);
    curl_close($ch);

    // 3. AUTOMATICALLY SEND THE DM NOTIFICATION TO THE USER VIA THE BOT
    // Step A: Open a Direct Message DM Channel gateway with the user's ID
    $dm_url = "https://discord.com";
    $dm_payload = json_encode(["recipient_id" => $discord_tag]);

    $ch_dm = curl_init($dm_url);
    curl_setopt($ch_dm, CURLOPT_HTTPHEADER, [
        "Authorization: Bot $bot_token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch_dm, CURLOPT_POST, 1);
    curl_setopt($ch_dm, CURLOPT_POSTFIELDS, $dm_payload);
    curl_setopt($ch_dm, CURLOPT_RETURNTRANSFER, 1);
    $dm_response = json_decode(curl_exec($ch_dm), true);
    curl_close($ch_dm);

    // Step B: Send the actual DM text message if the channel gateway opened successfully
    if (isset($dm_response['id'])) {
        $channel_id = $dm_response['id'];
        $msg_url = "https://discord.com";
        
        // Define your custom DM message content text here
        $dm_message_text = "👋 **Hey there, $full_name!**\n\nYour application for **$position** has been successfully submitted to our recruitment team! \n\nWe have received your details and our management will review them shortly. Please make sure your DMs stay open so we can update you on your status. Thanks for applying! 🎮";
        
        $msg_payload = json_encode(["content" => $dm_message_text]);

        $ch_msg = curl_init($msg_url);
        curl_setopt($ch_msg, CURLOPT_HTTPHEADER, [
            "Authorization: Bot $bot_token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch_msg, CURLOPT_POST, 1);
        curl_setopt($ch_msg, CURLOPT_POSTFIELDS, $msg_payload);
        curl_setopt($ch_msg, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch_msg);
        $http_code = curl_getinfo($ch_msg, CURLINFO_HTTP_CODE);
        curl_close($ch_msg);

        if ($http_code == 200 || $http_code == 204) {
            $message = "🎉 Application sent! A confirmation DM has been delivered to your Discord.";
            $messageClass = "success-msg";
        } else {
            $message = "🎉 Application saved, but the bot couldn't slide into your DMs (Are your privacy settings blocking bot messages?).";
            $messageClass = "success-msg";
        }
    } else {
        $message = "🎉 Application recorded, but your DM channel could not be verified. Double-check your numerical User ID entry.";
        $messageClass = "success-msg";
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
            <label for="discord_tag">Discord User ID</label>
            <input type="text" id="discord_tag" name="discord_tag" required placeholder="e.g. 39481029482019482">
            <small style="color: #94a3b8; font-size: 11px; display: block; margin-top: 4px;">
                *Enable Developer Mode in Discord settings, tap your profile, and click "Copy User ID".
            </small>
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
