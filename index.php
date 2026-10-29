<?php
// 1. HEADER INCLUDE (Ye automatically DB connect karega aur SEO + Schema ke sath <head> aur Navbar print karega)
include_once 'includes/header.php';

// ---- 1. Hero & About Data ----
$B = q("SELECT banner_path FROM banners ORDER BY display_order ASC, id DESC LIMIT 1");
$hero = img($B[0]['banner_path'] ?? '', 'banners', un('photo-1596040033229-a9821ebd058d', 900));

$A = q("SELECT title, content, image_url FROM about_sections ORDER BY section_order ASC, id ASC LIMIT 1");
$about_t = txt($A[0]['title'] ?? '') ?: 'A trading partner built for the Gulf and Africa';
$about_c = txt($A[0]['content'] ?? '');
$about_i = img($A[0]['image_url'] ?? '', 'about_us', un('photo-1586528116311-ad8dd3c8310d', 1000));

// ---- 2. Products Data ----
$FP = [
    ['Premium California Almonds', 'Dry fruits & nuts', 'Whole kernels, moisture-controlled, carton or vacuum packed for GCC and African wholesale.', 'photo-1508061253366-f7da158b6d46'],
    ['White Cashew Kernels W180 / W240 / W320', 'Dry fruits & nuts', 'Uniformly graded ivory kernels in 10 kg and 20 kg tins or CO2-flushed cartons.', 'photo-1563292769-4e05b684851a'],
    ['Guntur & Byadgi Dry Red Chilli', 'Whole spices', 'High-pungency, deep-colour chillies sorted for global spice processors.', 'photo-1588252303782-cb80119abd6d'],
    ['Bold Green Cardamom 8mm+', 'Whole spices', 'Aromatic deep-green pods sourced direct from estates, ready for retail and bulk.', 'photo-1596040033229-a9821ebd058d']
];
$P = [];
if (isset($ok) && $ok) {
    $db_prods = q("SELECT pro_name, pro_img, is_disabled, slug_url FROM products WHERE is_disabled = 0 ORDER BY id DESC LIMIT 8");
    if (!empty($db_prods)) {
        foreach ($db_prods as $i => $r) {
            if (txt($r['pro_name']) === '') continue;
            $f = preg_split('/[,|;"]+/', trim(stripslashes((string)$r['pro_img']), '[]" '))[0];
            $P[] = [txt($r['pro_name']), 'Export grade', 'Graded and packed for GCC and African importers. Ask for FOB or CIF pricing.', img($f, 'products', un($FP[$i % 4][3], 700)), $r['slug_url'] ?? ''];
        }
    }
}
if (count($P) < 4) {
    $P = [];
    foreach ($FP as $x) $P[] = [$x[0], $x[1], $x[2], un($x[3], 700), ''];
}

// ---- 3. Services Data ----
$FS = [
    ['Global sourcing and procurement', 'Direct farm-gate and processing-unit buying for authentic origin, fair pricing and year-round bulk availability.'],
    ['Sea and air freight', 'FCL and LCL containers and express air cargo to Jebel Ali, Mombasa, Lagos, Durban and Muscat.'],
    ['Private labelling and packaging', 'Food-grade vacuum packs, tins and 25 kg / 50 kg bags with Arabic, English and French labels.']
];
$S = [];
if (isset($ok) && $ok) {
    $db_serv = q("SELECT service_name, short_desc FROM services ORDER BY id ASC LIMIT 6");
    if (!empty($db_serv)) {
        foreach ($db_serv as $r) {
            $t = txt($r['service_name']);
            $d = txt($r['short_desc']);
            if ($t && $d) $S[] = [$t, $d];
        }
    }
}
if (count($S) < 3) $S = $FS;

// ---- 4. Gallery Data ----
$G = [];
if (isset($ok) && $ok) {
    $db_gal = q("SELECT * FROM gallery ORDER BY id DESC LIMIT 6");
    if (!empty($db_gal)) {
        foreach ($db_gal as $r) {
            $path = $r['image_path'] ?? $r['image'] ?? $r['img'] ?? '';
            $name = $r['image_name'] ?? $r['title'] ?? 'Export operations';
            if ($path) {
                $G[] = [txt($name), img($path, 'gallery', un('photo-1586528116311-ad8dd3c8310d'))];
            }
        }
    }
}
if (count($G) < 3) {
    $G = [
        ['Container loading and port dispatch', un('photo-1586528116311-ad8dd3c8310d')],
        ['Spice grading and Sortex cleaning', un('photo-1596040033229-a9821ebd058d')],
        ['Cashew and dry fruit processing', un('photo-1508061253366-f7da158b6d46')]
    ];
}

// ---- 5. Testimonials Data ----
$T = [];
if (isset($ok) && $ok) {
    $db_test = q("SELECT * FROM testimonials WHERE status=1 ORDER BY display_order ASC, test_id DESC LIMIT 6");
    if (!empty($db_test)) {
        foreach ($db_test as $r) {
            $m = txt($r['message'] ?? '');
            if ($m === '') continue;
            $name = txt($r['name'] ?? '') ?: 'Importer';
            $desig = trim(txt($r['designation'] ?? '') . ' ' . txt($r['client_company'] ?? ''));
            $rating = (int)($r['rating'] ?? 5);
            $img_path = !empty($r['image']) ? img($r['image'], 'testimonials', '') : '';
            $T[] = [$name, $desig, $m, $rating, $img_path];
        }
    }
}
if (count($T) < 2) {
    $T = [
        ['Tariq Al-Mansoori', 'Procurement Director, Al-Baraka Foodstuff, Dubai', 'Our most dependable supplier for W240 cashews and green cardamom at Jebel Ali. Moisture control, packaging and paperwork are exact every time.', 5, ''],
        ['Samuel Ochieng', 'Managing Partner, East Africa Commodities, Mombasa', 'Full containers of spices need strict phytosanitary and SGS compliance. Every shipment left on schedule with no customs delays.', 5, '']
    ];
}

