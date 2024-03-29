CreateProductHasTagTable: create table `product_has_tag` (`product_id` bigint unsigned not null, `tag_id` bigint unsigned not null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateProductHasTagTable: alter table `product_has_tag` add constraint `product_has_tag_product_id_foreign` foreign key (`product_id`) references `product` (`id`) on delete cascade
CreateProductHasTagTable: alter table `product_has_tag` add constraint `product_has_tag_tag_id_foreign` foreign key (`tag_id`) references `tag` (`id`) on delete cascade
CreateProductHasTagTable: alter table `product_has_tag` add primary key `product_has_tag_product_id_tag_id_primary`(`product_id`, `tag_id`)
