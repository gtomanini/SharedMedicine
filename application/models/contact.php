<?php
class News_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    public function get_contact()
    {
            $query = $this->db->get('contact');
            return $query->result_array();


    }