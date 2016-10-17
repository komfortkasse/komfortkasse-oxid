<?php
/** komfortkasse OXID modul
 */
$sMetadataVersion = '1.0';
$aModule = array (
		'id' => 'komfortkasse',
		'title' => 'Komfortkasse',
		'url' => 'https://komfortkasse.eu',
		'description' => array (
				'en' => 'Komfortkasse enables automatic assignment of bank wire transfers for Prepayment, Invoice, and Cash On Delivery.',
				'de' => 'Mit Komfortkasse automatisieren Sie alle Zahlungsarten, die per Bank&uuml;berweisung get&auml;tigt werden: Vorkasse, Rechnung, Nachnahme.'
		),
		'lang' => 'en',
		'version' => '1.4.4.15',
		'author' => 'komfortkasse.eu Integration Team',
		'email' => 'integration@komfortkasse.eu',
		'thumbnail' => 'thumb.png',
		'extend' => array (
				'oxorder' => 'ltc/komfortkasse/models/komfortkasseoxorder'
		),
		'files' => array (
				'komfortkasseCallback' => 'ltc/komfortkasse/controllers/komfortkassecallback.php',
				'Komfortkasse' => 'ltc/komfortkasse/controllers/Komfortkasse.php',
				'Komfortkasse_Config' => 'ltc/komfortkasse/controllers/Komfortkasse_Config.php',
				'Komfortkasse_Order' => 'ltc/komfortkasse/controllers/Komfortkasse_Order.php'
		),
		'templates' => array (
				'komfortkassecallback.tpl' => 'ltc/komfortkasse/views/komfortkassecallback.tpl'
		),
		'settings' => array (
				array (
						'group' => 'main',
						'name' => 'kkActivateExport',
						'type' => 'bool',
						'value' => 'true'
				),
				array (
						'group' => 'main',
						'name' => 'kkActivateUpdate',
						'type' => 'bool',
						'value' => 'true'
				),
				array (
						'group' => 'main',
						'name' => 'kkPaymentMethods',
						'type' => 'str',
						'value' => 'oxidpayadvance'
				),
				array (
						'group' => 'main',
						'name' => 'kkPaymentMethodsInvoice',
						'type' => 'str',
						'value' => 'oxidinvoice'
				),
		        array (
						'group' => 'main',
						'name' => 'kkPaymentMethodsCod',
						'type' => 'str',
						'value' => 'oxidcashondel'
				),
		        array (
						'group' => 'komfortkasse_advanced',
						'name' => 'kkEncryption',
						'type' => 'str',
						'value' => 'undefined'
				),
				array (
						'group' => 'komfortkasse_advanced',
						'name' => 'kkAccesscode',
						'type' => 'str',
						'value' => 'undefined'
				),
				array (
						'group' => 'komfortkasse_advanced',
						'name' => 'kkApikey',
						'type' => 'str',
						'value' => 'undefined'
				),
				array (
						'group' => 'komfortkasse_advanced',
						'name' => 'kkPublickey',
						'type' => 'str',
						'value' => 'undefined'
				),
				array (
						'group' => 'komfortkasse_advanced',
						'name' => 'kkPrivatekey',
						'type' => 'str',
						'value' => 'undefined'
				)
		)

)
;
