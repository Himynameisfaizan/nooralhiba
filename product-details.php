<?php
/** NOOR AL HIBA TRADING LLC - Dynamic Product Details Page */

$page_title = 'Product Details | Noor Al Hiba Trading LLC';

// 1. Include Header
include_once 'includes/header.php';

// ---- Fetch Dynamic Product Details ----
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$product = [];
$gallery = [];
$reviews = [];
$related = [];
$map_iframe = '';

if ($ok && $slug !== '') {
    // FIX: Using slug_url
    $db_prod = q("SELECT * FROM products WHERE slug_url = '" . $conn->real_escape_string($slug) . "' AND is_disabled = 0 LIMIT 1");
    
    if(!empty($db_prod)) {
        $product = $db_prod[0];
        $pageTitle = e(txt($product['pro_name']));
        
        // 2. Fetch/Process Gallery Images
        $raw_imgs = preg_split('/[,|;"]+/', trim(stripslashes((string)$product['pro_img']), '[]" '));
        $raw_imgs = array_filter(array_map('trim', $raw_imgs));
        
        foreach($raw_imgs as $raw) {
            if(!empty($raw)) {
                $gallery[] = img($raw, 'products', un('photo-1563292769-4e05b684851a', 800));
            }
        }
        
        if (empty($gallery)) {
            $gallery[] = un('photo-1563292769-4e05b684851a', 800);
        }
        
        // 3. Fetch reviews 
        // FIX: Matching your exact columns: product_id, reviewer_name, review_text
        $table_check_rev = q("SHOW TABLES LIKE 'product_reviews'");
        if (count($table_check_rev) > 0) {
            $prod_id = (int)$product['id'];
            $reviews = q("SELECT * FROM product_reviews WHERE product_id = $prod_id AND status = 1 ORDER BY id DESC LIMIT 5");
        }
        
        // 4. Fetch related products 
        // FIX: Using slug_url
        $cat_condition = isset($product['category_slug']) ? "category_slug = '" . $conn->real_escape_string($product['category_slug']) . "'" : "1=1";
        $related = q("SELECT pro_name, short_desc, pro_img, slug_url FROM products WHERE $cat_condition AND id != " . $product['id'] . " AND is_disabled = 0 LIMIT 4");
    }
}

// 5. Fetch Map from Settings
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
        'category_slug' => 'N/A',
        'short_desc' => 'The product you are looking for does not exist or has been removed.',
        'long_desc' => '<p>Please navigate back to our products page to explore our export catalogue.</p>'
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
        $email = $conn->real_escape_string($_POST['email']); // Added Email
        $rating = (int)$_POST['rating'];
        $review = $conn->real_escape_string($_POST['review']);
        $prod_id = isset($product['id']) ? (int)$product['id'] : 0;
        
        $table_check = q("SHOW TABLES LIKE 'product_reviews'");
        if (count($table_check) > 0 && $prod_id > 0) {
            // FIX: Using your exact column names: product_id, reviewer_name, reviewer_email, rating, review_text, status
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
        <span class="prod-cat"><?= e(str_replace('-', ' ', $product['category_slug'] ?? 'Products')) ?></span>
        <h1><?= e(txt($product['pro_name'])) ?></h1>
        <p class="prod-short"><?= e(txt($product['short_desc'] ?? '')) ?></p>

        <div style="background: var(--paper); padding: 20px; border-radius: var(--r); border: 1px solid var(--line);">
            <p style="margin-bottom:8px;"><strong>Availability:</strong> <span
                    style="color:#25D366; font-weight:600;">In Stock (Ready for Dispatch)</span></p>
            <p style="margin-bottom:8px;"><strong>MOQ:</strong> LCL / 20ft FCL Container</p>
            <p style="margin-bottom:8px;"><strong>Quality:</strong> SGS Inspected & Certified</p>
            <p><strong>Shipping:</strong> FOB / CIF available for Gulf & Africa</p>
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
                <?= !empty($product['long_desc']) ? $product['long_desc'] : '<p>Detailed description will be updated soon. This product meets all export quality parameters required for international trade.</p>' ?>
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
                        <!-- FIX: Using reviewer_name -->
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

                    <!-- Added Email Field for DB match -->
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