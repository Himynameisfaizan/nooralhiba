<?php
/** NOOR AL HIBA TRADING LLC - Products Page */
session_start();
mysqli_report(MYSQLI_REPORT_OFF);

// Dynamic Variables
$wa = '971500000000'; // Is number ko DB se bhi fetch karwa sakte hain
$mail = 'export@nooralhibatrading.com';

try {
    $conn = new mysqli('localhost', 'root', '', 'nooralhiba');
} catch (Throwable $t) {
    $conn = null;
}
$ok = $conn && !$conn->connect_error;
if ($ok) $conn->set_charset('utf8mb4');

function q($sql) {
    global $conn, $ok;
    $r = [];
    if ($ok && ($res = @$conn->query($sql))) while ($x = $res->fetch_assoc()) $r[] = $x;
    return $r;
}

function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function txt($s, $n = 0) {
    $s = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string)$s))));
    if (preg_match('/Bhagirath|Khetarpal/i', $s)) return ''; 
    return $n ? mb_strimwidth($s, 0, $n, '...') : $s;
}

function img($f, $d, $fb) {
    $f = trim((string)$f);
    if ($f === '') return $fb;
    if (preg_match('#^https?://#', $f)) return $f;
    $c = ltrim($f, '/');
    $b = basename($c);
    foreach ([$c, "admin/$c", "uploads/$d/$b", "admin/uploads/$d/$b", "assets/img/uploads/$d/$b", "assets/images/$b"] as $p) if (is_file($p)) return $p;
    return $fb;
}

function un($id, $w = 800) {
    return "https://images.unsplash.com/$id?auto=format&fit=crop&w=$w&q=80";
}

function mark($w = 46) {
    return '<svg viewBox="290 55 815 710" width="' . $w . '" aria-hidden="true"><path fill="currentColor" d="M325 476l78-56v228l-78 40zM425 352l123-90v346l-123 32zM570 150l123-88v535l-123 8z"/><path fill="none" stroke="currentColor" stroke-width="7" d="M697 68l118 84v451l-118-6zM838 262l122 90v284l-120-29zM984 420l76 56v206l-76-37z"/><path fill="#D6AC56" d="M298 757C500 650 900 650 1097 757 900 700 500 700 298 757z"/></svg>';
}

// ---- Pagination & Filters ----
$limit = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat_filter = isset($_GET['cat']) ? trim($_GET['cat']) : '';

// ---- Fallback Data (If DB is empty or not connected) ----
$FCats = [
    'dry-fruits' => ['name' => 'Dry Fruits & Nuts', 'subs' => ['Almonds', 'Cashews', 'Raisins', 'Pistachios']],
    'whole-spices' => ['name' => 'Whole Spices', 'subs' => ['Red Chilli', 'Cardamom', 'Black Pepper', 'Cloves']],
    'oil-seeds' => ['name' => 'Oil Seeds', 'subs' => ['Cumin Seeds', 'Coriander Seeds', 'Sesame Seeds']]
];

$FProds = [
    ['Premium California Almonds', 'dry-fruits', 'Whole kernels, moisture-controlled, packed for wholesale.', 'photo-1508061253366-f7da158b6d46', 'almonds'],
    ['White Cashew Kernels W320', 'dry-fruits', 'Uniformly graded ivory kernels in 10 kg and 20 kg tins.', 'photo-1563292769-4e05b684851a', 'cashews'],
    ['Guntur Dry Red Chilli', 'whole-spices', 'High-pungency, deep-colour chillies for global spice processors.', 'photo-1588252303782-cb80119abd6d', 'red-chilli'],
    ['Bold Green Cardamom 8mm+', 'whole-spices', 'Aromatic deep-green pods sourced direct from estates.', 'photo-1596040033229-a9821ebd058d', 'cardamom'],
    ['Golden Seedless Raisins', 'dry-fruits', 'Double-cleaned, low-moisture raisins for bakery & confectionery.', 'photo-1596591606975-97ee5cef3a1e', 'raisins'],
    ['Malabar Black Pepper', 'whole-spices', 'Machine-cleaned, high-piperine peppercorns meeting ASTA norms.', 'photo-1532336414038-cf19250c5757', 'black-pepper'],
    ['Sortex Cumin Seeds', 'oil-seeds', '99% pure Sortex-cleaned seeds in export-grade packaging.', 'photo-1615485290382-441e4d049cb5', 'cumin'],
    ['Kashmiri Mongra Saffron', 'whole-spices', 'ISO 3632 Grade I strands in acrylic wholesale packaging.', 'photo-1509316975850-ff9c5deb0cd9', 'saffron'],
    ['Whole Coriander Seeds', 'oil-seeds', 'Premium green coriander seeds with high volatile oil content.', 'photo-1596040033229-a9821ebd058d', 'coriander'],
];