// ---- 6. Blogs & Corridors ----
$Bl = [];
if (isset($ok) && $ok) {
    $db_blogs = q("SELECT title, slug, author, image, meta_desc, description, created_at FROM blogs WHERE status=1 ORDER BY created_at DESC, blog_id DESC LIMIT 3");
    if (!empty($db_blogs)) $Bl = $db_blogs;
}

$C = [
    ['Gulf', 'Dubai, Oman and Saudi Arabia', 'Weekly sailings to Jebel Ali, Sohar, Muscat, Dammam and Jeddah.', 'Sea transit 4 to 8 days'],
    ['East Africa', 'Kenya, Tanzania and Uganda', 'Container supply via Mombasa and Dar es Salaam with COC / PVoC support.', 'Sea transit 10 to 14 days'],
    ['West Africa', 'Nigeria, Ghana and Ivory Coast', 'Moisture-protected packing for Apapa, Tin Can and Tema, with SONCAP and CTN help.', 'Full permit assistance'],
    ['North and South Africa', 'South Africa, Egypt and Morocco', 'Durban, Cape Town, Alexandria and Casablanca with private-label options.', 'Custom packaging'],
];
?>

<head>
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
        --lat: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64'%3E%3Cg fill='none' stroke='%23D6AC56' stroke-width='1'%3E%3Crect x='14' y='14' width='36' height='36'/%3E%3Crect x='14' y='14' width='36' height='36' transform='rotate(45 32 32)'/%3E%3C/g%3E%3C/svg%3E");
        --zig: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='10'%3E%3Cpath d='M0 9L6 1l6 8 6-8 6 8' fill='none' stroke='%23D6AC56' stroke-width='1.5'/%3E%3C/svg%3E")
    }

    * {
        box-sizing: border-box;
        margin: 0
    }

    html {
        scroll-behavior: smooth;
        scroll-padding-top: 84px
    }

    body {
        font: 400 1rem/1.7 'Hanken Grotesk', system-ui, sans-serif;
        color: var(--ink);
        background: var(--paper);
        overflow-x: hidden
    }

    img {
        max-width: 100%;
        display: block
    }

    a {
        color: inherit;
        text-decoration: none
    }

    ul {
        list-style: none;
        padding: 0
    }

    h1,
    h2,
    h3 {
        font-family: Marcellus, Georgia, serif;
        font-weight: 400;
        line-height: 1.15
    }

    :focus-visible {
        outline: 2px solid var(--gold);
        outline-offset: 3px
    }

    .wrap {
        width: min(1180px, 92%);
        margin: auto
    }

    section {
        padding: clamp(64px, 9vw, 112px) 0
    }

    .ar {
        font-family: 'Reem Kufi', serif;
        color: var(--bronze);
        font-weight: 500
    }

    .dk {
        position: relative;
        overflow: hidden;
        background: var(--ink2);
        color: #fff
    }

    .dk::before {
        content: '';
        position: absolute;
        inset: 0;
        background: var(--lat);
        opacity: .08;
        pointer-events: none
    }

    .dk>* {
        position: relative
    }

    .dk .ar {
        color: var(--gold)
    }

    .band {
        height: 10px;
        background: var(--zig) repeat-x center
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .85rem 1.7rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: .94rem;
        border: 1.5px solid var(--bronze);
        background: var(--bronze);
        color: #fff;
        cursor: pointer;
        font-family: inherit;
        transition: background .35s var(--e), color .35s, transform .35s var(--e)
    }

    .btn:hover {
        background: var(--ink);
        border-color: var(--ink);
        transform: translateY(-2px)
    }

    .btn.g {
        background: var(--gold);
        border-color: var(--gold);
        color: var(--ink2)
    }

    .btn.g:hover {
        background: #fff;
        border-color: #fff
    }

    .btn.o {
        background: transparent;
        color: inherit;
        border-color: currentColor
    }

    .btn.o:hover {
        background: var(--ink);
        color: #fff;
        border-color: var(--ink)
    }

    .dk .btn.o:hover {
        background: #fff;
        color: var(--ink2);
        border-color: #fff
    }

    .sh {
        max-width: 660px;
        margin-bottom: clamp(34px, 5vw, 56px)
    }

    .sh.c {
        margin-inline: auto;
        text-align: center
    }

    .sh h2 {
        font-size: clamp(1.9rem, 3.6vw, 2.9rem)
    }

    .sh .ar {
        display: block;
        font-size: 1.2rem;
        margin: 10px 0 14px
    }

    .sh p {
        color: var(--mut)
    }

    .dk .sh p {
        color: rgba(255, 255, 255, .7)
    }

    /* header */
    header {
        position: sticky;
        top: 0;
        z-index: 50;
        background: rgba(252, 250, 246, .93);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid transparent;
        transition: box-shadow .3s, border-color .3s
    }

    header.s {
        box-shadow: 0 8px 26px rgba(18, 35, 42, .07);
        border-color: var(--line)
    }

    .nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 78px;
        gap: 22px
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--bronze)
    }

    .brand b {
        display: block;
        font: 400 1.2rem/1 Marcellus, serif;
        letter-spacing: .09em;
        color: var(--ink)
    }

    .brand small {
        font-size: .64rem;
        letter-spacing: .32em;
        font-weight: 600
    }

    nav ul {
        display: flex;
        gap: 30px
    }

    nav a:not(.btn) {
        font-size: .92rem;
        font-weight: 500;
        position: relative;
        padding: 6px 0
    }

    nav a:not(.btn)::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        height: 1.5px;
        width: 0;
        background: var(--gold);
        transition: width .35s var(--e)
    }

    nav a:not(.btn):hover::after {
        width: 100%
    }

    .nav .btn {
        padding: .65rem 1.3rem
    }

    .tg {
        display: none;
        background: 0;
        border: 0;
        width: 44px;
        height: 44px;
        cursor: pointer
    }

    .tg i,
    .tg i::before,
    .tg i::after {
        display: block;
        width: 22px;
        height: 2px;
        background: var(--ink);
        position: relative;
        margin: auto
    }

    .tg i::before,
    .tg i::after {
        content: '';
        position: absolute;
        left: 0
    }

    .tg i::before {
        top: -7px
    }

    .tg i::after {
        top: 7px
    }

    /* hero */
    .hero {
        padding: clamp(56px, 8vw, 96px) 0 clamp(120px, 13vw, 170px)
    }

    .hero .wrap {
        display: grid;
        grid-template-columns: 1.12fr .88fr;
        gap: clamp(32px, 6vw, 84px);
        align-items: center
    }

    .greet {
        display: flex;
        gap: 18px;
        flex-wrap: wrap;
        color: var(--gold);
        font-size: .95rem
    }

    .greet .ar {
        font-size: 1.1rem
    }

    .hero h1 {
        font-size: clamp(2.35rem, 5vw, 4.3rem);
        color: #fff;
        margin: 18px 0 22px
    }

    .hero p {
        color: rgba(255, 255, 255, .78);
        max-width: 34em;
        margin-bottom: 32px;
        font-size: 1.06rem
    }

    .hero .cta {
        display: flex;
        gap: 14px;
        flex-wrap: wrap
    }

    .hero .in>* {
        animation: up .95s var(--e) both
    }

    .hero .in>:nth-child(2) {
        animation-delay: .12s
    }

    .hero .in>:nth-child(3) {
        animation-delay: .24s
    }

    .hero .in>:nth-child(4) {
        animation-delay: .36s
    }

    .arch {
        position: relative;
        width: min(400px, 100%);
        margin-left: auto;
        aspect-ratio: 3/4
    }

    .arch img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 999px 999px 0 0;
        animation: rise 1.5s var(--e) .15s both
    }

    .arch::before {
        content: '';
        position: absolute;
        inset: -14px -14px 0;
        border: 1.5px solid var(--gold);
        border-bottom: 0;
        border-radius: 999px 999px 0 0;
        animation: fade 2s .8s both
    }

    .tag {
        position: absolute;
        left: -30px;
        bottom: 34px;
        background: var(--paper);
        color: var(--ink);
        padding: 14px 20px;
        border-radius: var(--r);
        box-shadow: 0 18px 40px rgba(0, 0, 0, .28);
        border-left: 3px solid var(--gold);
        animation: up 1s var(--e) 1.1s both
    }

    .tag b {
        display: block;
        font: 400 1.05rem Marcellus, serif
    }

    .tag span {
        font-size: .82rem;
        color: var(--mut)
    }

    @keyframes up {
        from {
            opacity: 0;
            transform: translateY(24px)
        }
    }

    @keyframes fade {
        from {
            opacity: 0
        }
    }

    @keyframes rise {
        from {
            clip-path: inset(100% 0 0 0)
        }

        to {
            clip-path: inset(0)
        }
    }

    /* stats */
    .stats {
        padding: 0;
        margin-top: -72px;
        position: relative;
        z-index: 5
    }

    .sc {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        background: var(--paper);
        border-top: 3px solid var(--gold);
        box-shadow: 0 24px 60px rgba(18, 35, 42, .14);
        border-radius: 0 0 var(--r) var(--r)
    }

    .sc div {
        padding: 28px 24px;
        text-align: center;
        border-right: 1px solid var(--line)
    }

    .sc div:last-child {
        border: 0
    }

    .sc b {
        display: block;
        font: 400 2.5rem/1.1 Marcellus, serif;
        color: var(--bronze)
    }

    .sc span {
        font-size: .86rem;
        color: var(--mut)
    }

    /* about */
    .ab {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: clamp(36px, 6vw, 84px);
        align-items: center
    }

    .fr {
        position: relative;
        padding: 0 0 24px 24px
    }

    .fr::before {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 78%;
        height: 82%;
        border: 1.5px solid var(--gold)
    }

    .fr img {
        position: relative;
        width: 100%;
        aspect-ratio: 5/4.4;
        object-fit: cover;
        border-radius: var(--r)
    }

    .ab h2 {
        font-size: clamp(1.9rem, 3.4vw, 2.7rem);
        margin-bottom: 18px
    }

    .ab p {
        color: var(--mut);
        margin-bottom: 14px
    }

    .pts {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin: 22px 0 30px
    }

    .pts li {
        padding-left: 20px;
        position: relative;
        font-size: .92rem;
        color: var(--mut)
    }

    .pts li::before {
        content: '';
        position: absolute;
        left: 0;
        top: .62em;
        width: 8px;
        height: 8px;
        background: var(--gold);
        transform: rotate(45deg)
    }

    .pts b {
        display: block;
        color: var(--ink)
    }

    /* products */
    .pr {
        background: var(--sand)
    }

    .pg {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px
    }

    .pc {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r);
        padding: 14px 14px 22px;
        display: flex;
        flex-direction: column
    }

    .pc .im {
        overflow: hidden;
        border-radius: 999px 999px 6px 6px
    }

    .pc img {
        width: 100%;
        aspect-ratio: 4/4.4;
        object-fit: cover;
        transition: transform .9s var(--e)
    }

    .pc:hover img {
        transform: scale(1.07)
    }

    .pc small {
        display: block;
        margin: 18px 6px 4px;
        color: var(--bronze);
        font-weight: 600;
        font-size: .78rem
    }

    .pc h3 {
        font-size: 1.22rem;
        margin: 0 6px 8px;
        transition: color .3s
    }

    .pc:hover h3 {
        color: var(--bronze)
    }

    .pc p {
        font-size: .88rem;
        color: var(--mut);
        margin: 0 6px 16px;
        line-height: 1.6
    }

    .pc button {
        margin: auto 6px 0;
        align-self: flex-start;
        background: 0;
        border: 0;
        border-bottom: 1.5px solid var(--gold);
        padding: 2px 0;
        font: 600 .9rem/1.4 'Hanken Grotesk', sans-serif;
        color: var(--ink);
        cursor: pointer;
        transition: color .3s, padding .3s
    }

    .pc button:hover {
        color: var(--bronze);
        padding-right: 8px
    }

    /* services */
    .sg {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px
    }

    .sv {
        background: #fff;
        border: 1px solid var(--line);
        border-top: 3px solid var(--gold);
        padding: 32px 28px;
        border-radius: 0 0 var(--r) var(--r)
    }

    .sv h3 {
        font-size: 1.28rem;
        margin-bottom: 10px
    }

    .sv p {
        color: var(--mut);
        font-size: .93rem
    }

    /* corridors */
    .cg {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px
    }

    .cr {
        padding: 28px 24px;
        border: 1px solid rgba(214, 172, 86, .3);
        border-radius: var(--r);
        transition: background .4s, border-color .4s
    }

    .cr:hover {
        background: rgba(255, 255, 255, .06);
        border-color: var(--gold)
    }

    .cr em {
        font: normal 400 .85rem 'Hanken Grotesk';
        color: var(--gold)
    }

    .cr h3 {
        font-size: 1.25rem;
        color: #fff;
        margin: 8px 0 10px
    }

    .cr p {
        font-size: .88rem;
        color: rgba(255, 255, 255, .7);
        margin-bottom: 16px
    }

    .cr b {
        font-size: .84rem;
        color: var(--gold);
        font-weight: 600
    }

    /* gallery */
    .gg {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-auto-rows: 250px;
        gap: 16px
    }

    .gg figure {
        position: relative;
        overflow: hidden;
        border-radius: var(--r)
    }

    .gg figure:first-child {
        grid-row: span 2
    }

    .gg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 1s var(--e)
    }

    .gg figure:hover img {
        transform: scale(1.06)
    }

    .gg figcaption {
        position: absolute;
        inset: auto 0 0;
        padding: 44px 20px 18px;
        color: #fff;
        font-weight: 500;
        background: linear-gradient(transparent, rgba(18, 35, 42, .88))
    }

    /* testimonials */
    .te {
        background: var(--sand)
    }

    .tr {
        display: flex;
        gap: 22px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        padding-bottom: 18px;
        scrollbar-width: thin;
        scrollbar-color: var(--gold) transparent
    }

    .tc {
        flex: 0 0 min(400px, 86%);
        scroll-snap-align: start;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r);
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 24px
    }

    .tc .st {
        color: var(--gold);
        letter-spacing: 3px
    }

    .tc p {
        margin-top: 10px;
        font-size: .98rem
    }

    .tc div.who {
        display: flex;
        gap: 14px;
        align-items: center;
        border-top: 1px solid var(--line);
        padding-top: 18px
    }

    .tc i {
        flex: none;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--ink);
        color: var(--gold);
        display: grid;
        place-items: center;
        font: normal 1.1rem Marcellus, serif
    }

    .tc b {
        display: block;
        line-height: 1.3
    }

    .tc small {
        color: var(--mut)
    }

    /* blogs */
    .bh {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 20px;
        flex-wrap: wrap
    }

    .bg3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 26px
    }

    .bl {
        display: flex;
        flex-direction: column
    }

    .bl .im {
        overflow: hidden;
        border-radius: var(--r);
        margin-bottom: 18px
    }

    .bl img {
        width: 100%;
        aspect-ratio: 16/10;
        object-fit: cover;
        transition: transform .9s var(--e)
    }

    .bl:hover img {
        transform: scale(1.05)
    }

    .bl small {
        color: var(--mut);
        display: flex;
        gap: 16px;
        font-size: .82rem
    }

    .bl h3 {
        font-size: 1.28rem;
        margin: 8px 0 10px
    }

    .bl h3 a:hover {
        color: var(--bronze)
    }

    .bl p {
        color: var(--mut);
        font-size: .93rem;
        margin-bottom: 12px
    }

    .bl .rd {
        margin-top: auto;
        font-weight: 600;
        color: var(--bronze);
        font-size: .9rem
    }

    /* rfq */
    .rq {
        background: linear-gradient(var(--paper), var(--sand))
    }

    .rg {
        display: grid;
        grid-template-columns: 5fr 7fr;
        gap: clamp(32px, 6vw, 80px);
        align-items: start
    }

    .rg h2 {
        font-size: clamp(1.9rem, 3.4vw, 2.7rem)
    }

    .rg>div>p {
        color: var(--mut);
        margin: 12px 0 22px
    }

    .rg .pts {
        grid-template-columns: 1fr
    }

    .fm {
        background: #fff;
        border: 1px solid var(--line);
        border-top: 3px solid var(--gold);
        border-radius: var(--r);
        padding: clamp(24px, 4vw, 44px);
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        box-shadow: 0 24px 60px rgba(18, 35, 42, .08)
    }

    .fm .w {
        grid-column: 1/-1
    }

    .fm h3 {
        font-size: 1.4rem
    }

    label {
        display: block;
        font-size: .82rem;
        font-weight: 600;
        margin-bottom: 6px
    }

    input,
    select,
    textarea {
        width: 100%;
        font: inherit;
        padding: .8rem .95rem;
        border: 1px solid var(--line);
        border-radius: var(--r);
        background: var(--paper);
        color: var(--ink);
        transition: border-color .25s, box-shadow .25s
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: 0;
        border-color: var(--bronze);
        box-shadow: 0 0 0 3px rgba(214, 172, 86, .32)
    }

    .fm .btn {
        width: 100%;
        justify-content: center;
        padding: 1rem
    }

    /* footer */
    footer {
        padding: 0
    }

    footer .wrap {
        padding: clamp(48px, 7vw, 80px) 0 28px
    }

    .fg {
        display: grid;
        grid-template-columns: 1.4fr .8fr 1.1fr 1.1fr;
        gap: 36px;
        padding-bottom: 36px;
        border-bottom: 1px solid rgba(255, 255, 255, .12)
    }

    footer .brand b {
        color: #fff
    }

    footer p,
    footer li {
        font-size: .9rem;
        color: rgba(255, 255, 255, .7);
        margin-bottom: 8px
    }

    footer p {
        margin-top: 16px
    }

    footer h4 {
        font: 400 1.15rem Marcellus, serif;
        color: var(--gold);
        margin-bottom: 16px
    }

    footer a:hover {
        color: var(--gold)
    }

    .fb {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        padding-top: 24px;
        font-size: .84rem;
        color: rgba(255, 255, 255, .55)
    }

    .wa {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 60;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #25D366;
        display: grid;
        place-items: center;
        box-shadow: 0 10px 26px rgba(37, 211, 102, .4);
        transition: transform .35s var(--e)
    }

    .wa:hover {
        transform: scale(1.1)
    }

    /* motion on scroll */
    .js .rv {
        opacity: 0;
        transform: translateY(22px);
        transition: opacity .9s var(--e), transform .9s var(--e)
    }

    .js .rv.in {
        opacity: 1;
        transform: none
    }

    .pg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 24px;
    }

    .pc-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r);
        padding: 14px 14px 22px;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s;
    }

    .pc-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
    }

    .pc-img {
        border-radius: 999px 999px 6px 6px;
        width: 100%;
        aspect-ratio: 4/4.4;
        object-fit: cover;
        transition: transform .9s var(--e);
    }

    .pc-card:hover .pc-img {
        transform: scale(1.05);
    }

    .pc-cat {
        display: block;
        margin: 18px 6px 4px;
        color: var(--bronze);
        font-weight: 600;
        font-size: .78rem;
        text-transform: uppercase;
    }

    .pc-title {
        font-size: 1.22rem;
        margin: 0 6px 8px;
        color: var(--ink);
        transition: color 0.3s;
    }

    .pc-card:hover .pc-title {
        color: var(--bronze);
    }

    .pc-desc {
        font-size: .88rem;
        color: var(--mut);
        margin: 0 6px 16px;
        line-height: 1.6;
    }

    .pc-btn {
        margin: auto 6px 0;
        align-self: flex-start;
        background: 0;
        border: 0;
        border-bottom: 1.5px solid var(--gold);
        padding: 2px 0;
        font: 600 .9rem/1.4 'Hanken Grotesk', sans-serif;
        color: var(--ink);
        cursor: pointer;
        transition: 0.3s;
    }

    .pc-card:hover .pc-btn {
        color: var(--bronze);
        padding-right: 8px;
    }

    .sg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }

    .sv-card {
        background: #fff;
        border: 1px solid var(--line);
        border-top: 3px solid var(--gold);
        padding: 32px 28px;
        border-radius: 0 0 var(--r) var(--r);
        transition: transform 0.3s;
    }

    .sv-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
    }

    .sv-title {
        font-size: 1.28rem;
        margin-bottom: 10px;
        color: var(--ink);
    }

    .cg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .cr-card {
        padding: 28px 24px;
        border: 1px solid rgba(214, 172, 86, .3);
        border-radius: var(--r);
        transition: background .4s, border-color .4s;
    }

    .cr-card:hover {
        background: rgba(255, 255, 255, .06);
        border-color: var(--gold);
    }

    .gg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        grid-auto-rows: 250px;
        gap: 16px;
    }

    .gg-figure {
        position: relative;
        overflow: hidden;
        border-radius: var(--r);
    }

    .gg-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 1s var(--e);
    }

    .gg-figure:hover .gg-img {
        transform: scale(1.08);
    }

    .gg-cap {
        position: absolute;
        inset: auto 0 0;
        padding: 44px 20px 18px;
        color: #fff;
        font-weight: 500;
        background: linear-gradient(transparent, rgba(18, 35, 42, .88));
    }

    /* New Trusted Testimonial Design */
    .tc-scroll {
        display: flex;
        gap: 24px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        padding-bottom: 24px;
        scrollbar-width: thin;
        scrollbar-color: var(--gold) transparent;
    }

    .tc-card {
        flex: 0 0 min(420px, 85%);
        scroll-snap-align: start;
        background: #fff;
        border: 1px solid var(--line);
        border-top: 4px solid var(--gold);
        border-radius: var(--r);
        padding: 35px 30px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 24px;
        position: relative;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    }

    .tc-card::before {
        content: '“';
        position: absolute;
        top: 15px;
        right: 25px;
        font-size: 5.5rem;
        color: var(--sand);
        font-family: Georgia, serif;
        line-height: 1;
    }

    .tc-stars {
        color: var(--gold);
        letter-spacing: 3px;
        font-size: 1.1rem;
        position: relative;
        z-index: 2;
    }

    .tc-msg {
        margin-top: 10px;
        font-size: 1.05rem;
        font-style: italic;
        color: var(--mut);
        line-height: 1.6;
        position: relative;
        z-index: 2;
    }

    .tc-auth {
        display: flex;
        gap: 16px;
        align-items: center;
        border-top: 1px solid var(--line);
        padding-top: 20px;
        position: relative;
        z-index: 2;
    }

    .tc-auth img {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--sand);
    }

    .tc-auth i {
        flex: none;
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background: var(--ink2);
        color: var(--gold);
        display: grid;
        place-items: center;
        font: normal 1.3rem Marcellus, serif;
    }

    .tc-name {
        display: block;
        line-height: 1.2;
        color: var(--ink);
        font-size: 1.1rem;
        font-weight: 600;
    }

    .tc-desig {
        color: var(--mut);
        font-size: 0.85rem;
        display: block;
        margin-top: 2px;
    }

    .tc-verified {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        color: #25D366;
        font-weight: 700;
        margin-top: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 26px;
        margin-top: 30px;
    }

    @media(max-width:960px) {
        .tg {
            display: block
        }

        nav {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--paper);
            padding: 0 4%;
            max-height: 0;
            overflow: hidden;
            transition: max-height .45s var(--e);
            border-bottom: 1px solid var(--line)
        }

        nav.o {
            max-height: 520px
        }

        nav ul {
            flex-direction: column;
            gap: 0;
            padding-top: 8px
        }

        nav li a:not(.btn) {
            display: block;
            padding: 14px 0;
            border-bottom: 1px solid var(--line)
        }

        nav .btn {
            margin: 18px 0
        }

        .hero .wrap,
        .ab,
        .rg {
            grid-template-columns: 1fr
        }

        .arch {
            margin: 20px auto 0;
            width: min(320px, 80%)
        }

        .tag {
            left: -12px
        }

        .sc {
            grid-template-columns: 1fr 1fr
        }

        .sc div:nth-child(2) {
            border-right: 0
        }

        .sc div:nth-child(-n+2) {
            border-bottom: 1px solid var(--line)
        }

        .sg,
        .bg3 {
            grid-template-columns: 1fr 1fr
        }

        .cg {
            grid-template-columns: 1fr 1fr
        }

        .gg {
            grid-template-columns: 1fr 1fr
        }

        .fg {
            grid-template-columns: 1fr 1fr
        }
    }

    @media(max-width:600px) {

        .sg,
        .bg3,
        .cg,
        .gg,
        .fm,
        .pts {
            grid-template-columns: 1fr
        }

        .gg {
            grid-auto-rows: 220px
        }

        .gg figure:first-child {
            grid-row: auto
        }

        .sc b {
            font-size: 2rem
        }

        .sc div {
            padding: 20px 12px
        }

        .brand small {
            display: none
        }

        .fg {
            grid-template-columns: 1fr
        }

        .tag {
            display: none
        }
    }

    @media(prefers-reduced-motion:reduce) {

        *,
        *::before {
            animation: none !important;
            transition: none !important
        }

        html {
            scroll-behavior: auto
        }

        .js .rv {
            opacity: 1;
            transform: none
        }
    }
    </style>
