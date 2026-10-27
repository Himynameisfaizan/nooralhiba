    <header id="top">
        <div class="wrap nav">
            <a class="brand" href="index.php" aria-label="Noor Al Hiba Trading LLC">
                <?= mark(46) ?>
                <span><b>NOOR AL HIBA</b><small>TRADING LLC</small></span>
            </a>
            <button class="tg" aria-label="Menu" aria-expanded="false"><i></i></button>
            <nav>
                <ul>
                    <?php foreach ($nav as $id => $l): ?><li><a href="#<?= $id ?>"><?= $l ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <a href="#rfq" class="btn">Request a quote</a>
            </nav>
        </div>
    </header>