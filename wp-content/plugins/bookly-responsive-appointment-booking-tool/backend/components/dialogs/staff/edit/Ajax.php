<?php
namespace Bookly\Backend\Components\Dialogs\Staff\Edit;

use Bookly\Backend\Components\Schedule\BreakItem;
use Bookly\Backend\Components\Schedule\Component as ScheduleComponent;
use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Components\Dialogs\Staff\Edit
 */
class Ajax extends Lib\Base\Ajax
{
    /** @var Lib\Entities\Staff */
    protected static $staff;
    
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        $permissions = get_option( 'bookly_gen_allow_staff_edit_profile' )
            ? array( '_default' => 'staff' )
            : array();
        if ( Lib\Config::staffCabinetActive() ) {
            $permissions = array( '_default' => 'staff' );
        }

        return $permissions;
    }

    /**
     * Get data for staff
     */
    public static function getStaffData()
    {
        $data = Proxy\Shared::editStaff(
            array( 'alert' => array( 'error' => array() ), 'tpl' => array() ),
            self::$staff
        );

        $users_for_staff = Lib\Utils\Common::isCurrentUserAdmin() ? self::_getUsersForStaff( self::$staff->getId() ) : array();

        $staff_fields = self::$staff->getFields();
        $src = false;
        if ( $staff_fields['attachment_id'] ) {
            $src = wp_get_attachment_image_src( $staff_fields['attachment_id'], 'thumbnail' );
        }
        $staff_fields['avatar'] = $src ? $src[0] : null;

        $response = array(
            'html' => array(
                'edit'    => self::renderTemplate( 'dialog_body', array( 'staff' => self::$staff ), false ),
                'details' => self::renderTemplate( 'details', array( 'staff' => self::$staff, 'users_for_staff' => $users_for_staff ), false ),
            ),
        );
        if ( self::$staff->getId() ) {
            $response['holidays'] = self::$staff->getHolidays();
            $response['html']['advanced']     = Proxy\Pro::getAdvancedHtml( self::$staff, $data['tpl'], true );
            $response['html']['services']     = self::_getStaffServices( self::$staff->getId(), null );
            $response['html']['schedule']     = self::_getStaffSchedule( self::$staff->getId(), null );
            $response['html']['special_days'] = Proxy\SpecialDays::getStaffSpecialDaysHtml( '', self::$staff->getId() );
            $response['html']['holidays']     = self::renderTemplate( 'holidays', array( 'holidays' => $response['holidays'] ), false );
            $response['staff'] = $staff_fields;
            $response['alert'] = $data['alert'];
        }
		
		/* Start Code Before Update Holiday Store Value Session Variable */
		global $wpdb;
		$staff_id = self::$staff->getId();
		$before_update_holiday_list = $wpdb->get_results("select DISTINCT  h.date from ".$wpdb->prefix."bookly_staff s, 
				   ".$wpdb->prefix."bookly_holidays h
					where h.staff_id = '$staff_id'", ARRAY_A); // Get Old Holiday List Query
		$a=0;			
		foreach($before_update_holiday_list as $before_update_holiday_detail){
			 $beforedate[$a] = '"'.$before_update_holiday_detail['date'].'"';
			 $a++;
		}	
		if (!empty(session_id())){
			$_SESSION['flagValue_StaffScheduleOldHolidays'] = $beforedate;	//Store Value Session
		}
		/* End Code Before Update Holiday Store Value Session Variable */
		
        wp_send_json_success( $response );
    }

    /**
     * Get staff count.
     */
    public static function getStaffCount()
    {
        wp_send_json_success( array( 'count' => Lib\Entities\Staff::query()->count() ) );
    }

    /**
     * Update staff from POST request.
     */
    public static function updateStaff()
    {
		global $wpdb;
		
		/* Start Code for Updated/Deleted Holiday List for staff Member */
		if (!empty(session_id())){
			$old_holiday_date = $_SESSION['flagValue_StaffScheduleOldHolidays']; //Get Old Holiday List
			$staffScheduleArray = self::$staff->getFields();
			$staff_id = $staffScheduleArray['id']; //store current staff id
			
			if(!empty($staff_id) &&  !empty($old_holiday_date)){
				$schedule_new_Details = $wpdb->get_results("select DISTINCT  s.email,s.full_name,h.date from ".$wpdb->prefix."bookly_staff s, 
			 			   ".$wpdb->prefix."bookly_holidays h
							where s.id = h.staff_id  and h.staff_id = '$staff_id'", ARRAY_A); // Update/deleted Holiday list Query
				$a=0;
				foreach($schedule_new_Details as $schedule_detail){
					 $new_holiday_date[$a] = '"'.$schedule_detail['date'].'"';
					 $staff_full_name = $schedule_detail['full_name'];
					 $staff_email = $schedule_detail['email'];
					 $a++;
				}
				
			    $to_mail = get_option('admin_email');//$staff_email;
				$subject_email = 'Staff Holidays Schedule';
				$body_email = "<html><head><title></title></head><body>
					<p>Dear ".$staff_full_name."</p>";
					$added_holiday_diff = array_diff($new_holiday_date,$old_holiday_date); // Difference Both Date Array ADDed Holiday
					if(!empty($added_holiday_diff)){ 
						/* Start Get List of Year in Holiday */
						foreach($added_holiday_diff as $added_getlistofyear){
								$addholiday_year .= implode('',explode("/", date_format(date_create(trim($added_getlistofyear,'"')),"Y"))).',';
						}
						$addholidayyear = explode(",",rtrim($addholiday_year,','));
						$addholidayyear = implode(',',array_unique($addholidayyear));
						/* End Get List of Year in Holiday */
					
						$body_email .="<p> ".$staff_full_name." has updated the list of holidays ".$addholidayyear."</p>";
						foreach($added_holiday_diff as $apply_holiday_list){
							$body_email .= date_format(date_create(trim($apply_holiday_list,'"')),"m/d/Y").'</br>';
						}
					}
					
					$delete_holiday_diff = array_diff($old_holiday_date,$new_holiday_date); // Difference Both Date Array deleted Holiday
					if(!empty($delete_holiday_diff)){
						/* Start Get List of Year in Holiday */
						foreach($delete_holiday_diff as $deleted_getlistofyear){
								$deleteholiday_year .= implode('',explode("/", date_format(date_create(trim($deleted_getlistofyear,'"')),"Y"))).',';
						}
						$deleteholidayyear = explode(",",rtrim($deleteholiday_year,','));
						$deleteholidayyear = implode(',',array_unique($deleteholidayyear));
						/* End Get List of Year in Holiday */
						
						$body_email .="<p> ".$staff_full_name." has deleted the list of holidays ".$deleteholidayyear."</p>";
						foreach($delete_holiday_diff as $apply_holiday_list){
							$body_email .= date_format(date_create(trim($apply_holiday_list,'"')),"m/d/Y").'</br>';
						}	
                    }
					
					if(!empty($delete_holiday_diff) || !empty($added_holiday_diff)){
						$body_email .="<br/><br/>Thank you</body></html>";
						$headers  = "MIME-Version: 1.0" . "\r\n";
						$headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
						$headers .= "From: ".get_option( 'bookly_email_sender_name' )." <".get_option( 'bookly_email_sender' ).">" . "\r\n";
						
						wp_mail( $to_mail, $subject_email, $body_email, $headers );
						//wp_mail( 'chandani@freelancetoindia.com', $subject_email, $body_email, $headers );
					}						
				
				unset($_SESSION['flagValue_StaffScheduleOldHolidays']); //Unset Session Value
			}
		}
		/* End Code for Updated/Deleted Holiday List for staff Member */
		
        if ( ! Lib\Utils\Common::isCurrentUserAdmin() ) {
            // Check permissions to prevent one staff member from updating profile of another staff member.
            do {
                if ( self::parameter( 'staff_cabinet' ) && Lib\Config::staffCabinetActive() ) {
                    $allow = true;
                } else {
                    $allow = get_option( 'bookly_gen_allow_staff_edit_profile' );
                }
                if ( $allow ) {
                    unset ( $_POST['wp_user_id'] );
                    break;
                }
                do_action( 'admin_page_access_denied' );
                wp_die( 'Bookly: ' . __( 'You do not have sufficient permissions to access this page.' ) );
            } while ( 0 );
        } elseif ( self::parameter( 'id' ) == 0
                && ! Lib\Config::proActive()
                && Lib\Entities\Staff::query()->count() > 0
        ) {
            do_action( 'admin_page_access_denied' );
            wp_die( 'Bookly: ' . __( 'You do not have sufficient permissions to access this page.' ) );
        }

        $params = self::postParameters();
        if ( ! $params['category_id'] ) {
            $params['category_id'] = null;
        }
        if ( ! $params['time_zone'] ) {
            $params['time_zone'] = null;
        }

        self::$staff->setFields( $params );

        Proxy\Shared::preUpdateStaff( self::$staff, $params );
        self::$staff->save();
        Proxy\Shared::updateStaff( self::$staff, $params );
        
        /* start send mail to staff member when admin create */
            if ( $params['id'] < 1 && $params['email']) {
                $staffScheduleArray = self::$staff->getFields();
                $staff_id = $staffScheduleArray['id'];
                
                $linkURL = site_url().'/?staffId='.$staff_id;
                $to_mail = $params['email'];
    			$subject_email = 'Staff member at John Restaurant';
    			$body_email = "<html><head><title></title></head><body>
    			    <p>Dear ".$params['full_name']."</p>
    			    <p>Welcome to John Restaurant as Staff Member</p>
    			    <p>Please click below link to activate web pusher.</p>
    			    <p><a href='".$linkURL."'>Click Here</a></p>
    			</body></html>";
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
            	$headers .= "From: ".get_option( 'bookly_email_sender_name' )." <".get_option( 'bookly_email_sender' ).">" . "\r\n";
    	        
    	        wp_mail( $to_mail, $subject_email, $body_email, $headers );
            }
        /* end send mail to staff member when admin create */

        wp_send_json_success( array( 'staff' => self::$staff->getFields() ) );
    }

    /**
     * Get staff services
     */
    public static function getStaffServices()
    {
        $form        = new Forms\StaffServices();
        $staff_id    = self::parameter( 'staff_id' );
        $location_id = self::parameter( 'location_id' );
        $form->load( $staff_id, $location_id );
        $html = self::_getStaffServices( $staff_id, $location_id );
        wp_send_json_success( compact( 'html' ) );
    }

    /**
     * Update staff services.
     */
    public static function updateStaffServices()
    {
        $form = new Forms\StaffServices();
        $form->bind( self::postParameters() );
        $form->save();
        Proxy\Shared::updateStaffServices( self::postParameters() );
        wp_send_json_success();
    }

    /**
     * Get staff schedule.
     */
    public static function getStaffSchedule()
    {
        $staff_id    = self::parameter( 'staff_id' );
        $location_id = self::parameter( 'location_id' );
        $html        = self::_getStaffSchedule( $staff_id, $location_id );
        wp_send_json_success( compact( 'html' ) );
    }

    /**
     * Update staff schedule.
     */
    public static function updateStaffSchedule()
    {   
		global $wpdb;
		
		Proxy\Shared::updateStaffSchedule( self::postParameters() );
		$staffScheduleArray = self::postParameters();
		$staff_id = $staffScheduleArray['staff_id'];
		
		if(!empty($staff_id)){
			    // start Without Update Old Staff Schedule Value
				$schedule_old_Details = $wpdb->get_results("SELECT  DISTINCT i.id,i.start_time,i.end_time FROM 
				".$wpdb->prefix."bookly_staff s,".$wpdb->prefix."bookly_staff_schedule_items i 
				WHERE  i.staff_id=s.id  and i.staff_id = '$staff_id'", ARRAY_A);
		
				$start_time = array(); $end_time = array();
				foreach($schedule_old_Details as $old_detail){
					$old_id = $old_detail['id'];
					$old_start_time[$old_id] = $old_detail['start_time'];
					$old_end_time[$old_id] = $old_detail['end_time'];
					array_push($start_time,$old_start_time);
					array_push($end_time,$old_end_time);
				}
				 // end Without Update Old Staff Schedule Value
		
				$form = new Forms\StaffSchedule();
				$form->bind( self::postParameters() );
				$form->save(); 
		
				// start With Update New Staff Schedule Value
				$schedule_new_Details = $wpdb->get_results("SELECT  DISTINCT i.id,i.start_time,i.end_time FROM 
				".$wpdb->prefix."bookly_staff s,".$wpdb->prefix."bookly_staff_schedule_items i 
				WHERE  i.staff_id=s.id  and i.staff_id = '$staff_id'", ARRAY_A);

				$new_start_time = array(); $new_end_time = array();
				foreach($schedule_new_Details as $new_detail){
					$new_id = $new_detail['id'];
					$new_start_time_value[$new_id] = $new_detail['start_time'];
					$new_end_time_value[$new_id] = $new_detail['end_time'];
					array_push($new_start_time,$new_start_time_value);
					array_push($new_end_time,$new_end_time_value);
				}
				// end With Update New Staff Schedule Value
       
				$start_time_differnece = array_diff_assoc($old_start_time,$new_start_time_value);
				$end_time_differnece   = array_diff_assoc($old_end_time,$new_end_time_value);
        }
		
		
        /* start send mail to staff member when admin update staff  */
			
            $staffDetails = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bookly_staff WHERE id = '$staff_id'", ARRAY_A);
            if( $staffDetails['email'] != '')
			{	
				if ( !empty($start_time_differnece) || !empty($end_time_differnece))
				{
					$daysArray = array('2' => 'Monday', '3' => 'Tuesday', '4' => 'Wednesday', '5' => 'Thursday', '6' => 'Friday', '7' => 'Saturday', '1' => 'Sunday');
					$scheduleHtml = "<table cellpadding='1' cellspacing='10'>";
					foreach($staffScheduleArray['ssi'] as $daykey => $dayVal){
						$scheduleHtml .= "<tr>";
							$scheduleHtml .= "<td>".$daysArray[$dayVal]."</td>";
							if($staffScheduleArray['start_time'][$daykey]){
								$scheduleHtml .= "<td>".date('g:i a', strtotime($staffScheduleArray['start_time'][$daykey]))."</td>";
								$scheduleHtml .= "<td>".date('g:i a', strtotime($staffScheduleArray['end_time'][$daykey]))."</td>";
							}else{
								$scheduleHtml .= "<td>OFF</td>";
								$scheduleHtml .= "<td>OFF</td>";
							}
						$scheduleHtml .= "</tr>";
					}
					$scheduleHtml .= "</table>";
					
					$to_mail = get_option('admin_email'); //$staffDetails['email'];
					$subject_email = 'Staff Hours Updated/Scheduled';
					$body_email = "<html><head><title></title></head><body>
						<p>Dear ".$staffDetails['full_name']."</p>
						<p> ".$staffDetails['full_name']." has updated/Schedule their working hours as per the below.</p>
						".$scheduleHtml."<br/><br/>Thank you
					</body></html>";
					
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
					$headers .= "From: ".get_option( 'bookly_email_sender_name' )." <".get_option( 'bookly_email_sender' ).">" . "\r\n";
					
					wp_mail( $to_mail, $subject_email, $body_email, $headers );
					//wp_mail( 'chandani@freelancetoindia.com', $subject_email, $body_email, $headers );
				}
				
				/* Start Code For Added Staff Schedule */
				if (!empty($_SESSION['flagValue_StaffScheduleBreak'] && $_SESSION['flagValue_StaffScheduleBreak'] == 'StaffScheduleBreak'))
				{	
					$staff_break_Details = $wpdb->get_results("SELECT DISTINCT  s.id,s.full_name,i.day_index,i.id,
											b.start_time as break_start_time,b.end_time as break_end_time,i.start_time as working_start,i.end_time as working_end
											FROM 
											wp_bookly_staff s, wp_bookly_schedule_item_breaks b,
											wp_bookly_staff_schedule_items i WHERE  
											b.staff_schedule_item_id = i.id AND i.staff_id = '$staff_id' AND s.id = '$staff_id'");	
											
					/*$staff_break_Details = $wpdb->get_results("SELECT DISTINCT  s.id,s.full_name,i.day_index,i.id,
											b.start_time as break_start_time,b.end_time as break_end_time,i.start_time as working_start,i.end_time as working_end
											FROM 
											wp_bookly_staff s, wp_bookly_schedule_item_breaks b,
											wp_bookly_staff_schedule_items i WHERE  
											i.staff_id = '$staff_id' AND s.id = '$staff_id' GROUP BY day_index ");*/																		
											
					$no_row = $wpdb->num_rows;
					if( $no_row >=1 ){
						$scheduleBreakHtml = '';	
						$scheduleBreakHtml = "<table cellpadding='1' cellspacing='10'>";
							$scheduleBreakHtml .= "<tr>";
								$scheduleBreakHtml .= "<td>Day</td>";
								$scheduleBreakHtml .= "<td>Working Start</td>";
								$scheduleBreakHtml .= "<td>Working End</td>";
								$scheduleBreakHtml .= "<td>Start Time</td>";
								$scheduleBreakHtml .= "<td>End Time</td>";
							$scheduleBreakHtml .= "</tr>";
							
							foreach($staff_break_Details as $sbd){
								$daysArray = array('2' => 'Monday', '3' => 'Tuesday', '4' => 'Wednesday', '5' => 'Thursday', '6' => 'Friday', '7' => 'Saturday', '1' => 'Sunday');
								if (array_key_exists($sbd->day_index, $daysArray)) {
									$day_name = $daysArray[$sbd->day_index];
								}
								$sbd_full_time = $sbd->full_name;
								$scheduleBreakHtml .= "<tr>";
								$scheduleBreakHtml .= "<td>".$day_name."</td>";
								$scheduleBreakHtml .= "<td>".$sbd->working_start."</td>";
								$scheduleBreakHtml .= "<td>".$sbd->working_end."</td>";
								$scheduleBreakHtml .= "<td>".$sbd->break_start_time."</td>";
								$scheduleBreakHtml .= "<td>".$sbd->break_end_time."</td>";
								$scheduleBreakHtml .= "</tr>";
							}
								 
						$scheduleBreakHtml .= "</table>";
					
						//$to_mail_break = 'chandani@freelancetoindia.com';//$staffDetails['email'];
						$to_mail_break = get_option('admin_email'); //$staffDetails['email'];
						$break_subject_email = 'Staff Break Updated/Scheduled';
						$break_body_email = "<html><head><title></title></head><body>
										<p>Dear ".$staffDetails['full_name'].","."</p>
										<p>  ".$sbd_full_time." has updated/Schedule their break hours as per the below.</p>
										".$scheduleBreakHtml."<br/><br/>Thank you
										</body></html>";
										
						$headers = "MIME-Version: 1.0" . "\r\n";
						$headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
						$headers .= "From: ".get_option( 'bookly_email_sender_name' )." <".get_option( 'bookly_email_sender' ).">" . "\r\n";
											
						wp_mail( $to_mail_break, $break_subject_email, $break_body_email, $headers );
						
						unset($_SESSION['flagValue_StaffScheduleBreak']);
					} 	
				}	
				/* End Code For Added Staff Break Schedule */
				
                if( $staffDetails['wp_pusher_id'] != ''){
                    $get_admin_webpushr_key = get_option('admin_webpushr_key_enter');
                	$get_admin_webpushr_auth_token = get_option('admin_webpushr_authtoken_enter');
                	$end_point = 'https://api.webpushr.com/v1/notification/send/sid';
                	$http_header = array( 
                		"Content-Type: Application/Json", 
                		"webpushrKey: ".$get_admin_webpushr_key, 
                		"webpushrAuthToken: ".$get_admin_webpushr_auth_token
                	);
                
                	$site_title = get_bloginfo( 'name' );
                
                	$req_data = array(
                		'title' 	 => $site_title, //required
                		'message' 	 => "Your staff schedule has been changed.", //required
                		'target_url' => 'https://www.webpushr.com', //required
                		'sid'		 => $staffDetails['wp_pusher_id'] //required
                	);
                	$ch = curl_init();
                	curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
                	curl_setopt($ch, CURLOPT_URL, $end_point );
                	curl_setopt($ch, CURLOPT_POST, 1);
                	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data) );
                	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                	$response = curl_exec($ch); 
                }
            }
			
        /* end send mail to staff member when admin update staff  */
        wp_send_json_success();
    }

    /**
     * Update staff holidays.
     */
    public static function updateStaffHolidays()
    {
        $interval = self::parameter( 'range', array() );
        $range    = new Lib\Slots\Range( Lib\Slots\DatePoint::fromStr( $interval[0] ), Lib\Slots\DatePoint::fromStr( $interval[1] )->modify( 1 ) );
        if ( self::$staff ) {
            if ( self::parameter( 'holiday' ) == 'true' ) {
                $repeat   = (int) ( self::parameter( 'repeat' ) == 'true' );
                if ( ! $repeat ) {
                    Lib\Entities\Holiday::query( 'h' )
                        ->update()
                        ->set( 'h.repeat_event', 0 )
                        ->where( 'h.staff_id', self::$staff->getId() )
                        ->where( 'h.repeat_event', 1 )
                        ->whereRaw( 'CONVERT(DATE_FORMAT(h.date, \'1%%m%%d\'),UNSIGNED INTEGER) BETWEEN %d AND %d', array( $range->start()->value()->format( '1md' ), $range->end()->value()->format( '1md' ) ) )
                        ->execute();			
                }

                $holidays = Lib\Entities\Holiday::query()
                    ->whereBetween( 'date', $range->start()->value()->format( 'Y-m-d' ), $range->end()->value()->format( 'Y-m-d' ) )
                    ->where( 'staff_id', self::$staff->getId() )
                    ->indexBy( 'date' )
                    ->find();
				
                foreach ( $range->split( DAY_IN_SECONDS ) as $r ) {
                    $day = $r->start()->value()->format( 'Y-m-d' );
                    if ( array_key_exists( $day, $holidays ) ) {
                        $holiday = $holidays[ $day ];
                    } else {
                        $holiday = new Lib\Entities\Holiday();
                    }
                    $holiday
                        ->setDate( $day )
                        ->setRepeatEvent( $repeat )
                        ->setStaffId( self::$staff->getId() )
                        ->save();
                } 	
            } else {
                Lib\Entities\Holiday::query( 'h' )
                    ->delete()
                    ->where( 'h.staff_id', self::$staff->getId() )
                    ->where( 'h.repeat_event', 1 )
                    ->whereRaw( 'CONVERT(DATE_FORMAT(h.date, \'1%%m%%d\'),UNSIGNED INTEGER) BETWEEN %d AND %d', array( $range->start()->value()->format( '1md' ), $range->end()->value()->format( '1md' ) ) )
                    ->execute();

                Lib\Entities\Holiday::query()
                    ->delete()
                    ->whereBetween( 'date', $range->start()->value()->format( 'Y-m-d' ), $range->end()->value()->format( 'Y-m-d' ) )
                    ->where( 'staff_id', self::$staff->getId() )
                    ->execute();
						
            }
            wp_send_json_success( self::$staff->getHolidays() );
        }
        wp_send_json_error();
    }

    /**
     * Handle staff schedule break.
     */
    public static function staffScheduleHandleBreak()
    {
        $start_time    = self::parameter( 'start_time' );
        $end_time      = self::parameter( 'end_time' );
        $working_start = self::parameter( 'working_start' );
        $working_end   = self::parameter( 'working_end' );
        $break_id      = self::parameter( 'id', 0 );

        if ( Lib\Utils\DateTime::timeToSeconds( $start_time ) >= Lib\Utils\DateTime::timeToSeconds( $end_time ) ) {
            wp_send_json_error( array( 'message' => __( 'The start time must be less than the end one', 'bookly' ), ) );
        }

        $schedule_item = new Lib\Entities\StaffScheduleItem();
        $schedule_item->load( self::parameter( 'ss_id' ) );

        $in_working_time = $working_start <= $start_time && $start_time <= $working_end
            && $working_start <= $end_time && $end_time <= $working_end;
        if ( ! $in_working_time || ! $schedule_item->isBreakIntervalAvailable( $start_time, $end_time, $break_id ) ) {
            wp_send_json_error( array( 'message' => __( 'The requested interval is not available', 'bookly' ), ) );
        }

        $schedule_item_break = new Lib\Entities\ScheduleItemBreak();
        if ( $break_id ) {
            $schedule_item_break->load( $break_id );
        } else {
            $schedule_item_break->setStaffScheduleItemId( $schedule_item->getId() );
        }
        $schedule_item_break
            ->setStartTime( $start_time )
            ->setEndTime( $end_time )
            ->save();
		if (!empty(session_id())){
				$_SESSION['flagValue_StaffScheduleBreak'] = 'StaffScheduleBreak';
		}		
		$staff_schedule_item_id = $schedule_item->getId();
        if ( $schedule_item_break ) {
			$break = new BreakItem( $schedule_item_break->getId(), $schedule_item_break->getStartTime(), $schedule_item_break->getEndTime() );
            wp_send_json_success( array(
                'html'     => $break->render( false ),
                'interval' => $break->getFormatedInterval(),
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Error adding the break interval', 'bookly' ), ) );
        }
    }

    /**
     * Delete staff schedule break.
     */
    public static function deleteStaffScheduleBreak()
    {
		
        $break = new Lib\Entities\ScheduleItemBreak();
        $break->setId( self::parameter( 'id', 0 ) );
		
		$start_time    = self::parameter( 'start_time' );
        $end_time      = self::parameter( 'end_time' );
        $working_start = self::parameter( 'working_start' );
        $working_end   = self::parameter( 'working_end' );
        $break_id      = self::parameter( 'id', 0 );	
		$break->delete();
		if (!empty(session_id())){
			$_SESSION['flagValue_StaffScheduleBreak'] = 'StaffScheduleBreak';	
		}	
        wp_send_json_success();	
    }
	

    /**
     * Get list of users available for particular staff.
     *
     * @param integer $staff_id If null then it means new staff
     * @return array
     */
    private static function _getUsersForStaff( $staff_id = null )
    {
		
        /** @var \wpdb $wpdb */
        global $wpdb;
        if ( ! is_multisite() ) {
            $query = sprintf(
                'SELECT ID, user_email, display_name FROM ' . $wpdb->users . '
               WHERE ID NOT IN(SELECT DISTINCT IFNULL( wp_user_id, 0 ) FROM ' . Lib\Entities\Staff::getTableName() . ' %s)
               ORDER BY display_name',
                $staff_id !== null
                    ? 'WHERE ' . Lib\Entities\Staff::getTableName() . '.id <> ' . (int) $staff_id
                    : ''
            );
            $users = $wpdb->get_results( $query );
        } else {
            // In Multisite show users only for current blog.
            $query = Lib\Entities\Staff::query( 's' )->select( 'DISTINCT wp_user_id' )->whereNot( 'wp_user_id', null );
            if ( $staff_id != null ) {
                $query->whereNot( 'id', $staff_id );
            }
            $exclude_wp_users = array();
            foreach ( $query->fetchArray() as $staff ) {
                $exclude_wp_users[] = $staff['wp_user_id'];
            }
            $users = array_map(
                function ( \WP_User $wp_user ) {
                    $obj = new \stdClass();
                    $obj->ID = $wp_user->ID;
                    $obj->user_email = $wp_user->data->user_email;
                    $obj->display_name = $wp_user->data->display_name;

                    return $obj;
                },
                get_users( array( 'blog_id' => get_current_blog_id(), 'orderby' => 'display_name', 'exclude' => $exclude_wp_users ) )
            );
        }

        return $users;
    }

    /**
     * @param int      $staff_id
     * @param int|null $location_id
     * @return string
     */
    private static function _getStaffServices( $staff_id, $location_id )
    {
        $form = new Forms\StaffServices();
        $form->load( $staff_id, $location_id );
        $services_data = $form->getServicesData();

        return self::renderTemplate( 'services', compact( 'form', 'services_data', 'staff_id', 'location_id' ), false );
    }

    /**
     * Get staff schedule.
     *
     * @param int      $staff_id
     * @param int|null $location_id
     * @return string|void
     */
    private static function _getStaffSchedule( $staff_id, $location_id )
    {
	
        $staff = new Lib\Entities\Staff();
        $staff->load( $staff_id );

        $schedule = new ScheduleComponent( 'start_time[{index}]', 'end_time[{index}]' );
		
        $ss_ids = array();
        foreach ( $staff->getScheduleItems( $location_id ) as $item ) {
            $id = $item->getId();
            $schedule->addHours( $id, $item->getDayIndex(), $item->getStartTime(), $item->getEndTime() );
            $ss_ids[ $id ] = $item->getDayIndex();
        }

        foreach (
            Lib\Entities\ScheduleItemBreak::query()
                ->whereIn( 'staff_schedule_item_id', array_keys( $ss_ids) )
                ->sortBy( 'start_time, end_time' )
                ->fetchArray() as $break
        ) {
            $schedule->addBreak( $break['staff_schedule_item_id'], $break['id'], $break['start_time'], $break['end_time'] );
		} 
        return self::renderTemplate( 'schedule', compact( 'schedule', 'staff_id', 'location_id', 'ss_ids' ), false );
    }

    /**
     * Extend parent method to control access on staff member level.
     *
     * @param string $action
     * @return bool
     */
    protected static function hasAccess( $action )
    {
        if ( parent::hasAccess( $action ) ) {
            if ( ! Lib\Utils\Common::isCurrentUserAdmin() ) {
                self::$staff = Lib\Entities\Staff::query()->where( 'wp_user_id', get_current_user_id() )->findOne();
                if ( ! self::$staff ) {
                    return false;
                } else switch ( $action ) {
                    case 'getStaffData':
                    case 'getStaffCount':
                    case 'updateStaff':
                        return true;
                    case 'getStaffSchedule':
                    case 'getStaffServices':
                    case 'updateStaffHolidays':
							
                    case 'updateStaffServices':
							return self::$staff->getId() == self::parameter( 'staff_id' );
                    case 'staffScheduleHandleBreak':
							$res_schedule = new Lib\Entities\StaffScheduleItem();
							$res_schedule->load( self::parameter( 'ss_id' ) );
							return self::$staff->getId() == $res_schedule->getStaffId();
                    case 'deleteStaffScheduleBreak':
							$break = new Lib\Entities\ScheduleItemBreak();
							$break->load( self::parameter( 'id' ) );
							$res_schedule = new Lib\Entities\StaffScheduleItem();
							$res_schedule->load( $break->getStaffScheduleItemId() );
							return self::$staff->getId() == $res_schedule->getStaffId();
                    case 'updateStaffSchedule':
							if ( self::hasParameter( 'ssi' ) ) {
								foreach ( self::parameter( 'ssi' ) as $id => $day_index ) {
									$res_schedule = new Lib\Entities\StaffScheduleItem();
									$res_schedule->load( $id );
									$staff = new Lib\Entities\Staff();
									
									$staff->load( $res_schedule->getStaffId() );
									if ( $staff->getWpUserId() != get_current_user_id() ) {
										return false;
									}
								}
							}
							return true;
                    default:
							return false;
                }
            } elseif ( in_array( $action, array( 'getStaffData', 'updateStaff' ) ) ) {
                self::$staff = new Lib\Entities\Staff();
                self::$staff->load( self::parameter( 'id' ) );
            } elseif ( $action === 'updateStaffHolidays' ) {
                self::$staff = new Lib\Entities\Staff();
                self::$staff->load( self::parameter( 'staff_id' ) );
            }

            return true;
        }

        return false;
    }
}