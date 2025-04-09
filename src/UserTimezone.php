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
    public $actionRoute = '/site/timezone';

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
     * Registering offset-getter if timezone is not set
     */
    public function init()
    {
        $this->actionRoute = Url::toRoute($this->actionRoute);
        $this->_userTimezone = Yii::$app->session->get('timezone');

        if (($this->_userTimezone == null) || ($this->_userTimezone == "0")) {
            $this->registerTimezoneScript($this->actionRoute);
            $this->_userTimezone = $this->defaultTimezone;
        }

        Yii::$app->formatter->timeZone = $this->_userTimezone;
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
                let timezone = '';
                let timezoneAbbr = '';
                try {
                    timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                    timezoneAbbr = /\((.*)\)/.exec(new Date().toString())[1];
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