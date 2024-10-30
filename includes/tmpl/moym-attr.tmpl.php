<?php
/**
 * File to display sections of Attribute and Role Mapping.
 *
 * @package login-with-yourmembership\tmpl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function to display Attribute/Role Mapping tab.
 *
 * @return void
 */
function moym_attr_mapping() {

	echo '
    <div class="moym_table_layout">
        <h3>Basic Attribute Mapping</h3>
	    <hr>
	    <div>Map Attributes sent by YourMembership to the following user attributes in WordPress.</div><br/>
        <table width="80%">
            <tr><td><strong><span style="color: red">*</span> Username: </strong></td><td><b>Email</b></td></tr>
            <tr><td><strong><span style="color: red">*</span> Email: </strong></td><td><b>Email</b></td></tr>
            <tr><td><strong><span style="color: red">*</span> First Name: </strong></td><td><input type="text" name="moym_first_name" style="width:90%;background: #DCDAD1;color:darkslategray;" disabled placeholder="Enter attribute name for First Name"></td></tr>
            <tr><td><strong><span style="color: red">*</span> Last Name: </strong></td><td><input type="text" name="moym_last_name" style="width:90%;background: #DCDAD1;color:darkslategray;" disabled placeholder="Enter attribute name for Last Name"></td></tr>
            <tr><td><strong><span style="color: red">*</span> Group/Role: </strong></td><td><input type="text" name="moym_group_role" style="width:90%;background: #DCDAD1;color:darkslategray;" disabled placeholder="Enter attribute name for Group/Role"></td></tr>
            <tr>
                <td colspan="3"><br/>
                    <span style="color: red">*</span>
                    These attributes are configurable in <a href="' . esc_url( admin_url( 'admin.php?page=moym_settings&tab=pricing' ) ) . '">Premium and All-Inclusive</a> versions of the plugin.
                </td>
            </tr>
        </table>
	    <br>
	    
	    <h3>Custom Attribute Mapping</h3>
	    <hr>
	    <div>Map Attributes sent by YourMembership to the <strong>usermeta</strong> table of WordPress Database.</div>
		<table width="80%">
		    <tr><td colsapn="3">
		        <br/><input type="button" name="add_attribute" value="Add Attribute" disabled
		                    class="button button-primary button-large"><br/><br/>
		    </td></tr>
            <tr>
                <td colspan="3"><br/>
                    Customized Attribute Mapping is configurable in <a href="' . esc_url( admin_url( 'admin.php?page=moym_settings&tab=pricing' ) ) . '">All-Inclusive</a> version of the plugin.
                </td>
            </tr>
        </table>
                
        <br>
        <h3>Role Mapping</h3>
        <hr>
        <div>Map roles sent by YourMembership to the following user roles in WordPress.</div><br/>
        <table width="80%">
            <tr>
                <td><strong><span style="color: red">*</span>Default Role: </strong></td>
                <td>
                    <select id="moym_default_user_role" name="moym_default_user_role" style="width:150px;background-color: #DCDAD1;color:darkslategray;" disabled>
                        <option selected="selected" value="Subscriber"> Subscriber</option>
                    </select>
                    &nbsp;<i>Select the default role to assign to Users.</i>
                </td>
            </tr>';

		$wp_roles = new WP_Roles();
		$roles    = $wp_roles->get_names();
	foreach ( $roles as $role_value => $role_name ) {
		echo '<tr><td><span style="color: red">*</span><b>' . esc_html( $role_name ) . '</b></td><td><input type="text" placeholder="Semi-colon(;) separated Group/Role value for ' . esc_attr( $role_name ) . '" style="width: 400px;background-color: #DCDAD1;" disabled/></td></tr>';
	}
		echo '
            <tr id="save_config_element1">
                <td colspan="3" style="text-align:center;"><br/>
                    <input type="submit" style="width:100px;" name="submit" value="Save" 
                            class="button button-primary button-large" disabled/>
                </td>
            </tr>
            <tr>
                <td colspan="3"><br/>
                    <span style="color: red">*</span>
                    Default role mapping is configurable in <a href="' . esc_url( admin_url( 'admin.php?page=moym_settings&tab=pricing' ) ) . '">Premium and All-Inclusive</a> version of the plugin. 
                    <br>
                    <span style="color: red">*</span>
                    Customized role mapping is configurable in <a href="' . esc_url( admin_url( 'admin.php?page=moym_settings&tab=pricing' ) ) . '">All-Inclusive</a> version of the plugin.
                </td>
            </tr>
        </table>
    </div>';

}


