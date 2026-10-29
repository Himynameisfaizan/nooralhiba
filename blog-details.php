<?php
// 1. Database pehle connect karein (Header include karne se pehle)
require_once 'config/connect.php';

// 2. Data fetch karein
$slug = $_GET['slug'] ?? '';
if (isset($conn) && !$conn->connect_error && !empty($slug)) {
    $slug_safe = $conn->real_escape_string($slug);
    $res = $conn->query("SELECT * FROM blogs WHERE slug = '$slug_safe' LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $blog = $res->fetch_assoc();
        
        // 3. SET SEO VARIABLES (Ye variables header.php read kar lega)
        $meta_title = $blog['meta_title'] ?: $blog['title'];
        $meta_desc  = $blog['meta_desc'];
        $meta_key   = $blog['meta_key'];
        $schema     = $blog['schema_markup'];
    } else {
        header("Location: blog.php");
        exit;
    }
}

// 4. AB HEADER INCLUDE KAREIN
include_once 'includes/header.php';


$slug = $_GET['slug'] ?? '';
$blog = null;

if (isset($ok) && $ok && !empty($slug)) {
    $slug_safe = $conn->real_escape_string($slug);
    // Har ek SEO element database se laya gaya hai
    $res = q("SELECT * FROM blogs WHERE slug = '$slug_safe' AND status = 1 LIMIT 1");
    if (!empty($res)) {
        $blog = $res[0];
    }
}

if (!$blog) {
    header("Location: blog.php");
    exit;
}

// ---- SEO & Meta Data ----
$page_title = txt($blog['meta_title']) ?: txt($blog['title']);
$page_desc  = txt($blog['meta_desc']);
$page_key   = txt($blog['meta_key']);
$schema     = $blog['schema_markup'] ?? ''; // Direct JSON-LD

$bg_img     = img($blog['image'], 'blogs', un('photo-1596040033229-a9821ebd058d'));
$date       = date('F d, Y', strtotime($blog['created_at'] ?: 'now'));

// Fetch Recent Posts for Sidebar
$recent_posts = [];
if (isset($ok) && $ok) {
    $recent_posts = q("SELECT title, slug, image, created_at FROM blogs WHERE status = 1 AND blog_id != {$blog['blog_id']} ORDER BY created_at DESC LIMIT 4");
}

