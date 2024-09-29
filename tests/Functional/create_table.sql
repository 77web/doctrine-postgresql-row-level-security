CREATE TABLE "user"
(
    id uuid primary key,
    name varchar(255) not null
);
CREATE TABLE dog
(
    id uuid primary key,
    owner_id uuid not null,
    name varchar(255) not null
);
ALTER TABLE dog ADD CONSTRAINT user_id_fk FOREIGN KEY (owner_id) REFERENCES "user" (id);
