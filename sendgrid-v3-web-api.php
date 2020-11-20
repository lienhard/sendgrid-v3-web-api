<?php

/*

The data array was awkward to setup, so providing this to others.  Hope it helps.

Example usage:

$sendgrid = new SL_Sendgrid('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // pass the API key

// setup your template arguments, if any

$args = array(
  'Sender_Name' => 'Donald Duck',
  'Sender_Address' => '1234 Main Street',
  'Sender_City' => 'Schnectady',
  'Sender_State' => 'New York',
  'Sender_Zip' => '12345',
);
  
// Generate the email using the template ID

$result = $sendgrid->sendEmailTemplate(
    'someone@somewhere.com', // to email address
    'Joe Smith', // to name
    'Using dynamic template', // subject
    'd-f36c0bbdb48b44b7aa9598bf9a916936', // template ID
    $args, // template arguments - e.g. the template would contain {{Sender_Name}}, {{Sender_Address}}, etc.
    'noreply@mydomain.com', // from email
    '', // from name
    'noreply@mydomain', // reply email
    ''); // reply name

  echo '<pre>'.print_r($result, true).'</pre>';

exit;

*/


class SL_Sendgrid {

  private $apiKey;
	
	function __construct($apiKey) {

    $this->apiKey = $apiKey;

  }

  function sendEmailTemplate($toEmail, $toName, $subject, $templateID, $templateArgs, $fromEmail, $fromName, $replyEmail, $replyName) {

        $data = array (
          'personalizations' => 
              array (
                array (
                  'to' => 
                      array (
                        array (
                          'email' => $toEmail,
                          'name' => $toName,
                        ),
                      ),
                  'dynamic_template_data' => 
                      $templateArgs,
                  'subject' => $subject,
                ),
              ),
          'from' => 
              array (
                'email' => $fromEmail,
                'name' => $fromName,
              ),
          'reply_to' => 
            array (
              'email' => $replyEmail,
              'name' => $replyName,
            ),
          'template_id' => $templateID,
        );

        $dataEncode = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.sendgrid.com/v3/mail/send",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $dataEncode,
          CURLOPT_HTTPHEADER => array(
          "authorization: Bearer ".$this->apiKey,
          "content-type: application/json"
          ),
        ));

        $result = array(
          'success' => true,
          'errorMsg' => '',
        );

        $response = json_decode(curl_exec($curl));

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          $result['success'] = false;
          $result['errorMsg'] = 'cURL Error';
          $result['errorDetails'] = $err;
        } elseif (!empty($response->errors)) {
          $result['success'] = false;
          $result['errorMsg'] = $response->errors[0]->message;
          $result['errorDetails'] = $response->errors[0];
        }

        return $result;
  }

}



