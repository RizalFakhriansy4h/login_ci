<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    class  Menu extends CI_Controller {
        public function __construct(){
            parent::__construct();
            is_logged_in();
        }

        public function index(){
            $data['judul'] = 'Menu Management';
            $data['user'] = $this->db->get_where('users', ['email' => $this->session->userdata('email')])->row_array();
            
            $data['menu'] = $this->db->get('user_menu')->result_array();

            $this->form_validation->set_rules('menu', 'Menu', 'required');

            if ($this->form_validation->run() == false) {

                $this->load->view('templates/header', $data);
                $this->load->view('templates/sidebar', $data);
                $this->load->view('templates/topbar');
                $this->load->view('menu/index', $data);
                $this->load->view('templates/footer');
            } else {
                $this->db->insert('user_menu', ['menu' => $this->input->post('menu')]);
                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">New menu added! </div>');
                redirect('menu');
            }

            
        }

        public function submenu() {
            $data['judul'] = 'Submenu Management';
            $data['user'] = $this->db->get_where('users', ['email' => $this->session->userdata('email')])->row_array();
            $this->load->model('Menu_Model');

            $data['subMenu'] = $this->Menu_Model->getSubMenu();
            $data['menu'] = $this->db->get('user_menu')->result_array();
            

            $this->form_validation->set_rules('title', 'title', 'required');
            $this->form_validation->set_rules('menu_id', 'Menu', 'required');
            $this->form_validation->set_rules('url', 'Url', 'required');
            $this->form_validation->set_rules('icon', 'Icon', 'required');

            if ($this->form_validation->run() == false) {

                $this->load->view('templates/header', $data);
                $this->load->view('templates/sidebar', $data);
                $this->load->view('templates/topbar');
                $this->load->view('menu/submenu', $data);
                $this->load->view('templates/footer');

            } else {
                $data['subMenu'] = $this->Menu_Model->insertSubMenu();
                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">New Submenu added! </div>');
                redirect('menu/submenu');
            }
            
        }





    }
