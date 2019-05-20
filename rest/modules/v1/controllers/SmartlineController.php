<?php

namespace rest\modules\v1\controllers;

use yii\rest\Controller;

/**
 * Class SmartlineController
 * @package rest\modules\v1\controllers
 */
class SmartlineController extends Controller
{
    /**
     * @var string
     */
    public $xml;

    /**
     * SmartlineController constructor.
     *
     * @param $id
     * @param $module
     * @param array $config
     */
    public function __construct($id, $module, $config = [])
    {
        $this->xml = simplexml_load_file(\Yii::getAlias('@rest') .'/web/files/057-6776453695.xml');
        parent::__construct($id, $module, $config);
    }

    /**
     * @return string
     */
    public function actionStart()
    {
        $arr = [];
        foreach ($this->xml->AirSegments->AirSegment as $item) {
            $board = explode('/', (string)$item->Board['City']);
            $board = ucfirst(strtolower($board[0]));
            $off = explode('/', (string)$item->Off['City']);
            $off = ucfirst(strtolower($off[0]));

            $arr[] = [
                'departure' => $item->Departure['Date'] . ' ' . $item->Departure['Time'],
                'arrival' => $item->Arrival['Date'] . ' ' . $item->Arrival['Time'],
                'board' => $board,
                'off' => $off,
            ];
        }

        usort($arr, function ($a, $b) {
            return strcmp($a["departure"], $b["departure"]);
        });

        $result = '';
        for ($i = 0; $i < count($arr); $i++) {
            if ($i == 0) {
                $result = $arr[$i]['board'];
            } elseif ($i == count($arr) - 1) {
                $result .= ' - ' . $arr[$i]['board'] . ' - ' . $arr[$i]['off'];
            } else {
                if ($arr[$i]['board'] == $arr[$i-1]['off']) {
                    $result .= ' - ' . $arr[$i]['board'];
                } else {
                    $result .= ' - точка разрыва - ' . $arr[$i]['board'];
                }
            }

        }

        return $result;
    }
}
