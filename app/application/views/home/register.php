<?php
//get list of CUs

$_uni_cu = array();

foreach($uni_cu->result() as $row){
	$_uni_cu[$row->cuid] = $row->uni." - ".$row->cu;
}

//get list of affiliation types

$_aff_type = array();
foreach($aff_type->result() as $row){
	$_aff_type[$row->atid] = $row->name;
}

//generate list of years
$_year = array();
for($i=date("Y"); $i>=1950; $i--){
	$_year[$i] = $i;
}



?>
<div class="row">
	<div class="col-md-6">
		<h3>Basic Information </h3>

		<?php $this->load->view("inc/progress_reg"); ?>

		<div class="description">
			Please provide few basic details about yourself below. The password you set 
			here is the one you will use for logging in into the system any time.
		</div>
		<?php

		echo validation_errors('<div class="alert alert-danger" role="alert">','</div>');

		echo form_open("home/register/submit","class='form'");
		echo form_input("first_name",set_value("first_name"),"class='half'");
		echo form_input("last_name",set_value("last_name"),"class='half'");
		echo form_label("First Name <span class='right'>Last Name</span>","first_name");
		if($in_cu == 1){
			echo form_dropdown("cuid",$_uni_cu,137);
			echo form_label("Christian Union","cuid");
			echo form_dropdown("atid",$_aff_type,1, "class='half'");
			echo form_dropdown("grad_year",$_year,"","class='half'");
			echo form_label("Affiliation <span class='right'>Year of Graduation</span>","atid");
		}else{
			echo form_hidden(
				array(
					"cuid"=>146, //none
					"atid"=>1
					)
			);	
		}
		echo form_input("phone",set_value("phone"),"class='half'");
		echo form_dropdown("gender", 
			array("Male"=>"Male","Female"=>"Female"),"",
			"class='half'");
		echo form_label("Phone Number <span class='right'>Gender</span>","phone");

		echo form_input("email",set_value("email"));
		echo form_label("Email","email");
		echo form_password("password",set_value("password"),"class='half'");
		echo form_password("password_confirm",set_value("password_confirm"),"class='half'");
		echo form_label("Set new Password for this sytem <span class='right'>Password Confirm</span>","password");

		echo form_hidden("in_cu",$in_cu);

		echo form_submit("register","Register","class='btn btn-lg btn-success'");

		?>
	</div>
</div>
