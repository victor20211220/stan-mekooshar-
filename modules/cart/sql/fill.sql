INSERT INTO `settings` (`name`, `key`, `value`, `type`, `rules`, `options`, `root`, `position`) VALUES
('Test mode', 'sandbox', '1', 'checkbox', NULL, NULL, 1, NULL),
('Payment system', 'paymentgateway', 'paypal', 'select', '{"required":true}', '{"paypal":"PayPal", "authorize":"Authorize.NET"}', NULL, NULL),
('Paypal Payments type', 'paypalType', 'paypal', 'select', '{"required":true}', '{"paypalStandart":"PayPal Payments Standart", "paypalPro":"PayPal Payments Pro"}', NULL, NULL),
('Paypal email', 'paypalEmail', 'stetsyuk@ukietech.com', 'text', NULL, NULL, NULL, NULL),
('API Username', 'paypalUsername', 'delete-286543989P4520825_api1.sandbox.paypal.com', 'text', NULL, NULL, NULL, NULL),
('API Password', 'paypalPassword', 'M5QN3PV9HS6KBXJM', 'text', NULL, NULL, NULL, NULL),
('Signature', 'paypalSignature', 'AlcKpCIVqM3pzKACjHN.bRCbBugPAhlwWTkHbXzUlICuDvcHY8WDDqU8', 'text', NULL, NULL, NULL, NULL);