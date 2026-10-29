<?php
/** NOOR AL HIBA TRADING LLC - About Us Page */
session_start();
mysqli_report(MYSQLI_REPORT_OFF);

include 'config/connect.php';

    include 'includes/header.php';
    $pageTitle = $meta_title;
    include 'includes/breadcrumb.php';


function q($sql) {
global $conn, $ok;
$r = [];
if ($ok && ($res = @$conn->query($sql))) while ($x = $res->fetch_assoc()) $r[] = $x;
return $r;
}

function e($s) {
return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// Clean text function (removes other brand names and uses fallback)
function txt($s, $n = 0) {
$s = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string)$s))));
if (preg_match('/Bhagirath|Khetarpal|candle-making/i', $s)) return ''; // Skip irrelevant DB content
return $n ? mb_strimwidth($s, 0, $n, '...') : $s;
}

function img($f, $d, $fb) {
$f = trim((string)$f);
if ($f === '') return $fb;
if (preg_match('#^https?://#', $f)) return $f;
$c = ltrim($f, '/');
$b = basename($c);
foreach ([$c, "admin/$c", "uploads/$d/$b", "admin/uploads/$d/$b", "assets/img/uploads/$d/$b", "assets/images/$b"] as $p)
if (is_file($p)) return $p;
return $fb;
}

function un($id, $w = 800) {
return "https://images.unsplash.com/$id?auto=format&fit=crop&w=$w&q=80";
}

function mark($w = 46) {
return '<svg viewBox="290 55 815 710" width="' . $w . '" aria-hidden="true">
    <path fill="currentColor" d="M325 476l78-56v228l-78 40zM425 352l123-90v346l-123 32zM570 150l123-88v535l-123 8z" />
    <path fill="none" stroke="currentColor" stroke-width="7"
        d="M697 68l118 84v451l-118-6zM838 262l122 90v284l-120-29zM984 420l76 56v206l-76-37z" />
    <path fill="#D6AC56" d="M298 757C500 650 900 650 1097 757 900 700 500 700 298 757z" />
</svg>';
}

// ---- Fetch About Data ----
$A = q("SELECT title, content, image_url, meta_title, meta_desc, meta_key FROM about_sections ORDER BY section_order
ASC, id ASC LIMIT 1");
$AU = q("SELECT schema_markup, meta_title, meta_desc, meta_key FROM about_us LIMIT 1");

// Content Variables with Premium Fallbacks
$about_title = txt($A[0]['title'] ?? '') ?: 'Bridging the Gulf and Africa through Trusted Trade';
$about_content = txt($A[0]['content'] ?? '');
if (!$about_content) {
$about_content = "At Noor Al Hiba Trading LLC, we specialize in the procurement, processing, and export of premium
quality dry fruits, whole spices, and agricultural commodities. Headquartered in Dubai with strong sourcing networks in
India and across the globe, we are the vital link between farmers and international markets.<br><br>Our commitment is to
deliver farm-fresh, unadulterated, and richly flavored food products to the Gulf, East Africa, and West Africa. We
ensure strict compliance with global food safety standards, SGS inspections, and seamless logistics, guaranteeing that
every container arrives exactly as promised.";
}
$about_img = img($A[0]['image_url'] ?? '', 'about_us', un('photo-1586528116311-ad8dd3c8310d', 1000));

?>

<style>
/* Base Variables matching Homepage */
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
    transition: background .35s var(--e), color .35s, transform .35s var(--e)
}

