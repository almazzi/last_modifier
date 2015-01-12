<?php
/**
 * Created by PhpStorm.
 * User: almazeck
 * Date: 1/10/15
 * Time: 12:09 PM
 */
function repair_name($cname){
    $replace = array('"','[',']','\n',')','.','(');
    $cname = str_replace($replace,'',$cname);
    $cname = stripslashes($cname);
    if(preg_match('/[\p{Cyrillic}]/u',$cname)){
        $cname=mb_convert_case($cname, MB_CASE_LOWER, "UTF-8");
    }
    else{
        $cname = strtolower($cname);
    }

    return $cname;}
class tags_model extends CI_Model{
    var $ids_of_repeated = array();
    public  function __construct(){
        parent::__construct();
        $this->load->database();
    }
    public function get_all(){
        $query = $this->db->get('tags');
        return $query->result_array();
    }
    public function repair_names(){
        $repaired = array();
        $query = $this->db->get('tags');
        foreach($query->result_array() as $item){
            if($item['name']!==repair_name($item['name'])){
                $item['name']=repair_name($item['name']);
                $this->db->where('id',$item['id']);
                $this->db->set('name',$item['name']);
                $this->db->insert('tags');
                array_push($repaired,$item);

            }
        }
        return $repaired;

    }
    public function take_id_of_repeated_names(){
        $repeated = array();
        $ids = array();
        $this->db->select('name');
        $this->db->group_by('name');
        $this->db->having('count(name)>1');
        $query = $this->db->get('tags');
        foreach($query->result_array() as $item){
            array_push($repeated,$item);}
        for($i=0;$i<count($repeated);$i++){
            $this->db->select('id');
            $this->db->where('name',$repeated[$i]['name']);
            $query=$this->db->get('tags');

                array_push($ids,$query->result_array());
        }return $ids;
    }
    public function delete_from_geo($ids){
        $data = array();
        $temp = array();
        foreach($ids as $item){

           $this->db->select('geopoints_id,tags_id');
           $this->db->where('tags_id',$item[0]['id']);
           $query = $this->db->get('geo_points_tags');
            if($query->num_rows()>0){
                $row=$query->row();
                for($i=1;$i<count($item);$i++){
                $this->db->where('geopoints_id',$row->geopoints_id);
                $this->db->where('tags_id',$item[$i]['id']);
                $this->db->delete('geo_points_tags');}
            }
            for($i=1;$i<count($item);$i++){
                $this->db->set('tags_id',$item[0]['id']);
                $this->db->where('tags_id',$item[$i]['id']);
                $this->db->update('geo_points_tags');
            }



    }
    }

}