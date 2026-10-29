<?php
/** NOOR AL HIBA TRADING LLC - Global Header & Corrected SEO Engine */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
mysqli_report(MYSQLI_REPORT_OFF);

// 1. Database Connection
require_once __DIR__ . '/../config/connect.php';
$ok = isset($conn) && !$conn->connect_error;

// 2. Global Functions
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
        return $n ? mb_strimwidth($s, 0, $n, '...') : $s;
    }
}
if (!function_exists('img')) {
    function img($f, $d, $fb) {
        global $site;
        $f = trim((string)$f);
        if ($f === '') return $fb;
        if (preg_match('#^https?://#', $f)) return $f;
        
        $c = ltrim($f, '/');
        $b = basename($c);
        $base_url = $site ?? "http://localhost/projects/nooralhiba/"; 
        $doc_root = $_SERVER['DOCUMENT_ROOT'] . "/projects/nooralhiba/";
        
        $possible_paths = [
            $c, "admin/" . $c, "uploads/$d/$b", "admin/uploads/$d/$b", 
            "assets/img/uploads/$d/$b", "assets/images/$b",
            "admin/assets/img/uploads/$d/$b", "admin/assets/img/uploads/$b"
        ];
        
        foreach ($possible_paths as $p) {
            if (is_file($doc_root . $p)) return $base_url . $p;
        }
        return $fb;
    }
}
if (!function_exists('un')) {
    function un($id, $w = 800) { 
        return "https://images.unsplash.com/$id?auto=format&fit=crop&w=$w&q=80"; 
    }
}
if (!function_exists('mark')) {
    function mark($w = 46) {
        return '<svg viewBox="290 55 815 710" width="' . $w . '" aria-hidden="true"><path fill="currentColor" d="M325 476l78-56v228l-78 40zM425 352l123-90v346l-123 32zM570 150l123-88v535l-123 8z"/><path fill="none" stroke="currentColor" stroke-width="7" d="M697 68l118 84v451l-118-6zM838 262l122 90v284l-120-29zM984 420l76 56v206l-76-37z"/><path fill="#D6AC56" d="M298 757C500 650 900 650 1097 757 900 700 500 700 298 757z"/></svg>';
    }
}

// 3. Dynamic Global Settings (Email, Phone) from `contacts` table
$wa = '971500000000'; 
$mail = 'export@nooralhibatrading.com'; 

if ($ok) {
    $table_check_contacts = q("SHOW TABLES LIKE 'contacts'");
    if (count($table_check_contacts) > 0) {
        $contact_res = q("SELECT email, phone FROM contacts LIMIT 1");
        if (!empty($contact_res)) {
            $mail = !empty($contact_res[0]['email']) ? $contact_res[0]['email'] : $mail;
            $wa_raw = !empty($contact_res[0]['phone']) ? $contact_res[0]['phone'] : $wa;
            $wa = preg_replace('/[^0-9]/', '', $wa_raw); 
            $disp_phone = !empty($contact_res[0]['phone']) ? $contact_res[0]['phone'] : '+971 50 000 0000';
        }
    }
}
$disp_phone = $disp_phone ?? '+971 50 000 0000';

// 4. Fetch Categories for Dropdown
$nav_cats = [];
if ($ok) {
    $db_cats = q("SELECT id, categories, slug_url FROM categories WHERE status=1");
    if (!empty($db_cats)) {
        foreach ($db_cats as $c) {
            $subs = q("SELECT categories, slug_url FROM sub_categories WHERE cate_id=".$c['id']." AND status=1");
            $nav_cats[$c['slug_url']] = ['name' => $c['categories'], 'subs' => $subs];
        }
    }
}

// 5. SMART DYNAMIC SEO & SCHEMA ENGINE (Separated `meta` and `page_schemas` tables)
$current_page_name = basename($_SERVER['PHP_SELF']);

if (!isset($meta_title) || empty($meta_title)) {
    $meta_title = 'Noor Al Hiba Trading LLC | Global Import & Export';
    $meta_desc = 'Premium export-grade spices, dry fruits, and agro commodities for Dubai, GCC, and African markets.';
    $meta_key = 'import export dubai, spices wholesale, dry fruits supplier africa';
    $schema = '';

    if ($ok) {
        // Fetch from `meta` table using page_url
        $meta_data = q("SELECT meta_title, meta_desc, meta_key FROM meta WHERE page_url = '" . $conn->real_escape_string($current_page_name) . "' LIMIT 1");
        if (!empty($meta_data)) {
            $meta_title = !empty($meta_data[0]['meta_title']) ? $meta_data[0]['meta_title'] : $meta_title;
            $meta_desc  = !empty($meta_data[0]['meta_desc']) ? $meta_data[0]['meta_desc'] : $meta_desc;
            $meta_key   = !empty($meta_data[0]['meta_key']) ? $meta_data[0]['meta_key'] : $meta_key;
        }

        // Fetch from `page_schemas` table using page_url
        $schema_data = q("SELECT schema_markup FROM page_schemas WHERE page_url = '" . $conn->real_escape_string($current_page_name) . "' LIMIT 1");
        if (!empty($schema_data)) {
            $schema = !empty($schema_data[0]['schema_markup']) ? $schema_data[0]['schema_markup'] : '';
        }
    }
}

// Fallback safety
$meta_title = $meta_title ?: 'Noor Al Hiba Trading LLC';
$meta_desc  = $meta_desc ?: '';
$meta_key   = $meta_key ?: '';
$schema     = $schema ?? '';

// Canonical URL generator
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$canonical_url = $protocol . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SEO Meta Tags -->
    <title><?= e($meta_title) ?></title>
    <meta name="description" content="<?= e($meta_desc) ?>">
    <meta name="keywords" content="<?= e($meta_key) ?>">
    <link rel="canonical" href="<?= e($canonical_url) ?>">

    <!-- JSON-LD Schema Markup -->
    <?php if (!empty($schema)) { echo $schema; } ?>

    <link rel="icon" href="1.png">

    <!-- Google Fonts & CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Marcellus&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style/include.css">
    <link rel="stylesheet" href="assets/style/style.css">
    <link rel="stylesheet" href="assets/style/about.css">
    <link rel="stylesheet" href="assets/style/product.css">
    <link rel="stylesheet" href="assets/style/blog.css">
    <script>
    document.documentElement.classList.add('js')
    </script>

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
                <a href="tel:<?= e($wa) ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                        </path>
                    </svg>
                    <?= e($disp_phone) ?>
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
            <a class="brand" href="./">
                <?= mark(46) ?>
                <span><b>NOOR AL HIBA</b><small>TRADING LLC</small></span>
            </a>
            <button class="tg" aria-label="Menu" aria-expanded="false"><i></i></button>
            <nav>
                <ul>
                    <li><a href="./">Home</a></li>
                    <li><a href="about.php">About</a></li>

                    <!-- Products Dropdown (With Hover fixes applied) -->
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
                            <li class="<?= !empty($cData['subs']) ? 'has-sub-dropdown' : '' ?>">
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

                                <!-- Inner Subcategory Dropdown -->
                                <?php if(!empty($cData['subs'])): ?>
                                <ul class="sub-dropdown-menu">
                                    <?php foreach ($cData['subs'] as $sub): ?>
                                    <li><a
                                            href="products.php?cat=<?= e($slug) ?>&q=<?= urlencode($sub['categories']) ?>"><?= e($sub['subcat_name']) ?></a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <li><a href="service.php">Services</a></li>
                    <li><a href="blog.php">Insights</a></li>
                </ul>
                <a href="contact-us.php" class="btn">Request a quote</a>
            </nav>
        </div>
    </header>