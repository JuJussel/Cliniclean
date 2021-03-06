<?php
namespace App\Http\Controllers;

use App\Database\Queries\Setting;

class Healthcheckexams {

    public function all($req, $res) {

        $db_data = new Setting();

        $query = $db_data->get('kenkoushindan_kensa_list    ');
        if ($query->ok) {
            $res->success = true;    
            $query = json_decode($query->data[0]['config_value'], true);
            $res->data = [];
            foreach ($query as $key => $value) {
                array_push($res->data, $value);
            }
        } else {
            $res->message = $query->msg;
        }

    }

}