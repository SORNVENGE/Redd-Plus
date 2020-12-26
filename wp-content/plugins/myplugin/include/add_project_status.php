<?php
	session_start();
	global $wpdb;

	$table_project = $wpdb->prefix . "project";
    $table_project_type = $wpdb->prefix . "project_type";
    $table_project_progress = $wpdb->prefix . "project_progress";
    $table_project_status = $wpdb->prefix . "project_status";

    

    $edit = false;
	$id_project = '';
	if(isset($_GET['id_project'])){
		$id_project =  $_GET['id_project'];
        $projectInfo = $wpdb->get_row( "SELECT * FROM $table_project WHERE id = $id_project" );
        // check status of project
        // $checkStatus = $wpdb->get_row( "SELECT * FROM $table_project_progress where id_project = $id_project  order by id desc limit 1", OBJECT );
        // var_dump($checkStatus->id_status);

        // get status
        
        if($projectInfo->project_status == 1){
            
			$statusAll = $wpdb->get_results( "SELECT * FROM $table_project_status where id = 2 and parent = 0 order by id asc ,status_en asc", OBJECT );
		}else if($projectInfo->project_status == 7){
			$statusAll = $wpdb->get_results( "SELECT * FROM $table_project_status where parent = $projectInfo->project_status order by id asc ,status_en asc", OBJECT );
		}else if($projectInfo->project_status == 9){
			$statusAll = $wpdb->get_results( "SELECT * FROM $table_project_status where parent = $projectInfo->project_status order by id asc ,status_en asc", OBJECT );
		}else if($projectInfo->project_status == 10){
			$statusAll = $wpdb->get_results( "SELECT * FROM $table_project_status where id in (5,6) order by id asc ,status_en asc", OBJECT );
        }else{
            $statusAll = $wpdb->get_results( "SELECT * FROM $table_project_status where id in ($projectInfo->project_status,$projectInfo->project_status+1) and parent = 0 order by id asc ,status_en asc", OBJECT );
        }

        
    }
    
    
