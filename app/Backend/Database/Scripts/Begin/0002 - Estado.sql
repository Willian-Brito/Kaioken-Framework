
IF ( NOT EXISTS( SELECT Est.IdEstado
                   FROM Estado AS Est) )
THEN

    INSERT INTO Estado VALUES(1,'AC','Acre');
    INSERT INTO Estado VALUES(2,'AL','Alagoas');
    INSERT INTO Estado VALUES(3,'AP','Amapá');
    INSERT INTO Estado VALUES(4,'AM','Amazonas');
    INSERT INTO Estado VALUES(5,'BA','Bahia');
    INSERT INTO Estado VALUES(6,'CE','Ceará');
    INSERT INTO Estado VALUES(7,'DF','Distrito Federal');
    INSERT INTO Estado VALUES(8,'ES','Espírito Santo');
    INSERT INTO Estado VALUES(9,'GO','Goiás');
    INSERT INTO Estado VALUES(10,'MA','Maranhão');
    INSERT INTO Estado VALUES(11,'MT','Mato Grosso');
    INSERT INTO Estado VALUES(12,'MS','Mato Grosso do Sul');
    INSERT INTO Estado VALUES(13,'MG','Minas Gerais');
    INSERT INTO Estado VALUES(14,'PA','Pará');
    INSERT INTO Estado VALUES(15,'PB','Paraíba');
    INSERT INTO Estado VALUES(16,'PR','Paraná');
    INSERT INTO Estado VALUES(17,'PE','Pernambuco');
    INSERT INTO Estado VALUES(18,'PI','Piauí');
    INSERT INTO Estado VALUES(19,'RJ','Rio de Janeiro');
    INSERT INTO Estado VALUES(20,'RN','Rio Grande do Norte');
    INSERT INTO Estado VALUES(21,'RS','Rio Grande do Sul');
    INSERT INTO Estado VALUES(22,'RO','Rondônia');
    INSERT INTO Estado VALUES(23,'RR','Roraima');
    INSERT INTO Estado VALUES(24,'SC','Santa Catarina');
    INSERT INTO Estado VALUES(25,'SP','São Paulo');
    INSERT INTO Estado VALUES(26,'SE','Sergipe');
    INSERT INTO Estado VALUES(27,'TO','Tocantins');
    INSERT INTO Estado VALUES(28,'EX','Exterior');

END IF;
