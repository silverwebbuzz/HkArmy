<script type="text/javascript">
	var EVENT_LAN = "{{ __('languages.sidebar.Event Management') }}";
	var PRODUCT_LAN = "{{ __('languages.sidebar.Product') }}";
	var Select_MEMBER_LAN = "{{ __('languages.PurchaseProduct.Select_member') }}";

	var Money_LAN = "{{ __('languages.event.Money') }}";
	var Tokens_LAN = "{{ __('languages.event.Tokens') }}";
	
	//Product Page Add/Edit
	var PRODUCT_SUFFIX_NAME = "{{ __('languages.Product.product_suffix_name') }}";
	var PRODUCT_SUFFIX = "{{ __('languages.Product.product_suffix') }}";
	var ADD_MORE_SUFFIX = "{{ __('languages.Product.add_more_suffix') }}";
	var PRODUCT_SUFFIX_NAME = "{{ __('languages.Product.product_suffix_name') }}";
	var PRODUCT_MONEY = "{{ __('languages.Product.product_money') }}";
	var PRODUCT_TOKEN = "{{ __('languages.Product.product_token') }}";
	var PRODUCT_MONEY_TOKEN = "{{ __('languages.Product.product_money_token') }}";
	var ADD_PRODUCT_MONEY = "{{ __('languages.Product.add_product_money') }}";
	var ADD_PRODUCT_TOKEN = "{{ __('languages.Product.add_product_token') }}";
	var ADD_PRODUCT_MONEY_TOKEN = "{{ __('languages.Product.add_product_money_token') }}";
	var EVENT_MONEY_TOKEN = "{{ __('languages.event.Event_Money_Tokens') }}";
	var PLEASE_SELECT_EVENT_DATE = "{{ __('languages.event.please_select_event_dates') }}";

	var OPTION_CODE = "{{ __('languages.Product.option_code') }}";
	var OPTION_NAME = "{{ __('languages.Product.option_name') }}";
	var EVENT_MONEY = "{{__('languages.event.event_money')}}";
	var EVENT_TOKEN = "{{__('languages.event.event_token')}}";

	var VALIDATIONS = {
		PLEASE_ENTER_EVENT_NAME : "{{__('languages.validation.event.please_enter_event_name')}}",
		PLEASE_SELECT_EVENT_TYPE : "{{__('languages.validation.event.please_select_event_type')}}",
		PLEASE_SELECT_ASSESMENT : "{{__('languages.validation.event.please_select_assesment')}}",
		PLEASE_SELECT_ASSESMENT_TEXT : "{{__('languages.validation.event.please_enter_assesment')}}",
		PLEASE_SELECT_EVENT_DATES : "{{__('languages.validation.event.please_select_event_dates')}}",
		PLEASE_SELECT_STARTDATE : "{{__('languages.validation.event.please_select_startdate')}}",
		PLEASE_SELECT_STARTTIME : "{{__('languages.validation.event.please_select_starttime')}}",
		PLEASE_SELECT_ENDDATE : "{{__('languages.validation.event.please_select_enddate')}}",
		PLEASE_SELECT_ENDTIME : "{{__('languages.validation.event.please_select_endtime')}}",
		PLEASE_SELECT_STATUS : "{{__('languages.validation.event.please_select_status')}}",
		PLEASE_SELECT_COST_TYPE : "{{__('languages.please_select_post_type')}}",
		PLEASE_SELECT_DATE_FIRST : "{{__('languages.please_select_date_first')}}",
		PLEASE_SELECT_PRODUCT_AND_COST_TYPE : "{{__('languages.please_select_product_and_cost_type')}}",
		PLEASE_SELECT_PRODUCT_ : "{{__('languages.please_select_product')}}",
		PLEASE_SELECT_PRODUCT_COST_TYPE : "{{__('languages.please_select_cost_type')}}",
		PLEASE_SELECT_MEMBER : "{{__('languages.please_select_member')}}",
		PLEASE_SELECT_PRODUCT_ORDER : "{{__('languages.please_select_product_order')}}",
	};
	var SELECTED = "{{__('languages.selected')}}";
	var NONE_SELECTED = "{{__('languages.none_selected')}}";
	var SELECT_MEMBER = "{{__('languages.member.select_member')}}";
	var SELECT_CATEGORIES = "{{__('languages.select_categories')}}";
	var CATEGORIES_NOT_AVAILABLE = "{{__('languages.categories_not_available')}}";

	var BADGES_VALIDATION = {
		PLEASE_SELECT_BADGES_ENGLISH_NAME : "{{__('languages.badges.validations.please_select_badges_english_name')}}",
		PLEASE_SELECT_BADGES_CHINESE_NAME : "{{__('languages.badges.validations.please_select_badges_chinese_name')}}",
		PLEASE_SELECT_BADGES_TYPE : "{{__('languages.badges.validations.please_select_badges_type')}}",
		PLEASE_SELECT_BADGES_IMAGE : "{{__('languages.badges.validations.please_select_badges_image')}}",
		UPLOAD_VALID_IMAGE : "{{__('languages.badges.validations.upload_valid_image')}}",
		PLEASE_ENTER_ENGLISH_OTHER_BADGES_TYPE_NAME : "{{__('languages.badges.validations.please_enter_english_other_badges_type_name')}}",
		PLEASE_ENTER_CHINESE_OTHER_BADGES_TYPE_NAME : "{{__('languages.badges.validations.please_enter_chinese_other_badges_type_name')}}",
		PLEASE_SELECT_STATUS : "{{__('languages.badges.validations.please_select_status')}}",
	};

	var AWARD_VALIDATION = {
		PLEASE_ENTER_AWARD_NAME_ENGLISH : "{{__('languages.awards.validations.please_enter_award_name_english')}}",
		PLEASE_ENTER_AWARD_NAME_CHINESE : "{{__('languages.awards.validations.please_enter_award_name_chinese')}}",
		PLEASE_SELECT_AWARD_CATEGORIES : "{{__('languages.awards.validations.please_select_award_categories')}}",
		PLEASE_SELECT_YEAR : "{{__('languages.awards.validations.please_select_year')}}",
		PLEASE_SELECT_BADGE : "{{__('languages.awards.validations.please_select_badge')}}",
		PLEASE_SELECT_STATUS : "{{__('languages.awards.validations.please_select_status')}}",
		PLEASE_SELECT_OTHER_AWARD_TYPE_ENGLISH : "{{__('languages.awards.validations.please_select_other_award_type_english')}}",
		PLEASE_SELECT_OTHER_AWARD_TYPE_CHINESE : "{{__('languages.awards.validations.please_select_other_award_type_chinese')}}",
	};

	var AWARD_BADGES_CATEGORIES_VALIDATIONS = {
		PLEASE_SELECT_CATEGORIES_TYPE : "{{__('languages.awards_badges_categories.validations.please_select_categories_type')}}",
		PLEASE_ENTER_CATEGORIES_NAME_EN : "{{__('languages.awards_badges_categories.validations.please_enter_categories_name_en')}}",
		PLEASE_ENTER_CATEGORIES_NAME_CH : "{{__('languages.awards_badges_categories.validations.please_enter_categories_name_ch')}}",
		PLEASE_SELECT_STATUS : "{{__('languages.awards_badges_categories.validations.please_select_status')}}",
		PLEASE_SELECT_TEAM_MEMBER_MENTOR : "{{__('languages.awards_badges_categories.validations.please_select_team_member_mentor')}}",
	};

	var USER_MANAGEMENT_VALIDATION = {
		PLEASE_SELECT_ROLE :"{{__('languages.user_management.please_select_role')}}",
		PLEASE_ENTER_USER_NAME : "{{__('languages.user_management.please_enter_username')}}",
		PLEASE_ENTER_ENGLISH_NAME : "{{__('languages.user_management.please_enter_english_name')}}",
		PLEASE_ENTER_CHINESE_NAME : "{{__('languages.user_management.please_enter_chinese_name')}}",
		PLEASE_SELECT_GENDER : "{{__('languages.user_management.please_select_gender')}}",
		PLEASE_ENTER_EMAIL : "{{__('languages.user_management.please_enter_email')}}",
		PLEASE_ENTER_VALID_EMAIL : "{{__('languages.user_management.please_enter_valid_email')}}",
		PLEASE_ENTER_PASSWORD : "{{__('languages.user_management.please_enter_password')}}",
		PLEASE_ENTER_MIN_6_CHARACTER : "{{__('languages.user_management.please_enter_min_6_character')}}",
		PLEASE_ENTER_CONFIRM_PASSSWORD : "{{__('languages.user_management.please_enter_confirm_password')}}",
		CONFIRM_PASSWORD_CAN_NOT_MATCH : "{{__('languages.user_management.confirm_password_can_not_match')}}",
		PLEASE_ENTER_CONTACT_NUMBER : "{{__('languages.user_management.please_enter_contact_number')}}",
		PLEASE_ENTER_8_DIGITS : "{{__('languages.user_management.please_enter_8_digits')}}",
		PLEASE_ENTER_NUMERIC_VALUE : "{{__('languages.user_management.please_enter_numeric_value')}}",
		PLEASE_ENTER_NEW_PASSWORD : "{{__('languages.user_management.please_enter_new_password')}}"
	};

	var AWARD = "{{__('languages.awards_badges_categories.award')}}";
	var BADGE = "{{__('languages.awards_badges_categories.badge')}}";
	var PLEASE_SELECT_AWARD = "{{__('languages.award_assign.please_select_award')}}";
	var PLEASE_SELECT_BADGE = "{{__('languages.badge_assign.please_select_badge')}}";
	var PLEASE_SELECT_MEMBER = "{{__('languages.award_assign.please_select_atleast_one_member')}}";

	var SELECT_MEMBER_TYPE = "{{__('languages.member_type')}}";
	var MENTOR_TEAM = "{{__('languages.mentor_team')}}";
	var NOT_MENTOR_TEAM = "{{__('languages.not_mentor_team')}}";
	var PLEASE_SELECT_OPTION = "{{__('languages.please_select_option')}}";

	var AWARD_ASSIGN_VALIDATIONS = {
		PLEASE_SELECT_AWARD_CATEGORY : "{{__('languages.please_select_award_category')}}",
		PLEASE_ENTER_REFERENCE_NUMBER : "{{__('languages.please_enter_reference_number')}}",
		PLEASE_SELECT_ISSUE_DATE : "{{__('languages.please_select_issue_date')}}",
	};

	var BADGES_ASSIGN_VALIDATIONS = {
		PLEASE_SELECT_BADGES_CATEGORY : "{{__('languages.please_select_badges_category')}}",
		PLEASE_ENTER_REFERENCE_NUMBER : "{{__('languages.please_enter_reference_number')}}",
		PLEASE_SELECT_ISSUE_DATE : "{{__('languages.please_select_issue_date')}}",
	};

	var ARE_YOU_SURE_WANT_TO_CONFIRM_THIS = "{{__('languages.are_you_sure_want_to_confirm_this')}}";
	var SELECT_PRODUCT ="{{__('languages.select_product')}}";
	var ASSIGN_MEMBERS_TO_PRODUCT_ORDER = "{{__('languages.assign_members_to_product_order')}}";

</script>