.btn:hover {
    background: var(--ink);
    border-color: var(--ink);
    transform: translateY(-2px)
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

/* Header */
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

/* Inner Hero */
.inner-hero {
    padding: clamp(80px, 12vw, 120px) 0;
    text-align: center;
    border-bottom: 3px solid var(--gold);
}

.inner-hero h1 {
    font-size: clamp(2.5rem, 5vw, 4rem);
    margin-bottom: 12px;
}

.inner-hero p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

/* Main About */
.ab {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(40px, 8vw, 84px);
    align-items: center;
}

.fr {
    position: relative;
    padding: 0 0 24px 24px;
}

.fr::before {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 78%;
    height: 82%;
    border: 1.5px solid var(--gold);
}

.fr img {
    position: relative;
    width: 100%;
    aspect-ratio: 4/5;
    object-fit: cover;
    border-radius: var(--r);
}

.ab h2 {
    font-size: clamp(2rem, 3.5vw, 3rem);
    margin-bottom: 18px;
    color: var(--ink);
}

.ab p {
    color: var(--mut);
    margin-bottom: 16px;
    font-size: 1.05rem;
}

/* Trust Section (Static) */
.trust {
    background: var(--sand);
}

.sh {
    max-width: 660px;
    margin-bottom: clamp(34px, 5vw, 56px);
    text-align: center;
    margin-inline: auto;
}

.sh h2 {
    font-size: clamp(1.9rem, 3.6vw, 2.9rem);
}

.sh p {
    color: var(--mut);
}

.trust-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.trust-card {
    background: #fff;
    padding: 40px 30px;
    border-radius: var(--r);
    border: 1px solid var(--line);
    text-align: center;
    border-bottom: 3px solid var(--gold);
    transition: transform 0.4s var(--e), box-shadow 0.4s;
}

.trust-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
}

.trust-card svg {
    width: 60px;
    height: 60px;
    color: var(--bronze);
    margin-bottom: 20px;
}

.trust-card h3 {
    font-size: 1.4rem;
    margin-bottom: 12px;
}

.trust-card p {
    color: var(--mut);
    font-size: 0.95rem;
}

/* Mission / Vision */
.mv-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-top: 60px;
}

.mv-box {
    padding: 40px;
    background: var(--ink2);
    color: #fff;
    border-radius: var(--r);
    position: relative;
    overflow: hidden;
}

.mv-box::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 150px;
    height: 150px;
    background: var(--gold);
    opacity: 0.1;
    border-radius: 50%;
}

.mv-box h3 {
    color: var(--gold);
    font-size: 1.8rem;
    margin-bottom: 15px;
}

.mv-box p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1rem;
}

/* Footer (Same as home) */
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

/* Animation */
.js .rv {
    opacity: 0;
    transform: translateY(22px);
    transition: opacity .9s var(--e), transform .9s var(--e)
}

.js .rv.in {
    opacity: 1;
    transform: none
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

    .ab,
    .trust-grid,
    .mv-grid {
        grid-template-columns: 1fr;
    }

    .fg {
        grid-template-columns: 1fr 1fr;
    }
}

@media(max-width:600px) {
    .fg {
        grid-template-columns: 1fr;
    }

    .brand small {
        display: none
    }
}
</style>
</head>

