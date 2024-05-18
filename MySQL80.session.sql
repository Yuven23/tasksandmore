SELECT* from tasks;
ALTER TABLE tasks
ADD COLUMN t_completed INT DEFAULT 0,
ADD COLUMN t_posted INT DEFAULT 0;
DELETE FROM user_details WHERE user_name = 'Yuven Senthilkumar';
TRUNCATE TABLE user_details;
SELECT* from user_details;

update user_details set image_path='PHOTO (2).jpeg' where user_name='Yuven Senthilkumar';
select * from user_details;
delete from user_details;

alter table user_details modify image_path blob; 

describe user_details;
ALTER TABLE user_details MODIFY COLUMN image_path LONGBLOB;
