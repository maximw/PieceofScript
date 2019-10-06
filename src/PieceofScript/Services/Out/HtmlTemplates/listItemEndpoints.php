<?php

use PieceofScript\Services\Out\OutToString;
use PieceofScript\Services\Statistics\StatAssertion;
use PieceofScript\Services\Statistics\StatEndpoint;

/**
 * @var StatEndpoint $statEndpoint
 */

$hash = md5($statEndpoint->getEndpoint()->getDefinition()->getOriginalString());
if ($statEndpoint->isSuccess() === true) {
    $status = 'success';
} elseif ($statEndpoint->isSuccess() === false) {
    $status = 'danger';
} else {
    $status = 'warning';
}
?>
    <a name="endpoint_<?= $hash ?>"></a>
    <div class="row">
        <div class="col-md-12">
            <div class="callout callout-<?= $status ?>">
                <h4><?= htmlspecialchars($statEndpoint->getEndpoint()->getDefinition()->getOriginalString()) ?></h4>
                <?= $statEndpoint->getEndpoint()->getFile() ?>
            </div>
        </div>
    </div>
<?php foreach ($statEndpoint->getCalls() as $key => $call): ?>
    <?php
    if ($call->isSuccess() === true) {
        $status = 'success';
    } elseif ($call->isSuccess() === false) {
        $status = 'danger';
    } else {
        $status = 'warning';
    }
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-<?= $status ?> collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <?= htmlspecialchars($call->getCode()) ?>
                    </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_info<?= $hash . $key ?>" data-toggle="tab" aria-expanded="true">Info</a>
                            </li>
                            <li><a href="#tab_request<?= $hash . $key ?>" data-toggle="tab" aria-expanded="false">Request</a>
                            </li>
                            <li><a href="#tab_response<?= $hash . $key ?>" data-toggle="tab" aria-expanded="false">Response</a>
                            </li>
                            <li><a href="#tab_assertions<?= $hash . $key ?>" data-toggle="tab" aria-expanded="false">Assertions</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_info<?= $hash . $key ?>">
                                <dl class="dl-horizontal">
                                    <dt style="text-align: left">Called at</dt>
                                    <dd><?= $call->getFile() . ' : ' . ($call->getLine() + 1) ?></dd>
                                    <dt style="text-align: left">Total assertions</dt>
                                    <dd><?= $call->countAssertions() ?></dd>
                                    <dt style="text-align: left">Success assertions</dt>
                                    <dd><?= $call->countSuccessAssertions() ?></dd>
                                    <dt style="text-align: left">Failed assertions</dt>
                                    <dd><?= $call->countFailedAssertions() ?></dd>
                                    <dt style="text-align: left">Total duration</dt>
                                    <dd><?= ($call->getEndDate() - $call->getStartDate()) ?> sec</dd>
                                    <dt style="text-align: left">API call duration</dt>
                                    <dd><?= $call->getResponse()['duration']->getValue() ?> sec</dd>
                                  </dl>
                            </div>
                            <div class="tab-pane" id="tab_request<?= $hash . $key ?>">
                                <?php OutToString::printRequest($call->getRequest()); ?>
                                <pre><?= OutToString::getBuffer() ?></pre>
                            </div>
                            <div class="tab-pane" id="tab_response<?= $hash . $key ?>">
                                <?php OutToString::printResponse($call->getResponse()); ?>
                                <pre><?= OutToString::getBuffer() ?></pre>
                            </div>
                            <div class="tab-pane" id="tab_assertions<?= $hash . $key ?>">
                                <table class="table">
                                    <?php foreach ($call->getAssertions() as $assertion): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($assertion->getCode()) ?>
                                            </td>
                                            <td>
                                                <?php if ($assertion->getStatus() === true): ?>
                                                    <span class="label label-success">Success</span>
                                                <?php elseif ($assertion->getStatus() === false): ?>
                                                    <span class="label label-danger">Failed</span>
                                                <?php else: ?>
                                                    <span class="label label-warning">Error</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <p><?= $assertion->getFile() . ': ' . ($assertion->getLine() + 1)?></p>
                                                <?php if ($assertion->getMessage()): ?>
                                                    <p>Error message: <?= htmlspecialchars($assertion->getMessage()) ?></p>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>