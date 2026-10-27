<?php
/** NOOR AL HIBA TRADING LLC - Product Details Page */
session_start();
mysqli_report(MYSQLI_REPORT_OFF);

// Contact Variables
$wa_number = '971500000000'; 
$phone_number = '+971500000000';
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

function un($id, $w = 800) {
    return "https://images.unsplash.com/$id?auto=format&fit=crop&w=$w&q=80";
}

// Logo SVG
function mark($w = 46) {
    return '<svg viewBox="290 55 815 710" width="' . $w . '" aria-hidden="true"><path fill="currentColor" d="M325 476l78-56v228l-78 40zM425 352l123-90v346l-123 32zM570 150l123-88v535l-123 8z"/><path fill="none" stroke="currentColor" stroke-width="7" d="M697 68l118 84v451l-118-6zM838 262l122 90v284l-120-29zM984 420l76 56v206l-76-37z"/><path fill="#D6AC56" d="M298 757C500 650 900 650 1097 757 900 700 500 700 298 757z"/></svg>';
}

// ---- Handling Review Submission ----
$review_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if ($ok) {
        $name = $conn->real_escape_string($_POST['name']);
        $rating = (int)$_POST['rating'];
        $review = $conn->real_escape_string($_POST['review']);
        $slug = $conn->real_escape_string($_GET['slug'] ?? '');
        // Assuming a table `product_reviews` exists
        @$conn->query("INSERT INTO product_reviews (product_slug, user_name, rating, review_text, status) VALUES ('$slug', '$name', $rating, '$review', 0)");
        $review_msg = "Thank you! Your review has been submitted for approval.";
    } else {
        $review_msg = "Thank you! Your review will be visible shortly.";
    }
}

// ---- Fetch Product Details ----
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : 'demo-product';
$product = [];
$gallery = [];
$reviews = [];
$related = [];
$map_iframe = '';

if ($ok) {
    // 1. Try to fetch product
    $db_prod = q("SELECT * FROM products WHERE slug = '" . $conn->real_escape_string($slug) . "' LIMIT 1");
    if(!empty($db_prod)) {
        $product = $db_prod[0];
        // 2. Fetch gallery
        $gallery = q("SELECT image_path FROM product_images WHERE product_id = " . $product['id']);
        // 3. Fetch reviews
        $reviews = q("SELECT * FROM product_reviews WHERE product_slug = '" . $conn->real_escape_string($slug) . "' AND status = 1 ORDER BY id DESC LIMIT 5");
        // 4. Fetch related
        $related = q("SELECT * FROM products WHERE category_slug = '" . $product['category_slug'] . "' AND id != " . $product['id'] . " LIMIT 4");
    }
    // 5. Fetch Map
    $db_set = q("SELECT map_iframe FROM settings LIMIT 1");
    if(!empty($db_set)) $map_iframe = $db_set[0]['map_iframe'];
}

