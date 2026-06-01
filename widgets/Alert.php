<?php

namespace app\widgets;

use yii\bootstrap4\Alert as BootstrapAlert;

class Alert extends \yii\base\Widget
{
    public $alertTypes = [
        'error'   => 'alert-danger',
        'danger'  => 'alert-danger',
        'success' => 'alert-success',
        'info'    => 'alert-info',
        'warning' => 'alert-warning'
    ];

    public $closeButton = [];
    
    public $options = [];

    public function run()
    {
        $session = \Yii::$app->session;
        $flashes = $session->getAllFlashes();
        $appendCss = isset($this->options['class']) ? ' ' . $this->options['class'] : '';

        foreach ($flashes as $type => $data) {
            if (isset($this->alertTypes[$type])) {
                $data = (array) $data;
                foreach ($data as $i => $message) {
                    echo BootstrapAlert::widget([
                        'body' => $message,
                        'closeButton' => $this->closeButton,
                        'options' => array_merge($this->options, [
                            'id' => $this->getId() . '-' . $type . '-' . $i,
                            'class' => $this->alertTypes[$type] . $appendCss,
                        ]),
                    ]);
                }
                $session->removeFlash($type);
            }
        }
    }
}
