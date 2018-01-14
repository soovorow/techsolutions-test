<?php

namespace components;

use models\Model;

/**
 * @property Model model
 */
class ActiveInput extends Model
{
    public $label;
    public $model;
    public $type;
    public $property;

    public static function widget($params)
    {
        return (new ActiveInput($params))->render();
    }

    public function render()
    {
        $model_name = $this->model->getClassName();

        return implode([
            '<label>' . $this->label,
            '<input type="' . $this->type . '" 
                    name="' . $model_name . '[' . $this->property . ']" 
                    value="'.(isset($_GET[$model_name][$this->property]) ? $_GET[$model_name][$this->property] : null).'">',
            '</label>',
        ]);
    }
}