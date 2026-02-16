<?php

namespace Nebalus\Webapi\Config\Types;

class PermissionNodeTypes
{
    public const string ADMIN = 'admin';
    public const string ADMIN_USER = 'admin.user';
    public const string ADMIN_USER_ROLE = 'admin.user.role';
    public const string ADMIN_USER_ROLE_ADD = 'admin.user.role.add';
    public const string ADMIN_USER_ROLE_REMOVE = 'admin.user.role.remove';
    public const string ADMIN_ROLE = 'admin.role';
    public const string ADMIN_ROLE_CREATE = 'admin.role.create';
    public const string ADMIN_ROLE_EDIT = 'admin.role.edit';
    public const string ADMIN_ROLE_DELETE = 'admin.role.delete';

    public const string FEATURE = 'feature';
    public const string FEATURE_REFERRAL = 'feature.referral';
    public const string FEATURE_REFERRAL_OWN = 'feature.referral.own';
    public const string FEATURE_REFERRAL_OWN_CREATE = 'feature.referral.own.create';
    public const string FEATURE_REFERRAL_OWN_CREATE_LIMIT = 'feature.referral.own.create.limit';
    public const string FEATURE_REFERRAL_OWN_DELETE = 'feature.referral.own.delete';
    public const string FEATURE_REFERRAL_OWN_EDIT = 'feature.referral.own.edit';
    public const string FEATURE_REFERRAL_OWN_ENABLED = 'feature.referral.own.enabled';
    public const string FEATURE_REFERRAL_OTHER = 'feature.referral.other';
    public const string FEATURE_REFERRAL_OTHER_DELETE = 'feature.referral.other.delete';
    public const string FEATURE_REFERRAL_OTHER_EDIT = 'feature.referral.other.edit';

    public const string FEATURE_BLOG = 'feature.blog';
    public const string FEATURE_BLOG_OWN = 'feature.blog.own';
    public const string FEATURE_BLOG_OWN_CREATE = 'feature.blog.own.create';
    public const string FEATURE_BLOG_OWN_CREATE_LIMIT = 'feature.blog.own.create.limit';
    public const string FEATURE_BLOG_OWN_DELETE = 'feature.blog.own.delete';
    public const string FEATURE_BLOG_OWN_EDIT = 'feature.blog.own.edit';
    public const string FEATURE_BLOG_OWN_ENABLED = 'feature.blog.own.enabled';
    public const string FEATURE_BLOG_OTHER = 'feature.blog.other';
    public const string FEATURE_BLOG_OTHER_DELETE = 'feature.blog.other.delete';
    public const string FEATURE_BLOG_OTHER_EDIT = 'feature.blog.other.edit';


    public const string FEATURE_LINKTREE = 'feature.linktree';
    public const string FEATURE_LINKTREE_OWN = 'feature.linktree';

    public const string FEATURE_ACCOUNT = 'feature.account';
    public const string FEATURE_ACCOUNT_OWN = 'feature.account.own';
    public const string FEATURE_ACCOUNT_OWN_CHANGE_USERNAME = 'feature.account.own.change_username';
    public const string FEATURE_ACCOUNT_OWN_CHANGE_EMAIL = 'feature.account.own.change_email';
    public const string FEATURE_ACCOUNT_OWN_CHANGE_PASSWORD = 'feature.account.own.change_password';
    public const string FEATURE_ACCOUNT_OWN_UPLOAD_PROFILE_PICTURE = 'feature.account.own.upload_profile_picture';

}