<body>

    <!-- Main About Section (Dynamic DB + Fallback) -->
    <section id="about-us">
        <div class="wrap ab">
            <div class="fr rv">
                <img src="<?= e($about_img) ?>" alt="Noor Al Hiba Trading LLC" loading="lazy">
            </div>
            <div class="rv">
                <h2><?= e($about_title) ?></h2>
                <p><?= $about_content; ?></p>
                <br>
                <a href="index.php#products" class="btn">View Our Products</a>
            </div>
        </div>
    </section>

    <!-- Trustworthy Section (Static - Smooth & Professional) -->
    <section class="trust">
        <div class="wrap">
            <div class="sh rv">
                <span class="ar">لماذا نحن</span>
                <h2>Why Trade With Noor Al Hiba?</h2>
                <p>We don't just supply commodities; we build long-term partnerships based on transparency, quality, and
                    seamless logistics.</p>
            </div>
            <div class="trust-grid">
                <!-- Trust Card 1 -->
                <div class="trust-card rv">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                    <h3>Certified Quality</h3>
                    <p>Every shipment undergoes rigorous third-party SGS inspections. We provide COA, phytosanitary, and
                        health certificates ensuring compliance with Gulf and African standards.</p>
                </div>
                <!-- Trust Card 2 -->
                <div class="trust-card rv" style="transition-delay: 0.1s;">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    <h3>End-to-End Logistics</h3>
                    <p>From FCL/LCL sea freight to express air cargo. We handle loading, customs clearance, and delivery
                        to major ports like Jebel Ali, Mombasa, Lagos, and Durban.</p>
                </div>
                <!-- Trust Card 3 -->
                <div class="trust-card rv" style="transition-delay: 0.2s;">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                    <h3>Secure Transactions</h3>
                    <p>Trade with peace of mind. We offer transparent FOB, CIF, and CFR Incoterms backed by secure
                        banking channels like Irrevocable L/C and TT.</p>
                </div>
            </div>

            <!-- Vision & Mission -->
            <div class="mv-grid">
                <div class="mv-box rv">
                    <h3>Our Vision</h3>
                    <p>To be the most trusted and preferred agro-commodity supplier connecting the Middle East with the
                        African continent, recognized globally for our uncompromising quality, ethical sourcing, and
                        logistical excellence.</p>
                </div>
                <div class="mv-box rv" style="transition-delay: 0.1s;">
                    <h3>Our Mission</h3>
                    <p>To source the finest dry fruits and spices directly from origins, process them under stringent
                        hygiene standards, and deliver them seamlessly to our B2B partners, empowering their businesses
                        to grow.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <div class="band"></div>
    <footer class="dk">
        <div class="wrap">
            <div class="fg">
                <div>
                    <a class="brand" href="index.php" style="color:var(--gold)"><?= mark(44) ?><span><b>NOOR AL
                                HIBA</b><small>TRADING LLC</small></span></a>
                    <p>An international import-export house connecting spices, dry fruits, oil seeds and agro
                        commodities with buyers in Dubai, the Middle East and Africa.</p>
                </div>
                <div>
                    <h4>Navigate</h4>
                    <ul>
                        <?php foreach ($nav as $url => $l): ?>
                        <li><a href="<?= $url ?>"><?= $l ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h4>Key export lines</h4>
                    <ul>
                        <li>Whole spices: chilli, pepper</li>
                        <li>Cashew W180, W240, W320</li>
                        <li>Almonds, pistachios, raisins</li>
                        <li>Cumin and coriander seeds</li>
                        <li>Saffron and cardamom</li>
                    </ul>
                </div>
                <div>
                    <h4>Trade desk</h4>
                    <ul>
                        <li>Dubai, UAE</li>
                        <li>Azadpur, Delhi, India</li>
                        <li><a href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a></li>
                        <li>Mon to Sat, 9:00 to 19:00 (GST)</li>
                    </ul>
                </div>
            </div>
            <div class="fb">
                <span>© <?= date('Y') ?> Noor Al Hiba Trading LLC. All rights reserved.</span>
                <span class="ar">نور الهبة للتجارة ذ.م.م</span>
            </div>
        </div>
    </footer>

    <!-- WhatsApp CTA -->
    <a class="wa" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"
        href="https://wa.me/<?= e($wa) ?>?text=Hello%20Noor%20Al%20Hiba%2C%20I%20want%20to%20know%20more%20about%20your%20company.">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="#fff" aria-hidden="true">
            <path d="M12 2.5a9.5 9.5 0 0 0-8.2 14.300L2.5 21.500l4.8-1.3A9.5 9.5 0 1 0 12 2.5z" />
            <circle cx="8" cy="12" r="1.3" fill="#25D366" />
            <circle cx="12" cy="12" r="1.3" fill="#25D366" />
            <circle cx="16" cy="12" r="1.3" fill="#25D366" />
        </svg>
    </a>

    <!-- Scripts for animations and mobile menu -->
    <script>
    (function() {
        var h = document.querySelector('header'),
            n = document.querySelector('nav'),
            t = document.querySelector('.tg');
        addEventListener('scroll', function() {
            h.classList.toggle('s', scrollY > 10)
        }, {
            passive: !0
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
    })();
    </script>
</body>

</html>