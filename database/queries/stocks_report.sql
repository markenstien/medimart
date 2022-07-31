SELECT item.* , stock.total_stock,
    CASE
        WHEN stock.total_stock <= item.min_stock
            THEN 'LOW STOCK LEVEL'
        WHEN stock.total_stock >= item.max_stock
            THEN 'HIGH STOCK LEVEL'
        ELSE 'NORMAL STOCK LEVEL'
    END AS stock_level
FROM items as item
LEFT JOIN (
    SELECT ifnull(sum(quantity),0) as total_stock, item_id
        FROM stocks
        GROUP BY item_id
) as stock
ON stock.item_id = item.id

//add where 