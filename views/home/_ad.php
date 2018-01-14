<?php
/**
 * @var $model \models\Ad
 * @var $i integer
 */
?>
<tr>
    <td><?= $i ?></td>
    <td><?= $model->address ?></td>
    <td><?= $model->floor ?> / <?= $model->building_height ?></td>
    <td><?= $model->building_type ?></td>
    <td>
        <?= implode(', ', [
            $model->total_area,
            $model->living_area,
            $model->kitchen_area,
        ]) ?>
    </td>
    <td><?= $model->bathroom ?></td>
    <td><?= $model->subject ?></td>
    <td><?= $model->contact ?></td>
    <td><?= $model->note ?></td>
</tr>