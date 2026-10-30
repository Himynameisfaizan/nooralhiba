<?php
/** NOOR AL HIBA TRADING LLC - Dynamic Product Details Page */

// 1. Database connection pehle chahiye taaki SEO variables set kar sakein
require_once __DIR__ . '/config/connect.php';
$ok = isset($conn) && !$conn->connect_error;

// ---- Fetch Dynamic Product Details ----
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$product = [];
$gallery = [];
$reviews = [];
$related = [];
$map_iframe = '';

if ($ok && $slug !== '') {
    // FIX: Clean the slug and fetch safely
    $slug_clean = strtolower(str_replace([' ', '%20'], '-', $slug));
    $slug_safe = $conn->real_escape_string($slug_clean);
    
    $db_prod = $conn->query("SELECT * FROM products WHERE slug_url = '$slug_safe' AND is_disabled = 0 LIMIT 1");
    
    if ($db_prod && $db_prod->num_rows > 0) {
        $product = $db_prod->fetch_assoc();
        
        // --- 2. SETTING SEO VARIABLES FOR HEADER.PHP ---
        $meta_title = !empty($product['meta_title']) ? $product['meta_title'] : $product['pro_name'] . " | Noor Al Hiba Trading LLC";
        $meta_desc  = !empty($product['meta_desc']) ? $product['meta_desc'] : "Buy premium quality " . $product['pro_name'] . " for wholesale and export from Dubai.";
        $meta_key   = !empty($product['meta_key']) ? $product['meta_key'] : $product['pro_name'] . " wholesale, export dubai";
        $schema     = !empty($product['schema_markup']) ? $product['schema_markup'] : '';
        
        // FIX: Removed e() and txt() here because header is not included yet. Using native PHP to strip tags.
        $pageTitle = trim(strip_tags($product['pro_name']));
        
        // 3. Fetch/Process Gallery Images (Only raw paths for now)
        $raw_imgs = preg_split('/[,|;"]+/', trim(stripslashes((string)$product['pro_img']), '[]" '));
        $raw_imgs = array_filter(array_map('trim', $raw_imgs));
        
        foreach($raw_imgs as $raw) {
            if(!empty($raw)) {
                $gallery[] = $raw;
            }
        }
        
        // 4. Fetch reviews using direct $conn query
        $table_check_rev = $conn->query("SHOW TABLES LIKE 'product_reviews'");
        if ($table_check_rev && $table_check_rev->num_rows > 0) {
            $prod_id = (int)$product['id'];
            $reviews_res = $conn->query("SELECT * FROM product_reviews WHERE product_id = $prod_id AND status = 1 ORDER BY id DESC LIMIT 5");
            if ($reviews_res) {
                while($r = $reviews_res->fetch_assoc()) {
                    $reviews[] = $r;
                }
            }
        }
        
        // 5. Fetch related products using direct $conn query
        $cat_condition = !empty($product['pro_cate']) ? "pro_cate = '" . $conn->real_escape_string($product['pro_cate']) . "'" : "1=1";
        $related_res = $conn->query("SELECT pro_name, short_desc, pro_img, slug_url FROM products WHERE $cat_condition AND id != " . $product['id'] . " AND is_disabled = 0 LIMIT 4");
        if ($related_res) {
            while($rel = $related_res->fetch_assoc()) {
                $related[] = $rel;
            }
        }
    }
}

// 6. INCLUDE HEADER - YAHAN SE HTML, HEAD AUR FUNCTIONS (e, txt, un, img) LOAD HONGE
include_once 'includes/header.php';

// Post-header image processing (Ab un() aur img() functions safe hain use karne ke liye)
if (empty($gallery) && !empty($product)) {
    $gallery_processed[] = un('photo-1563292769-4e05b684851a', 800);
} elseif (!empty($product)) {
    $gallery_processed = [];
    foreach($gallery as $raw_path) {
        $gallery_processed[] = img($raw_path, 'products', un('photo-1563292769-4e05b684851a', 800));
    }
    $gallery = $gallery_processed;
}

// Fetch Map from Settings using q()
if ($ok) {
    $table_check_set = q("SHOW TABLES LIKE 'settings'");
    if (count($table_check_set) > 0) {
        $db_set = q("SELECT map_iframe FROM settings LIMIT 1");
        if(!empty($db_set)) $map_iframe = $db_set[0]['map_iframe'];
    }
}

// ---- Fallbacks ----
if (empty($product)) {
    $pageTitle = "Product Not Found";
    $product = [
        'pro_name' => 'Product Not Found',
        'pro_cate' => 'N/A',
        'short_desc' => 'The product you are looking for does not exist or has been removed.',
        'description' => '<p>Please navigate back to our products page to explore our export catalogue.</p>'
    ];
    $gallery = [un('photo-1563292769-4e05b684851a', 800)];
}

