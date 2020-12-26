<?php
	session_start();
	global $wpdb;
	$table_project = $wpdb->prefix . "project";
	$table_project_type = $wpdb->prefix . "project_type";

	$table_proponent_partner = $wpdb->prefix . "project_proponent_partner";
	$table_project_progress = $wpdb->prefix . "project_progress";
	$table_lists_documents = $wpdb->prefix . "lists_documents";
	$table_project_annual_emission_reductions = $wpdb->prefix ."project_annual_emission_reductions";
	// unset($_SESSION['hStep']);
	session_destroy();
	$statusSucc = '';
	$statusError = '';
	if(isset($_GET['action']) && $_GET['action'] == 'delete'){
        $id = $_GET['id'];
		
		$proInfo = $wpdb->get_row( "SELECT * FROM $table_project WHERE id = $id" );

		// check table table_proponent_partner
		
		$checkPro1 = $wpdb->get_row( "SELECT * FROM $table_project_progress WHERE id_project = $id" );
		$checkPro2 = $wpdb->get_row( "SELECT * FROM $table_lists_documents WHERE id_project = $id" );
		$checkPro3 = $wpdb->get_row( "SELECT * FROM $table_project_annual_emission_reductions WHERE id_project = $id" );
		if($checkPro1){
			$statusError = "This project ".$proInfo->project_name." can't delete. Because this project have a transaction";
		}else if($checkPro2){
			$statusError = "This project ".$proInfo->project_name." can't delete. Because this project have a transaction";
		}else if($checkPro3){
			$statusError = "This project ".$proInfo->project_name." can't delete. Because this project have a transaction";
		}else{
			$wpdb->query( "DELETE FROM $table_proponent_partner WHERE id_project= $id" );
			$wpdb->query( "DELETE FROM $table_project WHERE id= $id" );

        	$statusSucc = 1;
		}
    }
    
    if(isset($_GET['status'])){
        $statusSucc = $_GET['status'];
    }
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
	    
    });
</script>
<!-- Latest compiled and minified CSS -->
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> -->
<link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">



<!-- Latest compiled and minified JavaScript -->
<script src="<?php echo get_template_directory_uri();?>/js/bootstrap.min.js"></script>
<script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script>
	$(document).ready(function($) {
		$('#listallProject').DataTable({
		    "lengthChange": false,
		    "pageLength": 10,
		    "info": true,
		    "dom": 'ftipr',
		    "autoWidth" : true,
		});
		// $('#listallProject').DataTable();

	});
</script>
<style>
	table th{
		font-size: 14px;
		text-align:left;
	}
	table td{
		font-size: 13px;
		text-align:left;
	}

	ul.wp-submenu-wrap li:nth-child(4),ul.wp-submenu-wrap li:nth-child(5){
		display: none;
	}
</style>



<div class="container-fluid text-center">
	<div class="row pt-4">
		<h2>List Projects</h2>
		<div class="col-sm-12">
			<?php 
			if($statusSucc != ''){
			?>
				<div class="alert alert-success text-left">
					<strong>Success!</strong>
				</div>
			<?php 
			}
			?>

			<?php 
			if($statusError != ''){
			?>
				<div class="alert alert-danger text-left">
					<strong><?php echo $statusError;?></strong>
				</div>
			<?php 
			}
			?>
			<div class="table-responsive">
				<table class="table table-striped" id="listallProject">
					<thead>
						<tr>
							<th>N°</th>
							<th>Type</th>
							<th style="width: 200px;">Name</th>

							<th style="width: 450px;">Description</th>
							<th>Submit Date</th>
							<th>Start Date</th>
							<th>End Date</th>
							<!-- <th>Greenhouse Gases</th>
							<th>Standard</th>
							<th>Description Strategy</th> -->
							<!-- <th>MRV Has</th>
							<th>Safeguard Has</th>
							<th>Benefit-sharing Has</th> -->
							
							<th style="width: 250px">Action</th>
						</tr>
					</thead>
					<tbody>
					<?php 
						$rows = $wpdb->get_results("
									SELECT 

									rp.id, rp.project_name, rp.project_description, rp.project_submit_date, rp.date_start,
									rp.date_end, rp.greenhouse_gases, rp.standard, rp.description_strategy, rp.checkbox_mrv, rp.checkbox_safeguard, rp.checkbox_benefit_sharing,
									rpt.project_type_en
									
									FROM $table_project rp
									LEFT JOIN $table_project_type rpt
									ON rpt.id = rp.id_project_type
									ORDER BY rp.id DESC ");
						
						$i=1;
						foreach ($rows as $row) {
					?>
					<tr>
						<td><?php echo $row->id;?></td>

						<td><?php echo $row->project_type_en;?></td>
						<td>
							<?php 
								echo wp_trim_words( $row->project_name, 35, '[...]' );
							?>
						</td>
						<td>
							<?php 
								echo wp_trim_words( $row->project_description, 35, '[...]' );
							?>
						</td>

						<td><?php echo $row->project_submit_date;?></td>
						<td><?php echo $row->date_start;?></td>
						<td><?php echo $row->date_end;?></td>

						<!-- <td><?php echo $row->greenhouse_gases;?></td>
						<td><?php echo $row->standard;?></td>
						<td><?php echo $row->description_strategy;?></td> -->

						<!-- <td><?php echo $row->checkbox_mrv;?></td>
						<td><?php echo $row->checkbox_safeguard;?></td>
						<td><?php echo $row->checkbox_benefit_sharing;?></td> -->

						<td>
							<a class="btn btn-outline-secondary btn-sm" href="?page=project_status&id=<?php echo $row->id;?>" role="button">Status</a>
							<a class="btn btn-outline-secondary btn-sm" href="?page=project_document&id=<?php echo $row->id;?>" role="button">Document</a>
							<a class="btn btn-outline-secondary btn-sm" href="?page=annual_emission_reductions&id=<?php echo $row->id;?>" role="button">Annual reductions</a>
							<a class="btn btn-outline-secondary btn-sm" href="?page=add_project&id=<?php echo $row->id;?>" role="button">Edit</a>
							<a class="btn btn-outline-danger btn-sm" href="?page=admin-page&id=<?php echo $row->id;?>&action=delete" role="button">Delete</a>
						</td>
					</tr>
					<?php 
							$i++; 
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

