<?php
namespace App\Http\Controllers;

use App\Database\Queries\Setting;
use stdClass;

class Settings {

    public function all($req, $res) {
    }

    public function get($req, $res) {

        $query_data = $req->itemId;
        if ($query_data == 0) {
            $settings_array = $GLOBALS['config'];
            
            foreach ($settings_array as $key => $value) {
                foreach ($value as $s_key => $s_value) {
                    $value[$s_key] = [
                        'value' => $s_value,
                        'type' => gettype($s_value)
                    ];
                }
                $settings_array[$key] = $value;
            }


            $res->data = $settings_array;
            $res->success = true;
            return;
        }

        $db = New Setting();
        $query = $db->get($query_data);
        if (!$query->ok) {
            $res->message = $query->msg;
        } else {
            $res->data = json_decode($query->data[0]['config_value']);
            $res->success = true;
        }
    }

    public function update($req, $res) {
        
        $item_id = $req->itemId;
        $req_data = $req->update[0];

        $query_data = (object) [
            'id' => $item_id,
            'value' => $req_data
        ];

        $db = New Setting();
        $query = $db->update($query_data);
        if (!$query->ok) {
            $res->message = $query->msg;
        } else {
            $res->success = true;
        }



    }

}