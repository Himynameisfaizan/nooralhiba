<?php
/** NOOR AL HIBA TRADING LLC - Contact Us Page */

// 1. Database Connection (Header se pehle SEO nikalne ke liye)
require_once __DIR__ . '/config/connect.php';
$ok = isset($conn) && !$conn->connect_error;

// 2. FETCH SEO EXPLICITLY FOR CONTACT PAGE
if ($ok) {
    // Exact 'Contact Us' page_name se SEO uthayenge, taaki URL ka issue na aaye
    $seo_check = $conn->query("SELECT meta_title, meta_desc, meta_key FROM meta WHERE page_name = 'Contact Us' LIMIT 1");
    if ($seo_check && $seo_check->num_rows > 0) {
        $seo_row = $seo_check->fetch_assoc();
        $meta_title = $seo_row['meta_title'];
        $meta_desc  = $seo_row['meta_desc'];
        $meta_key   = $seo_row['meta_key'];
    }
    
    // Schema fetch
    $schema_check = $conn->query("SELECT schema_markup FROM page_schemas WHERE page_name = 'Contact Us' LIMIT 1");
    if ($schema_check && $schema_check->num_rows > 0) {
        $schema = $schema_check->fetch_assoc()['schema_markup'];
    }
}

// 3. INCLUDE HEADER (Ye automatically upar wale $meta_title aur $schema ko use karega)
include_once 'includes/header.php';

// ---- Fetch Settings (Email, Phone, Address, Map) from `contacts` table ----
$contact_email = 'export@nooralhibatrading.com';
$contact_phone = '+971 50 000 0000';
$contact_address = 'Dubai, United Arab Emirates';
$map_iframe = '';

if ($ok) {
    $table_check_contacts = $conn->query("SHOW TABLES LIKE 'contacts'");
    if ($table_check_contacts && $table_check_contacts->num_rows > 0) {
        $db_set = $conn->query("SELECT email, phone, address, map FROM contacts LIMIT 1");
        if ($db_set && $db_set->num_rows > 0) {
            $row = $db_set->fetch_assoc();
            $contact_email = !empty($row['email']) ? $row['email'] : $contact_email;
            $contact_phone = !empty($row['phone']) ? $row['phone'] : $contact_phone;
            $contact_address = !empty($row['address']) ? $row['address'] : $contact_address;
            
            // --- FIX FOR MAP RENDERING ---
            $raw_map = trim($row['map']);
            if (!empty($raw_map)) {
                // Agar DB mein link <iframe se shuru nahi hota, toh usko iframe mein wrap kardo
                if (strpos($raw_map, '<iframe') === false) {
                    $map_iframe = '<iframe src="' . htmlspecialchars($raw_map, ENT_QUOTES, 'UTF-8') . '" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';
                } else {
                    $map_iframe = $raw_map;
                }
            }
        }
    }
}

// Fallback Map
if (empty($map_iframe)) {
    $map_iframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14008.114827184209!2d77.1630325!3d28.7040592!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d02330a133d1b%3A0xc6651da039b4b9fb!2sAzadpur%2C%20Delhi!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';
}

// ---- Handling Form Submission ----
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    if ($ok) {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $subject = $conn->real_escape_string($_POST['subject']);
        $message = $conn->real_escape_string($_POST['message']);
        
        $table_check_inq = $conn->query("SHOW TABLES LIKE 'inquiries'");
        if ($table_check_inq && $table_check_inq->num_rows > 0) {
            $sql = "INSERT INTO inquiries (name, email, phone, subject, message) VALUES ('$name', '$email', '$phone', '$subject', '$message')";
            if ($conn->query($sql)) {
                $msg = "<div class='alert success'>Thank you! Your inquiry has been sent successfully. Our team will contact you soon.</div>";
            } else {
                $msg = "<div class='alert error'>Database Error: " . $conn->error . "</div>";
            }
        } else {
            $msg = "<div class='alert success'>Thank you! Your message has been received.</div>";
        }
    }
}

// 4. Setup Breadcrumb (Ab Title Fix Hai)
$pageTitle = "Contact Us";
include_once 'includes/breadcrumb.php';
?>

<style>
.contact-layout {
    padding: 80px 0;
}

.contact-grid {
    display: grid;
    grid-template-columns: 4fr 8fr;
    gap: clamp(40px, 6vw, 60px);
    align-items: start;
}

