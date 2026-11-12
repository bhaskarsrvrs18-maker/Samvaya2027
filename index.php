<?php
session_start();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$events = [
    'Quiz',
    'Debate',
    'Dumb Charades',
    'Chess',
    'E-Sports',
    'Carrom',
    'Code-IT',
    'Table Tennis',
    'Treasure Hunt',
    'Antyakshari'
];

$eventIcons = [
    'Quiz' => '🧠',
    'Debate' => '🎤',
    'Dumb Charades' => '🎭',
    'Chess' => '♟️',
    'E-Sports' => '🎮',
    'Carrom' => '🎯',
    'Code-IT' => '💻',
    'Table Tennis' => '🏓',
    'Treasure Hunt' => '🗺️',
    'Antyakshari' => '🎵'
];

$festDates = ['2027-05-16', '2027-05-17'];

$data = [
    'name' => '',
    'usn' => '',
    'department' => '',
    'year' => '',
    'email' => '',
    'phone' => '',
    'type' => 'Individual',
    'events' => [],
    'fest_date' => '',
    'team_name' => '',
    'team_size' => '',
    'leader_name' => '',
    'leader_phone' => '',
    'notes' => '',
    'declaration' => ''
];

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $key => $value) {
        if ($key === 'events') {
            continue;
        }
        $data[$key] = trim($_POST[$key] ?? '');
    }

    $data['events'] = $_POST['events'] ?? [];

    if (!is_array($data['events'])) {
        $data['events'] = [];
    }

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $errors[] = 'Session expired. Refresh the page and try again.';
    }

    if (mb_strlen($data['name']) < 2) {
        $errors[] = 'Enter your full name.';
    }

    if ($data['usn'] === '') {
        $errors[] = 'Enter your USN or student ID.';
    }

    if ($data['department'] === '') {
        $errors[] = 'Enter your department.';
    }

    if (!in_array($data['year'], ['1st Year', '2nd Year', '3rd Year', '4th Year'], true)) {
        $errors[] = 'Select your year of study.';
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if (!preg_match('/^[0-9+\-\s()]{8,16}$/', $data['phone'])) {
        $errors[] = 'Enter a valid contact number.';
    }

    if (!in_array($data['type'], ['Individual', 'Team'], true)) {
        $errors[] = 'Select a participation type.';
    }

    if (!$data['events']) {
        $errors[] = 'Select at least one event.';
    }

    foreach ($data['events'] as $event) {
        if (!in_array($event, $events, true)) {
            $errors[] = 'Invalid event selection.';
            break;
        }
    }

    if (!in_array($data['fest_date'], $festDates, true)) {
        $errors[] = 'Select a valid fest date.';
    }

    if ($data['type'] === 'Team') {
        if ($data['team_name'] === '') {
            $errors[] = 'Enter your team name.';
        }

        if (
            !ctype_digit($data['team_size']) ||
            (int)$data['team_size'] < 2 ||
            (int)$data['team_size'] > 30
        ) {
            $errors[] = 'Team size must be between 2 and 30.';
        }

        if ($data['leader_name'] === '') {
            $errors[] = 'Enter the team leader name.';
        }

        if (!preg_match('/^[0-9+\-\s()]{8,16}$/', $data['leader_phone'])) {
            $errors[] = 'Enter a valid team leader contact number.';
        }
    }

    if ($data['declaration'] !== 'yes') {
        $errors[] = 'Please accept the declaration.';
    }

    if (!$errors) {
        $success = true;
        $reference = 'SV27-' . strtoupper(bin2hex(random_bytes(3)));
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="SAMVAYA 2027 hostel fest registration at S.R.N.G. Sampannappa Hostel, Bengaluru.">

    <title>SAMVAYA 2027 | Hostel Fest</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header class="navbar">
    <a href="index.php" class="brand">
        <span class="brand-mark">S</span>
        <span class="brand-name">SAMVAYA<small>HOSTEL FEST 2027</small></span>
    </a>

    <nav>
        <a href="#about">About</a>
        <a href="#events">Events</a>
        <a href="#register" class="nav-register">Register ↗</a>
    </nav>
</header>

<?php if ($success): ?>

<section class="success-page">
    <div class="success-card">

        <div class="success-icon">✓</div>

        <p class="eyebrow">REGISTRATION RECEIVED</p>

        <h1>You're part of<br><em>the celebration.</em></h1>

        <p class="success-copy">
            Thank you, <?= e($data['name']) ?>!
            Your SAMVAYA 2027 registration details have been submitted.
        </p>

        <div class="reference-box">
            <small>YOUR REGISTRATION REFERENCE</small>
            <strong><?= e($reference) ?></strong>
        </div>

        <div class="receipt-details">
            <div>
                <small>PARTICIPANT</small>
                <strong><?= e($data['name']) ?></strong>
            </div>

            <div>
                <small>PARTICIPATION</small>
                <strong><?= e($data['type']) ?></strong>
            </div>

            <div>
                <small>FEST DATE</small>
                <strong><?= e(date('d M Y', strtotime($data['fest_date']))) ?></strong>
            </div>

            <?php if ($data['type'] === 'Team'): ?>
            <div>
                <small>TEAM NAME</small>
                <strong><?= e($data['team_name']) ?></strong>
            </div>
            <?php endif; ?>

            <div class="receipt-wide">
                <small>SELECTED EVENTS</small>
                <strong><?= e(implode(', ', $data['events'])) ?></strong>
            </div>
        </div>

        <button class="gold-button" onclick="window.print()">
            Print registration ↗
        </button>

        <a href="index.php" class="back-link">← Back to SAMVAYA</a>

    </div>
</section>

<?php else: ?>

<section class="hero" id="about">

    <div class="hero-overlay"></div>

    <div class="hero-content">
        <p class="eyebrow">S.R.N.G. SAMPANNAPPA HOSTEL PRESENTS</p>

        <p class="hero-tagline">FROM BOARDS TO BRAINS, WE CHALLENGE ALL</p>

        <h1>SAMVAYA<span>2027</span></h1>

        <div class="gold-divider"><span>✦</span></div>

        <p class="hero-description">
            Two days. Ten events. Endless possibilities.
            Bring your talent, challenge your limits and be part
            of an unforgettable hostel fest.
        </p>

        <div class="hero-date">
            <span>16</span>
            <span class="date-divider"></span>
            <span>17</span>
            <div>
                <strong>MAY</strong>
                <small>2027</small>
            </div>
        </div>

        <a href="#register" class="gold-button">Register now <span>↗</span></a>
    </div>

    <div class="hero-bottom">
        <span>SWAMI VIVEKANANDA VIDYARTHI SANGHA</span>
        <span>BENGALURU · INDIA</span>
    </div>

</section>

<section class="intro-section">
    <div class="intro-symbol">✦</div>

    <p class="eyebrow">THE SPIRIT OF SAMVAYA</p>

    <h2>Where talent meets<br><em>the challenge.</em></h2>

    <p>
        From mind games to technology, strategy to entertainment,
        SAMVAYA brings together a variety of competitions.
        Choose your events, gather your team and get ready to participate.
    </p>

    <div class="venue">
        <span>⌖</span>
        <div>
            <strong>S.R.N.G. Sampannappa Hostel</strong>
            <small>1091, O.T.C. Road, Nagarathpet, Bengaluru - 560002</small>
        </div>
    </div>
</section>

<section class="events-section" id="events">

    <div class="section-heading">
        <p class="eyebrow">THE COMPETITIONS</p>
        <h2>Explore the <em>events.</em></h2>
        <p>Ten events. One unforgettable celebration.</p>
    </div>

    <div class="events-grid">
        <?php foreach ($events as $index => $event): ?>
            <article class="event-card">
                <span class="event-number"><?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?></span>
                <span class="event-icon"><?= $eventIcons[$event] ?></span>
                <h3><?= e($event) ?></h3>
                <span class="event-line"></span>
                <a href="#register">PARTICIPATE ↗</a>
            </article>
        <?php endforeach; ?>
    </div>

</section>

<section class="register-section" id="register">

    <div class="register-intro">
        <p class="eyebrow">YOUR MOMENT STARTS HERE</p>

        <h2>Take the<br><em>challenge.</em></h2>

        <p>
            Complete the registration form and select the events
            you wish to participate in. You can register individually
            or as a team.
        </p>

        <div class="contact-card">
            <span class="contact-icon">☎</span>
            <div>
                <strong>Need help?</strong>
                <p>Sandeep · <a href="tel:7204968112">7204968112</a></p>
                <p>Niranjan · <a href="tel:7204675193">7204675193</a></p>
            </div>
        </div>
    </div>

    <div class="form-card">

        <div class="form-heading">
            <div>
                <p class="eyebrow">SAMVAYA 2027 / PARTICIPANT ENTRY</p>
                <h3>Fest registration</h3>
                <p>Fields marked * are required.</p>
            </div>
            <span class="form-count">01 / 01</span>
        </div>

        <?php if ($errors): ?>
            <div class="error-box" role="alert">
                <strong>Please check the following:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="festForm" method="POST" action="index.php">

            <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

            <div class="form-section-title">
                <span>01</span>
                <h4>Participant details</h4>
            </div>

            <div class="form-grid">

                <div class="field full">
                    <label for="name">Full name *</label>
                    <input id="name" name="name" required
                           value="<?= e($data['name']) ?>"
                           placeholder="Enter your full name">
                </div>

                <div class="field">
                    <label for="usn">USN / Student ID *</label>
                    <input id="usn" name="usn" required
                           value="<?= e($data['usn']) ?>"
                           placeholder="Enter your USN">
                </div>

                <div class="field">
                    <label for="department">Department *</label>
                    <input id="department" name="department" required
                           value="<?= e($data['department']) ?>"
                           placeholder="e.g. Computer Science">
                </div>

                <div class="field">
                    <label for="year">Year of study *</label>
                    <select id="year" name="year" required>
                        <option value="">Select year</option>
                        <?php foreach (['1st Year', '2nd Year', '3rd Year', '4th Year'] as $year): ?>
                            <option value="<?= e($year) ?>" <?= $data['year'] === $year ? 'selected' : '' ?>>
                                <?= e($year) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="email">Email address *</label>
                    <input type="email" id="email" name="email" required
                           value="<?= e($data['email']) ?>"
                           placeholder="name@example.com">
                </div>

                <div class="field full">
                    <label for="phone">Contact number *</label>
                    <input type="tel" id="phone" name="phone" required
                           value="<?= e($data['phone']) ?>"
                           placeholder="+91 98765 43210">
                </div>

            </div>

            <div class="form-section-title">
                <span>02</span>
                <h4>Choose your participation</h4>
            </div>

            <div class="participation-options">

                <label class="participation-card">
                    <input type="radio" name="type" value="Individual"
                        <?= $data['type'] === 'Individual' ? 'checked' : '' ?>>
                    <span class="participation-icon">♙</span>
                    <strong>Individual</strong>
                    <small>I'm participating on my own</small>
                </label>

                <label class="participation-card">
                    <input type="radio" name="type" value="Team"
                        <?= $data['type'] === 'Team' ? 'checked' : '' ?>>
                    <span class="participation-icon">♟</span>
                    <strong>Team</strong>
                    <small>We're participating together</small>
                </label>

            </div>

            <div class="team-fields" id="teamFields" style="display:none">

                <div class="form-grid">

                    <div class="field">
                        <label for="team_name">Team name *</label>
                        <input id="team_name" name="team_name"
                               value="<?= e($data['team_name']) ?>"
                               placeholder="Enter your team name">
                    </div>

                    <div class="field">
                        <label for="team_size">Number of members *</label>
                        <input id="team_size" name="team_size"
                               type="number" min="2" max="30"
                               value="<?= e($data['team_size']) ?>"
                               placeholder="2 to 30">
                    </div>

                    <div class="field">
                        <label for="leader_name">Team leader name *</label>
                        <input id="leader_name" name="leader_name"
                               value="<?= e($data['leader_name']) ?>"
                               placeholder="Enter leader name">
                    </div>

                    <div class="field">
                        <label for="leader_phone">Team leader contact *</label>
                        <input id="leader_phone" name="leader_phone"
                               value="<?= e($data['leader_phone']) ?>"
                               placeholder="Contact number">
                    </div>

                </div>

            </div>

            <div class="form-section-title">
                <span>03</span>
                <h4>Select your events</h4>
            </div>

            <p class="form-hint">Choose one or more events you wish to participate in.</p>

            <div class="event-select-grid">
                <?php foreach ($events as $event): ?>
                    <label class="event-select">
                        <input type="checkbox" name="events[]"
                               value="<?= e($event) ?>"
                               <?= in_array($event, $data['events'], true) ? 'checked' : '' ?>>
                        <span class="event-check">✓</span>
                        <span><?= $eventIcons[$event] ?></span>
                        <strong><?= e($event) ?></strong>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="form-section-title">
                <span>04</span>
                <h4>Choose your fest day</h4>
            </div>

            <div class="date-options">

                <label class="date-card">
                    <input type="radio" name="fest_date" value="2027-05-16"
                        <?= $data['fest_date'] === '2027-05-16' ? 'checked' : '' ?>>
                    <span class="date-day">16</span>
                    <span class="date-month">MAY 2027</span>
                    <small>DAY ONE</small>
                </label>

                <label class="date-card">
                    <input type="radio" name="fest_date" value="2027-05-17"
                        <?= $data['fest_date'] === '2027-05-17' ? 'checked' : '' ?>>
                    <span class="date-day">17</span>
                    <span class="date-month">MAY 2027</span>
                    <small>DAY TWO</small>
                </label>

            </div>

            <div class="field notes-field">
                <label for="notes">Additional information <span>OPTIONAL</span></label>
                <textarea id="notes" name="notes" rows="3"
                          placeholder="Any additional details you'd like to share?"><?= e($data['notes']) ?></textarea>
            </div>

            <div class="declaration">
                <label>
                    <input type="checkbox" name="declaration" value="yes"
                        <?= $data['declaration'] === 'yes' ? 'checked' : '' ?> required>
                    <span>
                        I confirm that the information provided is correct
                        and agree to follow the event guidelines. *
                    </span>
                </label>
            </div>

            <button type="submit" class="gold-button submit-button">
                Complete registration <span>↗</span>
            </button>

            <p class="form-bottom-note">
                SAMVAYA 2027 · LET THE CHALLENGE BEGIN!
            </p>

        </form>
    </div>

</section>

<?php endif; ?>

<footer class="footer">
    <div class="footer-brand">
        <span class="brand-mark">S</span>
        <span class="brand-name">SAMVAYA<small>HOSTEL FEST 2027</small></span>
    </div>

    <p class="footer-tagline">From Boards to Brains, We Challenge All.</p>

    <div class="footer-contact">
        <span>Sandeep: 7204968112</span>
        <span>Niranjan: 7204675193</span>
    </div>

    <div class="footer-divider"></div>

    <p class="footer-address">
        S.R.N.G. Sampannappa Hostel · Bengaluru - 560002
    </p>

    <small>© 2027 SAMVAYA · SWAMI VIVEKANANDA VIDYARTHI SANGHA</small>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="assets/js/script.js"></script>

</body>
</html>