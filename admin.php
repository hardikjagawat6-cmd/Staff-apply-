<?php
// Secure your admin panel with a simple password (change 'MySecretPass123' to whatever you want)
define('ADMIN_PASSWORD', 'MySecretPass123');

session_start();

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Handle Login Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = "❌ Incorrect password!";
    }
}

// If not logged in, show the login screen
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Staff Portal</title>
    <style>
        body { font-family: sans-serif; background: #141419; color: #e2e8f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-box { background: #1f232c; border: 2px solid #3f4756; border-top: 4px solid #55cdfc; padding: 30px; border-radius: 12px; width: 100%; max-width: 350px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h2 { margin-top: 0; text-transform: uppercase; letter-spacing: 1px; font-size: 20px; }
        input { width: 100%; padding: 12px; background: #15181f; border: 1px solid #3f4756; border-radius: 8px; color: #fff; margin: 15px 0; box-sizing: border-box; outline: none; }
        input:focus { border-color: #55cdfc; }
        button { width: 100%; padding: 12px; background: #22c55e; border: none; border-radius: 8px; color: #fff; font-weight: bold; cursor: pointer; text-transform: uppercase; box-shadow: 0 4px 0 #15803d; }
        button:active { transform: translateY(2px); box-shadow: 0 2px 0 #15803d; }
        .error { color: #ef4444; font-size: 14px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔒 Staff Admin Area</h2>
        <?php if (isset($login_error)) echo "<div class='error'>$login_error</div>"; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Enter Admin Password" required autocomplete="current-password">
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
<?php
exit;
endif;

// --- DASHBOARD LOGIC (Runs only if logged in) ---
try {
    $db = new PDO('sqlite:recruitment.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database table if it somehow doesn't exist yet
    $db->exec("CREATE TABLE IF NOT EXISTS staff_applications (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name TEXT NOT NULL,
        discord_tag TEXT NOT NULL,
        age INTEGER NOT NULL,
        position TEXT NOT NULL,
        timezone TEXT NOT NULL,
        cover_letter TEXT,
        status TEXT DEFAULT 'Pending',
        applied_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("Database failure: " . $e->getMessage());
}

// Handle Acceptance / Rejection / Deletion actions from buttons
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] == 'accept') {
        $stmt = $db->prepare("UPDATE staff_applications SET status = 'Accepted' WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($_GET['action'] == 'reject') {
        $stmt = $db->prepare("UPDATE staff_applications SET status = 'Rejected' WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($_GET['action'] == 'delete') {
        $stmt = $db->prepare("DELETE FROM staff_applications WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: admin.php");
    exit;
}

// Fetch all applications, newest first
$applications = $db->query("SELECT * FROM staff_applications ORDER BY applied_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Review Dashboard</title>
    <style>
        body { font-family: sans-serif; background: #141419; color: #e2e8f0; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; max-width: 800px; margin: 0 auto 20px auto; border-bottom: 2px solid #3f4756; padding-bottom: 10px; }
        h1 { margin: 0; font-size: 24px; text-transform: uppercase; color: #fff; }
        .logout-btn { color: #f87171; text-decoration: none; font-weight: bold; border: 1px solid #ef4444; padding: 6px 12px; border-radius: 6px; background: rgba(239, 68, 68, 0.1); }
        .dashboard-container { max-width: 800px; margin: 0 auto; }
        .card { background: #1f232c; border: 1px solid #3f4756; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); position: relative; }
        .status-badge { position: absolute; top: 20px; right: 20px; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-Pending { background: rgba(234, 179, 8, 0.2); border: 1px solid #eab308; color: #fde047; }
        .status-Accepted { background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #4ade80; }
        .status-Rejected { background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #f87171; }
        h3 { margin: 0 0 10px 0; color: #fff; font-size: 20px; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; font-size: 14px; background: #15181f; padding: 12px; border-radius: 8px; }
        .meta-item b { color: #94a3b8; text-transform: uppercase; font-size: 11px; display: block; margin-bottom: 2px; }
        .cover-letter-box { background: rgba(255,255,255,0.02); border-left: 3px solid #55cdfc; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; font-style: italic; }
        .actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { padding: 10px 16px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; text-align: center; font-size: 14px; flex-grow: 1; }
        .btn-accept { background: #22c55e; color: #fff; }
        .btn-reject { background: #eab308; color: #fff; }
        .btn-delete { background: #ef4444; color: #fff; flex-grow: 0; }
        .no-data { text-align: center; padding: 40px; color: #64748b; font-size: 16px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📥 Applications Inbox (<?= count($applications) ?>)</h1>
        <a href="admin.php?logout=1" class="logout-btn">Logout</a>
    </div>

    <div class="dashboard-container">
        <?php if (empty($applications)): ?>
            <div class="card no-data">No applications have been submitted yet.</div>
        <?php endif; ?>

        <?php foreach ($applications as $app): ?>
            <div class="card">
                <span class="status-badge status-<?= $app['status'] ?>"><?= $app['status'] ?></span>
                <h3><?= htmlspecialchars($app['full_name']) ?></h3>
                
                <div class="meta-grid">
                    <div class="meta-item"><b>Discord Profile / ID</b><span style="color:#55cdfc;"><a href="https://discord.com<?= $app['discord_tag'] ?>" target="_blank" style="color:#55cdfc;text-decoration:none;"><@<?= htmlspecialchars($app['discord_tag']) ?>></a></span></div>
                    <div class="meta-item"><b>Age</b><?= (int)$app['age'] ?> Yrs Old</div>
                    <div class="meta-item"><b>Applying For Rank</b><span style="font-weight:bold;color:#fff;"><?= htmlspecialchars($app['position']) ?></span></div>
                    <div class="meta-item"><b>Timezone / Region</b><?= htmlspecialchars($app['timezone']) ?></div>
                </div>

                <div class="cover-letter-box">
                    <b>Application Notes:</b><br>
                    <?= nl2br(htmlspecialchars($app['cover_letter'])) ?>
                </div>

                <div class="actions">
                    <?php if ($app['status'] == 'Pending'): ?>
                        <a href="admin.php?action=accept&id=<?= $app['id'] ?>" class="btn btn-accept">Accept ✅</a>
                        <a href="admin.php?action=reject&id=<?= $app['id'] ?>" class="btn btn-reject">Reject ❌</a>
                    <?php endif; ?>
                    <a href="admin.php?action=delete&id=<?= $app['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this application permanently?')">🗑️</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
              
