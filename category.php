<?php
/** NOOR AL HIBA TRADING LLC - Fixed Dynamic Category Page */

// 1. Database Connect (Header se pehle SEO aur data nikalne ke liye)
require_once __DIR__ . '/config/connect.php';
$ok = isset($conn) && !$conn->connect_error;

// FIX: Changed variable name from $slug to $current_slug to prevent conflict with header.php
$current_slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$cat_name = 'Products';

// ---- Pagination & Filters ----
$limit = 9; // Ek page par 9 products (3 per row)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

$products = [];
$total_pages = 1;
$categories_list = [];

if ($ok && !empty($current_slug)) {
    // DB COLUMN USE KIYA HAI: 'slug_url'
    $slug_safe = $conn->real_escape_string($current_slug);
    
    // A. Fetch Category Details & SEO
    $cat_res = $conn->query("SELECT * FROM categories WHERE slug_url = '$slug_safe' AND status = 1 LIMIT 1");
    
    if ($cat_res && $cat_res->num_rows > 0) {
        $cat_data = $cat_res->fetch_assoc();
        $cat_name = $cat_data['categories']; // Table mein column 'categories' hai
        $cate_id  = $cat_data['cate_id'];    // Product match karne ke liye cate_id
        
        // SETTING DYNAMIC SEO FOR HEADER
        $meta_title = !empty($cat_data['meta_title']) ? $cat_data['meta_title'] : $cat_name . " | Noor Al Hiba Trading LLC";
        $meta_desc  = !empty($cat_data['meta_desc']) ? $cat_data['meta_desc'] : "Buy premium quality " . $cat_name . " for wholesale and export from Dubai.";
        $meta_key   = !empty($cat_data['meta_key']) ? $cat_data['meta_key'] : $cat_name . " wholesale, export";
        $schema     = !empty($cat_data['schema_markup']) ? $cat_data['schema_markup'] : '';
        
        // B. Fetch Products (Using 'pro_cate' relation)
        $where = "WHERE is_disabled = 0 AND pro_cate = '$cate_id'";
        if ($search_query) {
            $where .= " AND pro_name LIKE '%" . $conn->real_escape_string($search_query) . "%'";
        }
        
        // Total Count for Pagination
        $count_res = $conn->query("SELECT COUNT(id) as c FROM products $where");
        if ($count_res) {
            $total_items = $count_res->fetch_assoc()['c'] ?? 0;
            $total_pages = ceil($total_items / $limit);
        }
        
        // Fetch Products List
        $db_prods = $conn->query("SELECT pro_name, short_desc, pro_img, slug_url FROM products $where ORDER BY id DESC LIMIT $limit OFFSET $offset");
        if ($db_prods && $db_prods->num_rows > 0) {
            while ($p = $db_prods->fetch_assoc()) {
                $f = preg_split('/[,|;"]+/', trim(stripslashes((string)$p['pro_img']), '[]" '))[0];
                $products[] = [
                    'name' => trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string)$p['pro_name'])))),
                    'desc' => trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string)$p['short_desc'])))),
                    'img' => $f,
                    'slug' => $p['slug_url']
                ];
            }
        }
    } else {
        // Agar slug match nahi hua to redirect
        header("Location: products.php");
        exit;
    }
    
    // C. Fetch all categories for sidebar
    $db_all_cats = $conn->query("SELECT categories as category_name, slug_url as slug FROM categories WHERE status=1");
    if ($db_all_cats) {
        while ($c = $db_all_cats->fetch_assoc()) {
            $categories_list[] = $c;
        }
    }
}

// 2. INCLUDE HEADER
include_once 'includes/header.php';

// Set Breadcrumb Title
$pageTitle = $cat_name; 
if ($search_query) {
    $pageTitle = "Search: " . e($search_query);
}
?>

<style>
/* Layout: 1fr (Products Left) aur 280px (Sidebar Right) */
.cat-layout {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 40px;
    padding: clamp(40px, 6vw, 80px) 0;
    align-items: start;
}

/* 3 Products Per Row Grid */
.pg {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

/* Product Card */
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
    border-color: var(--gold);
}