?>
<?php 
	
    $msg = '';

    if(isset($_POST['old_progress_id'])){

    	$id_progress = $_POST['old_progress_id'];
    	$id_status = $_POST['id_status'];
        $date = $_POST['date'];
        $process = $_POST['process'];
        $id_project =  $_GET['id_project'];

        //check status
         $check = $projectInfo = $wpdb->get_row( "SELECT * FROM $table_project_progress WHERE id_project = $id_project and process = '$process' and id_status = '$id_status' and id <> $id_progress" );
         if($check){
            $statusInfo = $wpdb->get_row( "SELECT * FROM $table_project_status WHERE id = $id_status" );
            $msg = 'This status '.$statusInfo->status_en.' with process '.$process.' already exit. please try other one';
         }else{
         	
            $data = array(
                'id_status' => $id_status,
                'date' => date("Y-m-d", strtotime(str_replace('/', '-', $date ))),
                'process' => $process,
                'updated_at' => date('Y-m-d H:i:s')
            );
            $id_CheckStatus = $wpdb->update( 
				$table_project_progress, 
				$data, 
				array( 'id' => $id_progress )
			); 
            if($id_CheckStatus){

                // update status project
				
				if($id_status == 5){
					$result_add = $wpdb->update( 
						$table_project, 
						array( 
							'project_status' => $id_status,	// number
							'date_approval' => date('Y-m-d H:i:s')	// number
						), 
						array( 'id' => $id_project )
					);
				}else{
					$result_add = $wpdb->update( 
						$table_project, 
						array( 
							'project_status' => $id_status	// number
						), 
						array( 'id' => $id_project )
					);
				}


                header("Location: ".admin_url()."/admin.php?page=project_status&id=".$id_project);
            }else{
                $msg = 'Data can\'t save . please try other one';
            }

            
         }


	}else if(isset($_POST['id_status'])){
        $id_status = $_POST['id_status'];
        $date = $_POST['date'];
        $process = $_POST['process'];
        $id_project =  $_GET['id_project'];
         //check status
         $check = $projectInfo = $wpdb->get_row( "SELECT * FROM $table_project_progress WHERE id = $id_project and process = '$process' and id_status = '$id_status'" );
         if($check){
            $statusInfo = $wpdb->get_row( "SELECT * FROM $table_project_status WHERE id_project = $id_status" );
            $msg = 'This status '.$statusInfo->status_en.' with process '.$process.' already exit. please try other one';
         }else{
            $data = array(
                'id_project' => $id_project, 
                'id_status' => $id_status,
                'date' => date("Y-m-d", strtotime(str_replace('/', '-', $date ))),
                'process' => $process,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => get_current_user_id(),
                'updated_by' => get_current_user_id()
            );
            $format = array('%d','%d','%s','%s','%s','%s','%d','%d');
            $wpdb->insert($table_project_progress,$data,$format);
            $id_CheckStatus = $wpdb->insert_id;
            if($id_CheckStatus){

                // update status project
				
				if($id_status == 5){
					$result_add = $wpdb->update( 
						$table_project, 
						array( 
							'project_status' => $id_status,	// number
							'date_approval' => date('Y-m-d H:i:s')	// number
						), 
						array( 'id' => $id_project )
					);
				}else{
					$result_add = $wpdb->update( 
						$table_project, 
						array( 
							'project_status' => $id_status	// number
						), 
						array( 'id' => $id_project )
					);
				}


                header("Location: ".admin_url()."/admin.php?page=project_status&id=".$id_project);
            }else{
                $msg = 'Data can\'t save . please try other one';
            }

            
         }



	}else if(isset($_GET['id_progress']) && isset($_GET['id_project'])){
		$edit = true;
		$idProgress = $_GET['id_progress'];

		$processInfo = $wpdb->get_row( "SELECT * FROM $table_project_progress WHERE id = $idProgress" );

		if($projectInfo->project_status == 7){
			$statusAll = $wpdb->get_results( "SELECT * FROM $table_project_status where parent = $projectInfo->project_status order by id asc ,status_en asc", OBJECT );
		}else if($projectInfo->project_status == 9){
			$statusAll = $wpdb->get_results( "SELECT * FROM $table_project_status where parent = $projectInfo->project_status order by id asc ,status_en asc", OBJECT );
		}else if($projectInfo->project_status == 10){
			$statusAll = $wpdb->get_results( "SELECT * FROM $table_project_status where id in (5,6) order by id asc ,status_en asc", OBJECT );
        }else{

        	if($projectInfo->project_status > 2){
        		$statusAll = $wpdb->get_results( "SELECT * FROM $table_project_status where id in ($projectInfo->project_status,$projectInfo->project_status-1) and parent = 0 order by id asc ,status_en asc", OBJECT );
        	}else{
        		$statusAll = $wpdb->get_results( "SELECT * FROM $table_project_status where id in ($projectInfo->project_status) and parent = 0 order by id asc ,status_en asc", OBJECT );
        	}
            
        }

	}	


?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/bootstrap-datepicker.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="<?php echo get_template_directory_uri();?>/js/bootstrap.min.js"></script>
<script src="<?php echo get_template_directory_uri();?>/js/bootstrap-datepicker.min.js"></script>

<script src="<?php echo get_template_directory_uri();?>/js/jquery.session.js"></script>

