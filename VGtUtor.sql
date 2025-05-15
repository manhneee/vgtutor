CREATE TABLE account (
	userid int primary key,
    password varchar(255)
);

CREATE TABLE admin_account (
	adminid int primary key,
    name varchar(50),
    phone_number varchar(20),
    foreign key (adminid) references account (userid)
);

CREATE TABLE student_account (
	accountid int primary key,
    email varchar(50),
    name varchar(50),
    major varchar(3),
    studentid int unique,
    intake int,
    foreign key (accountid) references account (userid)
);

CREATE TABLE tutor_account (
	accountid int primary key,
    bank_name varchar(50),
    bank_acc_no varchar(50),
    gpa NUMBER(2,1),
    description varchar(200),
    overall_rating NUMBER(2,1),
    foreign key (accountid) references student_account (accountid)
);

CREATE TABLE course (
	courseid int primary key,
    course_name varchar(50),
    major varchar(3),
    semester int
);

CREATE TABLE course_offering (
	tutorid int,
    courseid int,
    tutor_grade NUMBER(2,1),
    rating NUMBER(2,1),
    price int,
    primary key (tutorid, courseid),
    foreign key (tutorid) references tutor_account (accountid),
    foreign key (courseid) references course (courseid)
);

CREATE TABLE review (
	studentid int,
    tutorid int,
    courseid int,
    rating NUMBER(2,1),
    review varchar(200),
    primary key (studentid, tutorid, courseid),
    foreign key (tutorid, courseid) references course_offering (tutorid, courseid),
    foreign key (studentid) references student_account (accountid)
);

CREATE TABLE session (
	studentid int,
    tutorid int,
    courseid int,
    date_and_time datetime,
    duration float,
    paid boolean,
    primary key (studentid, tutorid, courseid, date_and_time),
    foreign key (tutorid, courseid) references course_offering (tutorid, courseid),
    foreign key (studentid) references student_account (accountid)
);