<?php

namespace asmoday74\timezone;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;

/**
 * Class UserTimezone
 */
class UserTimezone extends \yii\base\Component
{
    /**
     * Action to check the user timezone
     * @var string
     */
    public $controllerName = 'timezone';

    /**
     * Default timezone name (ex: Europe/Moscow)
     * @var $defaultTimezone string
     */
    public $defaultTimezone = 'Europe/Moscow';

    /**
     * Current user timezone
     * @var string
     */
    private $_userTimezone;

    /**
     * @var string
     */
    private $_actionRoute;

    /**
     * Registering offset-getter if timezone is not set
     */
    public function init()
    {
        $this->_actionRoute = Url::toRoute('/' . $this->controllerName . '/index');
        $this->_userTimezone = Yii::$app->session->get('timezone');

        if (($this->_userTimezone == null) || ($this->_userTimezone == "0")) {
            $this->registerTimezoneScript($this->_actionRoute);
            $this->_userTimezone = $this->defaultTimezone;
        }

        Yii::$app->formatter->timeZone = $this->_userTimezone;
        Yii::$app->controllerMap[$this->controllerName] = 'asmoday74\timezone\controllers\TimezoneController';
    }


    /**
     * Returns the current timezone of the user
     * @return string
     */
    public function getName()
    {
        return $this->_userTimezone;
    }

    /**
     * Registering script for timezone detection on before action event
     * @param $actionRoute
     */
    private function registerTimezoneScript($actionRoute)
    {
        Yii::$app->on(Controller::EVENT_BEFORE_ACTION, function ($event) use ($actionRoute) {
            $view = $event->sender->view;
            $js = <<<JS
                var timezone = '';
                var timezoneAbbr = '';
                try {
                    timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                    timezoneAbbr = /\((.*)\)/.exec(new Date().toString())[1];
                    console.log(timezone);
                    console.log(timezoneAbbr);
                }
                catch(err) {
                    console.log(err);
                }
                $.post("$actionRoute", {
                    timezone: timezone,
                    timezoneAbbr: timezoneAbbr,
                    timezoneOffset: -new Date().getTimezoneOffset() / 60
                });
            JS;
            $view->registerJs($js);
        });
    }
}