<script type="text/javascript">

	fun_validate = function(class_div) {
        var num;
        num = 0;
        $(class_div + " .require").each(function(index, element) {
			var id, info, value;
			value = $(element).val();
			id = $(element).attr('id');
			
			if (value == '') {
				console.log(id+'=>'+value);
				// $(class_div+' #' + id).parents().find('label').addClass('okok');
				if (id == "upload_document") {
					$(class_div + ' #title-upload-document').addClass('label_red');
				}
				else if (id == "upload_kml") {
					$(class_div + ' #project-location').addClass('label_red');
				}
				else if (id == "upload_optional") {
					$(class_div + ' #document-optional').addClass('label_red');
				}
				else if (id == "upload_mrv") {
					$(class_div + ' #display-name-upload_mrv').addClass('label_red');
				}
				else if (id == "upload_safeguard") {
					$(class_div + ' #display-name-safeguard').addClass('label_red');
				}
				else if (id == "upload_benefit") {
					$(class_div + ' #display-name-benefit').addClass('label_red');
				}else{
					$(class_div+' #' + id).prev('label').addClass('label_red');
				}
				
				num = num + 1;
			} else {
				if (id === 'emails') {
					pattern = /^[0-9a-z\._\-]+@[a-zA-Z_\-]+?\.[a-zA-Z]{2,}?(\.[a-zA-Z]{2,})?$/
					if(!pattern.test(value)){
						// $(class_div+' #' + id).parents().find('label').addClass('label_red');
						$(class_div+' #' + id).prev('label').addClass('label_red');
						num = num + 1;
					}else{
						$(class_div+' #' + id).prev('label').removeClass('label_red');
					}
				} else {
					if (id == "upload_document") {
						$(class_div + ' #title-upload-document').removeClass('label_red');
					}
					else if (id == "upload_kml") {
						$(class_div + ' #project-location').removeClass('label_red');
					}
					else if (id == "upload_optional") {
						$(class_div + ' #document-optional').removeClass('label_red');
					}
					else if (id == "upload_mrv") {
						$(class_div + ' #display-name-upload_mrv').removeClass('border_label_red');
					}
					else if (id == "upload_safeguard") {
						$(class_div + ' #display-name-safeguard').removeClass('border_label_red');
					}
					else if (id == "upload_benefit") {
						$(class_div + ' #display-name-benefit').removeClass('border_label_red');
					}
					else if (id == "greenhouse_gases") {
						$(class_div + ' #label-greenhouse').removeClass('label_red');
					}
					else {
						$(class_div+' #' + id).prev('label').removeClass('label_red');
					}
					
				}
			}
        });
        if (num > 0) {
          return 1;
        } else {
          return 0;
        }
    };

	// storeData = function(class_div) {
	// 	$.each($('form'+class_div).serializeArray(), function(i, field) {
	// 		var input = $('input[name='+field.name+']');
	// 		field.value = $.trim(field.value);
	// 		console.log(field.value);
	// 	});
	// };

	$(document).ready(function() {
		// var content = '<div class="container-main needLoopItem"><div class="row"><div class="col-8"><div class="form-group"><label for="exampleFormControlInput1" class="labelinput ">Name of Organization(Project Partner)*</label><input type="text" class="form-control require" name="organization_name_partner[]" id="organization_name_partner" placeholder="Fill information here..."></div></div><div class="col-4"><div class="form-group"><label for="exampleFormControlSelect1" class="labelinput">Organizational Category*</label><select class="form-control selectOwrite require" id="id_cate_org_partner" name="id_cate_org_partner[]"><option value="">Choose one of the following...</option><?php foreach ( $organizations as $row ) { ?><option value="<?php echo $row->id;?>"><?php echo $row->title_en;?></option><?php }?></select></div></div></div><div class="row"><div class="col-6"><div class="form-group"><label for="exampleFormControlInput1" class="labelinput">Fuction/Responsibility*</label><textarea class="form-control require" id="funtcion_partner" rows="3" placeholder="Fill information here..." name="funtcion_partner[]"></textarea></div></div><div class="col-6"><div class="form-group"><label for="exampleFormControlInput1" class="labelinput">Address*</label><textarea class="form-control require" id="address_partner" rows="3" placeholder="Fill information here..." name="address_partner[]"></textarea></div></div></div><div class="row"><div class="col-6"><div class="form-group"><label for="exampleFormControlInput1" class="labelinput">Postal Address (if different from Address)</label><textarea class="form-control " id="postal_address_partner" rows="3" placeholder="Fill information here..." name="postal_address_partner[]"></textarea></div></div><div class="col-6"><div class="form-group"><label for="exampleFormControlInput1" class="labelinput">Contact Person(s)*</label><textarea class="form-control require" id="contact_person_partner" rows="3" placeholder="Fill information here..." name="contact_person_partner[]"></textarea></div></div></div><div class="row"><div class="col-6"><div class="form-group"><label for="exampleFormControlInput1" class="labelinput ">Email Address*</label><input type="email" class="form-control require" id="emails_partner" placeholder="Fill information here..." name="emails_partner[]"></div></div><div class="col-6"><div class="form-group"><label for="exampleFormControlSelect1" class="labelinput">Telephone Numbers*</label><input type="text" class="form-control require" id="office_number_partner" placeholder="Office number(s)" name="office_number_partner[]"><input type="text" class="form-control require" id="cell_number_partner" placeholder="Cell number(s)" name="cell_number_partner[]"></div></div></div><div class="row"><div class="col-6"><div class="form-group"><label for="exampleFormControlInput1" class="labelinput ">Fax Number(s)</label><input type="text" class="form-control require" id="fax_number_partner" placeholder="Fill information here..." name="fax_number_partner[]"></div></div><div class="col-6"></div></div></div>';
	    var countCat = 0;
		

		$('.save_step').click(function(e) {
            if(fun_validate('form#blockStepStatus') == 0){
                $('form#blockStepStatus').submit();
            }
		});


		// $('.datepicker').datepicker({
		// 	format: 'dd/mm/yy'
		// });
		$('.bootstrap-date').datepicker({
            format: 'dd/mm/yyyy'
        });
        $('.bootstrap-date-range').datepicker({
            format: 'dd/mm/yyyy',
            inputs: $('.actual_range')
        });
	});
	
	var i = 0;
	