if (empty($map_iframe)) {
    $map_iframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14008.114827184209!2d77.1630325!3d28.7040592!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d02330a133d1b%3A0xc6651da039b4b9fb!2sAzadpur%2C%20Delhi!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';
}

// ---- Handling Review Submission ----
$review_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if ($ok) {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $rating = (int)$_POST['rating'];
        $review = $conn->real_escape_string($_POST['review']);
        $prod_id = isset($product['id']) ? (int)$product['id'] : 0;
        
        $table_check = q("SHOW TABLES LIKE 'product_reviews'");
        if (count($table_check) > 0 && $prod_id > 0) {
            $sql = "INSERT INTO product_reviews (product_id, reviewer_name, reviewer_email, rating, review_text, status) VALUES ($prod_id, '$name', '$email', $rating, '$review', 0)";
            
            if ($conn->query($sql)) {
                $review_msg = "<span style='color:#155724;'>Thank you! Your review has been submitted for approval.</span>";
            } else {
                $review_msg = "<span style='color:red;'>Database Error: " . $conn->error . "</span>";
            }
        } else {
            $review_msg = "<span style='color:red;'>Error: Could not find product ID.</span>";
        }
    }
}
?>

<style>
.prod-top {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(40px, 6vw, 70px);
    align-items: start;
    padding-top: 40px;
    padding-bottom: 60px;
}

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
    transform: translateY(-2px);
}

.btn-call {
    background: var(--ink2);
    border-color: var(--ink2);
    color: #fff;
}

.btn-call:hover {
    background: var(--gold);
    border-color: var(--gold);
    transform: translateY(-2px);
}

.prod-mid {
    background: #fff;
    border-top: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
    padding: 60px 0;
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

.desc-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 2rem 0;
    background: #fff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
}

.desc-content th,
.desc-content td {
    padding: 12px 15px;
    border: 1px solid var(--line);
    text-align: left;
}

.desc-content th {
    background: var(--sand);
    font-family: Marcellus, serif;
    font-size: 1.1rem;
    color: var(--ink);
}

.desc-content h2,
.desc-content h3 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    color: var(--ink);
}

.desc-content ul {
    padding-left: 20px;
    margin: 16px 0;
}

.desc-content li {
    margin-bottom: 8px;
    list-style-type: disc;
}

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
    background: #e3f2fd;
    border-left: 4px solid var(--bronze);
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 16px;
    font-size: 0.9rem;
    font-weight: 500;
}

.prod-related {
    background: var(--paper);
    padding: 80px 0;
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

.prod-inquiry {
    background: var(--ink2);
    color: #fff;
    padding: 80px 0;
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
    justify-content: center;
}

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
}