</head>

<body>
    <section class="hero dk" id="home">
        <div class="wrap">
            <div class="in">
                <div class="greet"><span class="ar">أهلاً وسهلاً</span><span>Karibu</span><span>Bienvenue</span></div>
                <h1>From Dubai to the ports of Africa, trade you can rely on</h1>
                <p>Noor Al Hiba Trading LLC sources, grades and ships export-quality spices, dry fruits and agro
                    commodities to importers across the Gulf, East Africa and West Africa.</p>
                <div class="cta"><a href="products.php" class="btn g">View export catalogue</a><a href="#rfq"
                        class="btn o">Get FOB / CIF pricing</a></div>
            </div>
            <div class="arch">
                <img src="<?= e($hero) ?>" alt="Export-grade spices and dry fruits">
                <div class="tag"><b>Dubai to Mombasa</b><span>10 to 14 days by sea</span></div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="wrap">
            <div class="sc rv">
                <div><b>35+</b><span>Export destinations</span></div>
                <div><b>12,500+</b><span>Metric tons shipped</span></div>
                <div><b>100%</b><span>SGS and COA tested lots</span></div>
                <div><b>24/7</b><span>Export desk support</span></div>
            </div>
        </div>
    </section>

    <section id="about">
        <div class="wrap ab">
            <div class="fr rv"><img src="<?= e($about_i) ?>" alt="Noor Al Hiba warehouse and export operations"
                    loading="lazy"></div>
            <div class="rv">
                <span class="ar">من نحن</span>
                <h2><?= e($about_t) ?></h2>
                <?php if ($about_c): ?><p><?= e(mb_strimwidth($about_c, 0, 520, '...')) ?></p><?php else: ?>
                <p>We process, source and export premium whole spices, dry fruits, nuts and seeds to wholesalers,
                    supermarket chains and food manufacturers in the UAE, the wider GCC, and across East and West
                    Africa.</p>
                <p>Every shipment follows the same standard: lab-tested quality, clear trade documents and containers
                    that leave on the date we promise.</p><?php endif; ?>
                <ul class="pts">
                    <li><b>Direct origin sourcing</b>Bought from certified farms and processing hubs.</li>
                    <li><b>Gulf and Africa compliant</b>Dubai Municipality, SFDA, KEBS, NAFDAC and EU norms.</li>
                </ul>
                <a href="about.php" class="btn">Partner with us</a>
            </div>
        </div>
    </section>

    <section class="pr" id="products">
        <div class="wrap">
            <div class="sh c rv">
                <h2>Export-grade commodities</h2><span class="ar">منتجات التصدير</span>
                <p>Sorted, graded and packed in moisture-locked bulk packaging for wholesalers, supermarkets and food
                    processors.</p>
            </div>
            <div class="pg-grid">
                <?php foreach ($P as $p): ?>
                <article class="pc-card rv">
                    <div style="overflow: hidden; border-radius: 999px 999px 6px 6px;">
                        <a href="product-details.php?slug=<?= e($p[4]) ?>">
                            <img class="pc-img" src="<?= e($p[3]) ?>" alt="<?= e($p[0]) ?>" loading="lazy">
                        </a>
                    </div>
                    <small class="pc-cat"><?= e($p[1]) ?></small>
                    <h3 class="pc-title"><a href="product-details.php?slug=<?= e($p[4]) ?>"><?= e($p[0]) ?></a></h3>
                    <p class="pc-desc"><?= e(mb_strimwidth($p[2], 0, 100, '...')) ?></p>
                    <button type="button" class="pc-btn"
                        onclick="document.getElementById('pi').value='<?= e($p[0]) ?>'; document.getElementById('rfq').scrollIntoView({behavior: 'smooth'}); setTimeout(()=>document.getElementById('pi').focus(),700);">Request
                        bulk price</button>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="services">
        <div class="wrap">
            <div class="sh rv">
                <h2>Complete import and export services</h2><span class="ar">خدمات التجارة</span>
                <p>From farm-level buying and lab testing to container loading and customs clearance at Dubai and
                    African ports.</p>
            </div>
            <div class="sg-grid">
                <?php foreach ($S as $s): ?>
                <div class="sv-card rv">
                    <h3 class="sv-title"><?= e($s[0]) ?></h3>
                    <p style="color: var(--mut); font-size: .93rem;"><?= e(mb_strimwidth($s[1], 0, 170, '...')) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <div class="band"></div>
    <section class="dk" id="corridors">
        <div class="wrap">
            <div class="sh rv">
                <h2>Trade routes across the Gulf and Africa</h2><span class="ar">ممرات التجارة</span>
                <p>Dedicated shipping-line contracts keep your cargo fresh, intact and fully documented from loading to
                    discharge.</p>
            </div>
            <div class="cg-grid">
                <?php foreach ($C as $c): ?>
                <div class="cr-card rv">
                    <em style="font: normal 400 .85rem 'Hanken Grotesk'; color: var(--gold);"><?= e($c[0]) ?></em>
                    <h3 style="font-size: 1.25rem; color: #fff; margin: 8px 0 10px;"><?= e($c[1]) ?></h3>
                    <p style="font-size: .88rem; color: rgba(255, 255, 255, .7); margin-bottom: 16px;"><?= e($c[2]) ?>
                    </p>
                    <b style="font-size: .84rem; color: var(--gold); font-weight: 600;"><?= e($c[3]) ?></b>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="gallery">
        <div class="wrap">
            <div class="sh c rv">
                <h2>Inside our export operations</h2><span class="ar">عملياتنا</span>
            </div>
            <div class="gg-grid">
                <?php foreach ($G as $g): ?>
                <figure class="gg-figure rv">
                    <img class="gg-img" src="<?= e($g[1]) ?>" alt="<?= e($g[0]) ?>" loading="lazy">
                    <figcaption class="gg-cap"><?= e($g[0]) ?></figcaption>
                </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="te" id="testimonials" style="background: var(--sand);">
        <div class="wrap">
            <div class="sh c rv">
                <h2>Trusted by importers in Dubai and Africa</h2><span class="ar">آراء عملائنا</span>
            </div>
            <div class="tc-scroll rv">
                <?php foreach ($T as $t): ?>
                <div class="tc-card">
                    <div>
                        <span class="tc-stars"><?= str_repeat('★', max(1, min(5, $t[3]))) ?></span>
                        <p class="tc-msg">“<?= e($t[2]) ?>”</p>
                    </div>
                    <div class="tc-auth">
                        <?php if (!empty($t[4])): ?>
                        <img src="<?= e($t[4]) ?>" alt="<?= e($t[0]) ?>">
                        <?php else: ?>
                        <i><?= e(strtoupper(mb_substr($t[0], 0, 1))) ?></i>
                        <?php endif; ?>

                        <div>
                            <b class="tc-name"><?= e($t[0]) ?></b>
                            <small class="tc-desig"><?= e($t[1]) ?></small>
                            <span class="tc-verified">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                                Verified Buyer
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if (!empty($Bl)): ?>
    <section id="blogs">
        <div class="wrap">
            <div class="bh sh"
                style="max-width:none; display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; flex-wrap: wrap;">
                <div class="rv">
                    <h2>Export guides and trade insights</h2><span class="ar">رؤى تجارية</span>
                </div><a href="blog.php" class="btn o">All articles</a>
            </div>
            <div class="bg-grid">
                <?php foreach ($Bl as $b):
                $bi = img($b['image'], 'blogs', un('photo-1596040033229-a9821ebd058d'));
                $ex = txt($b['meta_desc'], 130) ?: txt($b['description'], 130);
                $u = 'blog-details.php?slug=' . urlencode($b['slug']); ?>
                <article class="bl rv" style="display: flex; flex-direction: column;">
                    <a class="im" href="<?= e($u) ?>"
                        style="overflow: hidden; border-radius: var(--r); margin-bottom: 18px;"><img src="<?= e($bi) ?>"
                            alt="<?= e($b['title']) ?>" loading="lazy"
                            style="width: 100%; aspect-ratio: 16/10; object-fit: cover; transition: transform .9s var(--e);"></a>
                    <small
                        style="color: var(--mut); display: flex; gap: 16px; font-size: .82rem;"><span><?= e(date('d M Y', strtotime($b['created_at'] ?: 'now'))) ?></span><span><?= e($b['author']) ?></span></small>
                    <h3 style="font-size: 1.28rem; margin: 8px 0 10px;"><a href="<?= e($u) ?>"><?= e($b['title']) ?></a>
                    </h3>
                    <p style="color: var(--mut); font-size: .93rem; margin-bottom: 12px;"><?= e($ex) ?></p>
                    <a class="rd" href="<?= e($u) ?>"
                        style="margin-top: auto; font-weight: 600; color: var(--bronze); font-size: .9rem;">Read the
                        guide</a>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="rq" id="rfq" style="background: linear-gradient(var(--paper), var(--sand));">
        <div class="wrap rg"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: clamp(32px, 6vw, 80px); align-items: start;">
            <div class="rv">
                <h2 style="font-size: clamp(1.9rem, 3.4vw, 2.7rem);">Request a custom FOB or CIF quotation</h2><span
                    class="ar" style="display:block;margin-top:8px">اطلب عرض سعر</span>
                <p style="color: var(--mut); margin: 12px 0 22px;">Tell us the commodity, grade, packing and destination
                    port. Our export team replies with a formal proforma within 4 business hours.</p>
                <ul class="pts" style="display: grid; grid-template-columns: 1fr; gap: 18px; margin: 22px 0 30px;">
                    <li style="padding-left: 20px; position: relative; font-size: .92rem; color: var(--mut);"><b
                            style="display: block; color: var(--ink);">Samples by DHL or FedEx</b>Representative samples
                        before you commit.</li>
                    <li style="padding-left: 20px; position: relative; font-size: .92rem; color: var(--mut);"><b
                            style="display: block; color: var(--ink);">Third-party inspection</b>SGS or Geo-Chem checks
                        before loading.</li>
                    <li style="padding-left: 20px; position: relative; font-size: .92rem; color: var(--mut);"><b
                            style="display: block; color: var(--ink);">Labels in Arabic, English and French</b>Private
                        labelling on request.</li>
                </ul>
            </div>
            <form class="fm rv" action="contact.php" method="POST"
                style="background: #fff; border: 1px solid var(--line); border-top: 3px solid var(--gold); border-radius: var(--r); padding: clamp(24px, 4vw, 44px); display: grid; grid-template-columns: 1fr 1fr; gap: 16px; box-shadow: 0 24px 60px rgba(18, 35, 42, .08);">
                <h3 class="w" style="grid-column: 1/-1; font-size: 1.4rem;">Bulk import / export inquiry</h3>
                <div><label style="display: block; font-size: .82rem; font-weight: 600; margin-bottom: 6px;">Contact
                        person *</label><input
                        style="width: 100%; font: inherit; padding: .8rem .95rem; border: 1px solid var(--line); border-radius: var(--r); background: var(--paper); color: var(--ink);"
                        name="name" placeholder="Ahmed Al-Mazrouei" required></div>
                <div><label style="display: block; font-size: .82rem; font-weight: 600; margin-bottom: 6px;">Company
                        *</label><input
                        style="width: 100%; font: inherit; padding: .8rem .95rem; border: 1px solid var(--line); border-radius: var(--r); background: var(--paper); color: var(--ink);"
                        name="company" placeholder="Gulf Horizon Foodstuff LLC" required></div>
                <div><label style="display: block; font-size: .82rem; font-weight: 600; margin-bottom: 6px;">Business
                        email *</label><input
                        style="width: 100%; font: inherit; padding: .8rem .95rem; border: 1px solid var(--line); border-radius: var(--r); background: var(--paper); color: var(--ink);"
                        type="email" name="email" placeholder="import@company.com" required></div>
                <div><label style="display: block; font-size: .82rem; font-weight: 600; margin-bottom: 6px;">WhatsApp /
                        phone *</label><input
                        style="width: 100%; font: inherit; padding: .8rem .95rem; border: 1px solid var(--line); border-radius: var(--r); background: var(--paper); color: var(--ink);"
                        type="tel" name="phone" placeholder="+971 50 123 4567" required></div>
                <div><label style="display: block; font-size: .82rem; font-weight: 600; margin-bottom: 6px;">Product
                        *</label><input
                        style="width: 100%; font: inherit; padding: .8rem .95rem; border: 1px solid var(--line); border-radius: var(--r); background: var(--paper); color: var(--ink);"
                        id="pi" name="subject" placeholder="W240 cashews, red chilli..." required></div>
                <div><label style="display: block; font-size: .82rem; font-weight: 600; margin-bottom: 6px;">Order
                        volume</label><select
                        style="width: 100%; font: inherit; padding: .8rem .95rem; border: 1px solid var(--line); border-radius: var(--r); background: var(--paper); color: var(--ink);"
                        name="quantity">
                        <option>1 x 20ft FCL container</option>
                        <option>1 x 40ft FCL container</option>
                        <option>LCL / trial bulk (1 - 5 MT)</option>
                    </select></div>
                <div class="w" style="grid-column: 1/-1;"><label
                        style="display: block; font-size: .82rem; font-weight: 600; margin-bottom: 6px;">Destination
                        port and country</label><input
                        style="width: 100%; font: inherit; padding: .8rem .95rem; border: 1px solid var(--line); border-radius: var(--r); background: var(--paper); color: var(--ink);"
                        name="destination" placeholder="Jebel Ali, UAE / Mombasa, Kenya"></div>
                <div class="w" style="grid-column: 1/-1;"><label
                        style="display: block; font-size: .82rem; font-weight: 600; margin-bottom: 6px;">Specifications
                        *</label><textarea
                        style="width: 100%; font: inherit; padding: .8rem .95rem; border: 1px solid var(--line); border-radius: var(--r); background: var(--paper); color: var(--ink);"
                        name="message" rows="3"
                        placeholder="Grade, moisture limit, bag size (10 / 25 / 50 kg), Incoterm..."
                        required></textarea></div>
                <div class="w" style="grid-column: 1/-1;"><button type="submit" class="btn"
                        style="width: 100%; justify-content: center; padding: 1rem;">Request proforma quotation</button>
                </div>
            </form>
        </div>
    </section>

    <div class="band"></div>
    <?php include 'includes/footer.php' ?>

    <a class="wa" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"
        href="https://wa.me/<?= e($wa) ?>?text=Hello%20Noor%20Al%20Hiba%2C%20we%20have%20a%20bulk%20import-export%20inquiry.">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="#fff" aria-hidden="true">
            <path d="M12 2.5a9.5 9.5 0 0 0-8.2 14.300L2.5 21.500l4.8-1.3A9.5 9.5 0 1 0 12 2.5z" />
            <circle cx="8" cy="12" r="1.3" fill="#25D366" />
            <circle cx="12" cy="12" r="1.3" fill="#25D366" />
            <circle cx="16" cy="12" r="1.3" fill="#25D366" />
        </svg>
    </a>

    <script>
    (function() {
        var h = document.querySelector('header'),
            n = document.querySelector('nav'),
            t = document.querySelector('.tg');
        addEventListener('scroll', function() {
            h.classList.toggle('s', scrollY > 10)
        }, {
            passive: true
        });
        t.onclick = function() {
            n.classList.toggle('o');
            t.setAttribute('aria-expanded', n.classList.contains('o'))
        };
        n.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('click', function() {
                n.classList.remove('o')
            })
        });
        var els = document.querySelectorAll('.rv');
        if (!('IntersectionObserver' in window)) {
            els.forEach(function(x) {
                x.classList.add('in')
            });
        } else {
            var io = new IntersectionObserver(function(es) {
                es.forEach(function(x) {
                    if (!x.isIntersecting) return;
                    var el = x.target;
                    el.classList.add('in');
                    io.unobserve(el);
                    setTimeout(function() {
                        el.style.transitionDelay = ''
                    }, 1500);
                })
            }, {
                threshold: .12
            });
            els.forEach(function(el, i) {
                el.style.transitionDelay = (i % 4) * 80 + 'ms';
                io.observe(el)
            });
        }
        document.querySelectorAll('[data-p]').forEach(function(b) {
            b.addEventListener('click', function() {
                var i = document.getElementById('pi');
                i.value = b.dataset.p;
                document.getElementById('rfq').scrollIntoView({
                    behavior: 'smooth'
                });
                setTimeout(function() {
                    i.focus({
                        preventScroll: true
                    })
                }, 700);
            })
        });
    })();
    </script>
</body>

</html>