</script>

<style>
	ul.wp-submenu-wrap li:nth-child(4),ul.wp-submenu-wrap li:nth-child(5){
		display: none;
	}
	body{
		background: none !important;
	}
	.hideItem{
		display:none;
	}
	.container-main{
		max-width: 780px;
	}
	.header-title{
		font-family: Tahoma;
		font-style: normal;
		font-weight: bold;
		font-size: 20px;
		line-height: 25px;
		/* identical to box height, or 125% */

		text-align: center;

		/* Redd+ Black */

		color: #333333;

		border-bottom: 2px solid #83BC83;
		padding-bottom: 10px;
	}
	label.labelinput{
		font-family: Tahoma;
		font-style: normal;
		font-weight: bold;
		font-size: 15px;
		line-height: 20px;
		color: #333333;
	}
	.sublabelinput{
		font-family: Tahoma;
		font-style: normal;
		font-weight: normal;
		font-size: 10px;
		line-height: 15px;

		color: #333333;
	}
	.sublabelinputStyle2{
		font-family: Tahoma;
		font-style: normal;
		font-weight: normal;
		font-size: 15px;
		line-height: 25px;
		color: #333333;
	}
	input.form-control::placeholder, textarea.form-control::placeholder{
		font-family: Tahoma;
		font-style: normal;
		font-weight: normal;
		font-size: 10px;
		line-height: 15px;
		color: #999999;
	}
	.label_red{
		color: #F00808 !important;
	}
	select.selectOwrite{
		height: 38px !important;
	}
	select.form-control{
		font-family: Tahoma;
		font-style: normal;
		font-weight: normal;
		font-size: 10px;
		line-height: 15px;
		color: #999999;
	}
	#office_number, #office_number_partner{
		margin-bottom: 10px;
	}
	.content-style-1{
		font-family: Tahoma;
		font-style: normal;
		font-weight: normal;
		font-size: 15px;
		line-height: 25px;
		color: #333333;
	}

	label.labelinput-style2{
		font-family: Tahoma;
		font-style: normal;
		font-weight: normal;
		font-size: 12px;
		line-height: 18px;

		color: #333333;
	}

	.btn_add_more{
		font-family: Tahoma;
		font-style: normal;
		font-weight: normal;
		font-size: 12px;
		line-height: 20px;
		text-align: center;
		color: #077907;

		border: 1px solid #077907;
		box-sizing: border-box;

		padding: 5px 10px;
    	background: none;
	}
	.btn_add_more:hover{
		color: #999999;
		border-color: #999999;
	}
	.info_require{
		font-family: Verdana;
		font-style: italic;
		font-weight: normal;
		font-size: 8px;
		line-height: 18px;
		color: #F00808;
	}
	.btn_next{
		background: #077907;
		padding: 10px 30px;

		font-family: Tahoma;
		font-style: normal;
		font-weight: normal;
		font-size: 12px;
		line-height: 20px;
		text-align: center;
		color: #FFFFFF;

		border: none;
		float: right;
	}
	.needLoopItem{
		margin-top: 20px;
		margin-bottom: 10px;
		padding: 20px;
		background: rgba(153, 153, 153, 0.5);
		position: relative;
	}
	.pStyle{
		font-family: Tahoma;
		font-style: normal;
		font-weight: normal;
		font-size: 15px;
		line-height: 25px;
		color: #333333;
	}
	.form-check-input{
		position: relative;
	}
	.form-check{
		padding-left:0px;
	}
	.hideCheckbox{
		display: none;
	}
	.removeIcon{
		position: absolute;
		right: -8px;
		top: -10px;
	}
	.txt-color-red{
		color: red;
		font-size: 25px;
	}
	.fileList{
		position: relative;
		margin-right: 8px;
    	margin-top: 10px;
	}
	.fileList .txt-color-red{
		color: red;
		font-size: 15px;
	}
	.fileList .listNameFile{

	}


	.form-group .input-file-style .form-control-file {
		padding: 10px;
		display: none;
	}
	.form-group .input-file-style .show-container{
		padding: 10px;
	}
	.form-group .input-file-style .show-container .button-file{
		width: 110px;
		text-align: center;
		font-family: Tahoma;
		font-style: normal;
		font-weight: normal;
		font-size: 12px;
		color: #FFFFFF;
		cursor: pointer;
		background-color:#077907;
		align-self: center;
		padding: 7px 0px 7px 0px;

	}
	.form-group .input-file-style .show-container .display-name{
		font-family: Tahoma;
		font-style: normal;
		font-weight: normal;
		font-size: 10px;
		line-height: 14px;
		padding: 9px;
		align-self: center;
		color: #FFFFFF;
		background-color: #828282;
	}
	
