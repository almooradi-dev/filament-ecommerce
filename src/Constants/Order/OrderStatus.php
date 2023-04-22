<?php

namespace Almooradi\FilamentEcommerce\Constants\Order;

class OrderStatus
{
	const PENDING = 1;
	const AWAITING_PAYMENT = 2;
	const IN_PROGRESS = 2;
	const ON_HOLD = 3;
	const REJECTED = 4;
	const CANCELED_BY_ADMIN = 5;
	const CANCELED_BY_CLIENT = 6;
	const PARTIALLY_REFUNDED = 7;
	const REFUNDED = 8;
	const COMPLETED = 9;
	const MANUAL_VERIFICATION_REQUIRED = 10;

	const ALL = [
		self::PENDING => 'Pending',
		self::IN_PROGRESS => 'In Proress',
		self::ON_HOLD => 'On Hold',
		self::REJECTED => 'Rejected',
		self::CANCELED_BY_ADMIN => 'Canceled By Admin',
		self::CANCELED_BY_CLIENT => 'Canceled By Client',
		self::REFUNDED => 'Refunded',
		self::PARTIALLY_REFUNDED => 'Refunded',
		self::COMPLETED => 'Completed',
		self::MANUAL_VERIFICATION_REQUIRED => 'Manual Verification Required',
	];
}
