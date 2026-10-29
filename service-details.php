<?php
// 1. Database pehle connect karein (Header include karne se pehle)
require_once 'config/connect.php';

// 2. Data fetch karein
$slug = $_GET['slug_url'] ?? '';
if (isset($conn) && !$conn->connect_error && !empty($slug)) {
    $slug_safe = $conn->real_escape_string($slug);
    $res = $conn->query("SELECT * FROM blogs WHERE slug_url = '$slug_safe' LIMIT 1");
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

$slug = $_GET['slug_url'] ?? '';
$service = null;

if (isset($ok) && $ok && !empty($slug)) {
    $slug_safe = $conn->real_escape_string($slug);
    $res = @q("SELECT * FROM services WHERE slug = '$slug_safe' LIMIT 1");
    if (empty($res)) {
        $res = @q("SELECT * FROM services WHERE id = '$slug_safe' LIMIT 1");
    }
    if (!empty($res)) {
        $service = $res[0];
    }
}

// Dummy data for fallback if service not found in DB
if (!$service) {
    $service = [
        'title' => 'Sea & Air Freight Logistics',
        'icon' => 'fa-ship',
        'content' => '<p>We provide comprehensive end-to-end 20ft & 40ft FCL and LCL ocean freight solutions, as well as express air cargo from Dubai to major African ports like Mombasa, Lagos, and Durban.</p><h3>Why Choose Our Freight Solutions?</h3><ul><li>Direct shipping line contracts ensuring priority loading.</li><li>Temperature and moisture-controlled containers for agro-commodities.</li><li>Real-time tracking and dedicated export desk support.</li></ul>',
        'image' => 'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?auto=format&fit=crop&w=1000&q=80'
    ];
}

$page_title = txt($service['meta_title'] ?? $service['title'] ?? $service['service_name']);
$page_desc  = txt($service['meta_desc'] ?? '');
$s_title    = txt($service['service_name'] ?? $service['title']);
$s_icon     = $service['icon'] ?? 'fa-ship';
$bg_img     = img($service['image'] ?? $service['image_url'] ?? '', 'services', 'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?auto=format&fit=crop&w=1000&q=80');

// Fetch All Services for Sidebar Navigation
$all_services = [];
if (isset($ok) && $ok) {
    $all_services = @q("SELECT id, title, service_name, slug FROM services ORDER BY id ASC LIMIT 6");
}
if (empty($all_services)) {
    $all_services = [
        ['title' => 'Global Strategic Sourcing', 'slug' => '1'],
        ['title' => 'Sea & Air Freight Logistics', 'slug' => '2'],
        ['title' => 'Private Labeling & Packaging', 'slug' => '3'],
        ['title' => 'SGS Quality Inspection', 'slug' => '4'],
    ];
}

// Fetch Map logic (dynamic contacts table handling)
$map_iframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3610.178653926922!2d55.27218771500953!3d25.197197083896173!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f43348a67e24b%3A0xff45e502e1ceb7e2!2sBurj%20Khalifa!5e0!3m2!1sen!2sae!4v1614073347000!5m2!1sen!2sae" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';
if (isset($ok) && $ok) {
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
    <link rel="icon" href="1.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Marcellus&display=swap"
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

    /* Top Read Header */
    .sd-header {
        background: var(--sand);
        border-bottom: 1px solid var(--line);
        padding: clamp(50px, 8vw, 80px) 0;
        text-align: center;
    }

    .sd-icon-box {
        width: 80px;
        height: 80px;
        background: #fff;
        border: 1px solid var(--gold);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--bronze);
        margin-bottom: 20px;
        box-shadow: 0 10px 25px rgba(154, 107, 38, 0.15);
    }

    .sd-header h1 {
        font-size: clamp(2rem, 4vw, 3rem);
        margin-bottom: 0;
        max-width: 900px;
        margin-inline: auto;
        color: var(--ink2);
    }

    /* Layout */
    .sd-layout {
        display: grid;
        grid-template-columns: 2.2fr 1fr;
        gap: 50px;
        padding: 60px 0 80px;
    }

    @media (max-width: 991px) {
        .sd-layout {
            grid-template-columns: 1fr;
        }
    }

    /* Left Content */
    .sd-hero-img {
        width: 100%;
        aspect-ratio: 16/9;
        object-fit: cover;
        border-radius: var(--r);
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--line);
    }

    .sd-content {
        font-size: 1.08rem;
        color: var(--ink2);
        line-height: 1.8;
    }

    .sd-content h2 {
        font-size: 2rem;
        margin: 2.5rem 0 1rem;
        border-bottom: 1px solid var(--line);
        padding-bottom: 8px;
    }

    .sd-content h3 {
        font-size: 1.5rem;
        color: var(--bronze);
        margin: 1.8rem 0 1rem;
    }

    .sd-content p {
        margin-bottom: 1.2rem;
        color: var(--mut);
    }

    .sd-content ul {
        list-style: none;
        padding-left: 0;
        margin-bottom: 1.5rem;
    }

    .sd-content ul li {
        position: relative;
        padding-left: 24px;
        margin-bottom: 12px;
        color: var(--mut);
        background: #fff;
        border: 1px solid var(--line);
        padding: 12px 15px 12px 35px;
        border-radius: 4px;
    }

    .sd-content ul li::before {
        content: '\f00c';
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        position: absolute;
        left: 12px;
        top: 13px;
        color: var(--gold);
        font-size: .9rem;
    }

    /* Right Sidebar */
    .sidebar {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .widget {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r);
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.02);
    }

    .widget h4 {
        font-size: 1.3rem;
        margin-bottom: 20px;
        border-bottom: 2px solid var(--gold);
        padding-bottom: 10px;
        display: inline-block;
        color: var(--ink2);
    }

    /* Service Navigation Menu */
    .service-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .service-menu li {
        margin-bottom: 8px;
    }

    .service-menu a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        background: var(--paper);
        border: 1px solid var(--line);
        border-radius: 4px;
        color: var(--mut);
        transition: .3s;
        font-weight: 600;
        font-size: .95rem;
    }

    .service-menu a:hover,
    .service-menu a.active {
        background: var(--gold);
        color: var(--ink2);
        border-color: var(--gold);
    }

    .service-menu a i {
        font-size: .85rem;
    }

    /* Brochure Widget */
    .brochure-widget {
        background: var(--ink2);
        color: #fff;
        text-align: center;
        border: none;
    }

    .brochure-widget h4 {
        border-bottom: none;
        margin-bottom: 10px;
        color: #fff;
    }

    .brochure-widget p {
        color: rgba(255, 255, 255, .7);
        font-size: .9rem;
        margin-bottom: 20px;
    }

    .brochure-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: transparent;
        color: var(--gold);
        border: 1.5px solid var(--gold);
        padding: 12px 25px;
        border-radius: 999px;
        font-weight: 600;
        transition: .3s;
    }

    .brochure-btn:hover {
        background: var(--gold);
        color: var(--ink2);
    }

    /* Contact Help Widget */
    .help-widget {
        background: var(--sand);
        text-align: center;
    }

    .help-icon {
        width: 50px;
        height: 50px;
        background: var(--gold);
        color: var(--ink2);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        margin-bottom: 15px;
    }

    .help-widget h4 {
        border: none;
        margin-bottom: 5px;
    }

    .help-widget p {
        color: var(--mut);
        font-size: .9rem;
        margin-bottom: 15px;
    }

    .help-widget h3 {
        color: var(--bronze);
        font-family: 'Hanken Grotesk', sans-serif;
        font-weight: 700;
        margin-bottom: 0;
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
    .frm-grp textarea,
    .frm-grp select {
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
    .frm-grp textarea:focus,
    .frm-grp select:focus {
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

    /* Animations */
    .js .rv {
        opacity: 0;
        transform: translateY(22px);
        transition: opacity .9s var(--e), transform .9s var(--e);
    }

    .js .rv.in {
        opacity: 1;
        transform: none;
    }
    </style>
</head>

<body>

    <!-- Header / Banner -->
    <?php 
    $pageTitle = $s_title;
    include 'includes/breadcrumb.php';
    ?>
    <main class="wrap">
        <div class="sd-layout">
            <!-- Left Column: Content -->
            <div class="main-content">
                <img src="<?= e($bg_img) ?>" alt="<?= e($s_title) ?>" class="sd-hero-img rv">

                <article class="sd-content rv">
                    <?= $service['content'] ?? $service['description'] ?? $service['short_desc'] ?>
                </article>
            </div>

            <!-- Right Column: Sidebar -->
            <aside class="sidebar">

                <!-- Services Navigation Widget -->
                <div class="widget rv">
                    <h4>All Services</h4>
                    <ul class="service-menu">
                        <?php foreach($all_services as $as): 
                            $as_slug = $as['slug'] ?? $as['id'];
                            $as_title = $as['title'] ?? $as['service_name'];
                            $is_active = ($as_slug == $slug) ? 'active' : '';
                        ?>
                        <li><a href="service-details.php?slug=<?= urlencode($as_slug) ?>" class="<?= $is_active ?>">
                                <?= e($as_title) ?> <i class="fa fa-angle-right"></i>
                            </a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Download Brochure Widget -->
                <div class="widget brochure-widget rv">
                    <h4>Company Profile</h4>
                    <p>Download our complete company profile and export capabilities brochure.</p>
                    <a href="javascript:void(0)" class="brochure-btn" onclick="alert('Brochure PDF downloading...')"><i
                            class="fa-solid fa-file-pdf"></i> Download PDF</a>
                </div>

                <!-- Contact Help Widget -->
                <div class="widget help-widget rv">
                    <div class="help-icon"><i class="fa-solid fa-headset"></i></div>
                    <h4>How Can We Help?</h4>
                    <p>Contact our Dubai export desk for immediate assistance regarding your shipment.</p>
                    <h3>+971 50 000 0000</h3>
                </div>
            </aside>
        </div>
    </main>

    <!-- Bottom Section: Map & Inquiry Form (Reused for UI consistency but tailored text) -->
    <section class="inquiry-section" id="inquiry-section">
        <div class="wrap">
            <div class="inq-grid">
                <!-- Left: Dynamic Map -->
                <div class="map-box">
                    <?= $map_iframe ?>
                </div>

                <!-- Right: Inquiry Form -->
                <div class="form-box">
                    <h3>Request This Service</h3>
                    <p>Require assistance with <b><?= e($s_title) ?></b>? Fill out the form below and our trade team
                        will contact you shortly.</p>

                    <form action="contact.php" method="POST">
                        <div style="display:flex; gap:15px;">
                            <div class="frm-grp" style="flex:1;">
                                <label>Contact Person *</label>
                                <input type="text" name="name" required placeholder="e.g. Ahmed Al-Mazrouei">
                            </div>
                            <div class="frm-grp" style="flex:1;">
                                <label>Company *</label>
                                <input type="text" name="company" required placeholder="e.g. Gulf Horizon LLC">
                            </div>
                        </div>

                        <div class="frm-grp">
                            <label>Business Email *</label>
                            <input type="email" name="email" required placeholder="import@company.com">
                        </div>

                        <div class="frm-grp">
                            <label>Service Required</label>
                            <input type="text" name="subject" value="<?= e($s_title) ?>" readonly
                                style="background: #f0f0f0; color: #777;">
                        </div>

                        <div class="frm-grp">
                            <label>Detailed Requirement</label>
                            <textarea name="message" rows="4" required
                                placeholder="Please describe your volume, origin, destination or specific requirements..."></textarea>
                        </div>

                        <button type="submit" class="frm-btn">Send Service Request <i class="fa fa-paper-plane"
                                style="margin-left:5px;"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
    (function() {
        var els = document.querySelectorAll('.rv');
        if (!('IntersectionObserver' in window)) {
            els.forEach(function(x) {
                x.classList.add('in')
            });
        } else {
            var io = new IntersectionObserver(function(es) {
                es.forEach(function(x) {
                    if (!x.isIntersecting) return;
                    x.target.classList.add('in');
                    io.unobserve(x.target);
                    setTimeout(function() {
                        x.target.style.transitionDelay = ''
                    }, 500);
                })
            }, {
                threshold: .05
            });
            els.forEach(function(el, i) {
                el.style.transitionDelay = (i * 100) + 'ms';
                io.observe(el);
            });
        }
    })();
    </script>
</body>

</html>