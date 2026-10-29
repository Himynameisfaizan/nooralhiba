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