<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Method Name: send_notification
 * Purpose: To send notifications ( push and email ) to user.
 * params:
 *      input: user_id, type, android_user_array, ios_user_array, email_user_array
 *      output: -
 */
if ( ! function_exists('send_notification'))
{
  function send_notification( $user_id, $type, $android_user_array, $ios_user_array, $email_user_array, $tables = "", $amount_payable = "", $text_message = "" )
  {
    $CI = &get_instance();
    $CI->load->model( 'webservices/push_notification_model', 'push_notification_model', TRUE );

    if( $text_message != "" )
    {
      $notification_type = constant( $type.'_TYPE' );
      $message = $text_message;
      $subject = 'Notification';
    }
    else
    {
      $notification_type = constant( $type.'_TYPE' );
      $message = constant( $type.'_MESSAGE' );
      $subject = constant( $type.'_SUBJECT');
    }

    // Replace variables in message
    if( !empty( $tables ) )
      $message = str_replace("{tables}", implode(", ", $tables), $message);

    if( !empty( $amount_payable ) )
      $message = str_replace("{amount}", $amount_payable, $message);

    // For android devices
    if( $android_user_array )
    {
      $google_api_key = ( ENVIRONMENT == 'development' ) ? DEV_FIREBASE_API_KEY : LIVE_FIREBASE_API_KEY;

      // Call for android notification
      $url = 'https://fcm.googleapis.com/fcm/send';
      $headers = array(
                  'Authorization: key=' . $google_api_key,
                  'Content-Type: application/json'
                );

      $message_data = array( 'title' => $subject, 'text' => $message );
      $type_data = array( 'type' => $notification_type );

      foreach ( $android_user_array as $android_user )
      {
        // Set POST variables
        $fields = array(
          'to' => $android_user['device_id'],
          'notification' => $message_data,
          'data' => $type_data
        );

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($fields) );
        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode( $result );

        //if ( $data->success == 1 )
        //{
          // Insert to database if success
          $insert_data = array(
                            'receiver_id' => $android_user['user_id'],
                            'sender_id' => $user_id,
                            'notification_type' => $notification_type,
                            'notification_message' => $message,
                            'notification_date' => date('Y-m-d H:i:s')
                          );
          $insert_result = $CI->push_notification_model->insert( $insert_data );
        //}
        //else if ( $data->failure == 1 )
        //{
          // Not sent
        //}
      }
    }

    // For ios devices
    if( $ios_user_array )
    {
      $passphrase = $url = '';

      if( ENVIRONMENT == 'development' )
      {
        $certificate = DEV_PEM_FILE;
        $passphrase = DEV_PASSPHRASE;
        $url = DEV_SSL_URL;
      }
      else
      {
        $certificate = LIVE_PEM_FILE;
        $passphrase = LIVE_PASSPHRASE;
        $url = LIVE_SSL_URL;
      }

      $stream_context = stream_context_create();
      stream_context_set_option( $stream_context, 'ssl', 'local_cert', $certificate );
      stream_context_set_option( $stream_context, 'ssl', 'passphrase', $passphrase );

      $fp = @stream_socket_client( $url, $err, $errstr, 60, (STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT), $stream_context );

      if( $fp )
      {
        // Build the payload
        $load = array(
                  'aps' => array( 'alert' => $message, 'badge' => 0, 'sound' => 'default' ),
                  'type' => $notification_type,
                  'title' => $subject,
                );
        // Encode the payload as JSON
        $payload = json_encode($load);

        foreach ( $ios_user_array as $ios_user )
        {
          $msg = chr(0) . pack('n', 32) . pack('H*', $ios_user['device_id']) . pack('n', strlen($payload)) . $payload;
          $result = fwrite($fp, $msg, strlen($msg));

          //if( $result )
          //{
            $insert_data = array(
                            'receiver_id' => $ios_user['user_id'],
                            'sender_id' => $user_id,
                            'notification_type' => $notification_type,
                            'notification_message' => $message,
                            'notification_date' => date('Y-m-d H:i:s')
                          );
            $insert_result = $CI->push_notification_model->insert( $insert_data );
          //}
          //else
          //{
            // Not sent
          //}
        }
        fclose($fp);
      }
      // else
      //   exit("Failed to connect: $err $errstr" . PHP_EOL);
    }

    // For email users
    if( $email_user_array )
    {
      $email_from = "info@halozi.com";
      $email_subject = constant( $type.'_SUBJECT' );
      $email_body = $message;

      foreach ( $email_user_array as $email )
      {
        if( $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "192.168.21.18" || $_SERVER['HTTP_HOST'] == "192.168.43.47" )
        {
          $config['protocol']     = 'smtp';
          $config['smtp_host']    = 'ssl://smtp.gmail.com';
          $config['smtp_port']    = '465';
          $config['smtp_timeout'] = '7';
          $config['smtp_user']    = 'genknooz501@gmail.com';
          $config['smtp_pass']    = 'genknooz!';
          $config['charset']      = 'utf-8';
          $config['newline']      = "\r\n";
          $config['mailtype']     = 'html'; // or html
          $config['validation']   = TRUE; // bool whether to validate email or not

          $CI->load->library('email',$config);
          $CI->email->from( $email_from, $CI->config->item('site_name') );
          $CI->email->to( $email );
          $CI->email->subject( $CI->config->item('site_name')." : ".$email_subject );
          $CI->email->message( $email_body );
          $CI->email->send();
        }
        else
        {
          $config = Array(
                'protocol' => 'smtp',
                'smtp_host' => 'mail.tabula.mobi',
                'smtp_port' => '25',
                'smtp_user' => 'noreply@tabula.mobi', // change it to yours
                'smtp_pass' => 'Tabula@123', // change it to yours
                'mailtype' => 'html',
                'charset' => 'iso-8859-1',
                'wordwrap' => TRUE,
                'smtp_crypto' => 'tls'
            );

          $CI->load->library('email',$config);
          $CI->email->from( $email_from, $CI->config->item('site_name') );
          $CI->email->to( $email );
          $CI->email->subject( $CI->config->item('site_name')." : ".$email_subject );
          $CI->email->message( $email_body );
          $CI->email->send();
        }
      }
    }

  }
}

/* End of file push_notification_helper.php */
/* Location: ./appplication/helpers/push_notification_helper.php */