</style>
<?php 
if(isset($_GET['id_project'])){
?>
<div class="container-fluid">

	<div class="row pt-4">
		<?php if($edit){?>
			<h2><a href="?page=admin-page">List Project</a> > <a href="?page=project_status&id=<?php echo $id_project;?>">Projects status</a> > Edit status project <?php echo $projectInfo->project_name;?></h2>
		<?php }else{?>
            <h2><a href="?page=admin-page">List Project</a> > <a href="?page=project_status&id=<?php echo $id_project;?>">Projects status</a> > Add status project <?php echo $projectInfo->project_name;?></h2>
		<?php }?>
	</div>

        <?php 
        if($msg != ''){
        ?>
            <div class="alert alert-success text-left">
                <strong><?php echo $msg;?></strong>
            </div>
        <?php 
        }
        ?>
	
		<form method="POST" enctype="multipart/form-data"  action="" id="blockStepStatus">
            <div class="container-main ">
                
                
                
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="exampleFormControlSelect1" class="labelinput">Status*</label>

                            <?php if(isset($processInfo->id)){?>
                            <input type="hidden" name="old_progress_id" value="<?php echo $processInfo->id;?>">
                        	<?php }?>

                            <select class="form-control selectOwrite require" name="id_status" id="id_status">
                                <option value="">Choose one of the following...</option>
                                <?php foreach ( $statusAll as $row ) { 
                                ?>
                                    <option value="<?php echo $row->id;?>" 
                                    		<?php if($_POST['id_status'] == $row->id) echo 'selected';?>

                                    		<?php if(isset($processInfo->id_status) && ($processInfo->id_status == $row->id)) echo 'selected';?> 

                                    		><?php echo $row->status_en;?>
                                    </option>
                                    <?php 
                                    if($projectInfo->project_status >= 4 && $projectInfo->project_status <= 6){
                                        $statusChail = $wpdb->get_results( "SELECT * FROM $table_project_status where parent = $row->id order by id asc, status_en asc", OBJECT );
                                        if($statusChail){
                                            foreach ( $statusChail as $subRow ) {
                                    ?>
                                                <option value="<?php echo $subRow->id;?>" 
                                                	<?php if($_POST["id_status"] == $subRow->id) echo 'selected';?> 

                                                	<?php if(isset($processInfo->id_status) && ($processInfo->id_status == $subRow->id)) echo 'selected';?> 

                                                	> &nbsp;&nbsp;&nbsp; <?php echo $subRow->status_en;?></option>
                                    <?php 
                                            }
                                        }
                                    }
                                    ?>
                                <?php 
                                } //end loop main
                                
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group datetime">
                            <label for="exampleFormControlSelect1" class="labelinput">Date</label>
                            <div class="input-group date" data-provide="bootstrap-date">
                                <input type="text" value="<?php if(isset($_POST['date'])){ echo $_POST['date'];}elseif(isset($processInfo->date)){echo date("d/m/Y", strtotime($processInfo->date ));} ?>" class="form-control bootstrap-date" id="date" name="date">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-th"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="exampleFormControlInput1" class="labelinput">Process content*</label>
                            <textarea class="form-control require" id="process" rows="3" placeholder="Fill information here..." name="process"><?php if(isset($_POST['process'])){ echo $_POST['process'];}elseif(isset($processInfo->process)){echo $processInfo->process;} ?></textarea>
                        </div>
                    </div>
                    
                </div>
                
            </div>
		</form>
	


	<div class="container-main">
		<div class="row pt-4">
			<div class="col-6">
				<span class="info_require">*Required fields</span>
			</div>
			
            <div class="col-6 ">
                <button type="button" class="btn_next save_step" >Save</button>
            </div>
			
		</div>
	</div>
	
</div>
<?php 
}
?>