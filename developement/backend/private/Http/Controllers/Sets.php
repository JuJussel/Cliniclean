<?php
namespace App\Http\Controllers;

use App\Database\Queries\Set;

class Sets {

    private function populate_branch($node, $nodes_by_parent) {

        $children = $nodes_by_parent[$node['id']] ?? [];

        foreach ($children as &$child){
            $child = $this->populate_branch($child, $nodes_by_parent);
        }
    
        $node['children'] = $children;
    
        return $node;    

    }

    public function all($req, $res) {

        $db = new Set();
        $query = $db->all();

        if (!$query->ok) {
            $res->message = $query->msg;
            return;
        }

        $flat_list = $query->data;
        $patient_node_ids = [];

        $tree = array();
        $patient_node = [
            'text' => '患者のみ',
            'id' => 'noParent',
            'children' => [],
            'data' => [
                'isFolder' => true
            ]
        ];

        $parent_nodes = [
            $patient_node
        ];
        $nodes_by_parent = array();

        foreach ($flat_list as $node) {
            $node['content'] = json_decode($node['content'], true);            
            $patient = $node['patient'];
            $patient_name = $node['name'];
            
            $node = [
                'parent' => $node['parent'],
                'id' => $node['id'],
                'data' => [
                    'id' => $node['id'],
                    'isFolder' => $node['folder'] == 1 ? true : false,
                    'content' => $node['content'],
                    'patient' => $patient
                ],
                'text' => $node['text'] = $node['content']['name']
            ];

            if ($node['parent']) {
                $nodes_by_parent[$node['parent']][] = $node;
            } else {
                if($patient) {
                    $newParentId = 'p_' . $patient;
                    
                    if (!in_array($newParentId, $patient_node_ids)) {
                        $nodes_by_parent['noParent'][] = [
                            'text' => $patient_name,
                            'id' => $newParentId,
                            'children' => [],
                            'data' => [
                                'isFolder' => true,
                                'patient' => $patient
                            ]            
                        ];
                    }

                    $node['parent'] = $newParentId;
                    $nodes_by_parent[$newParentId][] = $node;
                    array_push($patient_node_ids, $newParentId);

                }
                else {
                    $parent_nodes[] = $node;
                }
            }

        }
        foreach ($parent_nodes as $node) {
            $t = $this -> populate_branch($node, $nodes_by_parent);
            $tree[] = $t;
        }

        $res->data = $tree;
        $res->success = true;
   
    }

    public function get($req, $res) {

        $query_data = $req->itemId;

        $db = new Set();
        $query = $db->get($query_data);

        if (!$query->ok) {
            $res->message = $query->msg;
            return;
        }

        $flat_list = $query->data;
        $patient_node = [
            'text' => 'この患者のみ',
            'id' => 'noParent',
            'children' => [],
            'data' => [
                'isFolder' => true,
                'patient' => $query_data
            ]
        ];

        $tree = array();
        $parent_nodes = [
            $patient_node
        ];
        $nodes_by_parent = array();

        foreach ($flat_list as $node) {

            $node['content'] = json_decode($node['content'], true);            
            $patient = $node['patient'];
            
            $node = [
                'parent' => $node['parent'],
                'id' => $node['id'],
                'data' => [
                    'id' => $node['id'],
                    'isFolder' => $node['folder'] == 1 ? true : false,
                    'content' => $node['content'],
                    'patient' => $patient
                ],
                'text' => $node['text'] = $node['content']['name']
            ];

            if ($node['parent']) {
                $nodes_by_parent[$node['parent']][] = $node;
            }
            else {
                if($patient) {
                    $nodes_by_parent['noParent'][] = $node;
                }
                else {
                    $parent_nodes[] = $node;
                }
            }


        }

        foreach ($parent_nodes as $node) {
            $t = $this -> populate_branch($node, $nodes_by_parent);
            $tree[] = $t;
        }

        $res->data = $tree;
        $res->success = true;

    }

    public function post($req, $res) {

        $req_data = $req->post->data;
        $req_data->user = $_SESSION['user'];
        $req_data->is_folder = 0;
        $req_data->content = [
            'name' => $req_data->text,
            'items' => $req_data->content->items
        ];

        $req_data->content = json_encode($req_data->content);

        $db = new Set();
        $query = $db->post($req_data);
        if (!$query->ok) {
            $res->message = $query->msg;
            return;
        }

        $res->success = true;
    }

    public function destroy($req, $res) {

        $req_data = $req->itemId;

        $db = new Set();
        $query = $db->delete($req_data);
        if (!$query->ok) {
            $res->message = $query->msg;
            return;
        }

        $res->success = true;

    }

    public function update($req, $res) {
  
        $req_data = $req->update[0];

        $req_data->data->content = json_encode([
            'name' => $req_data->text,
            'items' => $req_data->data->content->items
        ]);

        $req_data->id = $req_data->data->id;

        $db = new Set();
        $query = $db->update($req_data);

        if (!$query->ok) {
            $res->message = $query->msg;
            return;
        }

        $res->success = true;


    }



}   