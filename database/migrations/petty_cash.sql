DROP TABLE IF EXISTS petty_cash;
create table petty_cash(
    id int(10) not null PRIMARY KEY AUTO_INCREMENT,
    user_id int(10) not null,
    amount decimal(10,2),
    remarks text,
    date date,
    created_at timestamp DEFAULT now(),
    created_by int(10)
);