<template>
    <div class="content-wrapper">
        <app-breadcrumb :page-title="pageTitle" :icon="pageIcon"/>
        <app-tab :tabs="tabs" :icon="pageIcon"/>
    </div>
</template>

<script>
    export default {
        name: "SettingsLayout",
        props: {
            permissions: {}
        },
        data() {
            return {
                pageTitle: 'Settings',
                pageIcon: 'settings',
                tabs: [
                    {
                        "name": this.$t('general'),
                        "title": this.$t('general'),
                        "component": "general-setting",
                        "permission": this.permissions.general,
                    },
                    /*{
                        "name": this.$t('email_setup'),
                        "title": this.$t('email_setup'),
                        "component": "email-setting",
                        "permission": this.permissions.email_settings,
                    },
                    {
                        "name": this.$t('sms_setup'),
                        "title": this.$t('sms_setup'),
                        "component": "sms-setting",
                        "permission": this.permissions.sms_settings,
                    },
                    {
                        "name": this.$t('google_re_captcha'),
                        "title": this.$t('google_re_captcha'),
                        "component": "google-re-captcha-setting",
                        "permission": this.permissions.recaptcha_settings,
                    },
                    {
                        "name": this.$t('payment_methods'),
                        "title": this.$t('payment_methods'),
                        "component": "payment-method",
                        "permission": this.permissions.payment_method,
                        "headerButton": {
                            "label": this.$t('add_payment_method'),
                            "class": 'btn btn-primary'
                        }
                    },
                    {
                        "name": this.$t('notification'),
                        "title": this.$t('notification'),
                        "component": "notification-settings",
                        "permission": this.permissions.notification,
                    },
                    {
                        headerButton: {
                            label: this.$t('add_custom_table'),
                            class: 'btn btn-primary'
                        },
                        'name': this.$t('custom_table'),
                        'title': this.$t('custom_table'),
                        'component': 'app-custom-tables',
                        'permission': true,
                    },
                    {
                        "name": this.$t('storage_configuration'),
                        "title": this.$t('storage_configuration'),
                        "component": "storage-setting",
                        "permission": true,
                    },*/
                ]
            }
        },
    }
</script>
