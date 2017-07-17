<?php

/**
 * Created by PhpStorm.
 * User: tjdal
 * Date: 7/16/2017
 * Time: 4:03 PM
 */
      class EmailSender{
        protected  $mail;
        protected  $admin_email = array("smkelly2000@gmail.com",
                                "gottfried009@gmail.com",
                                "tylerdalbora@gmail.com"
                              );

        public function __construct(){

          date_default_timezone_set('America/New_York');
          require_once '../vendor/autoload.php';

          // CREATE NEW MAIL OBJECT
          $this->mail = new PHPMailer(true);
          $this->mail->Encoding = "8bit";
          $this->mail->Charset = "UTF-8";
          $this->mail->isHTML();
          $this->mail->isSMTP();
          $this->mail->SMTPDebug = 0;
            $this->mail->Debugoutput = 'html';
          $this->mail->SMTPOptions = array (
                    'ssl' => array (
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
          $this->mail->setFrom('ataimaging@yahoo.com');
          $this->mail->Host = gethostbyname('smtp.mail.yahoo.com');
          $this->mail->SMTPAuth = true;
          $this->mail->Username = 'ataimaging@yahoo.com';
          $this->mail->Password = 'Drones123!';
          $this->mail->SMTPSecure = 'tls';
          $this->mail->port = 587;

        }

        public function getAdminEmails(){
          return $this->admin_email;
        }

        public function sendHTMLEmail($to_address_array, $HTMLmsg, $subject, $send_to_admin){
          try{
              // ADD EACH ADDRESS TO THE 'TO' FIELD
              foreach($to_address_array as $address){
                  $this->mail->addAddress($address);
              }

              // SET THE SUBJECT
              $this->mail->Subject = $subject;

              // SET THE BODY OF THE EMAIL
              $this->mail->Body = $HTMLmsg;

              // IF SEND TO ADMIN IS TRUE, ADD THE ADMIN EMAILS TO THE BCC FIELD
              if($send_to_admin){
                  foreach($this->admin_email as $email){
                      $this->mail->AddBCC($email);
                  }
              }

              if(!$this->mail->send()){
                  return $this->mail->ErrorInfo;
              } else {
                  return true;
              }

          } catch(Exception $e){
            return $e->getTraceAsString();
          }
        }

        public function get_mail_object(){
          return $this->mail;
        }
      }

?>
