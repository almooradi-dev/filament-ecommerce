<?php

namespace Almooradi\FilamentEcommerce\Constants\Order;

class OrderShippingStatus
{
	const PENDING = 1;
	const IN_PROGRESS = 2;
	const PACKAGING = 3;
	const SHIPPED = 4;
	const REJECTED_BY_ADMIN = 5;
	const REJECTED_BY_PROVIDER = 6;
	const DELIVERED = 7;
	const RETURNED_BY_ADMIN = 8;
	const RETURNED_BY_PROVIDER = 9;
	const RETURNED_BY_CLIENT = 10;
	const LOST_IN_TRANSIT = 11;

	const ALL = [
		self::PENDING => 'Pending',
		self::IN_PROGRESS => 'In Progress',
		self::PACKAGING => 'Packaging',
		self::SHIPPED => 'Shipped',
		self::REJECTED_BY_ADMIN => 'Rejected By Admin',
		self::REJECTED_BY_PROVIDER => 'Rejected By Provider',
		self::DELIVERED => 'Delivered',
		self::RETURNED_BY_ADMIN => 'Returned By Admin',
		self::RETURNED_BY_PROVIDER => 'Returned By Provider',
		self::RETURNED_BY_CLIENT => 'Returned By Client',
		self::LOST_IN_TRANSIT => 'Loast in Transit',
	];
}
