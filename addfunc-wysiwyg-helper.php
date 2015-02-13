<?php
/*
    Plugin Name: AddFunc WYSIWYG Helper
    Plugin URI:
    Description: Reveals the prominent HTML elements in the default WYSIWYG editor (TinyMCE) comprehensively, while maintaining edibility as well as any theme styles (in most cases). In effect, you have a WYSIWYG and a WYSIWYM (What You See Is What You Mean) combined. Can also cancel out certain default WordPress styling in the WYSIWYG such as the captions box/border.
    Version: 2.3
    Author: AddFunc
    Author URI: http://profiles.wordpress.org/addfunc
    License: Public Domain
    @since 3.0.1
           ______
       _  |  ___/   _ _ __   ____
     _| |_| |__| | | | '_ \ / __/™
    |_ Add|  _/| |_| | | | | (__
      |_| |_|   \__,_|_| |_|\___\
                    by Joe Rhoney
*/
$aFWH_Version = '2.3';



/*
    F U N C T I O N S
    =================
*/

add_action('init', 'aFCrntUsrMeta');
$aFCrntUsrID = '';
$aFWYSIWYM = '';
$aFCancelStyles = '';
function aFCrntUsrMeta(){
  global $current_user, $aFCrntUsrID, $aFWYSIWYM, $aFCancelStyles;
  $aFCrntUsrID = $current_user->ID;
  $aFWYSIWYM = get_user_meta($aFCrntUsrID,'aFWYSIWYM',true);
  $aFCancelStyles = get_user_meta($aFCrntUsrID,'aFCancelStyles',true);
}
function aFWHcss( $mce_css ) {
  global $aFWYSIWYM, $aFCancelStyles;
  if ($aFWYSIWYM == 1) {
    if (!empty($mce_css)) $mce_css .= ',';
    $mce_css .= plugins_url( 'wysiwym.css', __FILE__ );
  }
  if ($aFCancelStyles == 1) {
    if (!empty($mce_css)) $mce_css .= ',';
    $mce_css .= plugins_url( 'overrides.css', __FILE__ );
  }
  return $mce_css;
}
function aFWHUpgradeNag() {
  if ( !current_user_can('install_plugins') ) return;
  $aFWH_v = 'version';
  if(get_bloginfo('version') >= "4.0"){
    $aFFavs = network_admin_url('plugin-install.php?tab=favorites&user=addfunc');
    $aFFavsTarg = '';
  }
  else {
    $aFFavs = 'http://profiles.wordpress.org/addfunc';
    $aFFavsTarg = ' target="_blank"';
  }
  if ( get_site_option( $aFWH_v ) == $aFWH_Version ) return;
    $msg = sprintf(__('Thank you for updating AddFunc WYSIWYG Helper! If you like this plugin, please consider <a href="%s" target="_blank">rating it</a> and trying out <a href="%s"'.$aFFavsTarg.'>our other plugins</a>!'),'http://wordpress.org/support/view/plugin-reviews/addfunc-wysiwyg-helper',$aFFavs);
  echo "<div class='update-nag'>$msg</div>";
  update_site_option( $aFWH_v, $aFWH_Version );
}
if (is_admin()){
  add_filter('mce_css', 'aFWHcss');
  add_action('admin_notices', 'aFWHUpgradeNag');
}



/*
    H E L P   T A B
    ===============
*/

add_action('init','aFWHAddHT');
function aFWHHelpTab() {
    $screen = get_current_screen();
    $screen->add_help_tab( array(
        'id'      => 'aFWHHelpTab',
        'title'   => __('Highlighted Content'),
        'content' => '
        <p>'.__( 'The colored borders and highlighting you see in Visual editing mode are there to reveal for you what and where some of the various HTML elements are that comprise the content you are editing.' ).'</p>
        <p><strong>'.__( 'Legend' ).'</strong></p>
        <ul>
          <li style="border-radius:3px;box-sizing:border-box;border:1px solid rgba(255,0,105,0.15);">
            <strong>P:</strong> '.__('paragraph').'</li>
          <li style="border-radius:3px;box-sizing:border-box;border:1px solid rgba(255,150,0,0.35);">
            <strong>L:</strong> '.__('unordered list').'</li>
          <li style="border-radius:3px;box-sizing:border-box;border:1px solid rgba(255,215,0,0.3);">
            <strong>#:</strong> '.__('ordered list').'</li>
          <li style="border-radius:3px;box-sizing:border-box;border:1px solid rgba(205,255,0,0.4);">
            <strong>('.__('within').' L '.__('or').' #):</strong> '.__('individual list item').'</li>
          <li style="border-radius:3px;box-sizing:border-box;border:1px solid rgba(180,235,0,0.4);">
            <strong>V:</strong> '.__('div (a box, basically)').'</li>
          <li style="border-radius:3px;box-sizing:border-box;border:1px solid rgba(0,180,235,0.3);">
            <strong>1-6:</strong> '.__('heading 1, heading 2, etc.').'</li>
          <li style="background:rgba(150,0,255,0.1);outline:5px solid rgba(150,0,255,0.1);">
            <strong>('.__('highlights').'):</strong> '.__('span (text with added formatting)').'</li>
        </ul>',
    ));
}
function aFWHAddHT(){
  global $aFWYSIWYM;
  if($aFWYSIWYM == 1) {
    add_action('load-post.php', 'aFWHHelpTab');
  }
}



/*
    U S E R   P R E F E R E N C E S
    ===============================
*/

add_action( 'show_user_profile', 'aFWHUserPref' );
add_action( 'edit_user_profile', 'aFWHUserPref' );

function aFWHUserPref( $user ) { ?>
<h3><?php _e("WYSIWYG Helper", "blank"); ?></h3>
<table class="form-table">
  <tr>
    <th><label for="aFWYSIWYM">WYSIWYM</label></th>
    <td>
      <input type="checkbox" name="aFWYSIWYM" id="aFWYSIWYM" value="1" <?php if (esc_attr( get_the_author_meta( "aFWYSIWYM", $user->ID )) == 1) echo "checked"; ?> /><label for="aFWYSIWYM"><?php _e("Enable"); ?></label><br />
      <p class="description">WYSIWYM = <span style='text-decoration: underline;'>W</span>hat <span style='text-decoration: underline;'>Y</span>ou <span style='text-decoration: underline;'>S</span>ee <span style='text-decoration: underline;'>I</span>s <span style='text-decoration: underline;'>W</span>hat <span style='text-decoration: underline;'>Y</span>ou <span style='text-decoration: underline;'>M</span>ean</p>
    </td>
  </tr>
  <tr>
    <th><label for="aFCancelStyles"><?php _e("Cancel Default Styles"); ?></label></th>
    <td>
      <input type="checkbox" name="aFCancelStyles" id="aFCancelStyles" value="1" <?php if (esc_attr( get_the_author_meta( "aFCancelStyles", $user->ID )) == 1) echo "checked"; ?> /><label for="aFCancelStyles"><?php _e("Enable"); ?></label><br />
      <p class="description"><?php _e("This cancels the following default styles in WordPress' WYSIWYG editor (Visual mode): caption styles"); ?></p>
    </td>
  </tr>
</table>
<?php }

add_action( 'personal_options_update', 'save_aFWHUserPref' );
add_action( 'edit_user_profile_update', 'save_aFWHUserPref' );

function save_aFWHUserPref( $user_id ) {
  if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
  update_user_meta( $user_id, 'aFWYSIWYM', $_POST['aFWYSIWYM'] );
  update_user_meta( $user_id, 'aFCancelStyles', $_POST['aFCancelStyles'] );
}