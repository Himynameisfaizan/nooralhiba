<?php

// 1. Include Header
include_once 'includes/header.php';

// ---- Pagination & Filters ----
$limit = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat_filter = isset($_GET['cat']) ? trim($_GET['cat']) : '';

// Variables
$categories = [];
$products = [];
$total_pages = 1;

if ($ok) {
    // Check if tables exist
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
            // Fix for image string splitting
            $f = preg_split('/[,|;"]+/', trim(stripslashes((string)$p['pro_img']), '[]" '))[0];
            
            $products[] = [
                txt($p['pro_name']), 
                $p['category_slug'] ?? 'Export Grade', 
                txt($p['short_desc']), 
                img($f, 'products', un('photo-1508061253366-f7da158b6d46', 700)), // Image correctly passing via new header function
                $p['slug_url'] // FIX: Using 'slug_url' instead of 'slug'
            ];
        }
    }
}

// ---- Fallback Data (If DB is empty) ----
if(empty($categories)) {
    $categories = [
        'dry-fruits' => ['name' => 'Dry Fruits & Nuts', 'subs' => ['Almonds', 'Cashews', 'Raisins', 'Pistachios']],
        'whole-spices' => ['name' => 'Whole Spices', 'subs' => ['Red Chilli', 'Cardamom', 'Black Pepper', 'Cloves']],
        'oil-seeds' => ['name' => 'Oil Seeds', 'subs' => ['Cumin Seeds', 'Coriander Seeds', 'Sesame Seeds']]
    ];
}

if(empty($products) && empty($search_query) && empty($cat_filter)) {
    $FProds = [
        ['Premium California Almonds', 'dry-fruits', 'Whole kernels, moisture-controlled, packed for wholesale.', 'photo-1508061253366-f7da158b6d46', 'almonds'],
        ['White Cashew Kernels W320', 'dry-fruits', 'Uniformly graded ivory kernels in 10 kg and 20 kg tins.', 'photo-1563292769-4e05b684851a', 'cashews'],
        ['Guntur Dry Red Chilli', 'whole-spices', 'High-pungency, deep-colour chillies for global spice processors.', 'photo-1588252303782-cb80119abd6d', 'red-chilli'],
        ['Bold Green Cardamom 8mm+', 'whole-spices', 'Aromatic deep-green pods sourced direct from estates.', 'photo-1596040033229-a9821ebd058d', 'cardamom'],
        ['Golden Seedless Raisins', 'dry-fruits', 'Double-cleaned, low-moisture raisins for bakery & confectionery.', 'photo-1596591606975-97ee5cef3a1e', 'raisins'],
        ['Malabar Black Pepper', 'whole-spices', 'Machine-cleaned, high-piperine peppercorns meeting ASTA norms.', 'photo-1532336414038-cf19250c5757', 'black-pepper'],
    ];
    foreach($FProds as $fp) {
        $products[] = [$fp[0], $fp[1], $fp[2], un($fp[3], 700), $fp[4]];
    }
}
?>

<!-- Set Title for Breadcrumb -->
<?php 
$pageTitle = "Our Products"; 
if($cat_filter) {
    $pageTitle = (isset($categories[$cat_filter])) ? $categories[$cat_filter]['name'] : "Products";
}
if($search_query) {
    $pageTitle = "Search: " . e($search_query);
}
?>

<!-- Include Breadcrumb -->
<?php include_once 'includes/breadcrumb.php'; ?>

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
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
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
            <p style="color: var(--mut); margin-top: 10px;">We couldn't find any products matching your criteria.</p>
            <a href="products.php" class="btn" style="margin-top:20px;">Clear Filters</a>
        </div>
        <?php else: ?>
        <div class="pg">
            <?php foreach ($products as $p): ?>
            <article class="pc rv">
                <div class="im">
                    <a href="product-details.php?slug=<?= e($p[4] ?? 'demo') ?>">
                        <img src="<?= e(is_array($p) ? $p[3] : '') ?>" alt="<?= e($p[0]) ?>" loading="lazy">
                    </a>
                </div>
                <small><?= e(str_replace('-', ' ', $p[1])) ?></small>
                <h3><a href="product-details.php?slug=<?= e($p[4] ?? 'demo') ?>"><?= e($p[0]) ?></a></h3>
                <p><?= e(mb_strimwidth($p[2], 0, 90, '...')) ?></p>
                <a href="product-details.php?slug=<?= e($p[4] ?? 'demo') ?>" class="view-btn">View Details &rarr;</a>
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

<?php include_once 'includes/footer.php'; ?>