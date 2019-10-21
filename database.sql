CREATE DATABASE `db-its` CHARACTER SET utf8 COLLATE utf8_czech_ci;

create table Persons(personID int not null auto_increment, name varchar(64) not null, surname varchar(128) not null, role ENUM("customer", "worker", "manager", "senior manager", "admin"), password varchar(128),
    primary key (personID));

create table Products(productID int not null auto_increment, product_name text not null, parent_product int, manager int not null, 
    primary key (productID),
    foreign key (parent_product) references Products(productID), 
    foreign key (manager) references Persons(personID));

create table Tickets(ticketID int not null auto_increment, title varchar(128) not null, info text, state ENUM("pending", "in progress", "solved", "canceled", "refused", "retired") not null, date_posted date not null, author int not null, product int not null, 
    primary key ('ticketID'), 
    foreign key ('author') references 'Persons'('personID'),
    foreign key ('product') references 'Products'('productID'));

create table Attachments(ID int not null auto_increment, ticketID int not null, content blob not null, 
    primary key (ID), 
    foreign key (ticketID) references Tickets(ticketID));

create table Tasks(taskID int not null auto_increment, task_type ENUM("Bugfix", "Todo", "Feature") not null, state ENUM("pending", "in progress", "solved", "canceled", "refused") not null, ticketID int not null, 
    primary key (taskID),
    foreign key (ticketID) references Tickets(ticketID));
    
create table Work_on_tasks(taskID int not null, personID int not null,
    primary key (personID, taskID),
    foreign key (personID) references Persons(personID),
    foreign key (taskID) references Tasks(taskID));
    
create table Comments(commentID int not null auto_increment, ticketID int not null, comment_text text not null, date_posted date not null, author int not null,
    primary key (commentID, ticketID),
    foreign key (ticketID) references Tickets(ticketID),
    foreign key (author) references Persons(personID));
    

insert into Persons( name, surname, role, password) values("Jan", "Beran", "worker", "sdgfasdvgarfaegfaegf");
insert into Persons( name, surname, role, password) values("Daniel", "Bubeníček", "worker", "12324");
insert into Persons( name, surname, role, password) values("Jakub", "Horký", "manager", "Megahustekrutoprisneheslo");
insert into Persons( name, surname, role, password) values("Matouš", "Ruml", "senior manager", "Leksa");
insert into Persons( name, surname, role, password) values("Marek", "Semtex", "admin", "BigShock");
insert into Persons( name, surname, role, password) values("Lukáš", "Teplomer", "admin", "Sasha Grey");

 
insert into Products(parent_product, product_name, manager) values(NULL, "velka fičura", 3);
insert into Products(parent_product, product_name, manager) values(1, "mala fičura", 3);
insert into Products(parent_product, product_name, manager) values(1, "projekt ISA", 3);

insert into Tickets(title, info, state, date_posted, author, product) values("To je v zadku", "Proste vubec nemam cas :(", "in progress", DATE("2019-10-14"), 1, 3);

insert into Attachments(ticketID, content) values (1, "obrazek kocky");

insert into Comments(ticketID, comment_text, date_posted, author) values(1, "To mas napicu, ja hraju unikovku :P", DATE("2019-10-14"), 2);

insert into Tasks(task_type, state, ticketID) values("bugfix", "pending", 1);

insert into Work_on_tasks(taskID, personID) values(1,3);

/*
select * from Persons;
select * from Products;
select * from Tickets;
select * from Attachments;
select * from Comments;
select * from Work_on_tasks;
select * from Tasks;
*/

select name, surname, task_type from 
Persons natural join Work_on_tasks natural join Tasks;

/*todo:
Jak udelat, aby v kolonce manager u produktu mohl být jen manažer???
Otestovat novou verzi*/