@media(max-width:600px) {
    .pg {
        grid-template-columns: 1fr;
    }

    .action-btns .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- Include Breadcrumb -->
<?php include_once 'includes/breadcrumb.php'; ?>

<?php if(!empty($product['pro_name']) && $product['pro_name'] !== 'Product Not Found'): ?>
<section class="prod-top wrap">
    <div class="gallery-wrap">
        <div class="main-img-box" id="img-container">
            <img src="<?= e($gallery[0]) ?>" id="featured-img" alt="<?= e($product['pro_name']) ?>">
        </div>

        <?php if(count($gallery) > 1): ?>
        <div class="thumb-row">
            <?php foreach($gallery as $i => $imgUrl): ?>
            <div class="thumb <?= $i==0 ? 'active' : '' ?>" onclick="swapImg(this, '<?= e($imgUrl) ?>')">
                <img src="<?= e($imgUrl) ?>" alt="Thumbnail">
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="prod-info">
        <span class="prod-cat">Export Quality</span>
        <h1><?= e(txt($product['pro_name'])) ?></h1>
        <p class="prod-short"><?= e(txt($product['short_desc'] ?? '')) ?></p>

        <div style="background: var(--paper); padding: 20px; border-radius: var(--r); border: 1px solid var(--line);">
            <p style="margin-bottom:8px;"><strong>Availability:</strong> <span
                    style="color:#25D366; font-weight:600;">In Stock (Ready for Dispatch)</span></p>
            <p style="margin-bottom:8px;"><strong>MOQ:</strong> Check Trade Information</p>
            <p style="margin-bottom:8px;"><strong>Quality:</strong> ISO / SGS Standards</p>
            <p><strong>Shipping:</strong> FOB / CIF available for Global Markets</p>
        </div>

        <div class="action-btns">
            <a target="_blank"
                href="https://wa.me/<?= e($wa ?? '') ?>?text=Hello%20Noor%20Al%20Hiba,%20I'm%20interested%20in%20<?= urlencode($product['pro_name']) ?>"
                class="btn btn-wa">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2.5a9.5 9.5 0 0 0-8.2 14.300L2.5 21.500l4.8-1.3A9.5 9.5 0 1 0 12 2.5z" />
                    <circle cx="8" cy="12" r="1.3" fill="#fff" />
                    <circle cx="12" cy="12" r="1.3" fill="#fff" />
                    <circle cx="16" cy="12" r="1.3" fill="#fff" />
                </svg>
                WhatsApp Message
            </a>
            <a href="tel:+<?= e($wa ?? '') ?>" class="btn btn-call">
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

<section class="prod-mid">
    <div class="wrap mid-grid">
        <div class="desc-box">
            <h2>Product Overview</h2>
            <div class="desc-content">
                <?= !empty($product['description']) ? $product['description'] : '<p>Detailed description will be updated soon. This product meets all export quality parameters required for international trade.</p>' ?>
            </div>
        </div>

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
                        <span><?= e($r['reviewer_name'] ?? 'Guest') ?></span>
                        <span
                            style="color:var(--mut); font-weight:400;"><?= e(date('M d, Y', strtotime($r['created_at'] ?? 'now'))) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color:var(--mut); margin-bottom: 20px;">No reviews yet. Be the first to review!</p>
            <?php endif; ?>

            <div class="review-form">
                <h3>Write a Review</h3>
                <?php if($review_msg): ?>
                <div class="msg-alert"><?= $review_msg ?></div>
                <?php endif; ?>
                <form action="" method="POST">
                    <label>Your Name *</label>
                    <input type="text" name="name" required placeholder="John Doe">

                    <label>Your Email *</label>
                    <input type="email" name="email" required placeholder="john@example.com">

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

<?php if(!empty($related)): ?>
<section class="prod-related">
    <div class="wrap">
        <div class="sec-title">
            <h2>Related Export Commodities</h2>
            <span class="ar">منتجات ذات صلة</span>
        </div>
        <div class="pg">
            <?php foreach($related as $rp): 
                    $f_rp = preg_split('/[,|;"]+/', trim(stripslashes((string)$rp['pro_img']), '[]" '))[0];
                    $rp_img = img($f_rp, 'products', un('photo-1563292769-4e05b684851a', 700));
                ?>
            <div class="pc">
                <div class="im">
                    <a href="product-details.php?slug=<?= e($rp['slug_url']) ?>">
                        <img src="<?= e($rp_img) ?>" alt="<?= e($rp['pro_name']) ?>">
                    </a>
                </div>
                <h3><a href="product-details.php?slug=<?= e($rp['slug_url']) ?>"><?= e(txt($rp['pro_name'])) ?></a></h3>
                <p><?= e(mb_strimwidth($rp['short_desc'] ?? '', 0, 60, '...')) ?></p>
                <a href="product-details.php?slug=<?= e($rp['slug_url']) ?>" class="view-btn">View Details &rarr;</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="prod-inquiry" id="inquiry">
    <div class="wrap inq-grid">
        <div class="map-box">
            <?= $map_iframe ?>
        </div>

        <div class="inq-form-box">
            <h3>Request Bulk Quote</h3>
            <p style="color:var(--mut); margin-bottom:20px;">Interested in
                <strong><?= e(txt($product['pro_name'])) ?></strong>? Fill the form for FOB/CIF pricing.
            </p>
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

<?php else: ?>
<section class="wrap" style="text-align:center; padding: 100px 0;">
    <h2><?= e($pageTitle) ?></h2>
    <p style="margin:20px 0; color:var(--mut);">The product you are looking for is currently unavailable or the link is
        incorrect.</p>
    <a href="products.php" class="btn">View All Products</a>
</section>
<?php endif; ?>

<script>
function swapImg(el, src) {
    if (!src) return;
    document.getElementById('featured-img').src = src;
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

const container = document.getElementById('img-container');
const img = document.getElementById('featured-img');

if (container && img) {
    container.addEventListener('mousemove', (e) => {
        const rect = container.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const xPercent = (x / rect.width) * 100;
        const yPercent = (y / rect.height) * 100;

        img.style.transformOrigin = `${xPercent}% ${yPercent}%`;
        img.style.transform = 'scale(2)';
    });

    container.addEventListener('mouseleave', () => {
        img.style.transformOrigin = 'center center';
        img.style.transform = 'scale(1)';
    });
}
</script>

<?php include_once 'includes/footer.php'; ?>