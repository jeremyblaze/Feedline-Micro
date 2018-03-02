<?php

    // settings

        $SEND_MESSAGES_TO = "you@yourdomain.com";
        $MESSAGE_SUBJECT = "Feedline Support";
    
    // validation

        $result = array();
    
        $result['status'] = 'success';
    
        if ( $_POST['inputName'] && $_POST['inputName'] != '' ) {
            $inputName = $_POST['inputName'];
        } else {
            $result['status'] = 'error';
            $result['detail']['name'] = "Please enter your name";
        }
        
        function feedlineEmailValidation($email) {
            
            if ( !filter_var($email, FILTER_VALIDATE_EMAIL) == false ) {
                $result = true;
            } else {
                $result = false;
            }
            
            return $result;
            
        }
        
        if ( $_POST['inputEmail'] && $_POST['inputEmail'] != '' ) {
            if ( feedlineEmailValidation($_POST['inputEmail']) ) {
                $inputEmail = $_POST['inputEmail'];
            } else {
                $result['status'] = 'error';
                $result['detail']['email'] = "Sorry, your email doesn't look valid";
            }
        } else {
            $result['status'] = 'error';
            $result['detail']['email'] = "Please enter your email";
        }
    
        if ( $_POST['inputMessage'] && $_POST['inputMessage'] != '' ) {
            $inputMessage = $_POST['inputMessage'];
        } else {
            $result['status'] = 'error';
            $result['detail']['message'] = "Please enter a message";
        }
        
    // send email
    
        if ( $result['status'] == 'success' ) {
            
            function feedlineMailer($toEmail, $content, $validation = false, $customValidation = '', $test = false) {

                /*
                
                    $content = array(
                        "fromEmail" => "sender@example.com",
                        "fromName" => "John Smith",
                        "fromSubject" => "This is the subject line",
                        "fromMessage" => "This is the sender's message",
                        "contentType" => "text/plain"
                    );
                    
                    $customValidation = array(
                        "emailChars" => 10,
                        "nameChars" => 5,
                        "subjectChars" => 10,
                        "messageChars" => 50
                    );
                    
                    If there's a validation error the function will return...
                    
                        array(
                            "success" => false,
                            "error" => array(
                                "the field name" => array(
                                    "error type" => "error description"
                                )
                            ),
                            "data" => $content
                        );
                        
                    If the email sends successfully the function will return...
                    
                        array(
                            "success" => true
                        );
                        
                    Setting $test to true will stop the email from sending and just return the $result array
                    
                */
                
                // Validation settings
                    
                    $validationSettings = array(
                        "emailChars" => 5,
                        "nameChars" => 3,
                        "subjectChars" => 5,
                        "messageChars" => 20
                    );
                
                    if ( $customValidation != '' ) {
                        $validationSettings = $customValidation;
                    }

                // Validate
                
                    $success = true; /* This will be changed to false if any errors are found */
                    $error = array();
                
                    if ( $validation == true ) {
                
                        // Check for empty fields
                        
                            if (
                                $toEmail == '' ||
                                $content['fromEmail'] == '' ||
                                $content['fromName'] == '' ||
                                $content['fromSubject'] == '' ||
                                $content['fromMessage'] == ''
                            ) {
                                
                                if ( $toEmail == '' ) {
                                    $error['toEmail']['empty'] = 'Please enter an email';
                                }
                                    
                                if ( $content['fromEmail'] == '' ) {
                                    $error['fromEmail']['empty'] = 'Please enter an email';
                                }
                                    
                                if ( $content['fromName'] == '' ) {
                                    $error['fromName']['empty'] = 'Please enter a name';
                                }
                                    
                                if ( $content['fromSubject'] == '' ) {
                                    $error['fromSubject']['empty'] = 'Please enter a subject';
                                }
                                    
                                if ( $content['fromMessage'] == '' ) {
                                    $error['fromMessage']['empty'] = 'Please enter a message';
                                }
                                
                                $success = false;
                                
                            }
                            
                        // Check email is real
                        
                            if ( feedlineEmailValidation($toEmail) == false ) {
                                $success = false;
                                $error['toEmail']['invalid'] = 'The email you provided was not valid';
                            }
                        
                            if ( feedlineEmailValidation($content['fromEmail']) == false ) {
                                $success = false;
                                $error['fromEmail']['invalid'] = 'The email you provided was not valid';
                            }
                            
                        // Check character counts
                        
                            if ( strlen($content['fromEmail']) < $validationSettings['emailChars'] ) {
                                $success = false;
                                $error['fromEmail']['chars'] = 'Your email must be longer than '.$validationSettings['emailChars'].' characters';
                            }
                            
                            if ( strlen($content['fromName']) < $validationSettings['nameChars'] ) {
                                $success = false;
                                $error['fromName']['chars'] = 'Your name must be longer than '.$validationSettings['nameChars'].' characters';
                            }
                            
                            if ( strlen($content['fromSubject']) < $validationSettings['subjectChars'] ) {
                                $success = false;
                                $error['fromSubject']['chars'] = 'Your subject must be longer than '.$validationSettings['subjectChars'].' characters';
                            }
                            
                            if ( strlen($content['fromMessage']) < $validationSettings['messageChars'] ) {
                                $success = false;
                                $error['fromMessage']['chars'] = 'Your message must be longer than '.$validationSettings['messageChars'].' characters';
                            }
                        
                    }
                    
                // Attempt to send

                    if ( !isset($content['contentType']) ) {
                        $content['contentType'] = "text/html";
                    }
                
                    if ( $test == false ) {
                        if ( $success == true ) {
                            
                            $header = 'MIME-Version: 1.0' . "\r\n";
                            $header .= 'Content-type: '.$content['contentType'].'; charset=iso-8859-1' . "\r\n";                    
                            $header .= "From: ".$content['fromName']." <".$content['fromEmail'].">";
                            
                            mail($toEmail, $content['fromSubject'], $content['fromMessage'], $header);
                            
                        }
                    }
                
                // Return
                
                    $content['toEmail'] = $toEmail;
                
                    $result = array(
                        "success" => $success,
                        "error" => $error,
                        "data" => $content
                    );
                    
                    return $result;
                
            }
            
            $message = array(
                "fromEmail" => $inputEmail,
                "fromName" => $inputName,
                "fromSubject" => $MESSAGE_SUBJECT,
                "fromMessage" => $inputMessage,
                "contentType" => "text/plain"
            );
            
            feedlineMailer($SEND_MESSAGES_TO, $message);
            
        }
        
    // return result
    
        die(json_encode($result));

?>