<?php

/* Template Name: Project Listing */

get_header();


	// $mailResult = false;
	// $mailResult = wp_mail( 'vannarith.ny@bi-kay.com', 'test if mail works', 'hurray' );
	// var_dump($mailResult);

	if (have_posts()) { ?>
		<?php while ( have_posts() ) : the_post(); ?>
		<div class="body-container">
			<!-- Block menu page  -->
			<style type="text/css">
				.sf-menu > li > a{
					padding: 0px 14px !important;
				}
			</style>
			<div class="header-margin-bottom"></div>
			<div class="td-crumb-container">
				<?php echo td_page_generator::get_page_breadcrumbs(get_the_title()); ?>
			</div>
			<div class="header-margin-top"></div>
			 <!-- End Block menu page  -->
			 
			<div class="content-container-listing">
				<div class="container">
					<div class="row no-padding-listing ">
							<?php if( get_field('header_title_listing_project') ): ?>
								<p class="title col-md-10 col-sm-12"><?php echo get_field('header_title_listing_project');?></p>
							<?php endif; ?>
								<p class="drawline col-md-12 col-sm-12"></p>
							<?php if( get_field('header_detail_listing_project') ): ?>
								<p class="detail col-lg-12"><?php echo get_field('header_detail_listing_project');?></p>
							<?php endif; ?>
					</div>
					
					<div class="row table-block">
						<table class="table table-striped">
							<thead class="header-th">
								<tr>
									<th class="register-project"><?php _e("[:km]ឈ្មោះគម្រោងរេដបូកដែលបានចុះបញ្ជីរួច[:en]Name of Registered Projects[:]");?></th>
									<th class="status"><?php _e("[:km]ស្ថានភាពគម្រោង[:en]Project Status[:]");?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								global $wpdb;
								$table_name = $wpdb->prefix . "project";
								$projectData = $wpdb->get_results( "SELECT * FROM $table_name  order by  date_approval DESC", OBJECT );
								foreach ( $projectData as $row ) { 
									?>
										<tr>
											<td>
												<?php if ($row->date_approval == "0000-00-00 00:00:00"): ?>
													<a href="<?php echo get_home_url(); ?>/pipeline-project.html?id=<?= $row->id ?>" class="project-name-highligh">
														<?php echo substr($row->project_name,0,56);?>
													</a>
													<a href="<?php echo get_home_url(); ?>/pipeline-project.html?id=<?= $row->id ?>" class="project-name-highligh-mobile">
														<?php echo substr($row->project_name,0,15);?>
													</a>
													
												<?php else: ?>
													<a href="<?php echo get_home_url(); ?>/project-approved.html?id=<?= $row->id ?>" class="project-name">
														<?php echo substr($row->project_name,0,56);?>
													</a>
													<a href="<?php echo get_home_url(); ?>/pipeline-project.html?id=<?= $row->id ?>" class="project-name-mobile">
														<?php echo substr($row->project_name,0,15);?>
													</a>
													
												<?php endif; ?>
											</td>
											<td class="approve"><?php echo $row->date_approval != "0000-00-00 00:00:00" ?  _e('[:km]អនុម័តរួច[:en]Approved[:]') : _e('[:km]កំពុងរៀបចំ[:en]In Pipeline[:]');?></td>		
										</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>

					<div class="row">
						<div class="col-12">
							<div id='map_show' class="map"></div>
							<?php
								global $wpdb;
								$table_list_doc_type = $wpdb->prefix . "list_doc_type";
								$table_lists_documents = $wpdb->prefix . "lists_documents";
								$table_project = $wpdb->prefix . "project";
								$table_project_proponent_partner = $wpdb->prefix . "project_proponent_partner";
								// $GLOBALS[ 'kmlData' ] = $wpdb->get_results( "SELECT * from $table_list_doc_type as ldt INNER JOIN $table_lists_documents as ld ON ldt.id=ld.id_list_doc_type WHERE ldt.id=2");
								$GLOBALS[ 'kmlData' ] = $wpdb->get_results( "SELECT * from $table_list_doc_type as listDocType INNER JOIN $table_lists_documents as listDoc on listDoc.id_list_doc_type = listDocType.id INNER JOIN $table_project_proponent_partner as ppp on ppp.id_project= listDoc.id_project INNER JOIN $table_project as p on p.id=ppp.id_project where ppp.type='proponent' AND  listDocType.type_code='project_locations'");
								?>
							<!-- <div id="capture"  class="capture"></div> -->
						</div>
					</div>

				</div>
			</div>
		</div>
        <?php endwhile; ?>

	<?php }

get_footer();