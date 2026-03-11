<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @return bool
 */
function upgrade_module_1_4_0()
{
    return Db::getInstance()->execute(
        'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'dpdpoland_pickup_history` (
				`id_pickup_history` int(10) NOT NULL AUTO_INCREMENT,
				`order_number` varchar(255) NULL,
				`sender_address` varchar(255) NULL,
				`sender_company` varchar(255) NULL,
				`sender_name` varchar(255) NULL,
				`sender_phone` varchar(50) NULL,
				`pickup_date` datetime NULL,
				`pickup_time` varchar(50) NULL,
				`type` varchar(50) NULL,
				`envelope` int(10) NULL,
                `package` int(10) NULL,
                `package_weight_all` decimal(20,6) NULL,
				`package_heaviest_weight` decimal(20,6) NULL,
				`package_heaviest_width` decimal(20,6) NULL,
				`package_heaviest_length` decimal(20,6) NULL,
				`package_heaviest_height` decimal(20,6) NULL,
				`pallet` int(10) NULL,
				`pallet_weight` decimal(20,6) NULL,
				`pallet_heaviest_weight` decimal(20,6) NULL,
				`pallet_heaviest_height` decimal(20,6) NULL,
				`id_shop` int(10) NOT NULL,
				`date_add` datetime NOT NULL,
				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_pickup_history`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;'
    );
}
