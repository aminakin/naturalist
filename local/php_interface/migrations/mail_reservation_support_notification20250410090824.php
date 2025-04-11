<?php

namespace Sprint\Migration;


class mail_reservation_support_notification20250410090824 extends Version
{
    protected $author = "admin";

    protected $description = "";

    protected $moduleVersion = "4.12.6";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        $arrayMessages = $helper->Event()->getEventMessages('USER_RESERVATION');
        /**
         * @var int $key
         * @var array $message
         */
        foreach ($arrayMessages as $key => $message) {
            $message['CC'] = '';
            $message['EMAIL_TO'] = '#EMAIL#';
            $helper->Event()->updateEventMessageById($message['ID'], $message);
        }

            $helper->Event()->addEventMessage('USER_RESERVATION', array (
  'LID' =>
  array (
    0 => 's1',
  ),
  'ACTIVE' => 'Y',
  'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
  'EMAIL_TO' => 'support@naturalist.travel ',
  'SUBJECT' => '#SITE_NAME#: Бронирование подтверждено',
  'MESSAGE' => '<!--[if (mso 16)]>
    <style type="text/css">
    a {text-decoration: none;}
    </style>
    <![endif]--><!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--><!--[if gte mso 9]>
<xml>
    <o:OfficeDocumentSettings>
    <o:AllowPNG></o:AllowPNG>
    <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
</xml>
<![endif]--> <style type="text/css">
    .rollover:hover .rollover-first {
      max-height: 0px !important;
      display: none !important;
    }

    .rollover:hover .rollover-second {
      max-height: none !important;
      display: block !important;
    }

    .rollover span {
      font-size: 0px;
    }

    u+.body img~div div {
      display: none;
    }

    #outlook a {
      padding: 0;
    }

    span.MsoHyperlink,
    span.MsoHyperlinkFollowed {
      color: inherit;
      mso-style-priority: 99;
    }

    a.es-button {
      mso-style-priority: 100 !important;
      text-decoration: none !important;
    }

    a[x-apple-data-detectors] {
      color: inherit !important;
      text-decoration: none !important;
      font-size: inherit !important;
      font-family: inherit !important;
      font-weight: inherit !important;
      line-height: inherit !important;
    }

    .es-desk-hidden {
      display: none;
      float: left;
      overflow: hidden;
      width: 0;
      max-height: 0;
      line-height: 0;
      mso-hide: all;
    }

    .es-button-border:hover>a.es-button {
      color: #ffffff !important;
    }

    @media only screen and (max-width:600px) {
      .es-mobile-hidden {
        display: none !important
      }

      .es-desk-hidden {
        width: auto !important;
        overflow: visible !important;
        float: none !important;
        max-height: inherit !important;
        line-height: inherit !important
      }

      tr.es-desk-hidden {
        display: table-row !important
      }

      table.es-desk-hidden {
        display: table !important
      }

      td.es-desk-menu-hidden {
        display: table-cell !important
      }
    }

    @media screen and (max-width:384px) {
      .mail-message-content {
        width: 414px !important
      }
    }
  </style>
<div dir="ltr" class="es-wrapper-color" lang="RU" style="background-color:#F6F6F6">
	 <!--[if gte mso 9]>
			<v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
				<v:fill type="tile" color="#f6f6f6"></v:fill>
			</v:background>
		<![endif]-->
	<table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6">
	<tbody>
	<tr class="gmail-fix" height="0">
		<td style="padding:0;Margin:0">
			<table cellpadding="0" cellspacing="0" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:595px">
			<tbody>
			<tr>
				<td cellpadding="0" cellspacing="0" border="0" height="0" style="padding:0;Margin:0;line-height:1px;min-width:595px">
 <img src="/img/pdf/logo.png" height="1" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none;max-height:0px;min-height:0px;min-width:595px;width:595px" alt="">
				</td>
			</tr>
			</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top" style="padding:0;Margin:0">
			<table class="es-header" cellspacing="0" cellpadding="0" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important;background-color:transparent;background-repeat:repeat;background-position:center top">
			<tbody>
			<tr>
				<td align="center" style="padding:0;Margin:0">
					<table class="es-header-body" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:595px">
					<tbody>
					<tr>
						<td align="left" style="padding:0;Margin:0">
							<table class="es-right" cellspacing="0" cellpadding="0" align="right" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
							<tbody>
							<tr>
								<td class="es-m-p0r" valign="top" align="center" style="padding:0;Margin:0;width:595px">
									<table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
									<tbody>
									<tr>
										<td align="center" style="Margin:0;padding-top:15px;padding-right:30px;padding-bottom:25px;padding-left:30px;font-size:0">
 <img width="535" src="/img/pdf/logo.png" alt="" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none">
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
			<table class="es-content" cellspacing="0" cellpadding="0" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
			<tbody>
			<tr>
				<td align="center" style="padding:0;Margin:0">
					<table class="es-content-body" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:595px">
					<tbody>
					<tr>
						<td align="left" background="/img/pdf/background1.jpg" style="padding:0;Margin:0;background-image:url(/img/pdf/background1.jpg);background-repeat:no-repeat;background-position:left top">
							<table width="100%" cellspacing="0" cellpadding="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
							<tbody>
							<tr>
								<td valign="top" align="center" style="padding:0;Margin:0;width:595px;height:252px">
									<table width="100%" cellspacing="0" cellpadding="0" background="/img/pdf/background1.jpg" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-image:url(/img/pdf/background1.jpg);background-repeat:no-repeat;background-position:left top" role="presentation">
									<tbody>
									<tr>
									</tr>
									</tbody>
									</table>
 <br>
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
			<table class="es-content" cellspacing="0" cellpadding="0" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
			<tbody>
			<tr>
				<td align="center" bgcolor="transparent" style="padding:0;Margin:0">
					<table class="es-content-body" cellpadding="0" cellspacing="0" bgcolor="#ffffff" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:595px">
					<tbody>
					<tr>
						<td align="left" style="padding:0;Margin:0">
							<table cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
							<tbody>
							<tr>
								<td align="left" style="padding:0;Margin:0;width:595px">
									<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
									<tbody>
									<tr>
										<td align="left" style="Margin:0;padding-top:30px;padding-right:50px;padding-bottom:20px;padding-left:50px">
											<p align="center" style="Margin:0;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:21px;letter-spacing:0;color:#333333;font-size:14px">
												 Привет #NAME#!<br>
												 #SUPPORT_NOTIFICATION#<br>
												 Ура, ваша поездка уже не за горами!<br>
												 Перед отъездом не забудьте проверить самое важное: погоду, удобную одежду и, конечно, ваучер на бронирование (файл во вложении).<br>
 <br>
												 Если нужна помощь или возникнут вопросы, обращайся в нашу службу заботы – мы всегда рады помочь!
											</p>
											<p class="line" style="Margin:0;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:21px;letter-spacing:0;color:#333333;font-size:14px;background-color:black;height:1px;margin-top:30px">
											</p>
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
			<table class="es-content" cellspacing="0" cellpadding="0" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
			<tbody>
			<tr>
				<td align="center" bgcolor="transparent" style="padding:0;Margin:0">
					<table class="es-content-body" cellpadding="0" cellspacing="0" bgcolor="#ffffff" align="center" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:595px">
					<tbody>
					<tr>
						<td align="left" style="padding:0;Margin:0;padding-right:50px;padding-left:50px;padding-bottom:30px">
							 <!--[if mso]><table style="width:495px" cellpadding="0" cellspacing="0"><tr><td style="width:212px" valign="top"><![endif]-->
							<table cellpadding="0" cellspacing="0" class="es-left" align="left" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
							<tbody>
							<tr>
								<td align="left" style="padding:0;Margin:0;width:212px">
									<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
									<tbody>
									<tr>
										<td align="left" class="es-text-1508" style="padding:0;Margin:0">
											<p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:24px;letter-spacing:0;color:#333333;font-size:16px">
 <strong>Социальные сети</strong>
											</p>
										</td>
									</tr>
									<tr>
										<td align="left" style="padding:0;Margin:0;padding-top:30px">
											<table style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%" role="presentation">
											<tbody>
											<tr>
												<td style="padding:0;Margin:0">
 <a href="https://t.me/naturalist_travel" style="mso-line-height-rule:exactly;text-decoration:none;color:#333333;font-size:10px"><img src="/img/pdf/telegram.jpg" height="15"></a>
												</td>
												<td style="padding:0;Margin:0">
 <a href="https://www.instagram.com/naturalist.travel?igsh=MzRlODBiNWFlZA==" style="mso-line-height-rule:exactly;text-decoration:none;color:#333333;font-size:10px"><img src="/img/pdf/insta.jpg" height="15"></a>
												</td>
											</tr>
											<tr>
												<td style="padding:0;Margin:0;padding-top:15px">
 <a href="https://dzen.ru/id/630fa80dd0c60d4cfe6c4f58" style="mso-line-height-rule:exactly;text-decoration:none;color:#333333;font-size:10px"><img src="/img/pdf/dzen.jpg" height="15"></a>
												</td>
												<td style="padding:0;Margin:0;padding-top:15px">
 <a href="https://vk.com/naturalist_travel" style="mso-line-height-rule:exactly;text-decoration:none;color:#333333;font-size:10px"><img src="/img/pdf/vk.jpg" height="15"></a>
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
							 <!--[if mso]></td><td style="width:60px"></td><td style="width:223px" valign="top"><![endif]-->
							<table cellpadding="0" cellspacing="0" class="es-right" align="right" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
							<tbody>
							<tr>
								<td align="left" style="padding:0;Margin:0;width:223px">
									<table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
									<tbody>
									<tr>
										<td align="left" class="es-text-1597" style="padding:0;Margin:0">
											<p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:24px;letter-spacing:0;color:#333333;font-size:16px">
 <strong>Связаться с нами</strong>
											</p>
										</td>
									</tr>
									<tr>
										<td align="left" class="es-text-7249" style="padding:0;Margin:0;padding-top:20px">
 <a href="mailto:support@naturalist.travel" style="mso-line-height-rule:exactly;text-decoration:underline;color:#333333;font-size:10px;font-family:verdana, geneva, sans-serif;line-height:15px" class="es-text-mobile-size-10">​support@naturalist.travel</a>
											<p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:15px;letter-spacing:0;color:#333333;font-size:10px" class="es-text-mobile-size-10">
												 Россия, г. Мо​<span class="es-text-mobile-size-10">​</span>сква, Астраханский пер., 5с3
											</p>
 <a href="tel:+74993227822" style="mso-line-height-rule:exactly;text-decoration:underline;color:#333333;font-size:10px;font-family:verdana, geneva, sans-serif;line-height:15px" class="es-text-mobile-size-10">+7 (499) 322-78-22</a>
										</td>
									</tr>
									</tbody>
									</table>
								</td>
							</tr>
							</tbody>
							</table>
							 <!--[if mso]></td></tr></table><![endif]-->
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
</div>
 <br>',
  'BODY_TYPE' => 'html',
  'BCC' => '',
  'REPLY_TO' => '',
  'CC' => '',
  'IN_REPLY_TO' => '',
  'PRIORITY' => '',
  'FIELD1_NAME' => '',
  'FIELD1_VALUE' => '',
  'FIELD2_NAME' => '',
  'FIELD2_VALUE' => '',
  'SITE_TEMPLATE_ID' => '',
  'ADDITIONAL_FIELD' =>
  array (
  ),
  'LANGUAGE_ID' => 'ru',
  'EVENT_TYPE' => '[ USER_RESERVATION ] Бронирование',
));
        }
}
