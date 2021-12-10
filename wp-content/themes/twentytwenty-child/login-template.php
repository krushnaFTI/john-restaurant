<?php  
/** 
 * Template Name: Login Page Template 
 */  
get_header();   
global $wpdb;    
$msg = '';

if (is_user_logged_in()) {
    echo "<script type='text/javascript'>window.location.href='". home_url() ."'</script>";  
    exit();
}

if(isset($_POST['login-submit'])) 
{  
    $username = $wpdb->escape($_REQUEST['username']);  
    $password = $wpdb->escape($_REQUEST['password']);  
    
    $remember = "false";  

    $login_data = array();  
    $login_data['user_login'] = $username;  
    $login_data['user_password'] = $password;  
    $login_data['remember'] = $remember;  

    $user_verify = wp_signon( $login_data, false );   

    if ( is_wp_error($user_verify) ){  
        $msg = 'Invalid login details';  
    } else {    
        echo "<script type='text/javascript'>window.location.href='". home_url() ."'</script>";  
        exit();  
    }  
} 
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
        <div class="login-form-main-div">
            <?php if($msg) { ?><div class="login-form-error-div" style="color: red; margin-bottom: 15px;"><?php echo $msg; ?></div><?php } ?>
            <div class="login-form">
                <form id="login1" name="form" action="<?php echo home_url(); ?>/login/" method="post">            
                    <input id="username" type="text" placeholder="Username / Email" name="username"><br>  
                    <input id="password" type="password" placeholder="Password" name="password"><br> 
                    <input id="submit" type="submit" name="login-submit" value="Login">
                </form>    
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>  