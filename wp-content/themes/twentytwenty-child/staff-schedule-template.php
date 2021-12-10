<?php  
/** 
 * Template Name: Staff Schedule Template 
 */  
get_header();   
global $wpdb;    
$msg = '';

if (!is_user_logged_in()) {
    echo "<script type='text/javascript'>window.location.href='". home_url() ."'</script>";  
    exit();
}

$booklyStaffTable = $wpdb->prefix.'bookly_staff';
$userID = get_current_user_id();
$staffQuery = $wpdb->get_row("SELECT * FROM $booklyStaffTable WHERE wp_user_id = $userID", ARRAY_A);
if(empty($staffQuery)){
    echo "<script type='text/javascript'>window.location.href='". home_url() ."'</script>";  
    exit();
}

$booklyStaffScheduleItemTable = $wpdb->prefix.'bookly_staff_schedule_items';
$staffID = $staffQuery['id'];
$staffScheduleItemArray = $wpdb->get_results("SELECT * FROM $booklyStaffScheduleItemTable WHERE staff_id = $staffID", ARRAY_A);
//echo '<pre>'; print_r($staffScheduleItemArray); die();

$daysArray = array('2' => 'Monday', '3' => 'Tuesday', '4' => 'Wednesday', '5' => 'Thursday', '6' => 'Friday', '7' => 'Saturday', '1' => 'Sunday');
?>  
<main id="site-content" role="main">
    <?php
        if ( have_posts() ) {
            while ( have_posts() ) {
                the_post();
                get_template_part( 'template-parts/content', get_post_type() );
            }
        }
    ?>
</main>
<div class="main-content">
    <div class="container">  
        <?php if(!empty($staffScheduleItemArray)) { ?>
            <table cellpadding='1' cellspacing='10'>
                <?php foreach($daysArray as $dayID => $dayVal) { ?>
                    <tr>
                        <td><?php echo $dayVal; ?></td>    
                        <?php if($staffScheduleItemArray[$dayID - 1]['start_time']) { ?>
                            <td><?php echo date('g:i a', strtotime($staffScheduleItemArray[$dayID - 1]['start_time'])); ?></td>    
                            <td><?php echo date('g:i a', strtotime($staffScheduleItemArray[$dayID - 1]['end_time'])); ?></td>    
                        <?php } else { ?>
                            <td colspan="2">OFF</td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
</div>
<?php get_footer(); ?>  