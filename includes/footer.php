<?php
/** NOOR AL HIBA TRADING LLC - Global Footer */
// Required variables are already loaded via header.php
?>
<!-- Dark Footer Strip -->
<div class="band"></div>
<footer class="dk">
    <style>
    footer.dk {
        position: relative;
        overflow: hidden;
        background: var(--ink2);
        color: #fff;
    }

    footer.dk::before {
        content: '';
        position: absolute;
        inset: 0;
        background: var(--lat);
        opacity: .08;
        pointer-events: none;
    }

    footer .wrap {
        padding: clamp(48px, 7vw, 80px) 0 28px;
        position: relative;
    }

    .fg {
        display: grid;
        grid-template-columns: 1.4fr .8fr 1.1fr 1.1fr;
        gap: 36px;
        padding-bottom: 36px;
        border-bottom: 1px solid rgba(255, 255, 255, .12);
    }

    footer .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--gold);
        margin-bottom: 16px;
    }

    footer .brand b {
        display: block;
        font: 400 1.2rem/1 Marcellus, serif;
        letter-spacing: .09em;
        color: #fff;
    }

    footer .brand small {
        font-size: .64rem;
        letter-spacing: .32em;
        font-weight: 600;
    }

    footer p,
    footer li {
        font-size: .9rem;
        color: rgba(255, 255, 255, .7);
        margin-bottom: 8px;
    }

    footer h4 {
        font: 400 1.15rem Marcellus, serif;
        color: var(--gold);
        margin-bottom: 16px;
    }

    footer a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s;
    }

    footer a:hover {
        color: var(--gold);
    }

    .fb {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        padding-top: 24px;
        font-size: .84rem;
        color: rgba(255, 255, 255, .55);
    }

    .wa-float {
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
        transition: transform .35s var(--e);
    }

    .wa-float:hover {
        transform: scale(1.1);
    }

    @media(max-width:960px) {
        .fg {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media(max-width:600px) {
        .fg {
            grid-template-columns: 1fr;
        }
    }

    /* Animation Classes */
    .js .rv {
        opacity: 0;
        transform: translateY(22px);
        transition: opacity .9s cubic-bezier(.2, .7, .2, 1), transform .9s cubic-bezier(.2, .7, .2, 1);
    }

    .js .rv.in {
        opacity: 1;
        transform: none;
    }
    </style>

    <div class="wrap">
        <div class="fg">
            <div>
                <a class="brand" href="index.php">
                    <?= mark(44) ?>
                    <span><b>NOOR AL HIBA</b><small>TRADING LLC</small></span>
                </a>
                <p>An international import-export house connecting spices, dry fruits, oil seeds and agro commodities
                    with buyers in Dubai, the Middle East and Africa.</p>
            </div>
            <div>
                <h4>Navigate</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="index.php#services">Services</a></li>
                    <li><a href="blog.php">Trade Insights</a></li>
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
            <span class="ar" style="font-family: 'Reem Kufi', serif; color: var(--gold); font-weight: 500;">نور الهبة
                للتجارة ذ.م.م</span>
        </div>
    </div>
</footer>

<!-- WhatsApp Floating Button -->
<a class="wa-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"
    href="https://wa.me/<?= e($wa) ?>?text=Hello%20Noor%20Al%20Hiba%2C%20we%20have%20a%20bulk%20import-export%20inquiry.">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="#fff" aria-hidden="true">
        <path d="M12 2.5a9.5 9.5 0 0 0-8.2 14.300L2.5 21.500l4.8-1.3A9.5 9.5 0 1 0 12 2.5z" />
        <circle cx="8" cy="12" r="1.3" fill="#25D366" />
        <circle cx="12" cy="12" r="1.3" fill="#25D366" />
        <circle cx="16" cy="12" r="1.3" fill="#25D366" />
    </svg>
</a>

<!-- Global Scripts (Sticky Header, Mobile Menu & Scroll Animations) -->
<script>
(function() {
    // Header Sticky
    var h = document.querySelector('.site-header'),
        n = document.querySelector('nav'),
        t = document.querySelector('.tg');

    window.addEventListener('scroll', function() {
        if (h) h.classList.toggle('s', window.scrollY > 10);
    }, {
        passive: true
    });

    // Mobile Menu Toggle
    if (t && n) {
        t.onclick = function() {
            n.classList.toggle('o');
            t.setAttribute('aria-expanded', n.classList.contains('o'));
        };

        // Mobile Dropdown Click Logic
        var ddTriggers = n.querySelectorAll('.dd-icon');
        ddTriggers.forEach(function(icon) {
            icon.addEventListener('click', function(e) {
                e.preventDefault();
                var parentLi = this.closest('li.has-dropdown');
                if (parentLi) parentLi.classList.toggle('open');
            });
        });

        var subDdTriggers = n.querySelectorAll('.sub-icon');
        subDdTriggers.forEach(function(icon) {
            icon.addEventListener('click', function(e) {
                e.preventDefault();
                var parentLi = this.closest('li.has-sub-dropdown');
                if (parentLi) parentLi.classList.toggle('open');
            });
        });
    }

    // Reveal Animations on Scroll
    var els = document.querySelectorAll('.rv');
    if (!('IntersectionObserver' in window)) {
        els.forEach(function(x) {
            x.classList.add('in');
        });
    } else {
        var io = new IntersectionObserver(function(es) {
            es.forEach(function(x) {
                if (!x.isIntersecting) return;
                var el = x.target;
                el.classList.add('in');
                io.unobserve(el);
                setTimeout(function() {
                    el.style.transitionDelay = '';
                }, 1500);
            });
        }, {
            threshold: .12
        });

        els.forEach(function(el, i) {
            el.style.transitionDelay = (i % 4) * 80 + 'ms';
            io.observe(el);
        });
    }
})();
</script>
</body>

</html>