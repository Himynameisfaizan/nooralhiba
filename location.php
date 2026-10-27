<?php
$pageTitle = 'Locations & Custom Pages | Bhagirath Enterprises';
$meta_title = 'Locations & Custom Pages | Bhagirath Enterprises';
$meta_desc = 'Explore location and information pages published by Bhagirath Enterprises.';
$meta_key = 'Bhagirath Enterprises, locations, custom pages';
$breadcrumb_title = 'Locations';

// Adjust this path if your database connection file is in a different folder.
include 'config/connect.php';

// Fetch active custom pages using the schema in custom_pages.sql.
$customPages = [];
$sql = "SELECT page_title, breadcrumb_title, slug_url, meta_title, meta_desc
        FROM custom_pages
        WHERE status = 1 AND slug_url <> ''
        ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $customPages[] = $row;
    }
}

function locationPageText($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Use the existing shared site header (it should output the opening HTML/head/body).
include 'includes/header.php';
?>

<style>
:root {
    --location-maroon: #711b3c;
    --location-pink: #df6e97;
    --location-ink: #171717;
    --location-muted: #6b7280;
    --location-bg: #f5f6f8;
}

body.location-page {
    margin: 0;
    background: var(--location-bg);
    color: #252525;
    font-family: "Inter", Arial, sans-serif;
}

.location-hero {
    position: relative;
    overflow: hidden;
    padding: 62px 0 58px;
    color: #fff;
    text-align: center;
    background:
        linear-gradient(115deg, rgba(30, 10, 19, .92), rgba(113, 27, 60, .82)),
        url("assets/images/banner/banner-bg.jpg") center/cover no-repeat,
        #711b3c;
}

.location-hero::after {
    content: "";
    position: absolute;
    inset: auto 0 0;
    height: 4px;
    background: var(--location-pink);
}

.location-hero .eyebrow {
    display: inline-block;
    margin-bottom: 12px;
    padding: 6px 13px;
    border: 1px solid rgba(255, 255, 255, .28);
    border-radius: 30px;
    color: #fff;
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: 1.3px;
    text-transform: uppercase;
}

.location-hero h1 {
    margin: 0 0 12px;
    font-size: clamp(1.8rem, 3vw, 2.65rem);
    font-weight: 800;
    line-height: 1.2;
}

.location-hero p {
    max-width: 680px;
    margin: 0 auto;
    color: rgba(255, 255, 255, .84);
    line-height: 1.7;
}

.location-breadcrumb-wrap {
    background: #fff;
    border-bottom: 1px solid #ececf0;
}

.location-breadcrumb {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 9px;
    margin: 0;
    padding: 14px 0;
    list-style: none;
    color: var(--location-muted);
    font-size: .9rem;
}

.location-breadcrumb a {
    color: var(--location-maroon);
    text-decoration: none;
}

.location-breadcrumb a:hover {
    text-decoration: underline;
}

.location-section {
    padding: 54px 0 70px;
}

.location-section-heading {
    max-width: 720px;
    margin: 0 auto 32px;
    text-align: center;
}

.location-section-heading .small-label {
    color: var(--location-maroon);
    font-size: .78rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}

.location-section-heading h2 {
    margin: 9px 0 12px;
    color: #202020;
    font-size: clamp(1.55rem, 2.4vw, 2.1rem);
    font-weight: 800;
}

.location-section-heading p {
    margin: 0;
    color: var(--location-muted);
    line-height: 1.75;
}

.location-card {
    display: flex;
    height: 100%;
    flex-direction: column;
    padding: 25px;
    border: 1px solid #ececf0;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 5px 22px rgba(25, 25, 25, .045);
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
}

.location-card:hover {
    transform: translateY(-5px);
    border-color: rgba(113, 27, 60, .28);
    box-shadow: 0 14px 32px rgba(25, 25, 25, .10);
}

.location-card-top {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 17px;
}

.location-card-icon {
    display: inline-flex;
    width: 46px;
    height: 46px;
    flex: 0 0 46px;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: rgba(113, 27, 60, .09);
    color: var(--location-maroon);
    font-size: 1.25rem;
}

.location-card h3 {
    margin: 1px 0 0;
    color: #202020;
    font-size: 1.12rem;
    font-weight: 750;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.location-card p {
    display: -webkit-box;
    overflow: hidden;
    margin: 0 0 22px;
    color: var(--location-muted);
    font-size: .94rem;
    line-height: 1.75;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
}

.location-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: auto;
    padding-top: 17px;
    border-top: 1px solid #f0f0f2;
}

.location-card-label {
    color: #858585;
    font-size: .78rem;
}

.location-card-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 42px;
    padding: 10px 16px;
    border: 0;
    border-radius: 7px;
    background: var(--location-maroon);
    color: #fff;
    font-size: .88rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: background .2s ease, transform .2s ease;
}

