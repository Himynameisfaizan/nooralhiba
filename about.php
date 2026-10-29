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
    <?php include 'includes/footer.php'?>

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