.pc .im {
    overflow: hidden;
    border-radius: 4px;
    margin-bottom: 15px;
    position: relative;
    background: var(--paper);
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

.pc small {
    display: block;
    margin-bottom: 8px;
    color: var(--bronze);
    font-weight: 600;
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pc h3 {
    font-size: 1.25rem;
    margin-bottom: 8px;
    color: var(--ink);
    line-height: 1.3;
}

.pc p {
    font-size: .88rem;
    color: var(--mut);
    margin-bottom: 16px;
    line-height: 1.6;
}

.pc .view-btn {
    margin-top: auto;
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

/* Sidebar (Right side) */
.sidebar {
    position: sticky;
    top: 100px;
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.sb-widget {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--r);
    padding: 24px;
}

.sb-widget h3 {
    font-size: 1.15rem;
    margin-bottom: 16px;
    color: var(--ink);
    border-bottom: 1px solid var(--line);
    padding-bottom: 10px;
}

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
    background: var(--paper);
}

.search-form input:focus {
    border-color: var(--gold);
}

.search-form button {
    background: var(--ink);
    border: none;
    color: #fff;
    padding: 0 16px;
    border-radius: var(--r);
    cursor: pointer;
    transition: background 0.3s;
}

.search-form button:hover {
    background: var(--bronze);
}

.cat-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.cat-list>li {
    margin-bottom: 10px;
}

.cat-list>li>a {
    display: block;
    padding: 10px 15px;
    border: 1px solid var(--line);
    border-radius: 4px;
    font-weight: 500;
    font-size: 0.95rem;
    color: var(--mut);
    transition: .3s;
}

.cat-list>li>a:hover,
.cat-list>li>a.active {
    background: var(--sand);
    color: var(--ink);
    border-color: var(--gold);
}

/* RFQ Banner */
.rfq-banner {
    background: var(--ink2);
    color: #fff;
    text-align: center;
    border-radius: var(--r);
    padding: 30px 20px;
    border-bottom: 4px solid var(--gold);
}

.rfq-banner h4 {
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

@media(max-width: 960px) {

    /* Mobile view: Layout switch */
    .cat-layout {
        grid-template-columns: 1fr;
    }

    .sidebar {
        display: grid;
        grid-template-columns: 1fr 1fr;
        position: static;
    }

    .pg {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media(max-width: 600px) {
    .sidebar {
        grid-template-columns: 1fr;
    }

    .pg {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- 3. BREADCRUMB -->
<?php include_once 'includes/breadcrumb.php'; ?>

<!-- 4. PAGE CONTENT -->
<div class="wrap cat-layout">

    <!-- LEFT COLUMN: Products Grid -->
    <main>
        <?php if(empty($products)): ?>
        <div class="rv"
            style="text-align:center; padding: 60px; background: #fff; border-radius: var(--r); border: 1px solid var(--line);">
            <i class="fa-solid fa-box-open mb-3" style="font-size: 3rem; color: var(--line);"></i>
            <h3>No products found</h3>
            <p style="color: var(--mut); margin-top: 10px;">We couldn't find any products in this category matching your
                search.</p>
            <a href="category.php?slug=<?= e($current_slug) ?>" class="btn" style="margin-top:20px;">Clear Search</a>
        </div>
        <?php else: ?>
        <div class="pg">
            <?php foreach ($products as $p): 
                    $img_url = img($p['img'], 'products', un('photo-1508061253366-f7da158b6d46', 700));
                ?>
            <article class="pc rv">
                <div class="im">
                    <a href="product-details.php?slug=<?= e($p['slug']) ?>">
                        <img src="<?= e($img_url) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
                    </a>
                </div>
                <small>Export Grade</small>
                <h3><a href="product-details.php?slug=<?= e($p['slug']) ?>"><?= e($p['name']) ?></a></h3>
                <p><?= e(mb_strimwidth($p['desc'], 0, 85, '...')) ?></p>
                <a href="product-details.php?slug=<?= e($p['slug']) ?>" class="view-btn">View Details &rarr;</a>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <div class="pagination rv">
            <?php if($page > 1): ?>
            <a href="?slug=<?= e($current_slug) ?>&page=<?= $page-1 ?><?= $search_query ? '&q='.urlencode($search_query) : '' ?>"
                class="nav-arr">Prev</a>
            <?php endif; ?>

            <?php for($i=1; $i<=$total_pages; $i++): ?>
            <?php if($i == $page): ?>
            <span class="active"><?= $i ?></span>
            <?php else: ?>
            <a
                href="?slug=<?= e($current_slug) ?>&page=<?= $i ?><?= $search_query ? '&q='.urlencode($search_query) : '' ?>"><?= $i ?></a>
            <?php endif; ?>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
            <a href="?slug=<?= e($current_slug) ?>&page=<?= $page+1 ?><?= $search_query ? '&q='.urlencode($search_query) : '' ?>"
                class="nav-arr">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </main>

    <!-- RIGHT COLUMN: Sidebar -->
    <aside class="sidebar">

        <!-- Search Widget -->
        <div class="sb-widget rv">
            <h3>Search Products</h3>
            <form class="search-form" method="GET" action="category.php">
                <input type="hidden" name="slug" value="<?= e($current_slug) ?>">
                <input type="text" name="q" placeholder="E.g. Almonds..." value="<?= e($search_query) ?>">
                <button type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Categories List Widget -->
        <div class="sb-widget rv">
            <h3>Categories</h3>
            <ul class="cat-list">
                <li><a href="products.php">All Products</a></li>
                <?php foreach($categories_list as $c): ?>
                <li>
                    <!-- FIX: Here we use $current_slug instead of $slug to check the active category -->
                    <a href="category.php?slug=<?= e($c['slug']) ?>"
                        class="<?= $current_slug == $c['slug'] ? 'active' : '' ?>">
                        <?= e($c['category_name']) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Request Quote Banner -->
        <div class="rfq-banner rv">
            <h4>Need Bulk Export?</h4>
            <p>Contact our trade desk directly for FOB/CIF pricing and shipping schedules.</p>
            <a target="_blank"
                href="https://wa.me/<?= e($wa ?? '971500000000') ?>?text=Hello%20Noor%20Al%20Hiba,%20we%20need%20a%20bulk%20quotation."
                class="btn">
                <i class="fa-brands fa-whatsapp me-2" style="font-size: 1.2rem;"></i> Chat on WhatsApp
            </a>
        </div>

    </aside>

</div>

<?php include_once 'includes/footer.php'; ?>

<!-- Animation Script -->
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
            el.style.transitionDelay = (i % 3) * 100 + 'ms';
            io.observe(el);
        });
    }
})();
</script>