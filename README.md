Prueba Tecnica, php 3.2

dos tablas con sus respectivos Crud, Con borrado logico para no mostrar productos en la interfaz del cliente (Home), Registro, login, dashboard protegido.

Query SQL.
insert into categories (name) values('Camperas'),('Remeras'),('Medias'), ('Pantalones')


insert into products (name, image, deleted, category_id) values('Campera 1', '9c528c4b2804b5cd95222f866a076d23.jpeg',0,1), ('Remera 1', '80df5caadc3382e68133ea4f377e20c4.jpeg',0,2),('Remera 2', '8f38da07708dbc39b9485cafaa0476d9.png',0,2),('Remera 3', 'f9ed169a4ee23a3df88a4389d9c6a818.png',0,2), ('Remera 4', 'd54af79ac640b5bcffeb75196aef021e.png',0,2), ('Remera 5','0963511848f0bc323836e03bcd4b103e.jpeg',0,2),('media 0', '489c0d3f5931e680e9339a513dc16b8e.jpeg', 0,3)
