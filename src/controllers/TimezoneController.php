<?php

namespace asmoday74\timezone\controllers;

use Yii;
use yii\web\Controller;
use DateTimeZone;

class TimezoneController extends Controller
{
    public function actionIndex()
    {
        $timezone = Yii::$app->getRequest()->post('timezone', false);
        $zoneList = DateTimeZone::listIdentifiers();

        if (empty($timezone) || !in_array($timezone, $zoneList)) {
            $timezoneAbbr = Yii::$app->getRequest()->post('timezoneAbbr');
            $timezoneOffset = Yii::$app->getRequest()->post('timezoneOffset');
            if (is_int($timezoneOffset) && is_string($timezoneAbbr)) {
                $timezone = timezone_name_from_abbr($timezoneAbbr, $timezoneOffset * 3600);
            }
        }

        if (!$timezone || !in_array($timezone, $zoneList)) {
            $timezone = "Europe/Moscow";
        } else {
            Yii::$app->session->set('timezone', $timezone);
        }

        Yii::$app->formatter->timeZone = $timezone;

        Yii::$app->end();
    }
}