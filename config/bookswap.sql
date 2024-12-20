CREATE TABLE `Autor` (
  `Aut_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Aut_nom` varchar(100) NOT NULL,
  `Aut_apellido` varchar(100) NOT NULL
);

CREATE TABLE `Chat` (
  `chat_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `chat_inter_id` int(11) NOT NULL
);

CREATE TABLE `Comuna` (
  `Com_nom` varchar(30) NOT NULL PRIMARY KEY,
  `Oculto` tinyint(1) NOT NULL
);

CREATE TABLE `Editorial` (
  `Edit_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Edit_nom` varchar(30) NOT NULL,
  `Edit_idioma` varchar(30) NOT NULL,
  `Oculto` tinyint(1) NOT NULL
);

CREATE TABLE `Estado` (
  `Est_nom` varchar(50) NOT NULL PRIMARY KEY,
  `Oculto` tinyint(1) NOT NULL
);

CREATE TABLE `Etiqueta` (
  `Etiq_nom` varchar(20) NOT NULL PRIMARY KEY,
  `Oculto` tinyint(1) NOT NULL
);

CREATE TABLE `Idioma` (
  `Idio_nom` varchar(30) NOT NULL PRIMARY KEY,
  `Oculto` tinyint(1) NOT NULL
);

CREATE TABLE `Intercambio` (
  `Inter_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Lib_cod` int(11) NOT NULL
);

CREATE TABLE `Intercambio_estado` (
  `Ines_id` int(11) NOT NULL PRIMARY KEY,
  `Ines_nom` varchar(50) NOT NULL
);

CREATE TABLE `Lector` (
  `Lec_mail` varchar(50) NOT NULL PRIMARY KEY,
  `Lec_username` varchar(50) NOT NULL,
  `Lec_nom` varchar(50) NOT NULL,
  `Lec_apellido` varchar(30) NOT NULL,
  `Lec_comuna` varchar(30) NOT NULL,
  `Lec_cal` int(11) NOT NULL,
  `Lec_img` blob NOT NULL,
  `Oculto` tinyint(1) NOT NULL
);

CREATE TABLE `Lector_intercambio` (
  `Lecin_intercambio_id` int(11) NOT NULL,
  `Lecin_usu_mail` varchar(50) NOT NULL,
  `Lecin_inter_cal` int(11) NOT NULL
);

CREATE TABLE `Libro` (
  `Lib_cod` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Lib_autor` varchar(50) NOT NULL,
  `Lib_editorial` varchar(50) NOT NULL,
  `Lib_idioma` varchar(50) NOT NULL,
  `Lib_etiqueta` varchar(50) NOT NULL,
  `Lib_nom` varchar(50) NOT NULL,
  `Lib_estado` int(11) NOT NULL,
  `Lib_valorest` int(11) NOT NULL,
  `Lib_Usu_mail` varchar(50) NOT NULL,
  `Oculto` tinyint(1) NOT NULL
);

CREATE TABLE `Libro_Autor` (
  `Liba_Aut_id` int(11) NOT NULL,
  `Liba_Lib_cod` int(11) NOT NULL
);

CREATE TABLE `Libro_Editorial` (
  `Libed_Edit_id` int(11) NOT NULL,
  `Libed_Lib_cod` int(11) NOT NULL,
  `Libed_Edicion` int(11) NOT NULL,
  `Libed_anyo_edicion` int(11) NOT NULL
);

CREATE TABLE `Libro_etiqueta` (
  `Libet_Lib_cod` int(11) NOT NULL PRIMARY KEY,
  `Libet_etiq_nom` varchar(20) NOT NULL
);

CREATE TABLE `Lista_deseos` (
  `Listd_usu_id` varchar(50) NOT NULL,
  `Listd_Lib_cod` int(11) NOT NULL
);

CREATE TABLE `Mensaje` (
  `Men_mensaje` varchar(200) NOT NULL,
  `Men_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Men_fecha` datetime NOT NULL,
  `Men_usu_id` varchar(50) NOT NULL,
  `Men_chat_id` int(11) NOT NULL,
  `Oculto` tinyint(1) NOT NULL
);

CREATE TABLE `Moderador` (
  `Mod_mail` varchar(50) NOT NULL PRIMARY KEY,
  `Oculto` tinyint(1) NOT NULL
);

CREATE TABLE `Reportes` (
  `Rep_Usu_reporta` varchar(50) NOT NULL,
  `Rep_Usu_reportado` varchar(50) NOT NULL ,
  `Rep_Moderador_mail` varchar(50) NOT NULL,
  `Rep_Tratado` tinyint(1) NOT NULL
); 

