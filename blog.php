<?php
$page_title = 'Export Guides & Trade Insights | Noor Al Hiba Trading LLC';
$page_desc = 'Read the latest market insights, export guides, and trade news for spices and dry fruits across Dubai, GCC, and Africa.';
$page_key = 'export guides, trade insights, dubai import, africa export, spice trading news';

include_once 'includes/header.php';

$limit = 9; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

$Blogs = [];
$total_pages = 1;

if (isset($ok) && $ok) {
    $where = "WHERE status = 1";
    if (!empty($search)) {
        $where .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
    }

    $count_res = q("SELECT COUNT(blog_id) as total FROM blogs $where");
    $total_blogs = $count_res[0]['total'] ?? 0;
    $total_pages = ceil($total_blogs / $limit);

    $db_blogs = q("SELECT title, slug, author, image, meta_desc, description, created_at FROM blogs $where ORDER BY created_at DESC, blog_id DESC LIMIT $limit OFFSET $offset");
    if (!empty($db_blogs)) {
        $Blogs = $db_blogs;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?></title>
    <meta name="description" content="<?= e($page_desc) ?>">
    <meta name="keywords" content="<?= e($page_key) ?>">
    <link rel="icon" href="1.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Marcellus&family=Reem+Kufi:wght@500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <script>
    document.documentElement.classList.add('js')
    </script>

    <style>
    /* CSS Variables & Core Styles */
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
        font: 400 1rem/1.7 'Hanken Grotesk', system-ui, sans-serif;
        color: var(--ink);
        background: var(--paper);
        overflow-x: hidden;
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
        font-family: Marcellus, Georgia, serif;
        font-weight: 400;
        line-height: 1.15;
    }

    .wrap {
        width: min(1180px, 92%);
        margin: auto;
    }

    .dk {
        position: relative;
        overflow: hidden;
        background: var(--ink2);
        color: #fff;
        padding: clamp(60px, 8vw, 100px) 0;
        text-align: center;
    }

    .dk h1 {
        font-size: clamp(2.2rem, 4vw, 3.5rem);
        margin-bottom: 15px;
    }

    .dk p {
        color: rgba(255, 255, 255, .7);
        max-width: 600px;
        margin: 0 auto;
        font-size: 1.1rem;
    }

    /* Blog Grid */
    .bg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 32px;
        padding: 60px 0 40px;
    }

    .bl {
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r);
        padding: 20px;
        transition: transform .4s var(--e), box-shadow .4s var(--e);
    }

    .bl:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 35px rgba(18, 35, 42, .06);
        border-color: var(--gold);
    }

    .bl .im {
        overflow: hidden;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .bl img {
        width: 100%;
        aspect-ratio: 16/10;
        object-fit: cover;
        transition: transform .9s var(--e);
    }

    .bl:hover img {
        transform: scale(1.05);
    }

    .bl small {
        color: var(--mut);
        display: flex;
        gap: 16px;
        font-size: .85rem;
        margin-bottom: 8px;
    }

    .bl h3 {
        font-size: 1.35rem;
        margin: 8px 0 12px;
    }

    .bl p {
        color: var(--mut);
        font-size: .95rem;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .bl .rd {
        margin-top: auto;
        font-weight: 600;
        color: var(--bronze);
        font-size: .95rem;
        border-bottom: 1.5px solid transparent;
        align-self: flex-start;
        transition: .3s;
    }

    .bl:hover .rd {
        border-color: var(--bronze);
        padding-right: 8px;
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
    </style>
</head>

<body>

    <section class="dk">
        <div class="wrap">
            <h1>Trade Insights & Export Guides</h1>
            <p>Expert knowledge on sourcing, grading, and exporting spices, dry fruits, and agro-commodities.</p>
            <?php if(!empty($search)): ?>
            <p style="color: var(--gold); margin-top: 10px;">Search results for: "<?= e($search) ?>"</p>
            <?php endif; ?>
        </div>
    </section>

    <div class="wrap">
        <?php if (!empty($Blogs)): ?>
        <div class="bg-grid">
            <?php foreach ($Blogs as $b):
                $bi = img($b['image'], 'blogs', un('photo-1596040033229-a9821ebd058d'));
                $ex = txt($b['meta_desc'], 140) ?: txt($b['description'], 140);
                $u = 'blog-details.php?slug=' . urlencode($b['slug']); 
            ?>
            <article class="bl">
                <a class="im" href="<?= e($u) ?>"><img src="<?= e($bi) ?>" alt="<?= e($b['title']) ?>"
                        loading="lazy"></a>
                <small><span><?= e(date('d M Y', strtotime($b['created_at'] ?: 'now'))) ?></span><span><?= e($b['author']) ?></span></small>
                <h3><a href="<?= e($u) ?>"><?= e($b['title']) ?></a></h3>
                <p><?= e($ex) ?></p>
                <a class="rd" href="<?= e($u) ?>">Read the guide &rarr;</a>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
            <a href="?page=<?= $page-1 ?><?= !empty($search)?'&q='.$search:'' ?>"><i class="fa fa-angle-left"></i></a>
            <?php endif; ?>

            <?php for($i=1; $i<=$total_pages; $i++): ?>
            <a href="?page=<?= $i ?><?= !empty($search)?'&q='.$search:'' ?>"
                class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
            <a href="?page=<?= $page+1 ?><?= !empty($search)?'&q='.$search:'' ?>"><i class="fa fa-angle-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div style="text-align:center; padding: 100px 0;">
            <h3 style="color: var(--mut);">No articles found.</h3>
            <a href="blog.php" class="rd" style="color: var(--bronze); display: inline-block; margin-top: 15px;">Clear
                Search</a>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>