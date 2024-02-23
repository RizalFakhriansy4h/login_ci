<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Auth extends CI_Controller {
        public function __construct() {
            parent::__construct();
        }
        public function index() {
            if ($this->session->userdata('email')) {
	            redirect('user');
            }

            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
            $this->form_validation->set_rules('password', 'password', 'required|trim');

            if($this->form_validation->run() == FALSE){
                $data['judul'] = 'LOGIN';

                $this->load->view('templates/auth_header', $data);
                $this->load->view('auth/login');
                $this->load->view('templates/auth_footer');

            }else{
                // validasi sukses
                $this->_login();

            }
        
        }

        private function _login() {

            $email = $this->input->post('email');
            $password = $this->input->post('password');
            
            $user = $this->db->get_where('users', ['email' => $email])->row_array();
            
            if($user) {
            // usernya ada

                // cek apakah usernya aktif
                if ($user['aktif'] == 1) {
                    // cek passsword
                    if(password_verify($password, $user['password'])) {
                        $data = [
                            'email' => $user['email'],
                            'role_id' => $user['role_id']
                        ];

                        $this->session->set_userdata ($data);
                        
                        if ($user['role_id'] == 1) {
                        
                            redirect('admin');
                        
                        } else {
                        
                            redirect('user');
                        
                        }

                    } else {
                        $this->session->set_flashdata('alert', '<div class="alert alert-danger" role="alert">PASSWORD SALAH</div>');
                        redirect('auth');
                    }

                } else {
                    $this->session->set_flashdata('alert', '<div class="alert alert-warning" role="alert">Email Belum di aktivasi</div>');
                    redirect('auth');
                }


            } else {

                $this->session->set_flashdata('alert', '<div class="alert alert-danger" role="alert">Email Belum Terdaftar</div>'); 
                redirect('auth');
            }
        }

        public function register(){
            
            if ($this->session->userdata('email')) {
	            redirect('user');
            }

            $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]',
            [
                'is_unique'=> 'Email sudah terdaftar'
            ]);
            $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[6]|matches[password2]'
            ,[
                'matches' => 'Validasi password salah',
                'min_length' => 'Password terlalu pendek'
            ]);

            $this->form_validation->set_rules ('password2', 'Password','required|trim|matches[password1],'
            ,[
                'matches' => 'Validasi password salah',
            ]);

            if($this->form_validation->run() == FALSE){
                
                $data['judul'] = 'REGISTRASI';
                
                $this->load->view('templates/auth_header', $data);
                $this->load->view('auth/register');
                $this->load->view('templates/auth_footer');
            
            } else{

                $this->session->set_flashdata('alert', '<div class="alert alert-success" role="alert">Pendaftaran Berhasil Silahkan Login</div>');
                $data = [
                    'nama' => $this->input->post('nama',true),
                    'email' => $this->input->post('email',true),
                    'image' => 'default.jpg',
                    'password' => password_hash($this->input->post('password1'),PASSWORD_DEFAULT),
                    'role_id' => 2,
                    // aktif ganti 0 jika sudah bisa kirim email
                    'aktif' => 1,
                    'data_dibuat' => time()
                    ];
                    
                    $this->db->insert('users', $data);

                    // $this->_sendEmail();

                    redirect('auth');

            }
        }

        private function _sendEmail(){
            $config = [
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_port' => 20,
            'smtp_crypto'=> 'tls',
            'smtp_user' => 'nandenmonaiya898@gmail.com',
            'smtp_pass' => 'MOSES8787',
            'charset' => 'utf-8',
            'mailtype' => 'html',
            'newline' => "\r\n"
            // $config['protocol'] = 'smtp';
            // $config['smtp_host'] = 'smtp.gmail.com';
            // $config['smtp_port'] = 587;
            // $config['smtp_crypto'] = 'tls';
            // $config['smtp_user'] = 'your_email@gmail.com';
            // $config['smtp_pass'] = 'your_password';
            // $config['charset'] = 'utf-8';
            // $config['mailtype'] = 'html';
            // $config['newline'] = "\r\n";
            ];

            // $this->load->library('email',$config); 
            $this->email->initialize($config);

            $this->email->from ('nandenmonaiya898@gmail.com', 'Rizalludin'); 
            
            $this->email->to('fakhriansyahnugroho007@gmail.com');
            $this->email->subject('Testing');
            $this->email->message ('Hello World! whatup ma menzzzzz');
            $this->email->send();

            if ($this->email->send()) {
                return true;
            } else {
                echo $this->email->print_debugger();
                die;
            }



        }

        public function logout() {
            $this->session->unset_userdata('email');
            $this->session->unset_userdata('role_id');
            $this->session->set_flashdata('alert', '<div class="alert alert-success" role="alert">You have been logged out!</div>');
            redirect('auth');
        }

        public function blocked() {
            $this->load->view('auth/blocked');

        }




    }