.location-card-button:hover {
    transform: translateY(-1px);
    background: #55132d;
    color: #fff;
}

.location-empty {
    padding: 42px 24px;
    border: 1px dashed #d5d5db;
    border-radius: 14px;
    background: #fff;
    text-align: center;
}

.location-empty h3 {
    margin-bottom: 10px;
    font-size: 1.25rem;
    font-weight: 750;
}

.location-empty p {
    margin: 0;
    color: var(--location-muted);
}

@media (max-width: 575.98px) {
    .location-hero {
        padding: 45px 0 42px;
    }

    .location-section {
        padding: 38px 0 48px;
    }

    .location-card {
        padding: 20px;
    }

    .location-card-footer {
        align-items: flex-start;
        flex-direction: column;
    }

    .location-card-button {
        width: 100%;
    }
}
</style>

<header class="location-hero">
    <div class="container">
        <span class="eyebrow">Bhagirath Enterprises</span>
        <h1>Explore Our Pages</h1>
        <p>Discover our location-focused pages, services and useful information for wholesale and commercial
            requirements.</p>
    </div>
</header>

<div class="location-breadcrumb-wrap">
    <div class="container">
        <nav aria-label="Breadcrumb">
            <ol class="location-breadcrumb">
                <li><a href="index.php">Home</a></li>
                <li aria-hidden="true">›</li>
                <li aria-current="page">Locations</li>
            </ol>
        </nav>
    </div>
</div>

<main>
    <section class="location-section">
        <div class="container">
            <div class="location-section-heading">
                <span class="small-label">Find a Page</span>
                <h2>Our Locations &amp; Information Pages</h2>
                <p>Browse the pages published by Bhagirath Enterprises. Select a page to open its complete details in a
                    new tab.</p>
            </div>

            <?php if (!empty($customPages)): ?>
            <div class="row g-4">
                <?php foreach ($customPages as $customPage):
                        $cardTitle = trim((string)($customPage['breadcrumb_title'] ?? ''));
                        if ($cardTitle === '') {
                            $cardTitle = trim((string)($customPage['page_title'] ?? ''));
                        }
                        if ($cardTitle === '') {
                            $cardTitle = 'Explore Page';
                        }

                        $description = trim(strip_tags((string)($customPage['meta_desc'] ?? '')));
                        if ($description === '') {
                            $description = 'Explore details and information from Bhagirath Enterprises.';
                        }

                        $slug = trim((string)($customPage['slug_url'] ?? ''));
                        $pageUrl = 'page.php?slug=' . rawurlencode($slug);
                    ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="location-card">
                        <div class="location-card-top">
                            <div class="location-card-icon" aria-hidden="true">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z" />
                                    <path d="M14 3v6h6M8 13h8M8 17h8" />
                                </svg>
                            </div>
                            <h3><?= locationPageText($cardTitle); ?></h3>
                        </div>

                        <p><?= locationPageText($description); ?></p>

                        <div class="location-card-footer">
                            <span class="location-card-label">Bhagirath Enterprises</span>
                            <a class="location-card-button" href="<?= locationPageText($pageUrl); ?>" target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Open <?= locationPageText($cardTitle); ?> in a new tab">
                                View Page
                                <span aria-hidden="true">↗</span>
                            </a>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="location-empty">
                <h3>No pages available yet</h3>
                <p>Published custom pages will appear here automatically.</p>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>