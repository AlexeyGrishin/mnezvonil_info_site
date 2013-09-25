<?php
include_once dirname(__FILE__)."/../../Site.php";
include_once dirname(__FILE__)."/../../db/Connect.php";
include_once dirname(__FILE__)."/../../db/MailDB.php";
include_once dirname(__FILE__)."/../../db/DBHelper.php";
include_once dirname(__FILE__)."/../../services/libs/KLogger.php";

$logger = new KLogger("mailer.txt", KLogger::INFO);
$db = Site::connectDB("mailer");
$helper = new DBHelper($db);
$mails = $helper->get_list_objects("mail_notifications", "MailDB", "sent = 0");
foreach ($mails as $mail) {
    try {
      print_r($mail);
      mail(Site::$mail_real, $mail->subject, $mail->body);
      $helper->mark("mail_notifications", "sent", "1", "id = ".$mail->id);
    }
    catch (Exception $e) {
        $logger->logError($e);
    }
}