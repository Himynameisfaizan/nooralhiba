<?php
/** NOOR AL HIBA TRADING LLC - Contact Us Page */
$page_title = 'Contact Us | Noor Al Hiba Trading LLC';

// 1. Include Header (DB Connection aur Functions isme hain)
include_once 'includes/header.php';

// ---- Fetch Settings (Email, Phone, Address, Map) ----
$contact_email = 'export@nooralhibatrading.com';
$contact_phone = '+971 50 000 0000';
$contact_address = 'Dubai, United Arab Emirates';
$map_iframe = '';

if ($ok) {
    $table_check_set = q("SHOW TABLES LIKE 'settings'");
    if (count($table_check_set) > 0) {
        $db_set = q("SELECT email, phone, address, map_iframe FROM settings LIMIT 1");
        if (!empty($db_set)) {
            $contact_email = !empty($db_set[0]['email']) ? $db_set[0]['email'] : $contact_email;
            $contact_phone = !empty($db_set[0]['phone']) ? $db_set[0]['phone'] : $contact_phone;
            $contact_address = !empty($db_set[0]['address']) ? $db_set[0]['address'] : $contact_address;
            $map_iframe = !empty($db_set[0]['map_iframe']) ? $db_set[0]['map_iframe'] : '';
        }
    }
}

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
        
        // Checking if inquiries table exists
        $table_check_inq = q("SHOW TABLES LIKE 'inquiries'");
        if (count($table_check_inq) > 0) {
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

// 2. Setup Breadcrumb
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

/* Info Cards */
.info-boxes {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.info-card {
    background: #fff;
    border: 1px solid var(--line);
    padding: 30px;
    border-radius: var(--r);
    text-align: center;
    transition: transform 0.3s;
    border-bottom: 3px solid var(--gold);
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
}

.info-card svg {
    width: 40px;
    height: 40px;
    color: var(--bronze);
    margin-bottom: 16px;
}

.info-card h3 {
    font-size: 1.2rem;
    margin-bottom: 10px;
    color: var(--ink);
}

.info-card p,
.info-card a {
    font-size: 0.95rem;
    color: var(--mut);
    text-decoration: none;
    display: block;
}

.info-card a:hover {
    color: var(--bronze);
}

/* Form */
.contact-form-box {
    background: var(--paper);
    border: 1px solid var(--line);
    padding: 40px;
    border-radius: var(--r);
}

.contact-form-box h2 {
    font-size: 2rem;
    margin-bottom: 10px;
}

.contact-form-box>p {
    color: var(--mut);
    margin-bottom: 30px;
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
    background: #fff;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(214, 172, 86, 0.1);
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

/* Map */
.map-section {
    margin-top: 60px;
    border-radius: var(--r);
    overflow: hidden;
    height: 450px;
    border: 1px solid var(--line);
    background: #eee;
}

@media(max-width: 960px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }

    .info-boxes {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

        <!-- Left: Contact Details -->
        <div class="info-boxes rv">
            <div class="info-card">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                <h3>Our Office</h3>
                <p><?= nl2br(e($contact_address)) ?></p>
            </div>

            <div class="info-card">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.54-4.24-7.136-7.136l1.292-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                </svg>
                <h3>Call Us</h3>
                <a href="tel:<?= e($contact_phone) ?>"><?= e($contact_phone) ?></a>
            </div>

            <div class="info-card">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
                <h3>Email Us</h3>
                <a href="mailto:<?= e($contact_email) ?>"><?= e($contact_email) ?></a>
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
                        <input type="email" name="email" required placeholder="john@example.com">
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
                        <button type="submit" name="submit_contact" class="btn"
                            style="width: 100%; justify-content: center;">Send Message</button>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <!-- Map Section -->
    <div class="wrap rv">
        <div class="map-section">
            <?= $map_iframe ?>
        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>