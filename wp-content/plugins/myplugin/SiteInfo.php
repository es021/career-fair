<?php

class SiteInfo {

    const MES_REQUEST_FAILED = "Something went wrong. Unable to process request. Please refresh and try again.";
    const INNOVA_FB = "https://www.fb.com/innovaseedssolutions";
    const TERMS_OF_USE_PDF = "/wp-content/uploads/document/terms_of_use_and_service.pdf";
    const NOT_SPECIFIED_DISPLAY = "<span class='text-muted'>Details Not Available.</span>";
    //*** Image PATH ***//
    const HOME_PICTURES = '["/image/home/1_sm.jpg","/image/home/2_sm.jpg","/image/home/3_sm.jpg"]';
//*** Image PATH ***//
    const IMAGE_USER_DEFAULT = "/image/default-user.png";
    const IMAGE_COMPANY_DEFAULT = "/image/default-company.jpg";
    //**** general **********//
    const STATUS_ERROR = "Error";
    const STATUS_SUCCESS = "Success";
    const LIMIT_SEARCH_PER_PAGE = 5;
    //***** page offset  **********//
    const PAGE_OFFSET_ADMIN_PANEL = 20; // table listing
    const PAGE_OFFSET_CAREER_FAIR = 20; //company listing in homepage
    const PAGE_OFFSET_CAREER_FAIR_VACANCY = 2;
    const PAGE_OFFSET_SEARCH_SUGGEST = 5;
    const PAGE_OFFSET_DISPLAY_RECRUITER = 5;
    const PAGE_OFFSET_DISPLAY_VACANCY = 5;
    //***** PAGE path **********//
    const PAGE_TITLE_LOG_IN = "log-in";
    const PAGE_TITLE_LOG_OUT = "log-out";
    const PAGE_SIGN_UP_STUDENT = "sign-up-student";
    const PAGE_SIGN_UP_RECRUITER = "sign-up-recruiter";
    const PAGE_USER_ACTIVATION = "user-activation";
    const PAGE_REQUEST_USER_ACTIVATION = "request-user-activation";
    const PAGE_REGISTRATION_COMPLETE = "registration-complete";
    const PAGE_RESET_PASSWORD = "reset-password";
    //**** User Role ******//
    const ROLE_ADMIN = "administrator";
    const ROLE_EDITOR = "editor";
    const ROLE_ORGANIZER = "organizer";
    const ROLE_STUDENT = "student";
    const ROLE_RECRUITER = "recruiter";
    //**** User Profile filters *******//
    const REGIS_STUDENT_FIELD_ONLY = array("major", "university", "resume_/_cv");
    const REGIS_RECRUITER_FIELD_ONLY = array("");
    const ALLOWABLE_IMAGE_UPLOAD = '["jpeg","jpg","png"]';
    const ALLOWABLE_DOCUMENT_UPLOAD = '["pdf"]';
    //*** users table field ***///
    const USERS_ID = "ID";
    const USERS_LOGIN = "user_login";
    const USERS_EMAIL = "user_email";
    const USERS_URL = "user_url";
    const USERS_DATE_REGISTER = "user_registered";
    const USERS_PASS = "user_pass";
    //need to check
    const USERS_KEYS = '["user_email",
        "user_url",
        "user_login",
        "user_pass",
        "user_registered"]';
    //****** user status value **********//
    const USERMETA_STAT_NOT_ACTIVATED = "Not Activated";
    const USERMETA_STAT_ACTIVE = "Active";
    const USERMETA_STAT_BLOCKED = "Blocked";
    //*** usermeta FOR RECRUITER ONLY table field ***//
    const USERMETA_REC_POSITION = "rec_position";
    const USERMETA_REC_COMPANY = "rec_company";
    const USERMETA_REC_COMPANY_NAME = "rec_company_name";
    const USERMETA_REC_ZOOM_ID = "rec_zoom_id";
    //*** usermeta table field ***//
    const USERMETA_ACTIVATION_KEY = "activation_key";
    const USERMETA_STATUS = "user_status";
    const USERMETA_FIRST_NAME = "first_name";
    const USERMETA_LAST_NAME = "last_name";
    const USERMETA_RESUME_URL = "resume_url";
    const USERMETA_IMAGE_URL = "reg_profile_image_url";
    const USERMETA_PORTFOLIO_URL = "portfolio_url";
    const USERMETA_IMAGE_POSITION = "profile_image_position";
    const USERMETA_IMAGE_SIZE = "profile_image_size";
    const USERMETA_MAJOR = "major";
    const USERMETA_MINOR = "minor";
    const USERMETA_UNIVERSITY = "university";
    const USERMETA_DESCRIPTION = "description";
    const USERMETA_ROLES_ARRAY = "wp_cf_capabilities";
    const USERMETA_IS_ACTIVATED = "is_activated";
    const USERMETA_PHONE_NUMBER = "phone_number";
    const USERMETA_GRADUATION_MONTH = "graduation_month";
    const USERMETA_GRADUATION_YEAR = "graduation_year";
    const USERMETA_SPONSOR = "sponsor";
    const USERMETA_CGPA = "cgpa";
    const USERMETA_FEEDBACK = "feedback";
    //** default value **//
    const DEF_USERMETA_IMAGE_POSITION = "50% 50%";
    const DEF_USERMETA_IMAGE_SIZE = "cover";
    const DEF_USERMETA_IMAGE_URL = "/image/default-user.png";
    const DEF_USERMETA_CGPA = "<small class='text-muted'>Not Specified</small>";
    const DEF_USERMETA_MINOR = "<small class='text-muted'>Not Available</small>";
    //used in wzs21 save info ajax
    const USERMETA_REC_KEYS = '["first_name"
        , "last_name"
        , "rec_position"
        , "rec_company"
        , "reg_profile_image_url"
        , "profile_image_position"
        , "profile_image_size"
        , "last_name"
        , "feedback"
        , "wp_cf_capabilities"]';
    //used in wzs21 save info ajax
    const USERMETA_KEYS = '["first_name"
        , "last_name"
        , "resume_url"
        , "portfolio_url"
        , "reg_profile_image_url"
        , "profile_image_position"
        , "profile_image_size"
        , "major"
        , "minor"
        , "university"
        , "description"
        , "sponsor"
        , "cgpa"
        , "user_status"
        , "phone_number"
        , "feedback"
        , "graduation_month"
        , "graduation_year"
        , "wp_cf_capabilities"]';
    // table name *************//
    const TABLE_PASSWORD_RESET = "password_reset";
    const TABLE_LOGS = "logs";
    //general field from any table
    const FIELD_TOKEN = "token";
    const FIELD_IS_EXPIRED = "is_expired";
    const FIELD_USER_ID = "user_id";
    const FIELD_ID = "ID";
    const FIELD_CREATED_AT = "created_at";
    const FIELD_UPDATED_AT = "updated_at";
    //***** LIMIT ********//
    const USERMETA_DESCRIPTION_MAX_LENGTH = 1000;
    const MAX_FILE_SIZE_UPLOAD_MB = 2;
    const MB_TO_BYTE = 1000000;
    //******* $_FILE index *********//
    const FILE_INDEX_IMAGE = 0;
    const FILE_INDEX_RESUME = 1;
    const FILE_INDEX_RESUME_DROP = 2;
    //**** Email Type ****//
    const EMAIL_TYPE_USER_ACTIVATION = "email_user_activation";
    const EMAIL_TYPE_RESET_PASSWORD = "email_reset_password";
    const EMAIL_TYPE_NEW_REC = "email_new_recruiter";

}

?>