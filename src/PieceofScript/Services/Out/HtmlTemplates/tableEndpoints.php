<?php

use PieceofScript\Services\Statistics\Statistics;

/**
 * @var Statistics $stat
 */

?>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Endpoints list</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table no-margin">
                <thead>
                <tr>
                    <th>Endpoint</th>
                    <th>Status</th>
                    <th>Calls</th>
                    <th>Success calls</th>
                    <th>Failed calls</th>
                    <th>Assertions</th>
                    <th>Success assertions</th>
                    <th>Failed assertions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($stat->getStatistics() as $statEndpoint): ?>
                <tr>
                    <td>
                        <a href="#endpoint_<?= md5($statEndpoint->getEndpoint()->getDefinition()->getOriginalString()) ?>">
                            <?= htmlspecialchars($statEndpoint->getEndpoint()->getDefinition()->getOriginalString()) ?>
                        </a>
                    </td>
                    <td>
                        <?php if ($statEndpoint->isSuccess() === true): ?>
                            <span class="label label-success">Success</span>
                        <?php elseif ($statEndpoint->isSuccess() === false): ?>
                            <span class="label label-danger">Failed</span>
                        <?php else: ?>
                            <span class="label label-warning">Not tested</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $statEndpoint->countCalls() ?></td>
                    <td><?= $statEndpoint->countSuccessCalls() ?></td>
                    <td><?= $statEndpoint->countFailedCalls() ?></td>
                    <td><?= $statEndpoint->countAssertions() ?></td>
                    <td><?= $statEndpoint->countSuccessAssertions() ?></td>
                    <td><?= $statEndpoint->countFailedAssertions() ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>