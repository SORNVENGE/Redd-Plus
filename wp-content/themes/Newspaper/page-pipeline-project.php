<?php

/* Template Name: Pipeline Project */


get_header();
		global $wpdb;
		$table_project = $wpdb->prefix . "project";
		$table_project_progress = $wpdb->prefix . "project_progress";
		$table_project_status = $wpdb->prefix . "project_status";
		$project_id=$_GET['id'];
	if (have_posts()) { ?>
		<?php
         $getProjectData = $wpdb->get_row( "SELECT * FROM $table_project WHERE id=$project_id"  );
         if($getProjectData->date_approval != "0000-00-00 00:00:00"){
            $fullurl = get_permalink(get_page_by_path( 'project-approved' ));
	         $reUrl = str_replace("pipeline-project.html","project-approved.html",$fullurl);
            header("Location: ".$reUrl."?id=".$project_id);
         }
		?>
		<?php while ( have_posts() ) : the_post(); ?>
		<div class="body-container">
			<!-- Block menu page  -->
			<div class="header-margin-bottom"></div>
			<div class="td-crumb-container">
				<?php echo td_page_generator::get_page_breadcrumbs(get_the_title()); ?>
			</div>
			<div class="header-margin-top"></div>
		     <!-- End Block menu page  -->
			<div class="content-container-pipeline">
				<div class="container no-padding-pipeline ">
					<div class="row no-gutters">
						<p class="title col-md-10 col-sm-12">
							<?php echo $getProjectData->project_name;?>
						</p>
						<div class="drawline col-lg-12"></div>
						<p class="detail col-lg-12">
							<?php echo $getProjectData->project_description;?>	
						</p>
               </div>
               <div class="row no-gutters header">
                     <div class="col-lg-8 col-8 text-left pipeline-progress ">
                        <p><?php _e("[:km]វឌ្ឍនៈភាពគម្រោង[:en]Project Progress[:]");?></p>
                     </div>
                     <div class="col-lg-4 col-4 text-right pipeline-status">
                        <p><?php _e("[:km]ស្ថានភាពគម្រោង[:en]Status[:]");?></p>
                     </div>
                     
               </div>
               
					<?php
						$getProjectOne = $wpdb->get_row( "SELECT * from $table_project_progress as proProgress INNER JOIN $table_project_status as proStatus on proProgress.id_status = proStatus.id WHERE proProgress.id_project=$project_id  AND proStatus.id=1"  );
					?>
					<?php
						$fetchProject = $wpdb->get_results( "SELECT DISTINCT proProgress.id_project,proStatus.id,proStatus.status_en, proStatus.parent from redd_project_progress as proProgress INNER JOIN redd_project_status as proStatus on proProgress.id_status =proStatus.id WHERE proProgress.id_project=$project_id  AND proStatus.parent=0" );
                  if($fetchProject){
							foreach ( $fetchProject as $key=> $row ) {
					?>
                  
                  
                  <div class="row first "> 
                     <div class="col-lg-12 width-text">
                        <div class="row no-gutters">
                           <div class="col-sm-8 text-left first-title ">
                                 <?php echo $key+1 . '. ' . $row->status_en;?>
                           </div>
                           <div class="col-sm-4 text-lg-right text-center width-button">
                              <?php if($row->id==1) : ?>
                                 <p class='text-complete'>
                                    <?php _e("[:km]ដំណើរការបញ្ចប់[:en]COMPLETED[:]");?>
                                 </p>	
                              <?php elseif($row->id==2) : ?>
                                 <p class='text-ongoing'>
                                    <?php _e("[:km]កំពុងដំណើរការ[:en]ONGOING[:]");?>
                                 </p>
                              <?php elseif($row->id==3) : ?>
                                 <p class='text-complete'>
                                    <?php _e("[:km]ដំណើរការបញ្ចប់[:en]COMPLETED[:]");?>
                                    COMPLETED									
                                 </p>
                              <?php elseif($row->id==5) : ?>
                                 <p class='text-complete'>
                                    <?php _e("[:km]ស្ថានភាពគម្រោង[:en]COMPLETED[:]");?>
                                 </p>
                              <?php elseif($row->id==6) : ?>
                                 <p class='text-reject'>
                                    <?php _e("[:km]គម្រោងត្រូវបានបដិសេធ[:en]REJECTED PROJECT[:]");?>
                                 </p>
                              <?php else : ?>
                                 <p class='text-reject'>
                                    <?php _e("[:km]គម្រោងត្រូវបានបដិសេធ[:en]REJECTED PROJECT[:]");?>
                                    									
                                 </p>
                              <?php endif; ?>
                              
									</div>
                        </div>
                        <div class="row no-gutters mt-2 mb-4">
                           <?php
                              $projectDetail = $wpdb->get_results( "SELECT * from $table_project_progress as proProgress INNER JOIN $table_project_status as proStatus on proProgress.id_status = proStatus.id WHERE proProgress.id_project=$project_id  AND proStatus.id=$row->id" );
                              if($projectDetail){
                                 foreach ( $projectDetail as $subrow ) {
                           ?>
                                 <div class="col-lg-9 col-9 text-left text">
                                    <p class="pl-3">- <?php echo $subrow->process;?></p>
                                 </div>
                                 <div class="col-lg-3 col-3 text-right">
                                    <?php if($subrow->id==2 || $subrow->id==3) { ?>
                                       <p class='pr-2'>
                                          <?php echo $subrow->date;?>								
                                       </p>
                                    <?php 
                                    }
                                    ?>
                                 </div>
                           <?php
                                       
                                 }
                              }

                              if($row->id == 4){
                                 $statusSub = $wpdb->get_results( "SELECT * from $table_project_progress as proProgress INNER JOIN $table_project_status as proStatus on proProgress.id_status = proStatus.id WHERE proProgress.id_project=$project_id  AND proStatus.parent=$row->id" );
                                 // var_dump($statusSub);
                                 if($statusSub){
                                    foreach ( $statusSub as $subrow ) {
                           ?>

                                    <div class="col-lg-9 col-9 text-left text">
                                       <p class="pl-3">- <?php echo $subrow->process;?></p>
                                    </div>
                                    <div class="col-lg-3 col-3 text-right">
                                       <?php if($subrow->id==2 || $subrow->id==3) { ?>
                                          <p class='pr-2'>
                                             <?php echo $subrow->date;?>								
                                          </p>
                                       <?php 
                                       }
                                       ?>
                                    </div>

                                    <?php 
                                       if($subrow->id == 7){
                                          $statusSub2 = $wpdb->get_results( "SELECT * from $table_project_progress as proProgress INNER JOIN $table_project_status as proStatus on proProgress.id_status = proStatus.id WHERE proProgress.id_project=$project_id  AND proStatus.parent=$subrow->id" );
                                          // var_dump($statusSub);
                                          if($statusSub){
                                             foreach ( $statusSub2 as $subrow2 ) {
                                    ?>
                                                <div class="col-lg-9 col-9 text-left text">
                                                   <p class="pl-5">+ <?php echo $subrow2->status_en;?></p>
                                                </div>
                                                <div class="col-lg-3 col-3 text-right">
                                                   <?php if($subrow2->id==2 || $subrow2->id==3) { ?>
                                                      <p class='pr-2'>
                                                         <?php echo $subrow2->date;?>								
                                                      </p>
                                                   <?php 
                                                   }
                                                   ?>
                                                </div>

                                                <?php 
                                                   if($subrow2->id==9){
                                                      $statusSub3 = $wpdb->get_results( "SELECT * from $table_project_progress as proProgress INNER JOIN $table_project_status as proStatus on proProgress.id_status = proStatus.id WHERE proProgress.id_project=$project_id  AND proStatus.parent=$subrow2->id" );
                                                      // var_dump($statusSub);
                                                      if($statusSub3){
                                                         foreach ( $statusSub3 as $subrow3 ) {
                                                ?>
                                                            <div class="col-lg-9 col-9 text-left text">
                                                               <p class="" style="padding-left: 80px;">+ <?php echo $subrow3->status_en;?></p>
                                                            </div>
                                                            <div class="col-lg-3 col-3 text-right">
                                                               <?php if($subrow3->id==2 || $subrow3->id==3) { ?>
                                                                  <p class='pr-2'>
                                                                     <?php echo $subrow3->date;?>								
                                                                  </p>
                                                               <?php 
                                                               }
                                                               ?>
                                                            </div>
                                                <?php 
                                                         }
                                                      }
                                                   }
                                                ?>
                                    <?php 
                                             } //end forloop
                                          } // end if
                                       }
                                    ?>

                           <?php 
                                    } //end loop
                                 } //end if
                              } //end if
                           ?>
                        </div>
                     </div>
                  </div>
                  
					<?php
							}
						}
					?>
				</div>
		</div>
		</div>
        <?php endwhile; ?>
	<?php }
get_footer();