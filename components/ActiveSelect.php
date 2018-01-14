<?php

namespace components;

use models\Model;

/**
 * @property Model model
 */
class ActiveSelect extends Model
{
    public $label;
    public $multiple;
    public $model;
    public $property;
    public $options;

    public static function widget($params)
    {
        return (new ActiveSelect($params))->render();
    }

    public function render()
    {

        $multiple = $this->multiple ? 'multiple' : '';
        $multipart = $this->multiple ? '[]' : '';

        $model_name = $this->model->getClassName();

        $selected = [];
        if (isset($_GET[$model_name][$this->property])) {
            foreach ($_GET[$model_name][$this->property] as $k) {
                $selected[] = $k;
            }
        }

        $options = [];
        if ($this->options) {
            foreach ($this->options as $option) {
                $options[] = implode([
                    '<option value="' . $option['value'] . '" ' . (in_array($option['value'], $selected) ? 'selected' : null) . '>',
                    $option['option'],
                    '</option>',
                ]);
            }
        }

        return implode([
            '<label>' . $this->label,
            '<select ' . $multiple . ' name="' . $model_name . '[' . $this->property . ']' . $multipart . '">',
            implode($options),
            '</select>',
            '</label>',
        ]);
    }
}