<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once(dirname(__FILE__).'/PHPMailer/Exception.php');
include_once(dirname(__FILE__).'/PHPMailer/PHPMailer.php');
include_once(dirname(__FILE__).'/PHPMailer/SMTP.php');

class Mail{
    const TOPIC = [
        'TRACK'=>[
            'subject' => 'Idolmart แจ้งเลขแทร็กการจัดส่ง ออเดอร์ %1$s',
            'body' => '<br><br>&emsp;ในขณะนี้ทาง Idolmart ได้ทำการนำสินค้าออเดอร์ <b>%1$s</b> จัดส่งทางไปรษณีย์เป็นที่เรียบร้อยแล้ว โดยท่านสามารถตรวจสอบสถานะการจัดส่งผ่านเลขแทร็ก <b>%2$s</b> ได้ทาง <a href="https://www.parcelmonitor.com/">parcelmonitor.com</a> 
และในกรณีที่สินค้ามีปัญหา ท่านสามารถแจ้งร้องเรียนได้ที่  Email: support@idolm.art หรือส่งข้อความมาที่เพจได้โดยตรง',
        ]
    ];

    public static function sendTrack($desMail, $desName, $order, $track){
        $mail = new PHPMailer(true);
        try {

            $mail->Host = 'mail.idolm.art';
            $mail->SMTPAuth = true;
            $mail->Username = '';
            $mail->Password = '';
            $mail->SMTPOptions = array(
                    'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->Port = 587;

            $mail->setFrom('support@idolm.art', 'Idolmart');
            $mail->addAddress($desMail, $desName);
            $mail->addBCC('bcc@idolm.art');

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Idolmart แจ้งเลขแทร็กการจัดส่ง ออเดอร์ '.$order;
            $mail->Body    = '
<link href="https://fonts.googleapis.com/css?family=Kanit:300" rel="stylesheet">
<style type="text/css">
    #wrapper {
      text-align: center;
    }
    #logodiv {
      display: inline-block;
    }
    h1{
        font-size: 20px;
    }
    p{
        text-indent: 30px;
    }
</style>
<div style="background-color: #fafbfc; font-family:Kanit,sans-serif; box-shadow: 0 1px 2px rgba(0,0,0,.3);">
    <div id="wrapper" style="padding-top: 20px;">    
        <div id="logodiv"><img alt="Idolmart" src="https://idolm.art/assets/img/nav/logo_gray_209.png""></div>
        <div style="margin-top: 10px; width: 100%; height: 3px; background-color: #dc3545;"></div>
    </div>
    <div style="margin-left: 25px; margin-right: 25px; padding-bottom: 10px;">
        <h1>เรียน คุณ '.$desName.'</h1>
        <p>ในขณะนี้ทาง Idolmart ได้ทำการนำสินค้าออเดอร์ <b>'.$order.'</b> จัดส่งทางไปรษณีย์เป็นที่เรียบร้อยแล้ว โดยท่านสามารถตรวจสอบสถานะการจัดส่งผ่านเลขแทร็ก <b>'.$track.'</b> ได้ทาง <a href="https://www.parcelmonitor.com/">parcelmonitor.com</a> 
และในกรณีที่สินค้ามีปัญหา ท่านสามารถแจ้งร้องเรียนได้ที่  Email: support@idolm.art หรือส่งข้อความมาที่เพจได้โดยตรง</p>
        <p>ขอบคุณเป็นอย่างสูงที่ใช้บริการ<p>
        <p><b>Idolmart support Team</b></p><p style="margin-top: -20px;">&nbsp;&lt;support@idolm.art&gt;</p>
    </div>
</div>
            ';
            $mail->AltBody = '';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }

