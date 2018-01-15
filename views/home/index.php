<?php
/**
 * @var $data models\Ad[];
 * @var $search models\AdSearch;
 * @var $this \views\View;
 */

?>

<div>

    <form id="form" action="/">

        <div>
            Количество комнат:
            <?= \components\ActiveInput::widget([
                'label' => 'от',
                'type' => 'number',
                'model' => $search,
                'property' => 'kkv1',
            ]) ?>
            <?= \components\ActiveInput::widget([
                'label' => 'до',
                'type' => 'number',
                'model' => $search,
                'property' => 'kkv2',
            ]) ?>
        </div>

        <div>
            Цена:
            <?= \components\ActiveInput::widget([
                'label' => 'от',
                'type' => 'number',
                'model' => $search,
                'property' => 'price1',
            ]) ?>
            <?= \components\ActiveInput::widget([
                'label' => 'до',
                'type' => 'number',
                'model' => $search,
                'property' => 'price2',
            ]) ?>
        </div>

        <div>
            <?= \components\ActiveSelect::widget([
                'label' => 'Метро',
                'multiple' => true,
                'model' => $search,
                'property' => 'metro',
                'options' => $search->metro_options
            ]) ?>
        </div>

        <div>
            <input type="submit" value="Найти">
            <a href="/">сбросить</a>
        </div>
    </form>

</div>

<!--<pre>-->
<!--    --><?//= print_r($data) ?>
<!--</pre>-->

<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>Адрес</th>
        <th>Метро</th>
        <th>Этаж</th>
        <th>Тип дома</th>
        <th>Площадь (общая, жилая, кухня)</th>
        <th>Санузел</th>
        <th>Субъект</th>
        <th>Контакт</th>
        <th>Доп. сведения</th>
    </tr>
    </thead>
    <tbody id="results">
    <?php if (count($data) > 0) {
        foreach ($data as $i => $model) {
            $this->render('_ad', [
                'model' => $model,
                'i' => $i + 1
            ]);
        }
    } ?>
    </tbody>
</table>
