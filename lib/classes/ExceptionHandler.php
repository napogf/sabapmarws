<?php

class ExceptionHandler extends ErrorException
{
	private static $errors = array(
		E_ERROR				=> 'E_ERROR',
		E_WARNING			=> 'E_WARNING',
		E_PARSE				=> 'E_PARSE',
		E_NOTICE			=> 'E_NOTICE',
		E_CORE_ERROR		=> 'E_CORE_ERROR',
		E_CORE_WARNING		=> 'E_CORE_WARNING',
		E_COMPILE_ERROR		=> 'E_COMPILE_ERROR',
		E_COMPILE_WARNING	=> 'E_COMPILE_WARNING',
		E_USER_ERROR		=> 'E_USER_ERROR',
		E_USER_WARNING		=> 'E_USER_WARNING',
		E_USER_NOTICE		=> 'E_USER_NOTICE',
		E_STRICT			=> 'E_STRICT',
		E_RECOVERABLE_ERROR	=> 'E_RECOVERABLE_ERROR',
		E_DEPRECATED		=> 'E_DEPRECATED',
		E_USER_DEPRECATED	=> 'E_USER_DEPRECATED',
	);

	public static function printException(Exception $e)
	{
        $code_name = $e->getCode();
        if (isset(self::$errors[$code_name])) {
            $code_name = self::$errors[$code_name];
        }

		// In caso di sviluppo, stampo l'errore
		if (DEBUG) {
			?>
			<span style="text-align: left; background-color: #fcc; border: 1px solid #600; color: #600; display: block; margin: 1em 0; padding: .33em 6px">
				<b>Type:</b> <?php echo get_class($e); ?><br />
				<b>Error:</b> <?php echo $code_name; ?><br />
				<b>Message:</b> <?php echo htmlspecialchars($e->getMessage()); ?><br />
				<b>File:</b> <?php echo $e->getFile(); ?><br />
				<b>Line:</b> <?php echo $e->getLine(); ?><br />
				<b>Stack trace:</b><pre><?php echo htmlspecialchars($e->getTraceAsString(), ENT_COMPAT, 'ISO-8859-1'); ?></pre>
			</span>
			<?php

		// Altrimenti sopprimo l'errore e mando una email interna
		} else {
			try {
				$bodyArray = array(
					'Data'				=> date('l d F Y, H:i:s'),
					'REQUEST_URI'		=> $_SERVER['REQUEST_URI'],
					'REMOTE_ADDR'		=> $_SERVER['REMOTE_ADDR'],
					'Creatore'			=> get_class($e),
					'Tipo errore'		=> $code_name,
					'Messaggio'			=> $e->getMessage(),
					'File'				=> $e->getFile(),
					'Linea'				=> $e->getLine(),
				);

				$body = '';
				foreach ($bodyArray as $key => $val) {
					$body .= $key . "\n";
					$body .= "\t" . $val . "\n";
				}

				$body .= 'Stack trace:' . "\n\n";
				$body .= $e->getTraceAsString() . "\n\n";

				$body .= '$_SESSION:' . "\n\n";
				$body .= print_r($_SESSION, 1) . "\n\n";

				$body .= '$_POST:' . "\n\n";
				$body .= print_r($_POST, 1) . "\n\n";

				$mail = new fdlMail();
				$mail->addAddress(EMAIL_SVILUPPO);
				$mail->Subject = 'Errore: '.substr($e->getMessage(), 0, 60);
				$mail->IsHTML(false);
				$mail->Body = $body;

				if (!$mail->send()) {
				    throw new Page_Exception("Mailer Error: " . $mail->ErrorInfo);
				}


			} catch (Exception $e) {}

			header('HTTP/1.1 500 Internal Server Error');
			header('Content-Type: text/html; charset=utf-8');
			?>
			<!DOCTYPE html>
			<html><head>
			<title>TSI: 500 Errore interno</title>
			</head><body>
			<h1>TSI: 500 Errore interno</h1>
			<p>È accaduto un errore interno, gli amministratori sono stati avvisati.</p>
			<p>TSI S.r.l.</p>
			</body></html>
			<?php
		}
	}

	public static function handleException(Exception $e)
	{
		return self::printException($e);
	}
}
