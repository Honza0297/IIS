/*
CREATE DATABASE `db-its` CHARACTER SET utf8 COLLATE utf8_czech_ci;
Pokud vytvarime databazi

 */

create table Persons(personID int not null auto_increment, username varchar(64) not null, name varchar(64) not null, surname varchar(128) not null, role ENUM("customer", "worker", "manager", "senior manager", "admin"), password varchar(128),
    primary key (personID));

create table Products(productID int not null auto_increment, product_name text not null, description text, parent_product int, manager int not null,
    primary key (productID),
    foreign key (parent_product) references Products(productID) on delete cascade, 
    foreign key (manager) references Persons(personID));

create table Tickets(ticketID int not null auto_increment, title varchar(128) not null, info text, state ENUM("pending", "in progress", "solved", "canceled", "refused", "retired") not null, date_posted date not null, author int not null, product int not null, 
    primary key (ticketID), 
    foreign key (author) references Persons(personID) on delete cascade,
    foreign key (product) references Products(productID) on delete cascade);

create table Attachments(ID int not null auto_increment, ticketID int not null, filename varchar(256),
    primary key (ID), 
    foreign key (ticketID) references Tickets(ticketID) on delete cascade);

create table Tasks(taskID int not null auto_increment, title text not null, state ENUM("pending", "in progress", "solved", "cancelled", "refused") not null, ticketID int not null, description text not null, deadline date,
    primary key (taskID),
    foreign key (ticketID) references Tickets(ticketID) on delete cascade);
    
create table Work_on_tasks(taskID int not null, personID int not null, total_time int default 0,
    primary key (personID, taskID),
    foreign key (personID) references Persons(personID) on delete cascade,
    foreign key (taskID) references Tasks(taskID) on delete cascade);
    
create table Comments(commentID int not null auto_increment, ticketID int not null, comment_text text not null, date_posted date not null, author int not null,
    primary key (commentID, ticketID),
    foreign key (ticketID) references Tickets(ticketID) on delete cascade,
    foreign key (author) references Persons(personID) on delete cascade);
