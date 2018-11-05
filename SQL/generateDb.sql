create user sbapvr@localhost identified by 'sbapvr';
create database syssbapvr;
create database sbapvr;
create database vincoli;
grant all privileges on syssbapvr.* to sbapvr@localhost;
grant all privileges on sbapvr.* to sbapvr@localhost;
grant all privileges on vincoli.* to sbapvr@localhost;
...........
import databases
.................
DROP TABLE `exp_modelli`, `exp_pratiche`, `exp_zone`, `languages`;
