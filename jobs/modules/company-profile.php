<?php
$user_id = get_current_user_id();
$company_data = get_user_meta( $user_id, '_jobs_company_data', true );
if ( ! is_array( $company_data ) ) $company_data = array();
?>
<div class="jobs-module-header">
    <h2 class="jobs-module-title">Company Profile</h2>
</div>
<form id="jobs-company-form" class="jobs-form" enctype="multipart/form-data">
    <div class="jobs-form-group">
        <label for="company_logo" class="jobs-label">Company Logo</label>
        <?php if ( ! empty( $company_data['logo'] ) ) : ?>
            <div style="margin-bottom: 10px;">
                <img src="<?php echo esc_url( $company_data['logo'] ); ?>" alt="Company Logo" style="max-width: 150px; border-radius: 5px;">
            </div>
        <?php endif; ?>
        <input type="file" name="company_logo" id="company_logo" class="jobs-input" accept="image/*">
    </div>

    <div class="jobs-form-group">
        <label for="company_name" class="jobs-label">Company Name</label>
        <input type="text" name="company_data[name]" value="<?php echo esc_attr( isset($company_data['name']) ? $company_data['name'] : '' ); ?>" class="jobs-input">
    </div>

    <div class="jobs-form-group">
        <label for="company_address" class="jobs-label">Address</label>
        <input type="text" name="company_data[address]" value="<?php echo esc_attr( isset($company_data['address']) ? $company_data['address'] : '' ); ?>" class="jobs-input">
    </div>

    <div class="jobs-form-group">
        <label for="employee_count" class="jobs-label">Employee Count</label>
        <input type="text" name="company_data[employee_count]" value="<?php echo esc_attr( isset($company_data['employee_count']) ? $company_data['employee_count'] : '' ); ?>" class="jobs-input">
    </div>

    <div class="jobs-form-group">
        <label for="company_details" class="jobs-label">Details</label>
        <textarea name="company_data[details]" class="jobs-textarea" rows="5"><?php echo esc_textarea( isset($company_data['details']) ? $company_data['details'] : '' ); ?></textarea>
    </div>

    <div class="jobs-form-actions">
        <button type="submit" class="jobs-btn-primary">Save Profile</button>
    </div>
    <div id="jobs-company-message" class="jobs-message"></div>
</form>
