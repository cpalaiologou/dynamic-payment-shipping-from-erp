<?php
$_['heading_title']    = 'Customer dynamic payment';

$_['text_extension']   = 'Extensions';
$_['text_success']     = 'Success: You have modified customer dynamic payment!';
$_['text_edit']        = 'Edit customer dynamic payment';
$_['text_enabled']     = 'Enabled';
$_['text_disabled']    = 'Disabled';
$_['text_all_zones']   = 'All Zones';
$_['text_home']        = 'Home';
$_['text_no_methods']  = 'No payment profiles yet.';
$_['text_no_bindings'] = 'No customer links yet.';
$_['text_view_only']   = 'These lists are read-only. Profiles and links are maintained outside this screen (e.g. ERP / import).';
$_['text_confirm_delete'] = 'Delete this record?';
$_['text_pagination']  = 'Showing %d to %d of %d (%d Pages)';

$_['text_method_added']   = 'Profile added.';
$_['text_method_updated'] = 'Profile updated.';
$_['text_method_deleted'] = 'Profile deleted.';
$_['text_binding_added']   = 'Customer link added.';
$_['text_binding_deleted'] = 'Customer link removed.';

$_['tab_general']   = 'Settings';
$_['tab_methods']   = 'Payment profiles';
$_['tab_bindings']  = 'Customer links';

$_['entry_total_min']      = 'Minimum order total';
$_['entry_total_max']      = 'Maximum order total';
$_['entry_geo_zone']       = 'Geo Zone';
$_['entry_status']         = 'Status';
$_['entry_sort_order']     = 'Sort Order';
$_['entry_method_name']    = 'Display name';
$_['entry_method_code']    = 'Internal code';
$_['entry_method_api_id']  = 'API ID';
$_['entry_customer']       = 'Customer';
$_['entry_bind_method']    = 'Payment profile';
$_['entry_search']         = 'Search customer, email, profile, code, API ID';

$_['help_total_min'] = '0 = no minimum. Cart subtotal must be at least this value (store default currency).';
$_['help_total_max'] = '0 = no maximum. Cart subtotal must not exceed this value when set.';

$_['column_method_id'] = 'ID';
$_['column_name']      = 'Name';
$_['column_code']      = 'Code';
$_['column_api_id']    = 'API ID';
$_['column_bind_id']   = 'ID';
$_['column_customer']  = 'Customer';
$_['column_email']     = 'Email';
$_['column_method']    = 'Profile';
$_['column_action']    = 'Action';

$_['button_save']          = 'Save';
$_['button_cancel']        = 'Cancel';
$_['button_add_method']    = 'Add profile';
$_['button_update_method'] = 'Update profile';
$_['button_delete']        = 'Delete';
$_['button_add_binding']   = 'Add link';
$_['button_filter']        = 'Filter';
$_['button_edit']          = 'Edit';

$_['help_methods']  = 'Define payment options (name, code for ERP, API ID). Linked customers see each profile as its own payment choice at checkout.';
$_['help_bindings'] = 'Link customers to one or more profiles. Search filters by customer name, email, profile name, code, or API ID.';

$_['error_permission'] = 'Warning: You do not have permission to modify customer dynamic payment!';
$_['error_method_name'] = 'Display name is required.';
$_['error_method_code'] = 'Internal code is required.';
$_['error_binding']     = 'Select a customer and a payment profile.';
