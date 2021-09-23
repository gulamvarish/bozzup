    <?php
    /**
    * Template Name: Left Sidebar
    */

    get_header();
    ?>


    <?php while ( have_posts() ) : the_post(); ?>




              
                <div class="row m-0 sidebarmain">


                    <div id="wpsc_sm_filters1" class="col-sm-12 col-md-2 wpsc_sidebar">
                        <div class="row m-0">   
                            <div class="sidebarlogo">
                                <a href="/">
                                <img class="logobottomimage" src="<?php echo plugin_dir_url('/').'supportcandy/asset/images/login.png'; ?>"></a>
                            </div>
                            <hr>             
                            <?php                           

                           


                            if($_SESSION['user_type']=='supplier' && isset($_SESSION['user_type'])){
                                ?>               
                                <a href="/" class="sidebar-link"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/dashboard.png'; ?>" alt="Dashboard"/><p class="text">Dashboard</p></a>
                                <a href="/customers" class="sidebar-link"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/customer.png'; ?>" alt="Customers"/><p class="text">Customers</p></a>
                                <a href="/membership-account" class="sidebar-link"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/setting.png'; ?>" alt="Setting"/><p class="text">Setting</p></a>
                                
                                <?php }elseif($_SESSION['user_type']=='subscriber'){ ?>

                                      <a href="/edit-user?id=<?php echo $current_user->ID; ?>" class="sidebar-link"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/customer.png'; ?>" alt="Customers"/><p class="text">Edit Profile</p></a>
                                <?php 
                            }else{

                                /* wp_logout();
                                 wp_redirect(home_url());
                                exit;*/
                            } ?>

                            <a href="javascript:void(0)" id="helpemail" class="sidebar-link mailModal" data-toggle="modal" data-target="#mailModal"><img onclick="sidebar_menu();" src = "<?php echo plugin_dir_url('/').'supportcandy/asset/images/icon/help.png'; ?>" alt="help"/><p class="text">Help</p></a>
                            <?php  
                            $wpsc_support_page_id = get_option('wpsc_support_page_id');
                            $support_page_url = get_permalink($wpsc_support_page_id);
                            $wpsc_allow_sign_out = get_option('wpsc_sign_out');
                            if($wpsc_allow_sign_out){?>
                                <button class="btn btn-sm pull-right" type="button" id="wpsc_sign_out" onclick="window.location.href='<?php echo wp_logout_url($support_page_url) ?>'" style="background-color:#FF5733 !important;color:#FFFFFF !important;"><i class="fas fa-sign-out-alt"></i> <?php _e('Log Out','supportcandy')?></button>
                            <?php  }?>          
                        </div>

                    </div>


                    <div class="col-md-10 right-sidebarpage">
                        <?php

                       the_content();
                    ?>
                </div>


</div>




<?php endwhile; // End of the loop. ?>

<?php
get_footer();
