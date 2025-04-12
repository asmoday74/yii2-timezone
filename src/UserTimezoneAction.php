<?php

namespace asmoday74\timezone;

use Yii;
use DateTimeZone;

class UserTimezoneAction extends \yii\base\Action
{
    /**
     * @throws \yii\base\ExitException
     */
    public function run()
    {
        $timezone = Yii::$app->getRequest()->post('timezone', false);
        $zoneList = DateTimeZone::listIdentifiers();

        if (empty($timezone) || !in_array($timezone, $zoneList)) {
            $timezoneAbbr = Yii::$app->getRequest()->post('timezoneAbbr');
            $timezoneOffset = Yii::$app->getRequest()->post('timezoneOffset');
            $timezone = timezone_name_from_abbr($timezoneAbbr, $timezoneOffset * 3600);
        }

        if (!$timezone || !in_array($timezone, $zoneList)) {
            $timezone = "Europe/Moscow";
        }

        Yii::$app->formatter->timeZone = $timezone;

        Yii::$app->session->set('timezone', $timezone);
        Yii::$app->end();
    }
}