<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Champion extends CI_Controller {
	private $data;

	function __construct(){
		parent::__construct();
		// $this->load->model("cu_model");
		$this->load->model("champion_model");
		$this->is_logged_in();
	}

	private function is_logged_in(){
		if(!$this->session->userdata('logged_in')){
			$this->session->sess_destroy();
			redirect('home/login');
		}
	}

	private function _load_view(){
		$this->load->view("inc/temp",$this->data);
	}

	public function index()
	{
		$this->data['main'] = "champion/index";
		$this->_load_view();
	}

	public function profile(){
		$this->data['main'] = "champion/profile";
		$this->data['profile'] = $this->champion_model->get_champ_profile
									($this->session->userdata("email"));
		$this->_load_view();
	}

	public function commitment($mode="view"){
		//check first if made a commitment, if not, take to
		//commitment form
		if($mode=="view"){
			$cid = $this->session->userdata("cid");
			if(!$this->champion_model->made_commitment($cid)){
				redirect("champion/commitment/form");
			}

			$this->data['main'] = "champion/commitment_view";
			$cid = $this->session->userdata("cid");
			$this->data['cd'] = $this->champion_model->get_commitment_details($cid);
			$this->_load_view();
		}

		if($mode=="form"){
			//if already commited
			$this->_has_committed($this->session->userdata("cid"));

			$this->data['main'] = "champion/commitment_form";
			$this->data['commitment_type'] = $this->champion_model->get_commitment_type();
			$this->_load_view();
		}

		if($mode=="submit"){
			//if already commited
			$this->_has_committed($this->session->userdata("cid"));

			$this->load->library("form_validation");

			if($this->input->post("amount")==0){
				$rule = array(
						'field'=>'other_amount',
						'label'=>'Specified Amount',
						'rules'=>'required|greater_than[99]'
					);
			}else{
				$rule = array(
						'field'=>'other_amount',
						'label'=>'Specified Amount',
						'rules'=>'less_than[1]|greater_than[-1]'
					);
			}

			$rules = array(
				$rule,
				array(
					'field'=>'date_from',
					'label'=>'Start Date',
					'rules'=>'required'
					),
				array(
					'field'=>'date_to',
					'label'=>'End Date',
					'rules'=>'none'
					),
				array(
					'field'=>'lifetime',
					'label'=>'Lifetime',
					'rules'=>'none'
					)
				);

			$this->form_validation->set_rules($rules);
			if($this->form_validation->run()){
				$this->champion_model->save_commitment();
				redirect("champion/commitment");
			}else{
				$this->commitment("form");
			}
		}
	}

	private function _has_committed($cid){
		if($this->champion_model->made_commitment($cid)){
			redirect("champion/commitment/view");
		}
	}
}