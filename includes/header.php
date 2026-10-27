<?php
/** NOOR AL HIBA TRADING LLC - Global Header */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
mysqli_report(MYSQLI_REPORT_OFF);

// Global Variables
$wa = '971500000000';
$mail = 'export@nooralhibatrading.com';

// Aapki config/connect.php file include karein
require_once __DIR__ . '/../config/connect.php';

// Check connection status
$ok = isset($conn) && !$conn->connect_error;

// Functions
if (!function_exists('q')) {
    function q($sql) {
        global $conn, $ok;
        $r = [];
        if ($ok && ($res = @$conn->query($sql))) while ($x = $res->fetch_assoc()) $r[] = $x;
        return $r;
    }
}
if (!function_exists('e')) {
    function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('txt')) {
    function txt($s, $n = 0) {
        $s = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string)$s))));
        if (preg_match('/Bhagirath|Khetarpal|candle-making/i', $s)) return '';
        return $n ? mb_strimwidth($s, 0, $n, '...') : $s;
    }
}
if (!function_exists('img')) {
    function img($f, $d, $fb) {
        $f = trim((string)$f);
        if ($f === '') return $fb;
        if (preg_match('#^https?://#', $f)) return $f;
        $c = ltrim($f, '/');
        $b = basename($c);
        foreach ([$c, "admin/$c", "uploads/$d/$b", "admin/uploads/$d/$b", "assets/img/uploads/$d/$b", "assets/images/$b"] as $p) if (is_file($p)) return $p;
        return $fb;
    }
}
if (!function_exists('un')) {
    function un($id, $w = 800) { return "https://images.unsplash.com/$id?auto=format&fit=crop&w=$w&q=80"; }
}
if (!function_exists('mark')) {
    function mark($w = 46) {
        return '<svg viewBox="290 55 815 710" width="' . $w . '" aria-hidden="true"><path fill="currentColor" d="M325 476l78-56v228l-78 40zM425 352l123-90v346l-123 32zM570 150l123-88v535l-123 8z"/><path fill="none" stroke="currentColor" stroke-width="7" d="M697 68l118 84v451l-118-6zM838 262l122 90v284l-120-29zM984 420l76 56v206l-76-37z"/><path fill="#D6AC56" d="M298 757C500 650 900 650 1097 757 900 700 500 700 298 757z"/></svg>';
    }
}

// Fetch Categories for Dropdown
$nav_cats = [];
if ($ok) {
    $db_cats = q("SELECT id, category_name, slug FROM categories WHERE status=1");
    if (!empty($db_cats)) {
        foreach ($db_cats as $c) {
            $subs = q("SELECT subcat_name, slug FROM subcategories WHERE cat_id=".$c['id']." AND status=1");
            $nav_cats[$c['slug']] = ['name' => $c['category_name'], 'subs' => $subs];
        }
    }
}
// Fallback if DB empty
if (empty($nav_cats)) {
    $nav_cats = [
        'dry-fruits' => ['name' => 'Dry Fruits & Nuts', 'subs' => [['subcat_name'=>'Almonds','slug'=>'almonds'], ['subcat_name'=>'Cashews','slug'=>'cashews']]],
        'whole-spices' => ['name' => 'Whole Spices', 'subs' => [['subcat_name'=>'Red Chilli','slug'=>'red-chilli'], ['subcat_name'=>'Cardamom','slug'=>'cardamom']]],
        'oil-seeds' => ['name' => 'Oil Seeds', 'subs' => [['subcat_name'=>'Cumin Seeds','slug'=>'cumin-seeds']]]
    ];
}

