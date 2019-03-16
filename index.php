<?php 
	require __DIR__ . '/vendor/autoload.php';
	use \LINE\LINEBot;
	use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
	use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
	use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
	use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
	use \LINE\LINEBot\SignatureValidator as SignatureValidator;

	// set false for production
	$pass_signature = true;
	// Set LINE channel_access_token and channel_secret
	$channel_access_token = "";
	$channel_secret = "";

	// inisialisasi object bot
	$httpClient = new CurlHTTPClient($channel_access_token);
	$bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);

	$configs = [
		'setting' => ['displayErrorDetails' => true],
	];

	$app = new Slim\App($configs);

	// buat route untuk url homepage
	$app->get('/', function($req, $req)
	{
		echo "Welcome at Slim Framework";
	});

	// buat route untuk webhook
	$app->post('/webhook', function($request, $response) use ($bot, $pass_signature)
	{
		// get request body and line signature header
		$body = file_get_contents('php://input');
		$signature = isset($_SERVER['HTTP_X_LINE_SIGNATURE']) ? $_SERVER['HTTP_X_LINE_SIGNATURE'] : '';
		// log body and signature
		file_put_contents('php://stderr', 'Body:'.$body);

		if ($pass_signature === false) {
			// is LINE_SIGNATURE exists in request header?
			if (empty($signature)) {
				return $response->withStatus(400, 'Signature not set');
			}

			// is this request comes from LINE?
			if (!SignatureValidator::validateSignature($body, $channel_secret, $signature)) {
				return $response->withStatus(400, 'Invalid signature');
			}
		}
		// kode aplikasi kita nanti di sini

	});

	$app->run();
?>