/* FIX FOR UI: Info Cards (Proper Flex Layout) */
.info-boxes {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.info-card {
    background: #fff;
    border: 1px solid var(--line);
    padding: 25px;
    border-radius: var(--r);
    display: flex;
    align-items: flex-start;
    gap: 20px;
    text-align: left;
    transition: transform 0.3s;
    border-left: 4px solid var(--gold);
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
}

.info-card .icn {
    width: 50px;
    height: 50px;
    background: var(--sand);
    color: var(--bronze);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}

.info-card .txt h3 {
    font-size: 1.15rem;
    margin-bottom: 5px;
    color: var(--ink);
    font-family: 'Hanken Grotesk', sans-serif;
    font-weight: 700;
}

.info-card .txt p,
.info-card .txt a {
    font-size: 0.95rem;
    color: var(--mut);
    margin: 0;
    word-break: break-word;
    line-height: 1.5;
}

.info-card .txt a:hover {
    color: var(--bronze);
}

/* Form */
.contact-form-box {
    background: #fff;
    border: 1px solid var(--line);
    padding: 40px;
    border-radius: var(--r);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
}

.contact-form-box h2 {
    font-size: 2rem;
    margin-bottom: 10px;
    color: var(--ink);
}

.contact-form-box>p {
    color: var(--mut);
    margin-bottom: 30px;
    font-size: 0.95rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group.full {
    grid-column: 1 / -1;
}

.form-group label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--ink);
}

.form-group input,
.form-group textarea {
    padding: 12px;
    border: 1px solid var(--line);
    border-radius: var(--r);
    font-family: inherit;
    transition: 0.3s;
    background: var(--paper);
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(214, 172, 86, 0.15);
}

.form-group .btn {
    background: var(--ink);
    color: #fff;
    border: none;
    padding: 15px;
    border-radius: var(--r);
    width: 100%;
    justify-content: center;
    transition: 0.3s;
}

.form-group .btn:hover {
    background: var(--bronze);
}

.alert {
    padding: 15px;
    border-radius: var(--r);
    margin-bottom: 20px;
    font-weight: 500;
    font-size: 0.95rem;
}

.alert.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* FIX FOR MAP */
.map-section {
    margin-top: 60px;
    border-radius: var(--r);
    overflow: hidden;
    height: 450px;
    border: 1px solid var(--line);
    background: #eee;
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

@media(max-width: 960px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }

    .info-boxes {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }
}

@media(max-width: 600px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="contact-layout">
    <div class="wrap contact-grid">

        <!-- Left: Contact Details (UI Fixed) -->
        <div class="info-boxes rv">
            <div class="info-card">
                <div class="icn"><i class="fa-solid fa-location-dot"></i></div>
                <div class="txt">
                    <h3>Our Office</h3>
                    <p><?= nl2br(e($contact_address)) ?></p>
                </div>
            </div>

            <div class="info-card">
                <div class="icn"><i class="fa-solid fa-phone"></i></div>
                <div class="txt">
                    <h3>Call Us</h3>
                    <a href="tel:<?= e($contact_phone) ?>"><?= e($contact_phone) ?></a>
                </div>
            </div>

            <div class="info-card">
                <div class="icn"><i class="fa-solid fa-envelope"></i></div>
                <div class="txt">
                    <h3>Email Us</h3>
                    <a href="mailto:<?= e($contact_email) ?>"><?= e($contact_email) ?></a>
                </div>
            </div>
        </div>

        <!-- Right: Contact Form -->
        <div class="contact-form-box rv" style="transition-delay: 0.1s;">
            <h2>Send an Inquiry</h2>
            <p>Looking for a bulk quotation or have a question about our export process? Fill out the form below and our
                team will get back to you promptly.</p>

            <?= $msg ?>

            <form action="" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="name" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" required placeholder="import@company.com">
                    </div>
                    <div class="form-group">
                        <label>Phone / WhatsApp *</label>
                        <input type="tel" name="phone" required placeholder="+971 50 000 0000">
                    </div>
                    <div class="form-group">
                        <label>Subject *</label>
                        <input type="text" name="subject" required placeholder="Bulk Order Inquiry">
                    </div>
                    <div class="form-group full">
                        <label>Your Message *</label>
                        <textarea name="message" rows="5" required
                            placeholder="Please specify product, volume, and destination port..."></textarea>
                    </div>
                    <div class="form-group full">
                        <button type="submit" name="submit_contact" class="btn">Send Message <i
                                class="fa-solid fa-paper-plane ms-2"></i></button>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <!-- Map Section (Map Iframe Fixed) -->
    <div class="wrap rv">
        <div class="map-section">
            <?= $map_iframe ?>
        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>

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
            threshold: .05
        });
        els.forEach(function(el, i) {
            el.style.transitionDelay = (i % 3) * 100 + 'ms';
            io.observe(el);
        });
    }
})();
</script>