<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office"> 
<head>
<meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1" name="viewport">
@if( !empty($subject) )
<title>{{ $subject }}</title>
@endif
<meta name="x-apple-disable-message-reformatting">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="telephone=no" name="format-detection">
<!--[if (mso 16)]><style type="text/css">a {text-decoration: none;}</style><![endif]-->
<!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->
<!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG></o:AllowPNG><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
<style>
*{
    box-sizing: border-box;
}
html{
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
body{
    margin: 0;
    padding: 0;
}
a[x-apple-data-detectors]{
    color: inherit !important;
    text-decoration: inherit !important;
}
a{
    color: inherit;
    text-decoration: none;
}
p{
    line-height: inherit;
}
table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
}
#MessageViewBody a{
    color: inherit;
    text-decoration: none;
}
.notification-main-body{
    background-color: #FFFFFF;
    margin: 0;
    padding: 0;
    -webkit-text-size-adjust: none;
    text-size-adjust: none;
    height: auto !important;
}
.notification-main-wrapper{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    background-color: #FFFFFF;
}

.message-main-container{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
}
.message-main-container .message-main-table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    background-color: #f7f8fc;
    border-radius: 4px 4px 0 0;
    color:#333333;
    width: 600px;
}
.message-main-container .message-main-table .message-main-table-td{
    text-align: left;
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    font-weight: 400;
    padding-left: 24px;
    padding-right: 24px;
    vertical-align: top;
    padding-top: 32px;
    padding-bottom: 32px;
    border-top: 0;
    border-right: 0;
    border-bottom: 0;
    border-left: 0;
}
.message-main-container .message-main-table .message-main-table-td .message-main-table-td-table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    word-break: break-word;
}
.message-main-container .message-main-table .message-main-table-td .message-main-table-td-table .message-main-table-td-table-td{
    font-size:15px;
    mso-line-height-alt: 23px;
    color:#333333;
    line-height: 23px;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.message-main-container .message-main-table .message-main-table-td .message-main-table-td-table .message-main-table-td-table-td h1{
    font-size: 24px;
    margin: 0;
    color: #0C0D0E;
    mso-line-height-alt: 36px;
    margin-top: 0;
    margin-bottom: 0;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.message-main-container .message-main-table .message-main-table-td .message-main-table-td-table .message-main-table-td-table-td a{
    text-decoration: none;
    color: #086beb;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.message-main-container .message-main-table .message-main-table-td .message-main-table-td-table .message-main-table-td-table-td br{
    display: none;
}
.message-main-container .message-main-table .message-main-table-td .message-main-table-td-table .message-main-table-td-table-td a span{
    text-decoration: none;
    color: #086beb;
}
.message-main-container .message-main-table .message-main-table-td .message-main-table-td-table .message-main-table-td-table-td p{
    margin-top: 0;
    margin-bottom: 8px;
    font-size:15px;
    mso-line-height-alt: 23px;
    color:#333333;
    line-height: 23px;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.message-main-container .message-main-table .message-main-table-td .message-main-table-td-table .message-main-table-td-table-td p br{
    display: block;
}
.message-main-container .message-main-table .message-main-table-td .message-main-table-td-table .message-main-table-td-table-td:last-child{
    margin-bottom: 0;
}


.button-main-container{
    mso-table-lspace:0;
    mso-table-rspace:0;
}
.button-main-container .button-main-table{
    mso-table-lspace:0;
    mso-table-rspace:0;
    background-color:#f7f8fc;
    border-radius:0;
    color:#000;
    width:600px
}
.button-main-container .button-main-table .button-main-table-td{
    mso-table-lspace:0;
    mso-table-rspace:0;
    font-weight:400;
    text-align:left;
    padding-left:24px;
    padding-right:24px;
    vertical-align:top;
    padding-top:0;
    border-top:0;
    border-right:0;
    border-bottom:0;
    border-left:0;
}
.button-main-container .button-main-table .button-main-table-td .button-main-table-td-spacer{
    height:32px;
    line-height:32px;
    font-size:1px;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.button-main-container .button-main-table .button-main-table-td .button-main-table-td-table-desktop{
    mso-table-lspace:0;
    mso-table-rspace:0;
}
.button-main-container .button-main-table .button-main-table-td .button-main-table-td-table-mobile{
    mso-table-lspace:0;
    mso-table-rspace:0;
    mso-hide:all;
    display:none;
    max-height:0;
    overflow:hidden;
}
.button-main-container .button-main-table .button-main-table-td .button-main-table-td-table-desktop .button-main-table-td-table-td,
.button-main-container .button-main-table .button-main-table-td .button-main-table-td-table-mobile .button-main-table-td-table-td{
    text-align: left;
}
.button-main-container .button-main-table .button-main-table-td .button-main-table-td-table-desktop .button-main-table-td-table-td a{
    text-decoration:none;
    display:inline-block;
    color:#ffffff;
    background-color:#756fe6;
    border-radius:4px;
    width:auto;
    min-width: 280px;
    border-top:0px solid transparent;
    font-weight:700;
    border-right:0px solid transparent;
    border-bottom:0px solid transparent;
    border-left:0px solid transparent;
    padding-top:8px;
    padding-bottom:8px;
    text-align:center;
    mso-border-alt:none;
    word-break:keep-all;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.button-main-container .button-main-table .button-main-table-td .button-main-table-td-table-mobile .button-main-table-td-table-td a{
    text-decoration:none;
    display:block;
    color:#ffffff;
    background-color:#756fe6;
    border-radius:4px;
    width:100%;
    border-top:0px solid transparent;
    font-weight:700;
    border-right:0px solid transparent;
    border-bottom:0px solid transparent;
    border-left:0px solid transparent;
    padding-top:8px;
    padding-bottom:8px;
    text-align:center;
    mso-border-alt:none;
    word-break:keep-all;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.button-main-container .button-main-table .button-main-table-td .button-main-table-td-table-desktop .button-main-table-td-table-td a span,
.button-main-container .button-main-table .button-main-table-td .button-main-table-td-table-mobile .button-main-table-td-table-td a span{
    padding-left:32px;
    padding-right:32px;
    font-size:15px;
    display:inline-block;
    letter-spacing:normal;
    word-break: break-word;
    line-height: 30px;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}

.salutation-main-container{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
}
.salutation-main-container .salutation-main-table{
    mso-table-lspace:0;mso-table-rspace:0;background-color:#f7f8fc;border-radius:0;color:#000;width:600px
}
.salutation-main-container .salutation-main-table .salutation-main-table-td{
    text-align: left;
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    font-weight: 400;
    vertical-align: top;
    padding-top: 0;
    padding-bottom: 0;
    border-top: 0;
    border-right: 0;
    border-bottom: 0;
    border-left: 0;
}
.salutation-main-container .salutation-main-table .salutation-main-table-td .salutation-main-table-td-table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    word-break: break-word;
}
.salutation-main-container .salutation-main-table .salutation-main-table-td .salutation-main-table-td-table .salutation-main-table-td-table-td{
    padding-left: 24px;
    padding-right: 24px
}
.salutation-main-container .salutation-main-table .salutation-main-table-td .salutation-main-table-td-table .salutation-main-table-td-table-td p{
    font-size:15px;
    mso-line-height-alt: 23px;
    color:#333333;
    line-height: 23px;
    margin-top: 0;
    margin-bottom: 0;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.salutation-main-container .salutation-main-table .salutation-main-table-td .salutation-main-table-td-table .salutation-main-table-td-table-td a{
    text-decoration: none;
    color: #086beb;
}

.subcopy-main-container{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
}

.divider-main-container{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
}

.divider-main-container .divider-main-table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    background-color: #f7f8fc;
    border-radius: 0;
    color: #333333;
    width: 600px;
}
.divider-main-container .divider-main-table .divider-main-table-td{
    text-align: left;
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    font-weight: 400;
    vertical-align: top;
    padding-top: 0;
    padding-bottom: 0;
    border-top: 0;
    border-right: 0;
    border-bottom: 0;
    border-left: 0;
}
.divider-main-container .divider-main-table .divider-main-table-td .divider-main-table-td-table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
}
.divider-main-container .divider-main-table .divider-main-table-td .divider-main-table-td-table .divider-main-table-td-table-td{
    /* padding-bottom: 24px; */
    padding-bottom: 0;
    padding-left: 24px;
    padding-right: 24px;
    padding-top: 32px;
}
.divider-main-container .divider-main-table .divider-main-table-td .divider-main-table-td-table .divider-main-table-td-table-td table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
}
.divider-main-container .divider-main-table .divider-main-table-td .divider-main-table-td-table .divider-main-table-td-table-td table td{
    font-size: 1px;
    line-height: 1px;
    border-top:1px solid #e7e7e7;
    color: #f7f8fc;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.subcopy-main-container .subcopy-main-table{
    mso-table-lspace:0;
    mso-table-rspace:0;
    background-color: #f7f8fc;
    border-radius: 0;
    color:#989fa0;
    width: 600px;
}
.subcopy-main-container .subcopy-main-table .subcopy-main-desktop-table-td{
    text-align: left;
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    font-weight: 400;
    vertical-align: top;
    padding-top: 0;
    padding-bottom: 0;
    border-top: 0;
    border-right: 0;
    border-bottom: 0;
    border-left: 0;
    width: 100%;
}
.subcopy-main-container .subcopy-main-table .subcopy-main-desktop-table-td .subcopy-main-desktop-table-td-table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    word-break: break-word;
}
.subcopy-main-container .subcopy-main-table .subcopy-main-desktop-table-td .subcopy-main-desktop-table-td-table .subcopy-main-desktop-table-td-table-td{
    padding-left: 24px;
    padding-right: 24px;
    font-size: 13px;
    mso-line-height-alt: 18px;
    color:#989fa0;
    line-height: 18px;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.subcopy-main-container .subcopy-main-table .subcopy-main-desktop-table-td .subcopy-main-desktop-table-td-table .subcopy-main-desktop-table-td-table-td p{
    margin-top: 0;
    margin-bottom: 4px;
}
.subcopy-main-container .subcopy-main-table .subcopy-main-desktop-table-td .subcopy-main-desktop-table-td-table .subcopy-main-desktop-table-td-table-td a{
    text-decoration: none;
    color: #086beb;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.subcopy-main-container .subcopy-main-table .subcopy-main-desktop-table-td .subcopy-main-desktop-table-td-table .subcopy-main-desktop-table-td-table-td a span{
    text-decoration: none;
    color: #086beb;
}

.footer-main-container-mobile{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    mso-hide: all;
    display: none;
    max-height: 0;
    overflow: hidden;
}
.footer-main-container-mobile .footer-main-mobile-table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    mso-hide: all;
    display: none;
    max-height: 0;
    overflow: hidden;
    background-color: #f7f8fc;
    border-radius: 0;
    color:#333333;
    width: 600px;
}
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-left,
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-right{
    text-align: left;
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    font-weight: 400;
    padding-left: 24px;
    padding-right: 24px;
    vertical-align: top;
    border-top: 0;
    border-right: 0;
    border-bottom: 0;
    border-left: 0;
    width: 100%;
}
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-left .footer-main-mobile-table-td-table,
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-right .footer-main-mobile-table-td-table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    word-break: break-word;
    mso-hide: all;
    display: none;
    max-height: 0;
    overflow: hidden;
}
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-left .footer-main-mobile-table-td-table .footer-main-mobile-table-td-table-td,
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-right .footer-main-mobile-table-td-table .footer-main-mobile-table-td-table-td{
    padding-top: 25px;
    padding-bottom: 25px
}
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-left .footer-main-mobile-table-td-table .footer-main-mobile-table-td-table-td{
    padding-bottom: 0;
}
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-right .footer-main-mobile-table-td-table .footer-main-mobile-table-td-table-td{
    padding-top: 8px;
    text-align: right;
}
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-left .footer-main-mobile-table-td-table .footer-main-mobile-table-td-table-td .footer-main-mobile-table-td-table-td-box{
    font-size: 13px;
    mso-line-height-alt: 23px;
    color:#989fa0;
    line-height: 23px;
    margin: 0;
    text-align: left;
    display: inline-table;
    width: 100%;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-right .footer-main-mobile-table-td-table .footer-main-mobile-table-td-table-td .footer-main-mobile-table-td-table-td-box{
    font-size: 13px;
    mso-line-height-alt: 23px;
    color:#989fa0;
    line-height: 23px;
    margin: 0;
    text-align: right;
    display: inline-block;
    width: 100%;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-left .footer-main-mobile-table-td-table .footer-main-mobile-table-td-table-td .footer-main-mobile-table-td-table-td-box p,
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-right .footer-main-mobile-table-td-table .footer-main-mobile-table-td-table-td .footer-main-mobile-table-td-table-td-box p{
    margin-top: 0;
    margin-bottom: 0;
}
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-left .footer-main-mobile-table-td-table .footer-main-mobile-table-td-table-td .footer-main-mobile-table-td-table-td-box .box-link a,
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-right .footer-main-mobile-table-td-table .footer-main-mobile-table-td-table-td .footer-main-mobile-table-td-table-td-box .box-link a{
    text-decoration: none;
    color: #086beb;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-left .footer-main-mobile-table-td-table .footer-main-mobile-table-td-table-td .footer-main-mobile-table-td-table-td-box .box-link a span,
.footer-main-container-mobile .footer-main-mobile-table .footer-main-mobile-table-td-right .footer-main-mobile-table-td-table .footer-main-mobile-table-td-table-td .footer-main-mobile-table-td-table-td-box .box-link a span{
    color: #086beb;
    text-decoration: none;
}
.footer-main-container-desktop{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
}
.footer-main-container-desktop .footer-main-desktop-table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    background-color: #f7f8fc;
    border-radius: 0;
    color: #000000;
    width: 600px;
}
.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-left {
    text-align: left;
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    font-weight: 400;
    padding-left: 24px;
    vertical-align: top;
    border-top: 0;
    border-right: 0;
    border-bottom: 0;
    border-left: 0;
    width: 50%;
    word-break: break-word;
}
.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-right {
    text-align: left;
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    font-weight: 400;
    padding-right: 24px;
    vertical-align: top;
    border-top: 0;
    border-right: 0;
    border-bottom: 0;
    border-left: 0;
    width: 50%;
}

.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-left .footer-main-desktop-table-td-table,
.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-right .footer-main-desktop-table-td-table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    word-break: break-word;
}
.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-left .footer-main-desktop-table-td-table .footer-main-desktop-table-td-table-td,
.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-right .footer-main-desktop-table-td-table .footer-main-desktop-table-td-table-td{
    padding-top: 25px;
    padding-bottom: 25px;
}
.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-right .footer-main-desktop-table-td-table .footer-main-desktop-table-td-table-td{
    text-align: right;
}
.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-left .footer-main-desktop-table-td-table .footer-main-desktop-table-td-table-td .footer-main-desktop-table-td-table-td-box{
    font-size: 13px;
    mso-line-height-alt: 23px;
    color:#989fa0;
    line-height: 23px;
    margin: 0;
    text-align: left;
    display: inline-block;
    width: 100%;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-right .footer-main-desktop-table-td-table .footer-main-desktop-table-td-table-td .footer-main-desktop-table-td-table-td-box{
    font-size: 13px;
    mso-line-height-alt: 23px;
    color:#989fa0;
    line-height: 23px;
    margin: 0;
    text-align: right;
    display: inline-table;
    width: 100%;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-left .footer-main-desktop-table-td-table .footer-main-desktop-table-td-table-td .footer-main-desktop-table-td-table-td-box .box-link a,
.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-right .footer-main-desktop-table-td-table .footer-main-desktop-table-td-table-td .footer-main-desktop-table-td-table-td-box .box-link a{
    text-decoration: none;
    color: #086beb;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-left .footer-main-desktop-table-td-table .footer-main-desktop-table-td-table-td .footer-main-desktop-table-td-table-td-box .box-link a span,
.footer-main-container-desktop .footer-main-desktop-table .footer-main-desktop-table-td-right .footer-main-desktop-table-td-table .footer-main-desktop-table-td-table-td .footer-main-desktop-table-td-table-td-box .box-link a span{
    color: #086beb;
    text-decoration: none;
}

.footer-subscription-container{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
}
.footer-subscription-container .footer-subscription-table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    background-color: #f7f8fc;
    border-radius: 0;
    color: #000000;
    width: 600px;
}
.footer-subscription-container .footer-subscription-table .footer-subscription-table-td{
    text-align: left;
    mso-table-lspace:0;
    mso-table-rspace:0;
    font-weight: 400;
    padding-left: 24px;
    padding-right: 24px;
    vertical-align: top;
    padding-top: 20px;
    padding-bottom: 0;
    border-top: 0;
    border-right: 0;
    border-bottom: 0;
    border-left: 0;
    word-break: break-word;
    text-align:left;
    font-size: 13px;
    mso-line-height-alt: 23px;
    color: #989fa0;
    line-height: 23px;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.footer-subscription-container .footer-subscription-table .footer-subscription-table-td p{
    margin-top:0;
    margin-bottom: 4px;
}
.footer-subscription-container .footer-subscription-table .footer-subscription-table-td a{
    font-size: 13px;
    mso-line-height-alt: 23px;
    color: #086beb;
    line-height: 23px;
    margin:0;
    text-align:center;
    text-decoration: underline;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.footer-subscription-container .footer-subscription-table .footer-subscription-table-td a span{
    color: #086beb;
    font-size: 13px;
    line-height: 23px;
    text-decoration: underline;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.footer-spacer-container{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
}
.footer-spacer-container .footer-spacer-table{
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    background-color: #f7f8fc;
    color: #333333;
    border-radius:0 0 4px 4px;
    width: 600px;
}
.footer-spacer-container .footer-spacer-table .footer-spacer-table-td{
    text-align: left;
    mso-table-lspace: 0;
    mso-table-rspace: 0;
    font-weight: 400;
    vertical-align: top;
    padding-top: 0;
    padding-bottom: 0;
    border-top: 0;
    border-right: 0;
    border-bottom: 0;
    border-left: 0;
}
.footer-spacer-container .footer-spacer-table .footer-spacer-table-td .footer-spacer-table-td-box{
    height: 60px;
    line-height: 60px;
    font-size: 1px;
    width: 100%;
    font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
}
.button-main-table-td-table-mobile,
.footer-main-container-mobile,
.footer-main-container-mobile table{
    mso-hide: all;
    display: none;
    max-height: 0;
    overflow: hidden;
}
@media (max-width:385px){
    .footer-main-mobile-table-td-right .footer-main-mobile-table-td-table-td-box{
        text-align: left !important;
    }
}
@media (min-width:385px){
    .footer-main-mobile-table-td-table-td{
        padding-top: 25px !important;
    }
}
@media (min-width:385px){
    .footer-main-mobile-table-td-left{
        width: auto !important;
        display: table-cell!important;
    }
    .footer-main-mobile-table-td-right{
        width: auto !important;
        display: table-cell!important;
    }
    .footer-main-mobile-table-td-right .footer-main-mobile-table-td-table-td span{
        text-align: right !important;
    }
}
@media (min-width:545px){
    .footer-main-mobile-table-td-table-td-box p{
        display: inline-block !important;
    }
}
@media (max-width:620px){
    .table-stack{
        width: 100% !important;
    }
    .table-stack .column{
        width: 100%;
        display: block;
    }
    .button-main-table-td-table-desktop,
    .footer-main-container-desktop{
        display: none;
        min-height: 0;
        max-height: 0;
        max-width: 0;
        overflow: hidden;
        font-size: 0;
    }
    .button-main-table-td-table-mobile,
    .footer-main-container-mobile,
    .footer-main-container-mobile table{
        display: table !important;
        max-height: none !important;
    }
}
</style>
</head>
<body class="notification-main-body">
    <table class="notification-main-wrapper" width="100%" border="0" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td>
                    <table class="message-main-container" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                        <tbody>
                            <tr>
                                <td>
                                    <table class="message-main-table table-stack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                                        <tbody>
                                            <tr>
                                                <td class="message-main-table-td column" width="100%">
                                                    <table class="message-main-table-td-table" width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="message-main-table-td-table-td">
                                                                    @foreach ($introLines as $line)
                                                                        {!! $line !!}
                                                                    @endforeach
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @if( !empty($button) )
                        <table class="button-main-container" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                            <tbody>
                                <tr>
                                    <td>
                                        <table class="button-main-table table-stack" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" width="600">
                                            <tbody>
                                                <tr>
                                                    <td class="button-main-table-td column column-1" width="100%">
                                                        <table class="button-main-table-td-table-desktop" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                                                            <tr>
                                                                <td class="button-main-table-td-table-td">
                                                                    <div align="left">
                                                                        <!--[if mso]>
                                                                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $button['url'] }}" style="height:46px;width:280px;v-text-anchor:middle;" arcsize="8%" stroke="false" fillcolor="#756fe6">
                                                                            <v:textbox inset="0px,0px,0px,0px">
                                                                            <center style="color:#ffffff; font-family:Arial, sans-serif; font-size:15px">
                                                                        <![endif]-->
                                                                        <a href="{{ $button['url'] }}" target="_blank">
                                                                            <span>{{ $button['text'] }}</span>
                                                                        </a>
                                                                        <!--[if mso]></center></v:textbox></v:roundrect><![endif]-->
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table class="button-main-table-td-table-mobile" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                                                            <tr>
                                                                <td class="button-main-table-td-table-td">
                                                                    <div align="left">
                                                                        <!--[if mso]>
                                                                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $button['url'] }}" style="height:46px;width:552px;v-text-anchor:middle;" arcsize="8%" stroke="false" fillcolor="#756fe6">
                                                                            <v:textbox inset="0px,0px,0px,0px">
                                                                            <center style="color:#ffffff; font-family:Arial, sans-serif; font-size:15px">
                                                                        <![endif]-->
                                                                        <a href="{{ $button['url'] }}" target="_blank">
                                                                            <span>{{ $button['text'] }}</span>
                                                                        </a>
                                                                        <!--[if mso]></center></v:textbox></v:roundrect><![endif]-->
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <div class="button-main-table-td-spacer">&nbsp;</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                    <table class="salutation-main-container" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                        <tbody>
                            <tr>
                                <td>
                                    <table class="salutation-main-table table-stack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                                        <tbody>
                                            <tr>
                                                <td class="salutation-main-table-td column" width="100%">
                                                    <table class="salutation-main-table-td-table" width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="salutation-main-table-td-table-td">
                                                                    <p>С уважением,</p>
                                                                    <p>Организаторы конкурса <a href="{{ url(route('home')) }}" target="_blank"><span>«Проектная активация 2.0»</span></a></p>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="divider-main-container" align="center" width="100%" border="0" cellpadding="0" cellspacing="0"role="presentation">
                        <tbody>
                            <tr>
                                <td>
                                    <table class="divider-main-table table-stack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                                        <tbody>
                                            <tr>
                                                <td class="divider-main-table-td column" width="100%">
                                                    <table class="divider-main-table-td-table" width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="divider-main-table-td-table-td" align="center">
                                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <span>&nbsp;</span>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="footer-main-container-desktop" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                        <tbody>
                            <tr>
                                <td>
                                    <table class="footer-main-desktop-table table-stack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                                        <tbody>
                                            <tr>
                                                <td class="footer-main-desktop-table-td-left column" width="50%">
                                                    <table class="footer-main-desktop-table-td-table" width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="footer-main-desktop-table-td-table-td">
                                                                    <span class="footer-main-desktop-table-td-table-td-box">
                                                                        <span class="box-name">
                                                                            <strong>Организаторы:&nbsp;</strong>
                                                                        </span>
                                                                        <span class="box-link">
                                                                            <a href="mailto:activation@zdrav.mos.ru" target="_blank" title="activation@zdrav.mos.ru">
                                                                                <span>activation@zdrav.mos.ru</span>
                                                                            </a>
                                                                        </span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td class="footer-main-desktop-table-td-right column" width="50%">
                                                    <table class="footer-main-desktop-table-td-table" width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="footer-main-desktop-table-td-table-td">
                                                                    <span class="footer-main-desktop-table-td-table-td-box">
                                                                        <span class="box-name">
                                                                            <strong>Новости:&nbsp;</strong>
                                                                        </span>
                                                                        <span class="box-link">
                                                                            <a href="https://t.me/zdravyemysli" target="_blank">
                                                                                <span>https://t.me/zdravyemysli</span>
                                                                            </a>
                                                                        </span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="footer-main-container-mobile" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                        <tbody>
                            <tr>
                                <td>
                                    <table class="footer-main-mobile-table table-stack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                                        <tbody>
                                            <tr>
                                                <td class="footer-main-mobile-table-td-left column" width="50%">
                                                    <table class="footer-main-mobile-table-td-table" width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="footer-main-mobile-table-td-table-td">
                                                                    <span class="footer-main-mobile-table-td-table-td-box">
                                                                        <p class="box-name">
                                                                            <strong>Организаторы:&nbsp;</strong>
                                                                        </p>
                                                                        <p class="box-link">
                                                                            <a href="mailto:activation@zdrav.mos.ru" target="_blank" title="activation@zdrav.mos.ru">
                                                                                <span>activation@zdrav.mos.ru</span>
                                                                            </a>
                                                                        </p>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td class="footer-main-mobile-table-td-right column" width="50%">
                                                    <table class="footer-main-mobile-table-td-table" width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="footer-main-mobile-table-td-table-td">
                                                                    <span class="footer-main-mobile-table-td-table-td-box">
                                                                        <p class="box-name">
                                                                            <strong>Новости:&nbsp;</strong>
                                                                        </p>
                                                                        <p class="box-link">
                                                                            <a href="https://t.me/zdravyemysli" target="_blank">
                                                                                <span>https://t.me/zdravyemysli</span>
                                                                            </a>
                                                                        </p>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @if( !empty($button) )
                        <table class="subcopy-main-container" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                            <tbody>
                                <tr>
                                    <td>
                                        <table class="subcopy-main-table table-stack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                                            <tbody>
                                                <tr>
                                                    <td class="subcopy-main-desktop-table-td column" width="100%">
                                                        <table class="subcopy-main-desktop-table-td-table" width="100%" border="0" cellpadding="0" cellspacing="0">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="subcopy-main-desktop-table-td-table-td">
                                                                        @lang('Если кнопка «:actionText» по каким-либо причинам не работает, скопируйте и вставьте следующую ссылку в адресную строку браузера:', ['actionText' => $button['text']]) <div><a href="{{ $button['url'] }}"><span>{{ $button['url'] }}</span></a></div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                    <table class="footer-subscription-container" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                        <tbody>
                            <tr>
                                <td>
                                    <table class="footer-subscription-table table-stack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                                        <tbody>
                                            <tr>
                                                <td class="footer-subscription-table-td column" width="100%">
                                                    <p>Нажмите, чтобы посмотреть <a href="{{ url(route('notification.show.index', $notification_id)) }}" target="_blank"><span>веб-версию</span></a> письма.</p>
                                                    <p>Если вы не хотите получать эту рассылку, нажмите <a href="{{ url(route('notification.show.user.unsubscribe', [$notification_id, $notifiable->slug])) }}" target="_blank"><span>здесь</span></a>, чтобы отписаться.</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="footer-spacer-container" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                        <tbody>
                            <tr>
                                <td>
                                    <table class="footer-spacer-table table-stack" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                                        <tbody>
                                            <tr>
                                                <td class="footer-spacer-table-td column" width="100%">
                                                    <div class="footer-spacer-table-td-box">&nbsp;</d>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>