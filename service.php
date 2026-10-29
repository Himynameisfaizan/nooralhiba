<?php
$page_title = 'Our Trade Services | Noor Al Hiba Trading LLC';
$page_desc = 'Comprehensive end-to-end import and export solutions, sea and air freight, procurement, and customs clearance for Dubai and Africa.';

include_once 'includes/header.php';

// ---- Pagination Logic ----
$limit = 6; // Ek page par kitni services dikhani hain
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$Services = [];
$total_pages = 1;

if (isset($ok) && $ok) {
    // Count total services
    $count_res = @q("SELECT COUNT(id) as total FROM services");
    $total_services = $count_res[0]['total'] ?? 0;
    $total_pages = ceil($total_services / $limit);

    // Fetch Services
    $db_serv = @q("SELECT * FROM services ORDER BY id ASC LIMIT $limit OFFSET $offset");
    if (!empty($db_serv)) {
        $Services = $db_serv;
    }
}

// Fallback agar database mein abhi data nahi hai
if (empty($Services) && $page == 1) {
    $Services = [
        ['id' => 1, 'title' => 'Global Strategic Sourcing', 'icon' => 'fa-globe-africa', 'description' => 'Direct farm-gate and processing-unit procurement ensuring authentic origin and competitive pricing.'],
        ['id' => 2, 'title' => 'Sea & Air Freight Logistics', 'icon' => 'fa-ship', 'description' => 'End-to-end 20ft & 40ft FCL/LCL ocean freight and express air cargo solutions.'],
        ['id' => 3, 'title' => 'Private Labeling & Packaging', 'icon' => 'fa-box-open', 'description' => 'Tailored food-grade vacuum packs, tin cartons, and PP/Jute bags with multilingual labeling.']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?></title>
    <meta name="description" content="<?= e($page_desc) ?>">
    <link rel="icon" href="1.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Marcellus&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <script>
    document.documentElement.classList.add('js')
    </script>

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
        --e: cubic-bezier(.2, .7, .2, 1);
    }

    * {
        box-sizing: border-box;
        margin: 0;
    }

    body {
        font: 400 1rem/1.7 'Hanken Grotesk', sans-serif;
        color: var(--ink);
        background: var(--paper);
        overflow-x: hidden;
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
        line-height: 1.15;
    }

    .wrap {
        width: min(1180px, 92%);
        margin: auto;
    }

    /* Dark Header Banner */
    .dk {
        position: relative;
        overflow: hidden;
        background: var(--ink2);
        color: #fff;
        padding: clamp(80px, 10vw, 120px) 0;
        text-align: center;
    }

    .dk h1 {
        font-size: clamp(2.2rem, 4vw, 3.5rem);
        margin-bottom: 15px;
        color: #fff;
    }

    .dk p {
        color: rgba(255, 255, 255, .7);
        max-width: 600px;
        margin: 0 auto;
        font-size: 1.1rem;
    }

    .dk .ar {
        display: block;
        font-size: 1.2rem;
        color: var(--gold);
        margin-bottom: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    /* Service Grid */
    .sg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        padding: 60px 0 40px;
    }

    .sv-card {
        background: #fff;
        border: 1px solid var(--line);
        border-top: 3px solid var(--gold);
        padding: 40px 30px;
        border-radius: 0 0 var(--r) var(--r);
        transition: transform 0.4s var(--e), box-shadow 0.4s var(--e);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .sv-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(18, 35, 42, .08);
    }

    .sv-icon {
        width: 70px;
        height: 70px;
        background: var(--sand);
        border: 1px solid var(--line);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: var(--bronze);
        margin-bottom: 25px;
        transition: .3s;
    }

    .sv-card:hover .sv-icon {
        background: var(--gold);
        color: var(--ink2);
        border-color: var(--gold);
    }

    .sv-title {
        font-size: 1.4rem;
        margin-bottom: 15px;
        color: var(--ink);
    }

    .sv-desc {
        color: var(--mut);
        font-size: .95rem;
        margin-bottom: 25px;
        line-height: 1.6;
        flex-grow: 1;
    }

    .sv-link {
        font-weight: 600;
        color: var(--bronze);
        font-size: .95rem;
        border-bottom: 1.5px solid transparent;
        align-self: flex-start;
        transition: .3s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .sv-card:hover .sv-link {
        border-color: var(--bronze);
        gap: 10px;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        padding-bottom: 80px;
    }

    .pagination a,
    .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid var(--line);
        color: var(--ink);
        font-weight: 600;
        transition: .3s;
    }

    .pagination a:hover {
        background: var(--sand);
        border-color: var(--gold);
        color: var(--bronze);
    }

    .pagination .active {
        background: var(--gold);
        color: var(--ink2);
        border-color: var(--gold);
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

    <!-- Header Banner -->
    <?php
    $pageTitle= "Global Trade Services"; 
    include 'includes/breadcrumb.php';?>

    <!-- Services Grid -->
    <div class="wrap">
        <div class="sg-grid">
            <?php foreach ($Services as $s): 
                $s_title = $s['title'] ?? $s['service_name'] ?? $s['name'] ?? 'Trade Service';
                $s_desc  = $s['short_desc'] ?? $s['description'] ?? $s['content'] ?? '';
                $s_icon  = $s['icon'] ?? 'fa-ship';
                $s_slug  = $s['slug_url'] ?? $s['id'] ?? '';
                $s_url   = 'service-details.php?slug=' . urlencode($s_slug);
            ?>
            <div class="sv-card rv">
                <div class="sv-icon"><i class="fa-solid <?= e($s_icon) ?>"></i></div>
                <h3 class="sv-title"><?= e($s_title) ?></h3>
                <p class="sv-desc"><?= e(mb_strimwidth(strip_tags($s_desc), 0, 140, '...')) ?></p>
                <a href="<?= e($s_url) ?>" class="sv-link">Explore Service <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <div class="pagination rv">
            <?php if($page > 1): ?>
            <a href="?page=<?= $page-1 ?>"><i class="fa fa-angle-left"></i></a>
            <?php endif; ?>

            <?php for($i=1; $i<=$total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
            <a href="?page=<?= $page+1 ?>"><i class="fa fa-angle-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

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
                threshold: .1
            });
            els.forEach(function(el, i) {
                el.style.transitionDelay = (i % 3) * 100 + 'ms';
                io.observe(el);
            });
        }
    })();
    </script>
</body>

</html>