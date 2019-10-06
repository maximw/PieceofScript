<?php

/**
 * @var array $properties
 */

?>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Environment</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table no-margin">
                <thead>
                <tr>
                    <th>Parameter</th>
                    <th>Value</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($properties as $param => $value): ?>
                <tr>
                    <td><?= htmlspecialchars($param) ?></td>
                    <td><?= htmlspecialchars($value) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>