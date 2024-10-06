# biblio
 gestion d'utilisateurs et livres en mvc

 Mise en place les bases d'une application MVC pour la gestion d'utilisateurs et de livres (CRUD). Mise en place des mesures de sécurité dans cette application 
 Impératifs : 3 emprunts par utilisateur pour une durée de 3 semaines.

Les fichiers de connections à la base de données (/Core/Dbconnect et connect) sont anonymisés. Des liens ont été modifiés (et sont donc incorrects).


Les tables :

#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


#------------------------------------------------------------
# Table: emprunteur
#------------------------------------------------------------

CREATE TABLE emprunteur(
        id_emprunter Int  Auto_increment  NOT NULL ,
        nom          Varchar (100) NOT NULL ,
        email        Varchar (100) NOT NULL ,
        password     Varchar (255) NOT NULL
	,CONSTRAINT emprunteur_PK PRIMARY KEY (id_emprunter)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: livre
#------------------------------------------------------------

CREATE TABLE livre(
        id           Int  Auto_increment  NOT NULL ,
        titre        Varchar (100) NOT NULL ,
        auteur       Varchar (100) NOT NULL ,
        description  Text NOT NULL ,
        couverture   Varchar (255) NOT NULL ,
        id_emprunter Int
	,CONSTRAINT livre_PK PRIMARY KEY (id)

	,CONSTRAINT livre_emprunteur_FK FOREIGN KEY (id_emprunter) REFERENCES emprunteur(id_emprunter)
)ENGINE=InnoDB;

