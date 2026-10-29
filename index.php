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