    public static function sendCode($desMail, $desName, $codeList, $timeStr){
        $mail = new PHPMailer(true);
        try {

            //$mail->SMTPDebug = 3; //Full debug output
            $mail->Host = 'mail.idolm.art';
            $mail->SMTPAuth = true;
            $mail->Username = '';
            $mail->Password = '';
            $mail->SMTPOptions = array(
                    'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->Port = 587;

            $mail->setFrom('support@idolm.art', 'Idolmart');
            $mail->addAddress($desMail, $desName);
            $mail->addBCC('bcc@idolm.art');

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Idolmart แจ้งโค้ดที่ซื้อเมื่อ '.$timeStr;

            $imgTag = '';
            foreach ($codeList as $file) {
                $image = base64_encode(file_get_contents($file));
                $imgTag.= '<li><img alt=" โหลดรูปไม่สำเร็จ" class="img_code" src="data: '.mime_content_type($file).';base64,'.$image.'"></li>';
            }

            $mail->Body    = '
<link href="https://fonts.googleapis.com/css?family=Kanit:300" rel="stylesheet">
<style type="text/css">
    #wrapper {
      text-align: center;
    }
    #logodiv {
      display: inline-block;
    }
    h1{
        font-size: 20px;
    }
    p{
        text-indent: 30px;
    }
    .img_code{
        max-height: 50px;
        max-width: 100%;
    }
</style>
<div style="background-color: #fafbfc; font-family:Kanit,sans-serif; box-shadow: 0 1px 2px rgba(0,0,0,.3);">
    <div id="wrapper" style="padding-top: 20px;">    
        <div id="logodiv"><img alt="Idolmart" src="https://idolm.art/assets/img/nav/logo_gray_209.png""></div>
        <div style="margin-top: 10px; width: 100%; height: 3px; background-color: #dc3545;"></div>
    </div>
    <div style="margin-left: 25px; margin-right: 25px; padding-bottom: 10px;">
        <h1>เรียน คุณ '.$desName.'</h1>
        <p>ในขณะนี้ทาง Idolmart ได้ทำการอนุมัติการซื้อโค้ดแล้ว นี่คือโค้ดที่คุณซื้อเมื่อ <b>'.$timeStr.'</b> สามารถดูได้ด้านล่าง หรือหน้าเว็บ <a href="https://vote.idolm.art/">vote.idolm.art</a> หากโหลดรูปไม่สำเร็จ</p>
        <p>ในส่วนของการโหวต ท่านสามารถนำโค้ดไปโหวตเองได้ที่นี่ &gt;&gt;
            &nbsp;<a href="https://akb48-sousenkyo.jp/akb/vote?id=325">เฌอปราง</a>
            &nbsp;<a href="https://akb48-sousenkyo.jp/akb/vote?id=326">อิซึรินะ</a>
            &nbsp;<a href="https://akb48-sousenkyo.jp/akb/vote?id=327">จ๋า</a>
            &nbsp;<a href="https://akb48-sousenkyo.jp/akb/vote?id=328">เจนนิษฐ์</a>
            &nbsp;<a href="https://akb48-sousenkyo.jp/akb/vote?id=329">ไข่มุก</a>
            &nbsp;<a href="https://akb48-sousenkyo.jp/akb/vote?id=330">เคท</a>
            &nbsp;<a href="https://akb48-sousenkyo.jp/akb/vote?id=331">มิโอริ</a>
            &nbsp;<a href="https://akb48-sousenkyo.jp/akb/vote?id=332">มิวสิค</a>
            &nbsp;<a href="https://akb48-sousenkyo.jp/akb/vote?id=333">ซัทจัง</a>
            &nbsp;<a href="https://akb48-sousenkyo.jp/akb/vote?id=334">นิ้ง</a>
        </p>
        <p>
            <ol>
                '.$imgTag.'
            </ol>
        </p>
        <p>ขอบคุณเป็นอย่างสูงที่ใช้บริการ<p>
        <p><b>Idolmart support Team</b></p><p style="margin-top: -20px;">&nbsp;&lt;support@idolm.art&gt;</p>
    </div>
</div>
            ';
            $mail->AltBody = '';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }
}

?>