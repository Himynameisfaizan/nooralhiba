  <?php
  $wa = '971500000000';
$mail = 'export@nooralhibatrading.com';
try {
    $conn = new mysqli('localhost', 'root', '', 'nooralhiba');
} // Hostinger: apna DB user/pass/name yahan dalein
catch (Throwable $t) {
    $conn = null;
}
$ok = $conn && !$conn->connect_error;
if ($ok) $conn->set_charset('utf8mb4');
$nav = ['about.php' => 'About', 'products' => 'Products', 'services' => 'Services', 'corridors' => 'Trade routes', 'gallery' => 'Gallery', 'blogs' => 'Insights'];

  ?>

  <header id="top">
      <div class="wrap nav">
          <a class="brand" href="index.php" aria-label="Noor Al Hiba Trading LLC">
              <?= mark(46) ?>
              <span><b>NOOR AL HIBA</b><small>TRADING LLC</small></span>
          </a>
          <button class="tg" aria-label="Menu" aria-expanded="false"><i></i></button>
          <nav>
              <ul>
                  <?php foreach ($nav as $id => $l): ?><li><a href="<?= $url ?>"><?= $l ?></a></li>
                  <?php endforeach; ?>
              </ul>
              <a href="#rfq" class="btn">Request a quote</a>
          </nav>
      </div>
  </header>