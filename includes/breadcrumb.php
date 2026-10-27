<?php
/** NOOR AL HIBA TRADING LLC - Dynamic Breadcrumb */
// Agar pageTitle set nahi hai, toh fallback title use hoga
$b_title = isset($pageTitle) ? $pageTitle : 'Page';
?>
<style>
.breadcrumb-section {
    background: var(--ink2);
    /* Premium dark background */
    color: #fff;
    padding: clamp(50px, 8vw, 80px) 0;
    text-align: center;
    border-bottom: 3px solid var(--gold);
    position: relative;
    overflow: hidden;
}

.breadcrumb-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: var(--lat);
    /* Pattern from theme */
    opacity: .08;
    pointer-events: none;
}

.breadcrumb-section .wrap {
    position: relative;
    z-index: 2;
}

.breadcrumb-section .page-title {
    font-family: Marcellus, Georgia, serif;
    font-size: clamp(2.2rem, 4vw, 3.2rem);
    margin-bottom: 12px;
    color: #fff;
    line-height: 1.2;
}

.breadcrumb-links {
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.breadcrumb-links a {
    color: var(--gold);
    text-decoration: none;
    transition: color 0.3s ease;
    font-weight: 500;
}

.breadcrumb-links a:hover {
    color: #fff;
}

.breadcrumb-links .sep {
    color: rgba(255, 255, 255, 0.3);
    font-size: 0.85rem;
}

.breadcrumb-links .current {
    color: rgba(255, 255, 255, 0.9);
}
</style>

<section class="breadcrumb-section">
    <div class="wrap rv">
        <h1 class="page-title"><?= htmlspecialchars($b_title, ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="breadcrumb-links">
            <a href="index.php">Home</a>
            <span class="sep">/</span>
            <span class="current"><?= htmlspecialchars($b_title, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>
</section>