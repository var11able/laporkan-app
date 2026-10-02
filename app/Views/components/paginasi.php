<?php$pager->setSurroundCount(2);
?>
<?php if ($pager->getPageCount() > 1): ?>
<nav class="paginasi" aria-label="Halaman">
    <ul>
        <?php if ($pager->hasPreviousPage()): ?>
            <li><a href="<?= $pager->getPreviousPage() ?>" rel="prev"><?= ikon('chevron-left') ?><span class="sr-only">Halaman sebelumnya</span></a></li>
        <?php endif ?>
        <?php foreach ($pager->links() as $link): ?>
            <li>
                <?php if ($link['active']): ?>
                    <span aria-current="page"><span class="sr-only">Halaman </span><?= $link['title'] ?></span>
                <?php else: ?>
                    <a href="<?= $link['uri'] ?>"><span class="sr-only">Halaman </span><?= $link['title'] ?></a>
                <?php endif ?>
            </li>
        <?php endforeach ?>
        <?php if ($pager->hasNextPage()): ?>
            <li><a href="<?= $pager->getNextPage() ?>" rel="next"><span class="sr-only">Halaman berikutnya</span><?= ikon('chevron-right') ?></a></li>
        <?php endif ?>
    </ul>
</nav>
<?php endif ?>