// ---- Fetch Data Logic ----
$categories = [];
$products = [];
$total_pages = 3; // Simulated for fallback

if ($ok) {
    // Check if tables exist, otherwise use fallback
    $table_check = q("SHOW TABLES LIKE 'products'");
    if (count($table_check) > 0) {
        // Fetch Categories
        $db_cats = q("SELECT * FROM categories WHERE status=1");
        foreach($db_cats as $c) {
            $subs = q("SELECT * FROM subcategories WHERE cat_id=".$c['id']." AND status=1");
            $categories[$c['slug_url']] = [
                'name' => $c['categories'],
                'subs' => array_column($subs, 'subcat_name', 'slug')
            ];
        }
        
        // Fetch Products with Search & Pagination
        $where = "WHERE is_disabled = 0";
        if($search_query) $where .= " AND pro_name LIKE '%" . $conn->real_escape_string($search_query) . "%'";
        if($cat_filter) $where .= " AND category_slug = '" . $conn->real_escape_string($cat_filter) . "'";
        
        $total_items = q("SELECT COUNT(id) as c FROM products $where")[0]['c'] ?? 0;
        $total_pages = ceil($total_items / $limit);
        
        $db_prods = q("SELECT * FROM products $where ORDER BY id DESC LIMIT $limit OFFSET $offset");
        foreach($db_prods as $p) {
            $f = preg_split('/[,|;"]+/', trim(stripslashes((string)$p['pro_img']), '[]" '))[0];
            $products[] = [
                txt($p['pro_name']), 
                $p['category_slug'] ?? 'Export Grade', 
                txt($p['short_desc']), 
                img($f, 'products', un('photo-1508061253366-f7da158b6d46', 700)),
                $p['slug_url']
            ];
        }
    }
}

// Apply Fallbacks if DB is empty
if(empty($categories)) $categories = $FCats;
if(empty($products)) {
    // Simple array filtering for fallback
    $filtered = $FProds;
    if($search_query) {
        $filtered = array_filter($filtered, function($p) use ($search_query) {
            return stripos($p[0], $search_query) !== false;
        });
    }
    if($cat_filter) {
        $filtered = array_filter($filtered, function($p) use ($cat_filter) {
            return $p[1] === $cat_filter;
        });
    }
    $total_items = count($filtered);
    $total_pages = ceil($total_items / $limit);
    $products = array_slice($filtered, $offset, $limit);
}