// Fetch Map logic (dynamic contacts table handling)
$map_iframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3610.178653926922!2d55.27218771500953!3d25.197197083896173!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f43348a67e24b%3A0xff45e502e1ceb7e2!2sBurj%20Khalifa!5e0!3m2!1sen!2sae!4v1614073347000!5m2!1sen!2sae" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'; // Default Dubai map
if (isset($ok) && $ok) {
    // try to fetch dynamic map if contacts table exists
    $c_res = @q("SELECT map_iframe FROM contacts LIMIT 1");
    if (!empty($c_res) && !empty($c_res[0]['map_iframe'])) {
        $map_iframe = $c_res[0]['map_iframe'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?> | Noor Al Hiba Trading LLC</title>
    <meta name="description" content="<?= e($page_desc) ?>">
    <meta name="keywords" content="<?= e($page_key) ?>">
    <link rel="icon" href="1.png">

    <!-- Schema Markup directly from Database -->
    <?php if (!empty($schema)): ?>
    <?= $schema ?>
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Marcellus&family=Reem+Kufi:wght@500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
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
    }

    * {
        box-sizing: border-box;
        margin: 0;
    }

    body {
        font: 400 1rem/1.7 'Hanken Grotesk', sans-serif;
        color: var(--ink);
        background: var(--paper);
    }

    img {
        max-width: 100%;
        display: block;
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    h1,
    h2,
    h3,
    h4 {
        font-family: Marcellus, serif;
        font-weight: 400;
        line-height: 1.25;
        color: var(--ink);
    }

    .wrap {
        width: min(1180px, 92%);
        margin: auto;
    }

    /* Layout */
    .bd-header {
        text-align: center;
        padding: 60px 0 40px;
    }

    .bd-header h1 {
        font-size: clamp(2.2rem, 4vw, 3.2rem);
        margin-bottom: 20px;
        max-width: 900px;
        margin-inline: auto;
    }

    .bd-meta {
        display: flex;
        justify-content: center;
        gap: 20px;
        color: var(--mut);
        font-size: .9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .bd-layout {
        display: grid;
        grid-template-columns: 2.2fr 1fr;
        gap: 50px;
        padding-bottom: 80px;
        padding-top: 80px;
    }

    @media (max-width: 991px) {
        .bd-layout {
            grid-template-columns: 1fr;
        }
    }

    /* Left Content */
    .bd-hero-img {
        width: 100%;
        aspect-ratio: 16/9;
        object-fit: cover;
        border-radius: var(--r);
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .bd-content {
        font-size: 1.08rem;
        color: var(--ink2);
        line-height: 1.8;
    }

    .bd-content h2 {
        font-size: 2rem;
        margin: 2.5rem 0 1rem;
        border-bottom: 1px solid var(--line);
        padding-bottom: 8px;
    }

    .bd-content h3 {
        font-size: 1.5rem;
        color: var(--bronze);
        margin: 1.8rem 0 1rem;
    }

    .bd-content p {
        margin-bottom: 1.2rem;
        color: var(--mut);
    }

    .bd-content ul {
        list-style: none;
        padding-left: 0;
        margin-bottom: 1.5rem;
    }

    .bd-content ul li {
        position: relative;
        padding-left: 24px;
        margin-bottom: 8px;
        color: var(--mut);
    }

    .bd-content ul li::before {
        content: '';
        position: absolute;
        left: 0;
        top: .62em;
        width: 8px;
        height: 8px;
        background: var(--gold);
        transform: rotate(45deg);
    }

    .bd-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 2rem 0;
        background: #fff;
    }

    .bd-content th,
    .bd-content td {
        padding: 12px 15px;
        border: 1px solid var(--line);
        text-align: left;
    }

    .bd-content th {
        background: var(--sand);
        font-family: Marcellus, serif;
        color: var(--ink);
    }

    /* Right Sidebar */
    .sidebar {
        display: flex;
        flex-direction: column;
        gap: 35px;
    }

    .widget {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r);
        padding: 25px;
    }

    .widget h4 {
        font-size: 1.3rem;
        margin-bottom: 20px;
        border-bottom: 2px solid var(--gold);
        padding-bottom: 10px;
        display: inline-block;
    }

    .search-form {
        display: flex;
    }

    .search-form input {
        flex: 1;
        padding: 12px;
        border: 1px solid var(--line);
        border-radius: 4px 0 0 4px;
        outline: none;
        background: var(--paper);
    }

    .search-form button {
        padding: 12px 20px;
        background: var(--ink);
        color: #fff;
        border: none;
        border-radius: 0 4px 4px 0;
        cursor: pointer;
        transition: .3s;
    }

    .search-form button:hover {
        background: var(--bronze);
    }

    .recent-post {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        align-items: center;
    }

    .recent-post img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 4px;
    }

    .recent-post h5 {
        font-family: 'Hanken Grotesk', sans-serif;
        font-size: .95rem;
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .recent-post h5 a {
        transition: .3s;
    }

    .recent-post h5 a:hover {
        color: var(--bronze);
    }

    .recent-post small {
        color: var(--mut);
        font-size: .8rem;
    }

    .cat-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .cat-list li {
        margin-bottom: 10px;
    }

    .cat-list a {
        display: flex;
        justify-content: space-between;
        padding: 10px 15px;
        background: var(--paper);
        border: 1px solid var(--line);
        border-radius: 4px;
        color: var(--mut);
        transition: .3s;
        font-weight: 500;
    }

    .cat-list a:hover {
        background: var(--gold);
        color: var(--ink2);
        border-color: var(--gold);
    }

    .quote-widget {
        background: var(--ink2);
        color: #fff;
        text-align: center;
        border: none;
    }

    .quote-widget h4 {
        color: var(--gold);
        border-bottom: none;
        margin-bottom: 10px;
    }

    .quote-widget p {
        color: rgba(255, 255, 255, .7);
        font-size: .9rem;
        margin-bottom: 20px;
    }

    .quote-widget .btn {
        background: var(--gold);
        color: var(--ink2);
        padding: 10px 25px;
        border-radius: 999px;
        font-weight: 600;
        display: inline-block;
        transition: .3s;
    }

    .quote-widget .btn:hover {
        background: #fff;
    }

    /* Inquiry & Map Section */
    .inquiry-section {
        background: var(--sand);
        padding: 80px 0;
        border-top: 1px solid var(--line);
    }

    .inq-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r);
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.05);
    }

    @media (max-width: 768px) {
        .inq-grid {
            grid-template-columns: 1fr;
        }
    }

    .map-box {
        height: 100%;
        min-height: 400px;
    }

    .form-box {
        padding: 40px;
    }

    .form-box h3 {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    .form-box p {
        color: var(--mut);
        margin-bottom: 25px;
        font-size: .95rem;
    }

    .frm-grp {
        margin-bottom: 15px;
    }

    .frm-grp label {
        display: block;
        font-size: .85rem;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .frm-grp input,
    .frm-grp textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--line);
        border-radius: 4px;
        background: var(--paper);
        outline: none;
        transition: .3s;
        font-family: inherit;
    }

    .frm-grp input:focus,
    .frm-grp textarea:focus {
        border-color: var(--bronze);
        box-shadow: 0 0 0 2px rgba(214, 172, 86, .2);
    }

    .frm-btn {
        width: 100%;
        background: var(--ink);
        color: #fff;
        border: none;
        padding: 14px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        transition: .3s;
        font-size: 1rem;
    }

    .frm-btn:hover {
        background: var(--bronze);
    }
    </style>
