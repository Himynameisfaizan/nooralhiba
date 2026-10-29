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