$page_title = $page_title ?? 'Noor Al Hiba Trading LLC | Import & Export from Dubai to Africa';
$page_desc = $page_desc ?? 'Export-grade spices, dry fruits and agro commodities to importers across Dubai, GCC and Africa.';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?></title>
    <meta name="description" content="<?= e($page_desc) ?>">
    <link rel="icon" href="1.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Marcellus&family=Reem+Kufi:wght@500;600&display=swap"
        rel="stylesheet">

    <script>
    document.documentElement.classList.add('js')
    </script>
    <style>
    /* Base Variables & Styles */
    :root {
        --ink: #1D333D;
        --ink2: #12232A;
        --bronze: #996E23;
        --gold: #D6AC56;
        --sand: #F5F0E6;
        --paper: #FCFAF6;
        --mut: #5B6B72;
        --line: rgba(153, 110, 35, .24);
        --r: 6px;
        --e: cubic-bezier(.2, .7, .2, 1);
        --lat: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Cg fill='none' stroke='%23D6AC56' stroke-width='1'%3E%3Crect x='14' y='14' width='36' height='36'/%3E%3Crect x='14' y='14' width='36' height='36' transform='rotate(45 32 32)'/%3E%3C/g%3E%3C/svg%3E");
    }

    * {
        box-sizing: border-box;
        margin: 0;
    }

    html {
        scroll-behavior: smooth;
        scroll-padding-top: 114px;
    }

    body {
        font: 400 1rem/1.7 'Hanken Grotesk', system-ui, sans-serif;
        color: var(--ink);
        background: var(--paper);
        overflow-x: hidden;
    }

    img {
        max-width: 100%;
        display: block;
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    h1,
    h2,
    h3,
    h4 {
        font-family: Marcellus, Georgia, serif;
        font-weight: 400;
        line-height: 1.15;
    }

    .wrap {
        width: min(1180px, 92%);
        margin: auto;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .85rem 1.7rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: .94rem;
        border: 1.5px solid var(--bronze);
        background: var(--bronze);
        color: #fff;
        cursor: pointer;
        transition: all .35s var(--e);
        white-space: nowrap;
    }

    .btn:hover {
        background: var(--ink);
        border-color: var(--ink);
        transform: translateY(-2px);
    }

    /* Topbar Styles */
    .topbar {
        background: var(--ink2);
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.85rem;
        padding: 6px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .topbar .wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .top-contact,
    .top-links {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .top-contact a:hover,
    .top-links a:hover {
        color: var(--gold);
    }

    .top-contact svg {
        width: 14px;
        height: 14px;
        vertical-align: middle;
        margin-right: 4px;
        color: var(--gold);
    }

    /* Header Styles */
    .site-header {
        position: sticky;
        top: 0;
        z-index: 100;
        background: rgba(252, 250, 246, 0.95);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid transparent;
        transition: box-shadow 0.3s, border-color 0.3s;
    }

    .site-header.s {
        box-shadow: 0 8px 26px rgba(18, 35, 42, 0.07);
        border-color: var(--line);
    }

    .nav-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 78px;
        gap: 22px;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--bronze);
        flex-shrink: 0;
    }

    .brand b {
        display: block;
        font: 400 1.2rem/1 Marcellus, serif;
        letter-spacing: .09em;
        color: var(--ink);
    }

    .brand small {
        font-size: .64rem;
        letter-spacing: .32em;
        font-weight: 600;
    }

    /* Navigation layout fix */
    nav {
        display: flex;
        align-items: center;
        gap: 30px;
    }

    nav>ul {
        display: flex;
        gap: 30px;
        align-items: center;
        margin: 0;
    }

    nav li {
        position: relative;
    }

    nav>ul>li>a:not(.btn) {
        font-size: .92rem;
        font-weight: 500;
        position: relative;
        padding: 28px 0;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    nav>ul>li>a:not(.btn)::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 20px;
        height: 1.5px;
        width: 0;
        background: var(--gold);
        transition: width .35s var(--e);
    }

    nav>ul>li:hover>a:not(.btn)::after {
        width: 100%;
    }

    /* Dropdown Structure Fix */
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: #fff;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        border-radius: var(--r);
        min-width: 220px;
        padding: 10px 0;
        border: 1px solid var(--line);
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s ease;
        display: block;
        z-index: 101;
    }

    nav li.has-dropdown:hover>.dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-menu li {
        padding: 0 10px;
        display: block;
        width: 100%;
    }

    .dropdown-menu li a {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        color: var(--ink);
        font-size: 0.9rem;
        font-weight: 500;
        border-radius: 4px;
        transition: background 0.2s, color 0.2s;
        white-space: nowrap;
    }

    .dropdown-menu li a:hover {
        background: var(--sand);
        color: var(--bronze);
    }

    /* Nested Dropdown (Subcategories) */
    .sub-dropdown-menu {
        position: absolute;
        top: 0;
        left: 100%;
        background: #fff;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        border-radius: var(--r);
        min-width: 200px;
        padding: 10px 0;
        border: 1px solid var(--line);
        opacity: 0;
        visibility: hidden;
        transform: translateX(10px);
        transition: all 0.3s ease;
        margin-left: 2px;
        display: block;
    }

    .dropdown-menu li.has-sub-dropdown:hover>.sub-dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
    }

    .sub-dropdown-menu li {
        display: block;
        width: 100%;
    }

    /* Mobile Menu Toggle */
    .tg {
        display: none;
        background: 0;
        border: 0;
        width: 44px;
        height: 44px;
        cursor: pointer;
    }

    .tg i,
    .tg i::before,
    .tg i::after {
        display: block;
        width: 22px;
        height: 2px;
        background: var(--ink);
        position: relative;
        margin: auto;
        transition: 0.3s;
    }

    .tg i::before,
    .tg i::after {
        content: '';
        position: absolute;
        left: 0;
    }

    .tg i::before {
        top: -7px;
    }

    .tg i::after {
        top: 7px;
    }

    @media(max-width:960px) {
        .topbar {
            display: none;
        }

        .tg {
            display: block;
        }

        /* Mobile nav layout */
        nav {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--paper);
            flex-direction: column;
            align-items: flex-start;
            gap: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height .45s var(--e);
            border-bottom: 1px solid transparent;
        }

        nav.o {
            max-height: 100vh;
            overflow-y: auto;
            border-color: var(--line);
            padding: 10px 4%;
        }

        nav>ul {
            flex-direction: column;
            width: 100%;
            gap: 0;
            align-items: flex-start;
        }

        nav>ul>li {
            width: 100%;
        }

        nav>ul>li>a:not(.btn) {
            padding: 14px 0;
            border-bottom: 1px solid var(--line);
            width: 100%;
            justify-content: space-between;
        }

        nav>ul>li>a:not(.btn)::after {
            display: none;
        }

        /* Mobile Dropdown resets */
        .dropdown-menu,
        .sub-dropdown-menu {
            position: static;
            box-shadow: none;
            border: none;
            opacity: 1;
            visibility: visible;
            transform: none;
            display: none;
            padding: 0 0 0 15px;
            margin: 0;
            min-width: 100%;
            background: transparent;
            white-space: normal;
        }

        nav li.has-dropdown.open>.dropdown-menu {
            display: block;
        }

        .dropdown-menu li.has-sub-dropdown.open>.sub-dropdown-menu {
            display: block;
        }

        .dropdown-menu li a {
            border-bottom: 1px dashed var(--line);
            padding: 12px 0;
            white-space: normal;
        }

        nav .btn {
            margin: 18px 0;
            width: 100%;
            justify-content: center;
        }
    }

    @media(max-width:600px) {
        .brand small {
            display: none;
        }
    }
    </style>