$nav = ['index.php#home' => 'Home', 'about.php' => 'About', 'products.php' => 'Products', 'index.php#services' => 'Services', 'index.php#blogs' => 'Insights'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Export Products | Noor Al Hiba Trading LLC</title>
    <link rel="icon" href="1.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Marcellus&family=Reem+Kufi:wght@500;600&display=swap"
        rel="stylesheet">
    <script>
    document.documentElement.classList.add('js')
    </script>
    <style>
    /* Base styles from index.php */
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
    }

    * {
        box-sizing: border-box;
        margin: 0
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font: 400 1rem/1.7 'Hanken Grotesk', system-ui, sans-serif;
        color: var(--ink);
        background: var(--paper);
        overflow-x: hidden
    }

    img {
        max-width: 100%;
        display: block
    }

    a {
        color: inherit;
        text-decoration: none
    }

    ul {
        list-style: none;
        padding: 0
    }

    h1,
    h2,
    h3 {
        font-family: Marcellus, Georgia, serif;
        font-weight: 400;
        line-height: 1.15
    }

    .wrap {
        width: min(1180px, 92%);
        margin: auto
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
    }

    .btn:hover {
        background: var(--ink);
        border-color: var(--ink);
        transform: translateY(-2px)
    }

    .btn.o {
        background: transparent;
        color: inherit;
        border-color: currentColor
    }

    .btn.o:hover {
        background: var(--ink);
        color: #fff;
        border-color: var(--ink)
    }

    /* Header */
    header {
        position: sticky;
        top: 0;
        z-index: 50;
        background: rgba(252, 250, 246, .93);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid transparent;
        transition: box-shadow .3s, border-color .3s
    }

    header.s {
        box-shadow: 0 8px 26px rgba(18, 35, 42, .07);
        border-color: var(--line)
    }

    .nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 78px;
        gap: 22px
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--bronze)
    }

    .brand b {
        display: block;
        font: 400 1.2rem/1 Marcellus, serif;
        letter-spacing: .09em;
        color: var(--ink)
    }

    .brand small {
        font-size: .64rem;
        letter-spacing: .32em;
        font-weight: 600
    }

    nav ul {
        display: flex;
        gap: 30px
    }

    nav a:not(.btn) {
        font-size: .92rem;
        font-weight: 500;
        position: relative;
        padding: 6px 0
    }

    nav a:not(.btn)::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        height: 1.5px;
        width: 0;
        background: var(--gold);
        transition: width .35s var(--e)
    }

    nav a:not(.btn):hover::after {
        width: 100%
    }

    /* Inner Hero */
    .inner-hero {
        background: var(--ink2);
        color: #fff;
        padding: clamp(50px, 8vw, 80px) 0;
        text-align: center;
        border-bottom: 3px solid var(--gold);
        position: relative;
    }

    .inner-hero h1 {
        font-size: clamp(2rem, 4vw, 3.2rem);
        margin-bottom: 10px;
    }

    .inner-hero p {
        color: rgba(255, 255, 255, 0.8);
    }

    /* Page Layout */
    .prod-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 40px;
        padding: clamp(40px, 6vw, 80px) 0;
        align-items: start;
    }

    /* Left Sidebar */
    .sidebar {
        position: sticky;
        top: 100px;
    }

    .sb-widget {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r);
        padding: 24px;
        margin-bottom: 30px;
    }

    .sb-widget h3 {
        font-size: 1.15rem;
        margin-bottom: 16px;
        color: var(--ink);
        border-bottom: 1px solid var(--line);
        padding-bottom: 10px;
    }

    /* Search Bar */
    .search-form {
        display: flex;
        gap: 8px;
    }

    .search-form input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid var(--line);
        border-radius: var(--r);
        font: inherit;
        font-size: 0.9rem;
        outline: none;
    }

    .search-form input:focus {
        border-color: var(--gold);
    }

    .search-form button {
        background: var(--bronze);
        border: none;
        color: #fff;
        padding: 0 16px;
        border-radius: var(--r);
        cursor: pointer;
        transition: background 0.3s;
    }

    .search-form button:hover {
        background: var(--ink);
    }

    /* Category List */
    .cat-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .cat-list>li {
        margin-bottom: 12px;
    }

    .cat-list>li>a {
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--ink);
        display: block;
        padding-bottom: 6px;
        transition: color 0.2s;
    }

    .cat-list>li>a:hover {
        color: var(--bronze);
    }

    .cat-list>li>a.active {
        color: var(--bronze);
    }

    .sub-list {
        list-style: none;
        padding-left: 14px;
        border-left: 2px solid var(--line);
        margin-top: 6px;
        margin-bottom: 10px;
    }

    .sub-list li a {
        font-size: 0.88rem;
        color: var(--mut);
        display: block;
        padding: 4px 0;
        transition: color 0.2s;
    }

    .sub-list li a:hover {
        color: var(--bronze);
    }

    /* RFQ Banner (Dynamic Number) */
    .rfq-banner {
        background: var(--ink2);
        color: #fff;
        text-align: center;
        border-radius: var(--r);
        padding: 30px 20px;
        border-bottom: 4px solid var(--gold);
    }

    .rfq-banner h4 {
        font-family: 'Hanken Grotesk', sans-serif;
        font-size: 1.2rem;
        margin-bottom: 10px;
        color: var(--gold);
    }

    .rfq-banner p {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 20px;
    }

    .rfq-banner .btn {
        width: 100%;
        justify-content: center;
        background: #25D366;
        border-color: #25D366;
        color: #fff;
    }

    .rfq-banner .btn:hover {
        background: #128C7E;
        border-color: #128C7E;
    }

    /* Right Column - Product Grid */
    .pg {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px;
    }

    .pc {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r);
        padding: 14px 14px 22px;
        display: flex;
        flex-direction: column;
        transition: transform 0.4s var(--e), box-shadow 0.4s;
    }

    .pc:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
    }

    .pc .im {
        overflow: hidden;
        border-radius: 999px 999px 6px 6px;
        position: relative;
    }

    .pc img {
        width: 100%;
        aspect-ratio: 4/4.4;
        object-fit: cover;
        transition: transform .9s var(--e);
    }

    .pc:hover img {
        transform: scale(1.07);
    }

    .pc small {
        display: block;
        margin: 18px 6px 4px;
        color: var(--bronze);
        font-weight: 600;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pc h3 {
        font-size: 1.22rem;
        margin: 0 6px 8px;
        color: var(--ink);
    }

    .pc p {
        font-size: .88rem;
        color: var(--mut);
        margin: 0 6px 16px;
        line-height: 1.6;
    }

    .pc .view-btn {
        margin: auto 6px 0;
        align-self: flex-start;
        background: 0;
        border: 0;
        border-bottom: 1.5px solid var(--gold);
        padding: 2px 0;
        font: 600 .9rem/1.4 'Hanken Grotesk', sans-serif;
        color: var(--ink);
        cursor: pointer;
        transition: color .3s, padding .3s;
        display: inline-block;
    }

    .pc:hover .view-btn {
        color: var(--bronze);
        padding-right: 8px;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 50px;
    }

    .pagination a,
    .pagination span {
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-weight: 600;
        font-size: 0.9rem;
        transition: 0.3s;
    }

    .pagination a {
        border: 1px solid var(--line);
        color: var(--ink);
        background: #fff;
    }

    .pagination a:hover {
        border-color: var(--bronze);
        color: var(--bronze);
    }

    .pagination .active {
        background: var(--bronze);
        color: #fff;
        border-color: var(--bronze);
    }

    .pagination .nav-arr {
        width: auto;
        padding: 0 16px;
        border-radius: 999px;
    }

    /* Animations */
    .js .rv {
        opacity: 0;
        transform: translateY(22px);
        transition: opacity .9s var(--e), transform .9s var(--e)
    }

    .js .rv.in {
        opacity: 1;
        transform: none
    }

    /* Footer */
    footer {
        padding: 0;
        background: var(--ink2);
        color: #fff;
        position: relative;
    }

    footer::before {
        content: '';
        position: absolute;
        inset: 0;
        background: var(--lat);
        opacity: .08;
        pointer-events: none
    }

    footer .wrap {
        padding: clamp(48px, 7vw, 80px) 0 28px;
        position: relative;
    }

    .fg {
        display: grid;
        grid-template-columns: 1.4fr .8fr 1.1fr 1.1fr;
        gap: 36px;
        padding-bottom: 36px;
        border-bottom: 1px solid rgba(255, 255, 255, .12)
    }

    footer .brand b {
        color: #fff
    }

    footer p,
    footer li {
        font-size: .9rem;
        color: rgba(255, 255, 255, .7);
        margin-bottom: 8px
    }

    footer p {
        margin-top: 16px
    }

    footer h4 {
        font: 400 1.15rem Marcellus, serif;
        color: var(--gold);
        margin-bottom: 16px
    }

    .fb {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        padding-top: 24px;
        font-size: .84rem;
        color: rgba(255, 255, 255, .55)
    }

    @media(max-width:960px) {
        .prod-layout {
            grid-template-columns: 1fr;
        }

        .sidebar {
            position: static;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .sb-widget {
            margin-bottom: 0;
        }

        .tg {
            display: block
        }

        nav {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--paper);
            padding: 0 4%;
            max-height: 0;
            overflow: hidden;
            transition: max-height .45s var(--e);
            border-bottom: 1px solid var(--line)
        }

        nav.o {
            max-height: 520px
        }

        nav ul {
            flex-direction: column;
            gap: 0;
            padding-top: 8px
        }

        nav li a:not(.btn) {
            display: block;
            padding: 14px 0;
            border-bottom: 1px solid var(--line)
        }

        nav .btn {
            margin: 18px 0
        }

        .fg {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media(max-width:600px) {
        .sidebar {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .fg {
            grid-template-columns: 1fr;
        }

        .brand small {
            display: none
        }
    }
    </style>
</head>

<body>

    <!-- Header -->
    <?php 
    
    include 'includes/header.php'; 
    $pageTitle = 'Export Catalogue';
    
    include 'includes/breadcrumb.php';
    ?>

    <!-- Product Section -->
    <div class="wrap prod-layout">

        <!-- LEFT COLUMN: Sidebar -->
        <aside class="sidebar">

            <!-- Search Widget -->
            <div class="sb-widget rv">
                <h3>Search Products</h3>
                <form class="search-form" method="GET" action="products.php">
                    <input type="text" name="q" placeholder="E.g. Cashew W320..." value="<?= e($search_query) ?>">
                    <button type="submit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Categories Widget -->
            <div class="sb-widget rv">
                <h3>Categories</h3>
                <ul class="cat-list">
                    <li><a href="products.php" class="<?= $cat_filter == '' ? 'active' : '' ?>">All Products</a></li>
                    <?php foreach($categories as $slug => $cData): ?>
                    <li>
                        <a href="products.php?cat=<?= e($slug) ?>" class="<?= $cat_filter == $slug ? 'active' : '' ?>">
                            <?= e($cData['name']) ?>
                        </a>
                        <!-- Subcategories -->
                        <?php if(!empty($cData['subs'])): ?>
                        <ul class="sub-list">
                            <?php foreach($cData['subs'] as $subSlug => $subName): ?>
                            <li>
                                <!-- Assuming filtering by subcat works via search or distinct param -->
                                <a href="products.php?cat=<?= e($slug) ?>&q=<?= urlencode($subName) ?>">-
                                    <?= e($subName) ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Request Quote Banner -->
            <div class="rfq-banner rv">
                <h4>Need Bulk Export?</h4>
                <p>Contact our trade desk directly for FOB/CIF pricing and shipping schedules.</p>
                <a target="_blank"
                    href="https://wa.me/<?= e($wa) ?>?text=Hello%20Noor%20Al%20Hiba,%20we%20need%20a%20bulk%20quotation."
                    class="btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.5a9.5 9.5 0 0 0-8.2 14.300L2.5 21.500l4.8-1.3A9.5 9.5 0 1 0 12 2.5z" />
                        <circle cx="8" cy="12" r="1.3" fill="#fff" />
                        <circle cx="12" cy="12" r="1.3" fill="#fff" />
                        <circle cx="16" cy="12" r="1.3" fill="#fff" />
                    </svg>
                    Chat on WhatsApp
                </a>
            </div>

        </aside>

        <!-- RIGHT COLUMN: Products Grid -->
        <main>
            <?php if(empty($products)): ?>
            <div class="rv"
                style="text-align:center; padding: 40px; background: #fff; border-radius: var(--r); border: 1px solid var(--line);">
                <h3>No products found</h3>
                <p style="color: var(--mut); margin-top: 10px;">We couldn't find any products matching your criteria.
                </p>
                <a href="products.php" class="btn" style="margin-top:20px;">Clear Filters</a>
            </div>
            <?php else: ?>
            <div class="pg">
                <?php foreach ($products as $p): ?>
                <article class="pc rv">
                    <div class="im">
                        <!-- Fallback unsplash image logic from function -->
                        <img src="<?= e(is_array($p) ? $p[3] : '') ?>" alt="<?= e($p[0]) ?>" loading="lazy">
                    </div>
                    <small><?= e(str_replace('-', ' ', $p[1])) ?></small>
                    <h3><?= e($p[0]) ?></h3>
                    <p><?= e(mb_strimwidth($p[2], 0, 90, '...')) ?></p>
                    <!-- View Details Link (assumes product-details.php exists based on standard structure) -->
                    <a href="product-details.php?slug=<?= e($p[4] ?? 'demo') ?>" class="view-btn">View Details
                        &rarr;</a>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <div class="pagination rv">
                <?php if($page > 1): ?>
                <a href="?page=<?= $page-1 ?><?= $cat_filter ? '&cat='.$cat_filter : '' ?><?= $search_query ? '&q='.$search_query : '' ?>"
                    class="nav-arr">Prev</a>
                <?php endif; ?>

                <?php for($i=1; $i<=$total_pages; $i++): ?>
                <?php if($i == $page): ?>
                <span class="active"><?= $i ?></span>
                <?php else: ?>
                <a
                    href="?page=<?= $i ?><?= $cat_filter ? '&cat='.$cat_filter : '' ?><?= $search_query ? '&q='.$search_query : '' ?>"><?= $i ?></a>
                <?php endif; ?>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                <a href="?page=<?= $page+1 ?><?= $cat_filter ? '&cat='.$cat_filter : '' ?><?= $search_query ? '&q='.$search_query : '' ?>"
                    class="nav-arr">Next</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </main>

    </div>

    <!-- Footer -->
    <footer>
        <div class="wrap">
            <div class="fg">
                <div>
                    <a class="brand" href="index.php" style="color:var(--gold)"><?= mark(44) ?><span><b>NOOR AL
                                HIBA</b><small>TRADING LLC</small></span></a>
                    <p>An international import-export house connecting spices, dry fruits, oil seeds and agro
                        commodities with buyers in Dubai, the Middle East and Africa.</p>
                </div>
                <div>
                    <h4>Navigate</h4>
                    <ul>
                        <?php foreach ($nav as $url => $l): ?>
                        <li><a href="<?= $url ?>"><?= $l ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h4>Key export lines</h4>
                    <ul>
                        <li>Whole spices: chilli, pepper</li>
                        <li>Cashew W180, W240, W320</li>
                        <li>Almonds, pistachios, raisins</li>
                        <li>Cumin and coriander seeds</li>
                        <li>Saffron and cardamom</li>
                    </ul>
                </div>
                <div>
                    <h4>Trade desk</h4>
                    <ul>
                        <li>Dubai, UAE</li>
                        <li>Azadpur, Delhi, India</li>
                        <li><a href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a></li>
                        <li>Mon to Sat, 9:00 to 19:00 (GST)</li>
                    </ul>
                </div>
            </div>
            <div class="fb">
                <span>© <?= date('Y') ?> Noor Al Hiba Trading LLC. All rights reserved.</span>
                <span class="ar">نور الهبة للتجارة ذ.م.م</span>
            </div>
        </div>
    </footer>

    <!-- Scripts for animations and mobile menu -->
    <script>
    (function() {
        var h = document.querySelector('header'),
            n = document.querySelector('nav'),
            t = document.querySelector('.tg');
        addEventListener('scroll', function() {
            h.classList.toggle('s', scrollY > 10)
        }, {
            passive: !0
        });
        t.onclick = function() {
            n.classList.toggle('o');
            t.setAttribute('aria-expanded', n.classList.contains('o'))
        };
        n.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('click', function() {
                n.classList.remove('o')
            })
        });
        var els = document.querySelectorAll('.rv');
        if (!('IntersectionObserver' in window)) {
            els.forEach(function(x) {
                x.classList.add('in')
            });
        } else {
            var io = new IntersectionObserver(function(es) {
                es.forEach(function(x) {
                    if (!x.isIntersecting) return;
                    var el = x.target;
                    el.classList.add('in');
                    io.unobserve(el);
                    setTimeout(function() {
                        el.style.transitionDelay = ''
                    }, 1500);
                })
            }, {
                threshold: .12
            });
            els.forEach(function(el, i) {
                el.style.transitionDelay = (i % 4) * 80 + 'ms';
                io.observe(el)
            });
        }
    })();
    </script>
</body>

</html>