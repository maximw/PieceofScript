<?php

use PieceofScript\Services\Out\View;
use PieceofScript\Services\Statistics\Statistics;

/**
 * @var View $this
 * @var Statistics $stat
 */

?>
<?php foreach ($stat->getStatistics() as $statEndpoint): ?>
    <?= $this->render('listItemEndpoints', ['statEndpoint' => $statEndpoint]) ?>
<?php endforeach; ?>
