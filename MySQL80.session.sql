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

ALTER TABLE user_details
ADD COLUMN birthday DATE,
ADD COLUMN address VARCHAR(255),
ADD COLUMN about_me TEXT;

alter table user_details modify image_path blob; 

describe user_details;
ALTER TABLE user_details MODIFY COLUMN image_path LONGBLOB;
delete from user_details;
DROP TABLE user_details;

alter table tasks add image longblob;

UPDATE barters
INNER JOIN user_details ON barters.user_email = user_details.email
SET barters.image = user_details.image_path;

 alter table tasks add name varchar(191);

UPDATE tasks
INNER JOIN user_details ON tasks.user_email = user_details.email
SET tasks.name = user_details.user_name;

CREATE TABLE user_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(191) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    image_path LONGBLOB
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_description TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_email VARCHAR(191) NOT NULL,
    FOREIGN KEY (user_email) REFERENCES user_details(email)
);
ALTER TABLE tasks
ADD COLUMN t_posted INT DEFAULT 0;
ALTER TABLE barters
ADD COLUMN image LONGBLOB;

SELECT* from tasks;
drop table tasks;

ALTER TABLE tasks ADD COLUMN category VARCHAR(255);

CREATE TABLE tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    task_title VARCHAR(255) NOT NULL,
    task_description TEXT NOT NULL,
    due_date DATE NOT NULL,
    category VARCHAR(255),
    image LONGBLOB,
    tasks_posted_by VARCHAR(255) NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_email) REFERENCES user_details(email)
);
ALTER TABLE tasks
ADD COLUMN tasks_accepted INT DEFAULT 0,
ADD COLUMN tasks_completed INT DEFAULT 0;


CREATE TABLE accepted_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    accepted_by VARCHAR(255) NOT NULL,
    accepted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id)
);

ALTER TABLE tasks
DROP COLUMN tasks_accepted,
DROP COLUMN tasks_completed;

select* from barters;

ALTER TABLE accepted_tasks ADD accepted TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE tasks DROP COLUMN accepted;

delete from barters;

ALTER TABLE tasks ADD COLUMN completed BOOLEAN DEFAULT FALSE;

DELETE FROM tasks
WHERE tasks_posted_by = 'Yuvan';


CREATE TABLE user_logins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    login_date DATE NOT NULL,
    UNIQUE (user_email, login_date)
);

CREATE TABLE task_completions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    task_id INT NOT NULL,
    completion_date DATE NOT NULL
);
select * FROM accepted_tasks

ALTER TABLE tasks ADD completed_at TIMESTAMP DEFAULT NULL;
ALTER TABLE tasks DROP COLUMN updated_at;
