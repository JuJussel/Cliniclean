<?php
namespace App\Http\Controllers;

use App\Database\Queries\User;

class Users {

    private function passwordStrong($res, $pass) {

        $uppercase = preg_match('@[A-Z]@', $pass);
        $lowercase = preg_match('@[a-z]@', $pass);
        $number    = preg_match('@[0-9]@', $pass);
        $specialChars = preg_match('@[^\w]@', $pass);
        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($pass) < 8) {
            $res->message = "パスワード条件満たしてません。";
            return false;
        }
        return true;

    }

    public function get($req, $res) {

        if (!$req->post) {
            
            $id = $_SESSION['user'];

            $db = new User();
            $query = $db->get($id);

            if(!$query->ok) {
                $res->message = $query->msg;
                return;
            }

            $res->data = $query->data[0];
            $res->success = true;

        }

        $res->success = true;

    }

    public function all($req, $res) {

        $db = new User();
        $query = $db->all();

        if(!$query->ok) {
            $res->message = $query->msg;
            return;
        }
        foreach ($query->data as $key => $value) {
            $query->data[$key]['has_orca'] = boolval($value['has_orca']);
            $query->data[$key]['active'] = boolval($value['active']);
        }
        $res->data = $query->data;
        $res->success = true;

    }

    public function update($req, $res) {

        $request_data = $req->update[0];
        $db = new User();
        
        if (isset($request_data->changePassword)) {

            if (!$this->passwordStrong($res,$request_data->password)) {
                return;
            }
    
            $query = $db->password($request_data);

            if(!$query->ok) {
                $res->message = $query->msg;
                return;
            }
            $res->success = true;
            return;
    
        }

        $query = $db->update($request_data);

        if(!$query->ok) {
            $res->message = $query->msg;
            return;
        }
        $res->success = true;

    }

    public function post($req, $res) {

        $request_data = $req->post->data;

        if (!$this->passwordStrong($res,$request_data->password)) {
            return;
        }

        $db = new User();
        $query = $db->create($request_data);

        if(!$query->ok) {
            $res->message = $query->msg;
            return;
        }

        //copy initial avatar


        $res->success = true;

    }




}