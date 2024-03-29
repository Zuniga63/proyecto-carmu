DropCreateCategoryHasTagTable: drop table if exists `category_has_tag`
CreateColorTable: create table `color` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(20) not null, `hex` varchar(8) not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_spanish_ci'
CreateColorTable: alter table `color` add unique `color_name_unique`(`name`)
CreateColorTable: alter table `color` add unique `color_hex_unique`(`hex`)
CreateSizeTable: create table `size` (`id` bigint unsigned not null auto_increment primary key, `value` varchar(5) not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_spanish_ci'
CreateSizeTable: alter table `size` add unique `size_value_unique`(`value`)
AddColorColumnInProduct: alter table `product` add `color_id` bigint unsigned null after `brand_id`
AddColorColumnInProduct: alter table `product` add constraint `product_color_id_foreign` foreign key (`color_id`) references `color` (`id`) on delete set null on update cascade
AddSizeColumnInProduct: alter table `product` add `size_id` bigint unsigned null after `color_id`
AddSizeColumnInProduct: alter table `product` add constraint `product_size_id_foreign` foreign key (`size_id`) references `size` (`id`) on delete set null on update cascade
AddRefColumnInProduct: alter table `product` add `ref` varchar(50) null after `img`
AddBarcodeColumnInProduct: alter table `product` add `barcode` varchar(255) null after `ref`
AddBarcodeColumnInProduct: alter table `product` add unique `product_barcode_unique`(`barcode`)
CreateCustomerTable: create table `customer` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(50) not null, `nit` varchar(50) null, `email` varchar(100) null, `phone` varchar(20) null, `photo` varchar(255) null, `positive_balance` decimal(19, 2) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateCustomerTable: alter table `customer` add unique `customer_nit_unique`(`nit`)
CreateCustomerTable: alter table `customer` add unique `customer_email_unique`(`email`)
CreateProductImageTable: create table `product_image` (`id` bigint unsigned not null auto_increment primary key, `product_id` bigint unsigned not null, `path` varchar(255) not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateProductImageTable: alter table `product_image` add constraint `product_image_product_id_foreign` foreign key (`product_id`) references `product` (`id`)
CreatePromoTable: create table `promo` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(50) not null, `description` varchar(200) not null, `since` datetime default CURRENT_TIMESTAMP not null, `until` datetime not null, `min_item` tinyint unsigned not null default '1', `max_item` tinyint unsigned null, `off` double(8, 2) unsigned not null, `img` varchar(255) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateProductHasPromoTable: create table `product_has_promo` (`product_id` bigint unsigned not null, `promo_id` bigint unsigned not null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateProductHasPromoTable: alter table `product_has_promo` add constraint `product_has_promo_product_id_foreign` foreign key (`product_id`) references `product` (`id`) on delete cascade on update cascade
CreateProductHasPromoTable: alter table `product_has_promo` add constraint `product_has_promo_promo_id_foreign` foreign key (`promo_id`) references `promo` (`id`) on delete cascade
CreateProductHasPromoTable: alter table `product_has_promo` add primary key `product_has_promo_product_id_promo_id_primary`(`product_id`, `promo_id`)
