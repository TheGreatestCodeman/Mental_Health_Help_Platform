<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

$user = null;
if (isset($_SESSION["user_id"])) {
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crisis Helpline & Resources</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .crisis-banner {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .crisis-banner h1 {
            margin: 0;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .crisis-banner p {
            margin: 5px 0;
            font-size: 1.1em;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        .crisis-number {
            font-size: 2.2em;
            font-weight: bold;
            margin: 20px 0;
            font-family: monospace;
            letter-spacing: 2px;
            background: rgba(0,0,0,0.1);
            padding: 20px;
            border-radius: 8px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        .helpline-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .helpline-card {
            border: 2px solid #dc3545;
            padding: 20px;
            border-radius: 8px;
            background: burlywood;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .helpline-card h3 {
            color: #dc3545;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .helpline-card .number {
            font-size: 1.4em;
            font-weight: bold;
            color: white;
            font-family: monospace;
            margin: 15px 0;
            padding: 12px;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border-radius: 5px;
            text-align: center;
            letter-spacing: 1px;
        }
        .helpline-card p {
            margin: 10px 0;
            font-size: 0.95em;
            line-height: 1.6;
            color: #333;
        }
        .helpline-card ul {
            margin: 15px 0;
            padding-left: 20px;
        }
        .helpline-card li {
            margin: 8px 0;
            color: #555;
        }
        .resources-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin: 30px 0;
            border-left: 5px solid #0066cc;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .resources-section h2 {
            color: #0066cc;
            margin-top: 0;
        }
        .resources-section h3 {
            color: #0066cc;
            margin-top: 20px;
            font-size: 1.2em;
        }
        .resource-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .resource-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        .resource-item h4 {
            margin-top: 0;
            color: #0066cc;
        }
        .resource-item p {
            margin: 10px 0;
            font-size: 0.9em;
            color: #555;
        }
        .resource-item a {
            color: #0066cc;
            text-decoration: none;
            font-weight: bold;
        }
        .resource-item a:hover {
            text-decoration: underline;
        }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning-box strong {
            color: #856404;
            font-size: 1.1em;
        }
        .warning-box p {
            margin: 10px 0 0 0;
            color: #856404;
        }
        .nav-buttons {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
        .nav-buttons a {
            flex: 1;
        }
        .nav-buttons button {
            width: 100%;
        }
        .crisis-support {
            background: #d4edda;
            border-left: 5px solid #28a745;
            padding: 25px;
            border-radius: 5px;
            margin: 30px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .crisis-support h3 {
            color: #155724;
            margin-top: 0;
            font-size: 1.3em;
        }
        .crisis-support p {
            color: #155724;
            margin: 10px 0;
        }
        .crisis-support ul {
            color: #155724;
        }
        .crisis-support li {
            margin: 8px 0;
        }
    </style>
</head>
<body>

<?php if ($user): ?>
    <div class="nav-buttons">
        <a href="index.php"><button>← Back to Home</button></a>
        <a href="logout.php"><button>Log out</button></a>
    </div>
<?php else: ?>
    <div class="nav-buttons">
        <a href="index.php"><button>← Back to Home</button></a>
        <a href="login.php"><button>Log in</button></a>
    </div>
<?php endif; ?>

<div class="crisis-banner">
    <h1>🆘 Crisis & Emergency Support</h1>
    <p>You are not alone. Help is available 24/7</p>
    <div class="crisis-number">988 or Text HOME to 741741</div>
    <p><strong>In immediate danger? Call 911 or go to the nearest emergency room</strong></p>
</div>

<div class="helpline-grid">
    <!-- US Crisis Hotline -->
    <div class="helpline-card">
        <h3>🇺🇸 National Suicide Prevention Lifeline (US)</h3>
        <div class="number">988</div>
        <p><strong>Available 24/7 | Free & Confidential</strong></p>
        <p>The Lifeline provides free and confidential support to people in distress and to those around them.</p>
        <ul>
            <li>Call or text 988</li>
            <li>Online chat at suicidepreventionlifeline.org</li>
            <li>Spanish language: Press 2</li>
        </ul>
    </div>

    <!-- Crisis Text Line -->
    <div class="helpline-card">
        <h3>💬 Crisis Text Line</h3>
        <div class="number">Text HOME to 741741</div>
        <p><strong>24/7 | Text-based Support</strong></p>
        <p>Reach a crisis counselor anywhere, anytime by texting a keyword to 741741.</p>
        <ul>
            <li>Text "HOME" to 741741</li>
            <li>Trained crisis counselors respond</li>
            <li>Free & confidential</li>
        </ul>
    </div>

    <!-- Veterans Crisis Line -->
    <div class="helpline-card">
        <h3>🎖️ Veterans Crisis Line</h3>
        <div class="number">988 then press 1</div>
        <p><strong>24/7 | For Veterans & Military</strong></p>
        <p>Special support for veterans and active-duty service members.</p>
        <ul>
            <li>Call 988 then press 1</li>
            <li>Text 838255</li>
            <li>Chat at veteranscrisisline.net</li>
        </ul>
    </div>

    <!-- Trans Lifeline -->
    <div class="helpline-card">
        <h3>🏳️‍⚧️ Trans Lifeline</h3>
        <div class="number">877-565-8860</div>
        <p><strong>24/7 | Trans-specific Support</strong></p>
        <p>For transgender and non-binary individuals in crisis.</p>
        <ul>
            <li>Call 877-565-8860 (US)</li>
            <li>Call 877-330-6366 (Canada)</li>
            <li>Text 741741</li>
        </ul>
    </div>

    <!-- LGBTQ Support -->
    <div class="helpline-card">
        <h3>🏳️‍🌈 Trevor Project (LGBTQ+)</h3>
        <div class="number">1-866-488-7386</div>
        <p><strong>24/7 | LGBTQ+ Youth Support</strong></p>
        <p>Crisis support for lesbian, gay, bisexual, transgender, queer, and questioning youth.</p>
        <ul>
            <li>Call 1-866-488-7386</li>
            <li>Text START to 678-678</li>
            <li>Chat at thetrevorproject.org</li>
        </ul>
    </div>

    <!-- Substance Abuse -->
    <div class="helpline-card">
        <h3>💊 SAMHSA National Helpline</h3>
        <div class="number">1-800-662-4357</div>
        <p><strong>24/7 | Free, Confidential, Multilingual</strong></p>
        <p>Substance abuse and mental health services referral and information.</p>
        <ul>
            <li>Free and confidential</li>
            <li>Available in multiple languages</li>
            <li>Referrals to treatment providers</li>
        </ul>
    </div>

    <!-- Eating Disorders -->
    <div class="helpline-card">
        <h3>🥗 National Eating Disorders Association</h3>
        <div class="number">1-800-931-2237</div>
        <p><strong>Support for Eating Disorders</strong></p>
        <p>Help and support for eating disorders and related issues.</p>
        <ul>
            <li>Call 1-800-931-2237</li>
            <li>Text "NEDA" to 741741</li>
            <li>Chat at nationaleatingdisorders.org</li>
        </ul>
    </div>

    <!-- Domestic Violence -->
    <div class="helpline-card">
        <h3>🛡️ National Domestic Violence Hotline</h3>
        <div class="number">1-800-799-7233</div>
        <p><strong>24/7 | Confidential & Multilingual</strong></p>
        <p>Support for victims and survivors of domestic violence.</p>
        <ul>
            <li>Call 1-800-799-7233</li>
            <li>Text "START" to 88788</li>
            <li>Available in 200+ languages</li>
        </ul>
    </div>
</div>

<div class="warning-box">
    <strong>⚠️ In Immediate Danger?</strong>
    <p>If you or someone else is in immediate danger, please call 911 or go to the nearest emergency room. Don't wait - your safety is the priority.</p>
</div>

<div class="resources-section">
    <h2>Additional Support Resources</h2>
    
    <h3>Online Support Communities</h3>
    <div class="resource-list">
        <div class="resource-item">
            <h4>7 Cups</h4>
            <p>Free emotional support and counseling from trained listeners.</p>
            <p><a href="https://www.7cups.com" target="_blank">Visit 7cups.com</a></p>
        </div>
        <div class="resource-item">
            <h4>IMAlive</h4>
            <p>Online chat with trained crisis volunteers.</p>
            <p><a href="https://www.imalive.org" target="_blank">Visit imalive.org</a></p>
        </div>
        <div class="resource-item">
            <h4>Better Help</h4>
            <p>Online therapy with licensed therapists.</p>
            <p><a href="https://www.betterhelp.com" target="_blank">Visit betterhelp.com</a></p>
        </div>
        <div class="resource-item">
            <h4>Talkspace</h4>
            <p>Online therapy and psychiatric services.</p>
            <p><a href="https://www.talkspace.com" target="_blank">Visit talkspace.com</a></p>
        </div>
    </div>

    <h3>Self-Help & Coping Tools</h3>
    <div class="resource-list">
        <div class="resource-item">
            <h4>Mindfulness & Meditation</h4>
            <p>Apps like Headspace, Calm, and Insight Timer offer guided meditation and mindfulness exercises.</p>
        </div>
        <div class="resource-item">
            <h4>Grounding Techniques</h4>
            <p>The 5-4-3-2-1 technique: Identify 5 things you see, 4 you can touch, 3 you hear, 2 you smell, 1 you taste.</p>
        </div>
        <div class="resource-item">
            <h4>Journal Writing</h4>
            <p>Write down your thoughts and feelings to process emotions and gain clarity.</p>
        </div>
        <div class="resource-item">
            <h4>Exercise & Movement</h4>
            <p>Physical activity releases endorphins and reduces stress. Even a 10-minute walk can help.</p>
        </div>
    </div>

    <h3>Support Groups & Community</h3>
    <div class="resource-list">
        <div class="resource-item">
            <h4>SMART Recovery</h4>
            <p>Support for addiction recovery using scientific approaches.</p>
            <p><a href="https://www.smartrecovery.org" target="_blank">Visit smartrecovery.org</a></p>
        </div>
        <div class="resource-item">
            <h4>Mental Health America</h4>
            <p>Resources and screening tools for mental health conditions.</p>
            <p><a href="https://www.mhanational.org" target="_blank">Visit mhanational.org</a></p>
        </div>
        <div class="resource-item">
            <h4>NAMI (National Alliance on Mental Illness)</h4>
            <p>Support groups and resources for mental health conditions and recovery.</p>
            <p><a href="https://www.nami.org" target="_blank">Visit nami.org</a></p>
        </div>
        <div class="resource-item">
            <h4>Celebrate Recovery</h4>
            <p>Faith-based recovery program for various struggles and addictions.</p>
            <p><a href="https://www.celebraterecovery.com" target="_blank">Visit celebraterecovery.com</a></p>
        </div>
    </div>
</div>

<div class="crisis-support">
    <h3>Remember: You Matter 💙</h3>
    <p>If you're struggling, please reach out. There are people who care about you and want to help. It's okay to ask for support, and seeking help is a sign of strength, not weakness.</p>
    <p><strong>Things that might help right now:</strong></p>
    <ul>
        <li>Call someone you trust - a friend, family member, or counselor</li>
        <li>Go somewhere safe where you feel comfortable</li>
        <li>Do something you enjoy - listen to music, spend time in nature, pet an animal</li>
        <li>Practice a grounding technique to calm your nervous system</li>
        <li>Remember this feeling is temporary and will pass</li>
    </ul>
</div>

<div class="nav-buttons">
    <a href="index.php"><button>← Back to Home</button></a>
    <?php if ($user): ?>
        <a href="user_dashboard.php"><button>Go to Dashboard</button></a>
    <?php endif; ?>
</div>

</body>
</html>