CREATE TABLE `T_usuario` (
  `Tus_Tipo_usu` varchar(20) NOT NULL PRIMARY KEY,
  `Oculto` tinyint(1) NOT NULL
);

CREATE TABLE `Usuario` (
  `Usu_mail` varchar(50) NOT NULL PRIMARY KEY,
  `Usu_pass` varchar(50) NOT NULL,
  `Usu_T_usuario` varchar(20) NOT NULL,
  `Oculto` int(11) NOT NULL
);

ALTER TABLE `Reportes`
ADD CONSTRAINT fk_usu_reporta
FOREIGN KEY (Rep_Usu_reporta)
REFERENCES Lector(Lec_mail),
ADD CONSTRAINT fk_usu_reportado
FOREIGN KEY (Rep_Usu_reportado)
REFERENCES Lector(Lec_mail),
ADD CONSTRAINT fk_Moderador_mail
FOREIGN KEY (Rep_Moderador_mail)
REFERENCES Moderador(Mod_mail);

ALTER TABLE `Usuario`
ADD CONSTRAINT fk_T_usuario
FOREIGN KEY (Usu_T_usuario)
REFERENCES T_usuario(Tus_Tipo_usu);

ALTER TABLE `Lector`
ADD CONSTRAINT fk_lec_mail
FOREIGN KEY (Lec_mail)
REFERENCES Usuario(Usu_mail),
ADD CONSTRAINT fk_lec_comuna
FOREIGN KEY (Lec_comuna)
REFERENCES Comuna(Com_nom);

ALTER TABLE `Lista_deseos`
ADD CONSTRAINT fk_usu_id  
FOREIGN KEY (Listd_usu_id)
REFERENCES Lector(Lec_mail),
ADD CONSTRAINT fk_Lib_cod
FOREIGN KEY (Listd_Lib_cod)
REFERENCES Libro(Lib_cod);

ALTER TABLE `Lector_intercambio`
ADD CONSTRAINT fk_intercambio_id
FOREIGN KEY (Lecin_intercambio_id)
REFERENCES Intercambio(inter_id),
ADD CONSTRAINT fk_lecin_usu_mail
FOREIGN KEY (Lecin_usu_mail)
REFERENCES Lector(Lec_mail);

ALTER TABLE `Intercambio_estado`
ADD CONSTRAINT fk_inter_id
FOREIGN KEY (Ines_id)
REFERENCES Intercambio(inter_id),
ADD CONSTRAINT fk_estado_nom
FOREIGN KEY (Ines_nom)
REFERENCES Estado(Est_nom);

ALTER TABLE `Chat`
ADD CONSTRAINT fk_chat_inter_id
FOREIGN KEY (chat_inter_id)
REFERENCES Intercambio(inter_id);

ALTER TABLE `Mensaje`
ADD CONSTRAINT fk_mensaje_usu_id
FOREIGN KEY (Men_usu_id)
REFERENCES Lector(Lec_mail);

ALTER TABLE `Libro`
ADD CONSTRAINT fk_LIB_usu_mail
FOREIGN KEY (Lib_Usu_mail)
REFERENCES Lector(Lec_mail);

ALTER TABLE `Libro_Autor`
ADD CONSTRAINT fk_LA_Lib_cod
FOREIGN KEY (Liba_Lib_cod)
REFERENCES Libro (Lib_cod),
ADD CONSTRAINT fk_LA_Aut_id
FOREIGN KEY (Liba_Aut_id)
REFERENCES Autor (Aut_id);

ALTER TABLE `Libro_etiqueta`
ADD CONSTRAINT fk_LE_Lib_cod
FOREIGN KEY (Libet_Lib_cod)
REFERENCES Libro(Lib_cod),
ADD CONSTRAINT fk_etiq_nom
FOREIGN KEY (Libet_etiq_nom)
REFERENCES Etiqueta(etiq_nom);

ALTER TABLE `Libro_Editorial`
ADD CONSTRAINT fk_LED_Edit_id
FOREIGN KEY (Libed_Edit_id)
REFERENCES Editorial(Edit_id),
ADD CONSTRAINT fk_ED_Lib_cod
FOREIGN KEY (Libed_Lib_cod)
REFERENCES Libro(Lib_cod);

ALTER TABLE `Editorial`
ADD CONSTRAINT fk_Edit_id
FOREIGN KEY (Edit_idioma)
REFERENCES Idioma(Idio_nom);

ALTER TABLE `Moderador`
ADD CONSTRAINT fk_Mod_mail
FOREIGN KEY (Mod_mail)
REFERENCES Usuario(Usu_mail);
