-- auto-generated definition
create table cookbooks
(
  id          serial       not null
    constraint cookbooks_pkey
    primary key,
  title        varchar(255) not null,
  description varchar(255),
  body        text,
  image       varchar(255),
  user_id     integer      not null,
  created_at  timestamp default CURRENT_TIMESTAMP,
  updated_at  timestamp default CURRENT_TIMESTAMP
);

-- auto-generated definition
create table users
(
  id         serial       not null
    constraint users_pkey
    primary key,
  email      varchar(255) not null,
  username   varchar(255) not null,
  password   varchar(255) not null,
  token      varchar(255),
  created_at timestamp default CURRENT_TIMESTAMP,
  updated_at timestamp default CURRENT_TIMESTAMP
);

create unique index users_email_uindex
  on users (email);

create unique index users_username_uindex
  on users (username);