</head>

<body>

    <?php
    $pageTitle = $blog['title'];
    include 'includes/breadcrumb.php';

    ?>

    <main class="wrap"">
        <div class=" bd-layout">
        <!-- Left Column: Content -->
        <div class="main-content">
            <img src="<?= e($bg_img) ?>" alt="<?= e($blog['title']) ?>" class="bd-hero-img">

            <article class="bd-content">
                <?= $blog['description'] ?>
            </article>
        </div>

        <!-- Right Column: Sidebar -->
        <aside class="sidebar">
            <!-- Search Widget -->
            <div class="widget">
                <h4>Search Insights</h4>
                <form action="blog.php" method="GET" class="search-form">
                    <input type="text" name="q" placeholder="Search articles..." required>
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>

            <!-- Recent Posts Widget -->
            <div class="widget">
                <h4>Recent Articles</h4>
                <?php if (!empty($recent_posts)): foreach ($recent_posts as $rp):
                            $rp_img = img($rp['image'], 'blogs', un('photo-1596040033229-a9821ebd058d', 300));
                            $rp_url = 'blog-details.php?slug=' . urlencode($rp['slug']);
                    ?>
                <div class="recent-post">
                    <a href="<?= e($rp_url) ?>"><img src="<?= e($rp_img) ?>" alt="<?= e($rp['title']) ?>"></a>
                    <div>
                        <h5><a href="<?= e($rp_url) ?>"><?= e(mb_strimwidth($rp['title'], 0, 50, '...')) ?></a></h5>
                        <small><i class="fa-regular fa-calendar text-warning me-1"></i>
                            <?= date('d M Y', strtotime($rp['created_at'])) ?></small>
                    </div>
                </div>
                <?php endforeach;
                    endif; ?>
            </div>

            <!-- Categories Widget (Dynamic Filter Link) -->
            <div class="widget">
                <h4>Product Categories</h4>
                <ul class="cat-list">
                    <li><a href="products.php?category=whole-spices">Whole Spices <i class="fa fa-angle-right"></i></a>
                    </li>
                    <li><a href="products.php?category=dry-fruits-nuts">Dry Fruits & Nuts <i
                                class="fa fa-angle-right"></i></a></li>
                    <li><a href="products.php?category=oil-seeds">Agro Seeds <i class="fa fa-angle-right"></i></a>
                    </li>
                    <li><a href="products.php?category=luxury-spices">Luxury Spices (Saffron) <i
                                class="fa fa-angle-right"></i></a></li>
                </ul>
            </div>

            <!-- Request Quote CTA Widget -->
            <div class="widget quote-widget">
                <h4>Need Bulk Export Pricing?</h4>
                <p>Get FOB / CIF quotations for Dubai and African ports within 4 hours.</p>
                <a href="#inquiry-section" class="btn">Request a Quote</a>
            </div>
        </aside>
        </div>
    </main>

    <!-- Bottom Section: Map & Inquiry Form -->
    <section class="inquiry-section" id="inquiry-section">
        <div class="wrap">
            <div class="inq-grid">
                <!-- Left: Dynamic Map -->
                <div class="map-box">
                    <?= $map_iframe ?>
                </div>

                <!-- Right: Inquiry Form -->
                <div class="form-box">
                    <h3>Request Trade Quotation</h3>
                    <p>Connect with our export desk for bulk supply & logistics.</p>

                    <form action="contact.php" method="POST">
                        <div style="display:flex; gap:15px;">
                            <div class="frm-grp" style="flex:1;">
                                <label>Your Name *</label>
                                <input type="text" name="name" required placeholder="Ahmed Al-Mazrouei">
                            </div>
                            <div class="frm-grp" style="flex:1;">
                                <label>Company *</label>
                                <input type="text" name="company" required placeholder="Gulf Horizon LLC">
                            </div>
                        </div>

                        <div class="frm-grp">
                            <label>Email Address *</label>
                            <input type="email" name="email" required placeholder="import@company.com">
                        </div>

                        <div class="frm-grp">
                            <label>Interested Product</label>
                            <input type="text" name="subject" placeholder="e.g. W240 Cashews, Guntur Chilli">
                        </div>

                        <div class="frm-grp">
                            <label>Order Requirements & Destination Port</label>
                            <textarea name="message" rows="4" required
                                placeholder="Specify grade, packing (10/25kg), and destination (e.g. Jebel Ali / Mombasa)..."></textarea>
                        </div>

                        <button type="submit" class="frm-btn">Submit Export Inquiry <i class="fa fa-paper-plane"
                                style="margin-left:5px;"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>

</html>