// ---- Fallbacks if DB is empty or product not found ----
if (empty($product)) {
    $product = [
        'pro_name' => 'White Cashew Kernels W320',
        'category_slug' => 'dry-fruits-nuts',
        'short_desc' => 'Premium uniformly graded ivory cashew kernels sourced directly from farms.',
        'long_desc' => '<p>Our W320 white cashew kernels represent the finest quality in the global market. Carefully harvested, sorted, and packed to ensure maximum freshness, these cashews are ideal for retail repacking, wholesale distribution, and premium food manufacturing.</p><ul><li><strong>Grade:</strong> W320 (Export Quality)</li><li><strong>Origin:</strong> India / Africa</li><li><strong>Moisture:</strong> Less than 5%</li><li><strong>Packaging:</strong> 10kg/20kg Vacuum Tins or Cartons</li></ul><p>We ensure zero adulteration and strict compliance with SGS testing and global food safety standards. Perfect for the GCC and African markets.</p>',
        'pro_img' => un('photo-1563292769-4e05b684851a', 800)
    ];
    $gallery = [
        ['image_path' => un('photo-1563292769-4e05b684851a', 800)],
        ['image_path' => un('photo-1508061253366-f7da158b6d46', 800)],
        ['image_path' => un('photo-1596591606975-97ee5cef3a1e', 800)]
    ];
    $reviews = [
        ['user_name' => 'Ahmed Al-Maktoum', 'rating' => 5, 'review_text' => 'Excellent quality cashews. The vacuum packing was perfect for our Dubai warehouse.', 'created_at' => '2025-10-12'],
        ['user_name' => 'Samuel O.', 'rating' => 4, 'review_text' => 'Very good grading. Will definitely order the W240 next time.', 'created_at' => '2025-09-28']
    ];
    $related = [
        ['pro_name' => 'Premium California Almonds', 'short_desc' => 'Whole kernels, moisture-controlled.', 'slug' => 'almonds', 'pro_img' => un('photo-1508061253366-f7da158b6d46', 600)],
        ['pro_name' => 'Golden Seedless Raisins', 'short_desc' => 'Double-cleaned, low-moisture.', 'slug' => 'raisins', 'pro_img' => un('photo-1596591606975-97ee5cef3a1e', 600)],
        ['pro_name' => 'Guntur Dry Red Chilli', 'short_desc' => 'High-pungency, deep-colour.', 'slug' => 'chilli', 'pro_img' => un('photo-1588252303782-cb80119abd6d', 600)],
        ['pro_name' => 'Bold Green Cardamom', 'short_desc' => 'Aromatic deep-green pods.', 'slug' => 'cardamom', 'pro_img' => un('photo-1596040033229-a9821ebd058d', 600)]
    ];
}
if (empty($map_iframe)) {
    $map_iframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14008.114827184209!2d77.1630325!3d28.7040592!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d02330a133d1b%3A0xc6651da039b4b9fb!2sAzadpur%2C%20Delhi!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';
}

