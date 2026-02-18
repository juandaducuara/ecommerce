<?php $pager->setSurroundCount(2) ?>

<nav class="flex items-center justify-center gap-1 px-4 py-3 mt-4" aria-label="Paginación">
    <?php if ($pager->hasPrevious()): ?>
        <a href="<?= $pager->getPrevious() ?>"
           class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            ‹ Anterior
        </a>
    <?php else: ?>
        <span class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed select-none">
            ‹ Anterior
        </span>
    <?php endif ?>

    <?php foreach ($pager->links() as $link): ?>
        <?php if ($link['active']): ?>
            <span class="px-3 py-1.5 text-sm border border-indigo-500 bg-indigo-600 text-white rounded-lg font-medium">
                <?= $link['title'] ?>
            </span>
        <?php else: ?>
            <a href="<?= $link['uri'] ?>"
               class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <?= $link['title'] ?>
            </a>
        <?php endif ?>
    <?php endforeach ?>

    <?php if ($pager->hasNext()): ?>
        <a href="<?= $pager->getNext() ?>"
           class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            Siguiente ›
        </a>
    <?php else: ?>
        <span class="px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed select-none">
            Siguiente ›
        </span>
    <?php endif ?>
</nav>
