<?php
namespace block_notifications;

//***************************************************
// SMS notification abstract class
//***************************************************
class SMS extends AbstractSMS {
//class SMS {

	public function message( $changelist, $course ) {
		global $CFG;
		$charmax = 160;

		$events = report_eventlist_list_generator::get_all_events_list();
		$smsbody = '';
		foreach ( $changelist as $item ) {
			$smsbody .= preg_replace('/\\\.*$/', '', $events[$item->event]['raweventname']).': ';
			$smsbody .= $item->name."\r\n";
		}
		return $smsbody;
	}

	public function notify( $changelist, $user, $course ) {
		$message_size = 160; // 160 chars
		$course_shortname_limit = 10;
		$subject_overhead = ' () '; // space()space -> overhead of 4 chars in the subject
		$from = 'sms@provider.tec';
		$to = "$user->phone2@smsprovider.tec";
		// replace all occurences of ++ with 00
		$to = str_replace( '++', '00', $to );
		// replace all occurences of + with 00
		$to = str_replace( '+', '00', $to );
		$subject = substr( $course->shortname, 0, $course_shortname_limit );
		$smsmessage = $this->message( $changelist, $course );

		// if the sms message is longer than 160 chars then cut and put ... at the end of the message
		$message_length = strlen( $from . $subject_overhead . $subject . $smsmessage );
		//echo $message_length."\n";
		if( $message_length > $message_size ) {
			$proper_size = $message_size - strlen($from . $subject_overhead . $subject) - 3;
			$smsmessage = substr($smsmessage, 0, $proper_size) . '...';
		}

		/*
		echo "\n\n";
		echo "$from\n\n";
		echo "$to\n\n";
		echo "$subject\n\n";
		echo "$smsmessage\n\n";
		echo "\n\n";
		return;
		*/

		$mail = get_mailer();
        $mail->Sender = $from;
        $mail->From = $from;
        $mail->AddReplyTo( $from );
        $mail->Subject = $subject;
        $mail->AddAddress( $to ); //to $phone.self::$emailsuffix
        $mail->Body = $smsmessage;
        $mail->IsHTML( false );

		if ( $mail->Send() ) {
			$mail->IsSMTP(); // use SMTP directly
			if ( !empty($mail->SMTPDebug) ) {
				echo '</pre>';
				echo 'mail sent';
			}
		} else {
			if ( !empty($mail->SMTPDebug) ) {
				echo '</pre>';
			}
			//Send Error
			return $mail->ErrorInfo;
		}
	}
}

?>