$nav = ['index.php#home' => 'Home', 'about.php' => 'About', 'products.php' => 'Products', 'index.php#services' => 'Services', 'index.php#blogs' => 'Insights'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(txt($product['pro_name'])) ?> | Noor Al Hiba Trading LLC</title>
    <link rel="icon" href="1.png">
    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Marcellus&family=Reem+Kufi:wght@500;600&display=swap"
        rel="stylesheet">
    <script>
    document.documentElement.classList.add('js')
    </script>
    <style>
    /* Base styles matching theme */
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
    h3,
    h4 {
        font-family: Marcellus, Georgia, serif;
        font-weight: 400;
        line-height: 1.15
    }

    .wrap {
        width: min(1180px, 92%);
        margin: auto
    }

    section {
        padding: clamp(60px, 8vw, 100px) 0
    }

    .ar {
        font-family: 'Reem Kufi', serif;
        color: var(--bronze);
        font-weight: 500
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
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
        border-bottom: 1px solid var(--line);
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

    /* Breadcrumb */
    .breadcrumb {
        padding: 30px 0 10px;
        font-size: 0.9rem;
        color: var(--mut);
    }

    .breadcrumb a {
        color: var(--ink);
        font-weight: 600;
    }

    .breadcrumb a:hover {
        color: var(--bronze);
    }

    /* Top Section: Image & Details */
    .prod-top {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: clamp(40px, 6vw, 70px);
        align-items: start;
        padding-top: 20px;
    }

    /* Image Gallery with Zoom */
    .gallery-wrap {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .main-img-box {
        width: 100%;
        aspect-ratio: 4/4;
        border-radius: var(--r);
        border: 1px solid var(--line);
        overflow: hidden;
        position: relative;
        background: #fff;
        cursor: zoom-in;
    }

    .main-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.1s linear;
    }

    .thumb-row {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 5px;
        scrollbar-width: none;
    }

    .thumb-row::-webkit-scrollbar {
        display: none;
    }

    .thumb {
        width: 80px;
        height: 80px;
        border-radius: var(--r);
        border: 2px solid transparent;
        cursor: pointer;
        overflow: hidden;
        opacity: 0.7;
        transition: 0.3s;
        flex-shrink: 0;
    }

    .thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .thumb.active,
    .thumb:hover {
        opacity: 1;
        border-color: var(--gold);
    }

    /* Product Info */
    .prod-info h1 {
        font-size: clamp(2rem, 3.5vw, 2.8rem);
        color: var(--ink);
        margin-bottom: 12px;
    }

    .prod-cat {
        display: inline-block;
        background: var(--sand);
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--bronze);
        margin-bottom: 16px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .prod-short {
        font-size: 1.1rem;
        color: var(--mut);
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .action-btns {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 40px;
        border-top: 1px solid var(--line);
        padding-top: 30px;
    }

    .btn-wa {
        background: #25D366;
        border-color: #25D366;
        color: #fff;
    }

    .btn-wa:hover {
        background: #128C7E;
        border-color: #128C7E;
    }

    .btn-call {
        background: var(--ink2);
        border-color: var(--ink2);
    }

    .btn-call:hover {
        background: var(--gold);
        border-color: var(--gold);
    }

    /* Middle Section: Desc & Reviews */
    .prod-mid {
        background: #fff;
        border-top: 1px solid var(--line);
        border-bottom: 1px solid var(--line);
    }

    .mid-grid {
        display: grid;
        grid-template-columns: 6fr 4fr;
        gap: clamp(40px, 8vw, 80px);
    }

    .desc-box h2,
    .review-box h2 {
        font-size: 1.8rem;
        margin-bottom: 24px;
        color: var(--ink);
        position: relative;
        padding-bottom: 10px;
    }

    .desc-box h2::after,
    .review-box h2::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background: var(--gold);
    }

    .desc-content {
        color: var(--mut);
        line-height: 1.8;
        font-size: 1.05rem;
    }

    .desc-content ul {
        padding-left: 20px;
        margin: 16px 0;
    }

    .desc-content li {
        margin-bottom: 8px;
        list-style-type: disc;
    }

    /* Reviews System */
    .review-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-bottom: 30px;
        max-height: 400px;
        overflow-y: auto;
        padding-right: 10px;
    }

    .rev-item {
        background: var(--paper);
        padding: 20px;
        border-radius: var(--r);
        border: 1px solid var(--line);
    }

    .rev-stars {
        color: var(--gold);
        letter-spacing: 2px;
        font-size: 1.1rem;
        margin-bottom: 8px;
    }

    .rev-text {
        font-size: 0.95rem;
        color: var(--mut);
        margin-bottom: 12px;
    }

    .rev-author {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--ink);
        display: flex;
        justify-content: space-between;
    }

    .review-form {
        background: var(--sand);
        padding: 24px;
        border-radius: var(--r);
    }

    .review-form h3 {
        font-size: 1.2rem;
        margin-bottom: 16px;
    }

    .review-form label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .review-form input,
    .review-form select,
    .review-form textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--line);
        border-radius: var(--r);
        margin-bottom: 16px;
        font-family: inherit;
    }

    .review-form input:focus,
    .review-form select:focus,
    .review-form textarea:focus {
        outline: none;
        border-color: var(--gold);
    }

    .msg-alert {
        background: #d4edda;
        color: #155724;
        padding: 10px;
        border-radius: var(--r);
        margin-bottom: 16px;
        font-size: 0.9rem;
    }

    /* Related Products */
    .prod-related {
        background: var(--paper);
    }

    .sec-title {
        text-align: center;
        margin-bottom: 40px;
    }

    .sec-title h2 {
        font-size: 2.2rem;
    }

    .pg {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }

    .pc {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r);
        padding: 14px 14px 22px;
        display: flex;
        flex-direction: column;
        transition: transform 0.4s var(--e);
    }

    .pc:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
    }

    .pc .im {
        overflow: hidden;
        border-radius: 6px;
    }

    .pc img {
        width: 100%;
        aspect-ratio: 4/4;
        object-fit: cover;
        transition: transform .9s var(--e);
    }

    .pc:hover img {
        transform: scale(1.07);
    }

    .pc h3 {
        font-size: 1.1rem;
        margin: 12px 6px 6px;
    }

    .pc p {
        font-size: .85rem;
        color: var(--mut);
        margin: 0 6px 12px;
    }

    .pc .view-btn {
        margin: auto 6px 0;
        align-self: flex-start;
        font-weight: 600;
        font-size: .85rem;
        color: var(--bronze);
        border-bottom: 1px solid transparent;
        transition: 0.3s;
    }

    .pc:hover .view-btn {
        border-color: var(--bronze);
    }

    /* Inquiry Section */
    .prod-inquiry {
        background: var(--ink2);
        color: #fff;
    }

    .inq-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: clamp(30px, 6vw, 60px);
        align-items: stretch;
    }

    .map-box {
        border-radius: var(--r);
        overflow: hidden;
        min-height: 400px;
        background: #000;
    }

    .inq-form-box {
        background: #fff;
        padding: 40px;
        border-radius: var(--r);
        color: var(--ink);
    }

    .inq-form-box h3 {
        font-size: 1.8rem;
        margin-bottom: 20px;
    }

    .inq-form-box input,
    .inq-form-box textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--line);
        border-radius: var(--r);
        margin-bottom: 16px;
        background: var(--paper);
    }

    .inq-form-box input:focus,
    .inq-form-box textarea:focus {
        outline: none;
        border-color: var(--bronze);
    }

    .inq-form-box .btn {
        width: 100%;
    }

    /* Footer */
    footer {
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
        padding: 60px 0 30px;
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

    footer h4 {
        font: 400 1.15rem Marcellus, serif;
        color: var(--gold);
        margin-bottom: 16px
    }

    .fb {
        display: flex;
        justify-content: space-between;
        padding-top: 24px;
        font-size: .84rem;
        color: rgba(255, 255, 255, .55)
    }

    /* Responsive */
    @media(max-width:960px) {

        .prod-top,
        .mid-grid,
        .inq-grid {
            grid-template-columns: 1fr;
        }

        .pg {
            grid-template-columns: 1fr 1fr;
        }

        .map-box {
            min-height: 300px;
        }

        .fg {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media(max-width:600px) {

        .pg,
        .fg {
            grid-template-columns: 1fr;
        }

        .action-btns .btn {
            width: 100%;
        }
    }
    </style>
</head>

<body>

    <!-- Header -->
    <header id="top">
        <div class="wrap nav">
            <a class="brand" href="index.php">
                <?= mark(46) ?>
                <span><b>NOOR AL HIBA</b><small>TRADING LLC</small></span>
            </a>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="index.php#services">Services</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Breadcrumb -->
    <div class="wrap breadcrumb">
        <a href="index.php">Home</a> / <a href="products.php">Products</a> / <?= e(txt($product['pro_name'])) ?>
    </div>

    <!-- TOP: Image & Details -->
    <section class="prod-top wrap">
        <!-- Left: Images -->
        <div class="gallery-wrap">
            <div class="main-img-box" id="img-container">
                <img src="<?= e(is_array($gallery[0]) ? $gallery[0]['image_path'] : $product['pro_img']) ?>"
                    id="featured-img" alt="<?= e($product['pro_name']) ?>">
            </div>
            <?php if(count($gallery) > 1): ?>
            <div class="thumb-row">
                <?php foreach($gallery as $i => $g): ?>
                <div class="thumb <?= $i==0 ? 'active' : '' ?>"
                    onclick="swapImg(this, '<?= e($g['image_path'] ?? '') ?>')">
                    <img src="<?= e($g['image_path'] ?? '') ?>" alt="Thumbnail">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right: Details -->
        <div class="prod-info">
            <span class="prod-cat"><?= e(str_replace('-', ' ', $product['category_slug'])) ?></span>
            <h1><?= e(txt($product['pro_name'])) ?></h1>
            <p class="prod-short"><?= e(txt($product['short_desc'])) ?></p>

            <!-- Quick Features -->
            <div
                style="background: var(--paper); padding: 20px; border-radius: var(--r); border: 1px solid var(--line);">
                <p style="margin-bottom:8px;"><strong>Availability:</strong> <span
                        style="color:#25D366; font-weight:600;">In Stock (Ready for Dispatch)</span></p>
                <p style="margin-bottom:8px;"><strong>MOQ:</strong> LCL / 20ft FCL Container</p>
                <p style="margin-bottom:8px;"><strong>Quality:</strong> SGS Inspected & Certified</p>
                <p><strong>Shipping:</strong> FOB / CIF available for Gulf & Africa</p>
            </div>

            <!-- Buttons -->
            <div class="action-btns">
                <a target="_blank"
                    href="https://wa.me/<?= e($wa_number) ?>?text=Hello%20Noor%20Al%20Hiba,%20I'm%20interested%20in%20<?= urlencode($product['pro_name']) ?>"
                    class="btn btn-wa">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.5a9.5 9.5 0 0 0-8.2 14.300L2.5 21.500l4.8-1.3A9.5 9.5 0 1 0 12 2.5z" />
                        <circle cx="8" cy="12" r="1.3" fill="#fff" />
                        <circle cx="12" cy="12" r="1.3" fill="#fff" />
                        <circle cx="16" cy="12" r="1.3" fill="#fff" />
                    </svg>
                    WhatsApp Message
                </a>
                <a href="tel:<?= e($phone_number) ?>" class="btn btn-call">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                        </path>
                    </svg>
                    Request a Call
                </a>
            </div>
        </div>
    </section>

    <!-- MIDDLE: Description & Reviews -->
    <section class="prod-mid">
        <div class="wrap mid-grid">
            <!-- Left: Description -->
            <div class="desc-box">
                <h2>Product Overview</h2>
                <div class="desc-content">
                    <?= $product['long_desc'] ?? '<p>Detailed description will be updated soon. This product meets all export quality parameters required for international trade.</p>' ?>
                </div>
            </div>

            <!-- Right: Reviews -->
            <div class="review-box">
                <h2>Client Reviews</h2>

                <?php if(!empty($reviews)): ?>
                <div class="review-list">
                    <?php foreach($reviews as $r): ?>
                    <div class="rev-item">
                        <div class="rev-stars"><?= str_repeat('★', max(1, min(5, (int)$r['rating']))) ?><span
                                style="color:#ddd;"><?= str_repeat('★', 5 - max(1, min(5, (int)$r['rating']))) ?></span>
                        </div>
                        <p class="rev-text">"<?= e(txt($r['review_text'])) ?>"</p>
                        <div class="rev-author">
                            <span><?= e($r['user_name']) ?></span>
                            <span
                                style="color:var(--mut); font-weight:400;"><?= e(date('M d, Y', strtotime($r['created_at'] ?? 'now'))) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p style="color:var(--mut); margin-bottom: 20px;">No reviews yet. Be the first to review!</p>
                <?php endif; ?>

                <!-- Add Review Form -->
                <div class="review-form">
                    <h3>Write a Review</h3>
                    <?php if($review_msg): ?>
                    <div class="msg-alert"><?= e($review_msg) ?></div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <label>Your Name *</label>
                        <input type="text" name="name" required placeholder="John Doe">

                        <label>Rating *</label>
                        <select name="rating" required>
                            <option value="5">5 - Excellent (Export Quality)</option>
                            <option value="4">4 - Very Good</option>
                            <option value="3">3 - Average</option>
                            <option value="2">2 - Poor</option>
                            <option value="1">1 - Terrible</option>
                        </select>

                        <label>Your Review *</label>
                        <textarea name="review" rows="3" required
                            placeholder="Share your experience with our product and shipment..."></textarea>

                        <button type="submit" name="submit_review" class="btn">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- BOTTOM 1: Related Products -->
    <?php if(!empty($related)): ?>
    <section class="prod-related">
        <div class="wrap">
            <div class="sec-title">
                <h2>Related Export Commodities</h2>
                <span class="ar">منتجات ذات صلة</span>
            </div>
            <div class="pg">
                <?php foreach($related as $rp): ?>
                <div class="pc">
                    <div class="im">
                        <img src="<?= e($rp['pro_img'] ?? '') ?>" alt="<?= e($rp['pro_name']) ?>">
                    </div>
                    <h3><?= e(txt($rp['pro_name'])) ?></h3>
                    <p><?= e(mb_strimwidth($rp['short_desc'] ?? '', 0, 60, '...')) ?></p>
                    <a href="product-details.php?slug=<?= e($rp['slug'] ?? 'demo') ?>" class="view-btn">View Details
                        &rarr;</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- BOTTOM 2: Map & Inquiry Form -->
    <section class="prod-inquiry" id="inquiry">
        <div class="wrap inq-grid">
            <!-- Left: Map -->
            <div class="map-box">
                <?= $map_iframe ?>
            </div>

            <!-- Right: Form -->
            <div class="inq-form-box">
                <h3>Request Bulk Quote</h3>
                <p style="color:var(--mut); margin-bottom:20px;">Interested in
                    <strong><?= e(txt($product['pro_name'])) ?></strong>? Fill the form for FOB/CIF pricing.</p>
                <form action="contact.php" method="POST">
                    <input type="hidden" name="subject" value="Inquiry for <?= e(txt($product['pro_name'])) ?>">
                    <input type="text" name="name" placeholder="Contact Person *" required>
                    <input type="text" name="company" placeholder="Company Name *" required>
                    <input type="email" name="email" placeholder="Business Email *" required>
                    <input type="tel" name="phone" placeholder="WhatsApp / Phone *" required>
                    <textarea name="message" rows="3"
                        placeholder="Specify your requirements (Volume, Port of Destination, Packaging...) *"
                        required></textarea>
                    <button type="submit" class="btn">Get Quotation</button>
                </form>
            </div>
        </div>
    </section>

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
                    <h4>Trade desk</h4>
                    <ul>
                        <li>Dubai, UAE</li>
                        <li>Azadpur, Delhi, India</li>
                        <li><a href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="fb">
                <span>© <?= date('Y') ?> Noor Al Hiba Trading LLC. All rights reserved.</span>
            </div>
        </div>
    </footer>

    <!-- JS for Image Swap & Zoom -->
    <script>
    // Image Swap Logic
    function swapImg(el, src) {
        if (!src) return;
        document.getElementById('featured-img').src = src;
        // Update active class
        document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }

    // Image Zoom Logic
    const container = document.getElementById('img-container');
    const img = document.getElementById('featured-img');

    container.addEventListener('mousemove', (e) => {
        // Get dimensions
        const rect = container.getBoundingClientRect();
        // Calculate mouse position relative to container
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        // Convert to percentages
        const xPercent = (x / rect.width) * 100;
        const yPercent = (y / rect.height) * 100;

        // Apply transform origin and scale
        img.style.transformOrigin = `${xPercent}% ${yPercent}%`;
        img.style.transform = 'scale(2)';
    });

    container.addEventListener('mouseleave', () => {
        // Reset image on mouse leave
        img.style.transformOrigin = 'center center';
        img.style.transform = 'scale(1)';
    });
    </script>
</body>

</html>