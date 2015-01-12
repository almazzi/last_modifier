<?php
/**
 * Created by PhpStorm.
 * User: almazeck
 * Date: 1/10/15
 * Time: 12:06 PM
 */
class Tags extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('tags_model');
    }
    public function all(){
        $data['all'] = $this->tags_model->get_all();
        $this->load->view('all',$data);
    }
    public function index(){
        $this->load->view('index');
    }
   public function repaire_names(){
       $data['repaired'] = $this->tags_model->repair_names();
       $this->load->view('repaired',$data);
   }
   public function get_id_of_repeated_names(){
       $data['ids'] = $this->tags_model->take_id_of_repeated_names();
       $this->load->view('repeated',$data);
   }
    public function delete_from_geopoints(){
        $data['ids'] = $this->tags_model->take_id_of_repeated_names();
        $permition=$this->tags_model->delete_from_geo($data['ids']);
        if($permition===true){
            $this->tags_model->delete_ids_from_tags($data['ids']);
        }
        $this->load->view('top',$data);
        }
}