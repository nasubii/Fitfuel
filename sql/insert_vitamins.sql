-- ビタミン剤商品データ一括INSERT
-- カテゴリ: ビタミン (category_id = 1)
-- 実行前に product テーブルが存在することを確認してください

INSERT INTO `product` (
    `product_name`,
    `product_price`,
    `category_id`,
    `product_image`,
    `product_image2`,
    `product_nuts_image`,
    `product_stock`,
    `product_status`
) VALUES
-- マルチビタミン
('FitFuel マルチビタミン デイリー', 2980, 1, 'pimage/FitFuel MULTI VITAMIN DAILY_1.jpeg', 'pimage/FitFuel MULTI VITAMIN DAILY_2.jpeg', 'eimage/FitFuel MULTI VITAMIN DAILY_nut.jpeg', 500, 1),

-- ビタミンC
('FitFuel ビタミンC 1000', 1980, 1, 'pimage/FitFuel VITAMIN C 1000_1.jpeg', 'pimage/FitFuel VITAMIN C 1000_2.jpeg', 'eimage/FitFuel VITAMIN C 1000_nut.jpeg', 800, 1),

-- ビタミンB群
('FitFuel B-コンプレックス エナジー', 2280, 1, 'pimage/FitFuel B-COMPLEX ENERGY_1.jpeg', 'pimage/FitFuel B-COMPLEX ENERGY_2.jpeg', 'eimage/FitFuel B-COMPLEX ENERGY_nut.jpeg', 450, 1),

-- ビタミンD + カルシウム
('FitFuel ビタミンD + カルシウム', 2480, 1, 'pimage/FitFuel VITAMIN D + CALCIUM_1.jpeg', 'pimage/FitFuel VITAMIN D + CALCIUM_2.jpeg', 'eimage/FitFuel VITAMIN D + CALCIUM_nut.jpeg', 700, 1),

-- ルテイン & アイケア
('FitFuel ルテイン & アイケア', 3280, 1, 'pimage/FitFuel LUTEIN & EYE CARE_1.jpeg', 'pimage/FitFuel LUTEIN & EYE CARE_2.jpeg', 'eimage/FitFuel LUTEIN & EYE CARE_nut.jpeg', 400, 1);

-- 実行結果確認用
-- SELECT * FROM product WHERE category_id = 1;