</head>

<body>

    <!-- Topbar -->
    <div class="topbar">
        <div class="wrap">
            <div class="top-contact">
                <a href="mailto:<?= e($mail) ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <?= e($mail) ?>
                </a>
                <a href="tel:+<?= e($wa) ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                        </path>
                    </svg>
                    +<?= e($wa) ?>
                </a>
            </div>
            <div class="top-links">
                <span>Mon to Sat, 9:00 AM to 7:00 PM (GST)</span>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="site-header" id="top">
        <div class="wrap nav-wrap">
            <a class="brand" href="index.php">
                <?= mark(46) ?>
                <span><b>NOOR AL HIBA</b><small>TRADING LLC</small></span>
            </a>
            <button class="tg" aria-label="Menu" aria-expanded="false"><i></i></button>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>

                    <!-- Products Dropdown -->
                    <li class="has-dropdown">
                        <a href="products.php">
                            Products
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="dd-icon">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="products.php" style="color:var(--gold); font-weight:700;">View All Products</a>
                            </li>
                            <?php foreach ($nav_cats as $slug => $cData): ?>
                            <li class="has-sub-dropdown">
                                <a href="products.php?cat=<?= e($slug) ?>">
                                    <?= e($cData['name']) ?>
                                    <?php if(!empty($cData['subs'])): ?>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="sub-icon">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                    <?php endif; ?>
                                </a>

                                <!-- Subcategories -->
                                <?php if(!empty($cData['subs'])): ?>
                                <ul class="sub-dropdown-menu">
                                    <?php foreach ($cData['subs'] as $sub): ?>
                                    <li><a
                                            href="products.php?cat=<?= e($slug) ?>&q=<?= urlencode($sub['subcat_name']) ?>"><?= e($sub['subcat_name']) ?></a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <li><a href="index.php#services">Services</a></li>
                    <li><a href="blog.php">Insights</a></li>
                </ul>
                <a href="index.php#rfq" class="btn">Request a quote</a>
            </nav>
        </div>
    </header>