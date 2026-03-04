-- ============================================================
-- TecnoFutura Academy — Seed Data
-- Run AFTER: tecnofutura_academy.sql → migration.sql → este archivo
-- ============================================================

USE tecnofutura_academy;

-- ============================================================
-- ROLES
-- ============================================================
INSERT IGNORE INTO roles (id_rol, nombre_rol, descripcion) VALUES
  (1, 'ADMIN',    'Administrador del sistema'),
  (2, 'PROFESOR', 'Docente de la plataforma'),
  (3, 'USUARIO',  'Alumno de la plataforma');

-- ============================================================
-- ADMIN  (contraseña: admin123)
-- ============================================================
INSERT IGNORE INTO usuarios (id_usuario, nombre_completo, correo_electronico, contrasena, fecha_nacimiento, id_rol, activo) VALUES
  (1, 'Administrador TecnoFutura', 'admin@tecnofutura.academy',
   '$2y$12$.56Nl1e9QBS9Oo5KOSTTfuGqurJ6g/.BN/WzkEcyZbJpDhrdKTh66',
   '1985-01-01', 1, 1);

-- ============================================================
-- PROFESORES  (contraseña: profesor123)
-- ============================================================
INSERT IGNORE INTO usuarios (id_usuario, nombre_completo, correo_electronico, contrasena, fecha_nacimiento, id_rol, activo) VALUES
  (2, 'Ing. Roberto Sánchez Ávila', 'rsanchez@tecnofutura.academy',
   '$2y$12$/13q6nmMH/6PnWWJ6wtx0eM1gFlnTI0jrgT1VxNPfRF594MvBIQwK',
   '1980-03-15', 2, 1),
  (3, 'Dra. Laura Mendoza Torres', 'lmendoza@tecnofutura.academy',
   '$2y$12$/13q6nmMH/6PnWWJ6wtx0eM1gFlnTI0jrgT1VxNPfRF594MvBIQwK',
   '1978-07-22', 2, 1);

INSERT IGNORE INTO docentes (id_docente, id_usuario, cedula_profesional, especialidad, biografia, anos_experiencia) VALUES
  (1, 2, 'SARR8003154M1', 'Sistemas Embebidos y Arduino',
   'Ingeniero en Sistemas con 10 anios de experiencia en sistemas embebidos y Arduino.',
   10),
  (2, 3, 'METL7807224F2', 'Arquitectura de Computadoras y Ensamblador',
   'Doctora en Ciencias Computacionales especializada en arquitectura x86/ARM.',
   8);

-- ============================================================
-- ALUMNOS  (contraseña: alumno123)
-- ============================================================
INSERT IGNORE INTO usuarios (id_usuario, nombre_completo, correo_electronico, contrasena, fecha_nacimiento, id_rol, activo) VALUES
  (4, 'Carlos Hernandez Lopez', 'carlos@demo.com',  '$2y$12$EBjUCdyFo5yh9EtdsRDUPO5F83geK84Q9VVPnxkr1Hhe.JNC67geu', '2000-05-12', 3, 1),
  (5, 'Maria Garcia Ramirez',   'maria@demo.com',   '$2y$12$EBjUCdyFo5yh9EtdsRDUPO5F83geK84Q9VVPnxkr1Hhe.JNC67geu', '2001-09-03', 3, 1),
  (6, 'Jose Martinez Flores',   'jose@demo.com',    '$2y$12$EBjUCdyFo5yh9EtdsRDUPO5F83geK84Q9VVPnxkr1Hhe.JNC67geu', '1999-11-28', 3, 1),
  (7, 'Ana Torres Vega',        'ana@demo.com',     '$2y$12$EBjUCdyFo5yh9EtdsRDUPO5F83geK84Q9VVPnxkr1Hhe.JNC67geu', '2002-02-14', 3, 1),
  (8, 'Luis Perez Morales',     'luis@demo.com',    '$2y$12$EBjUCdyFo5yh9EtdsRDUPO5F83geK84Q9VVPnxkr1Hhe.JNC67geu', '2000-08-30', 3, 1),
  (9, 'Demo Alumno',            'alumno@demo.com',  '$2y$12$EBjUCdyFo5yh9EtdsRDUPO5F83geK84Q9VVPnxkr1Hhe.JNC67geu', '2000-01-01', 3, 1);

INSERT IGNORE INTO alumnos (id_alumno, id_usuario, matricula, semestre) VALUES
  (1, 4, 'TF-2024-001', 4),
  (2, 5, 'TF-2024-002', 3),
  (3, 6, 'TF-2024-003', 5),
  (4, 7, 'TF-2024-004', 2),
  (5, 8, 'TF-2024-005', 6),
  (6, 9, 'TF-2024-006', 1);

-- ============================================================
-- CURSOS (8 cursos del PDF)
-- ============================================================
INSERT IGNORE INTO cursos (id_curso, nombre_curso, descripcion, id_docente, nivel, precio, duracion_horas, activo) VALUES
  (1, 'Introduccion a Arduino',
   'Aprende los fundamentos de la plataforma Arduino desde cero. Conoceras el hardware, el IDE de Arduino, y realizaras tus primeros proyectos con LEDs, botones y sensores basicos.',
   1, 'Básico', 0.00, 8, 1),
  (2, 'Arduino Desde Cero',
   'Curso completo para principiantes que quieren dominar Arduino. Cubre GPIO, comunicacion serial, PWM, librerias populares y proyectos practicos paso a paso.',
   1, 'Básico', 299.00, 20, 1),
  (3, 'Programacion Aritmetica y Logica en Arduino',
   'Domina las operaciones aritmeticas y logicas aplicadas a microcontroladores Arduino. Trabajaras con operadores bit a bit, registros de configuracion y calculos numericos.',
   1, 'Básico', 399.00, 25, 1),
  (4, 'Fundamentos de Arquitectura de Computadoras',
   'Comprende como funciona una computadora: ciclo de instruccion, ALU, registros, memoria, buses y el camino de datos.',
   2, 'Básico', 299.00, 22, 1),
  (5, 'Introduccion al Lenguaje Ensamblador',
   'Ingresa al mundo del lenguaje ensamblador x86/x86-64. Aprende la sintaxis, el modelo de memoria, los modos de direccionamiento y como escribir tus primeros programas.',
   2, 'Intermedio', 449.00, 30, 1),
  (6, 'Arduino Intermedio: Sensores y Actuadores',
   'Expande tus habilidades con Arduino utilizando sensores (temperatura, humedad, ultrasonido, IR) y actuadores (motores DC, servos, reles, displays LCD/OLED).',
   1, 'Intermedio', 499.00, 35, 1),
  (7, 'Programacion en Ensamblador Aplicada',
   'Desarrolla aplicaciones reales en lenguaje ensamblador: interrupciones, llamadas al sistema, manipulacion de cadenas, archivos y optimizacion de codigo.',
   2, 'Avanzado', 599.00, 40, 1),
  (8, 'Sistemas Embebidos con Arduino',
   'Disena y desarrolla sistemas embebidos completos con Arduino: gestion de energia, RTOS basico, comunicacion I2C/SPI/UART, bootloaders personalizados.',
   1, 'Avanzado', 699.00, 45, 1);

-- ============================================================
-- MATERIALES DEL CURSO (5 lecciones por curso)
-- ============================================================

-- Curso 1
INSERT IGNORE INTO materiales_curso (id_material,id_curso,titulo,descripcion,tipo_material,url_archivo,url_material,orden,duracion_minutos) VALUES
  (1,1,'Que es Arduino? Historia y ecosistema','Conoce la historia de Arduino, los modelos mas populares y el ecosistema','video','','https://www.youtube.com/embed/nL34zDTPkcs',1,18),
  (2,1,'Instalacion del IDE de Arduino','Descarga e instala el IDE de Arduino en Windows, macOS y Linux','video','','https://www.youtube.com/embed/sEFdqsmFoaE',2,15),
  (3,1,'Tu primer programa: Hola Mundo con LED','Comprende la estructura basica de un sketch y haz parpadear tu primer LED','video','','https://www.youtube.com/embed/BtLwoNJ6klE',3,22),
  (4,1,'Entradas digitales: botones y switches','Aprende a leer senales digitales y controlar LEDs con botones','video','','https://www.youtube.com/embed/7aP5KL5clWA',4,20),
  (5,1,'Comunicacion Serial con el Monitor','Envia y recibe datos por el puerto serial para depurar tus proyectos','video','','https://www.youtube.com/embed/xJx1H1JGuhM',5,16);

-- Curso 2
INSERT IGNORE INTO materiales_curso (id_material,id_curso,titulo,descripcion,tipo_material,url_archivo,url_material,orden,duracion_minutos) VALUES
  (6,2,'GPIO: Entradas y Salidas Digitales','Domina el control de pines GPIO con pull-up, pull-down y debounce','video','','https://www.youtube.com/embed/K8Xx8QvHxOE',1,28),
  (7,2,'Senales Analogicas y PWM','Lee sensores analogicos y controla brillo/velocidad con PWM','video','','https://www.youtube.com/embed/i8sqib1Anto',2,32),
  (8,2,'Comunicacion Serial UART','Protocolo UART: configuracion, envio y recepcion de datos','video','','https://www.youtube.com/embed/r3KfS7yyFVc',3,24),
  (9,2,'Librerias populares de Arduino','Uso de librerias: Servo, LiquidCrystal, Wire, SPI','documento','','',4,20),
  (10,2,'Proyecto final: Estacion Meteorologica','Integra sensor DHT11, pantalla LCD y alertas sonoras','video','','https://www.youtube.com/embed/OogldLc9uYc',5,45);

-- Curso 3
INSERT IGNORE INTO materiales_curso (id_material,id_curso,titulo,descripcion,tipo_material,url_archivo,url_material,orden,duracion_minutos) VALUES
  (11,3,'Representacion binaria y hexadecimal','Sistemas numericos aplicados a Arduino','texto','','',1,25),
  (12,3,'Operadores bit a bit: AND, OR, XOR, NOT','Manipulacion directa de bits en registros de Arduino','video','','https://www.youtube.com/embed/KT2gl4XQUT0',2,30),
  (13,3,'Registros de configuracion DDRx y PORTx','Acceso directo a registros del microcontrolador ATMEGA328P','video','','https://www.youtube.com/embed/6q1yVb21nxk',3,35),
  (14,3,'Aritmetica en punto fijo para sensores','Tecnicas de calculo sin FPU: escalado, redondeo y filtros','texto','','',4,28),
  (15,3,'Proyecto: Calculadora de operaciones logicas','Display 7 segmentos y teclado matricial','video','','',5,40);

-- Curso 4
INSERT IGNORE INTO materiales_curso (id_material,id_curso,titulo,descripcion,tipo_material,url_archivo,url_material,orden,duracion_minutos) VALUES
  (16,4,'El ciclo fetch-decode-execute','Como el procesador ejecuta cada instruccion paso a paso','video','','https://www.youtube.com/embed/jFDMZpkUWCw',1,30),
  (17,4,'Registros de proposito general y especial','Registros: EAX, EBX, ECX, EDX, ESP, EBP, EIP, EFLAGS','video','','https://www.youtube.com/embed/NKTfNv2T0FE',2,35),
  (18,4,'Organizacion de la memoria RAM','Segmentos: codigo, datos, stack y heap','texto','','',3,28),
  (19,4,'El bus de datos, direccion y control','Comunicacion entre CPU, memoria y perifericos','video','','https://www.youtube.com/embed/XdkHiMpB_qE',4,32),
  (20,4,'Jerarquia de memoria: cache, RAM y disco','Localidad, hit rate y su impacto en rendimiento','texto','','',5,22);

-- Curso 5
INSERT IGNORE INTO materiales_curso (id_material,id_curso,titulo,descripcion,tipo_material,url_archivo,url_material,orden,duracion_minutos) VALUES
  (21,5,'Introduccion a x86: historia y arquitectura','Del 8086 al x86-64: evolucion y compatibilidad','video','','https://www.youtube.com/embed/75gBFiFtAb8',1,28),
  (22,5,'Sintaxis NASM y estructura de un programa','Secciones .text, .data, .bss y directivas NASM','video','','https://www.youtube.com/embed/VQAKkuLL31g',2,35),
  (23,5,'Instrucciones de movimiento y aritmeticas','MOV, ADD, SUB, MUL, DIV, INC, DEC y modos de direccionamiento','video','','https://www.youtube.com/embed/wLXIWKUWpSs',3,40),
  (24,5,'Control de flujo: JMP, CMP, JE, JNE, LOOP','Condicionales y ciclos en ensamblador','video','','https://www.youtube.com/embed/75gBFiFtAb8',4,38),
  (25,5,'Stack y procedimientos: PUSH, POP, CALL, RET','Manejo de la pila y llamada a subrutinas','video','','https://www.youtube.com/embed/RU4ygBBSMkI',5,42);

-- Curso 6
INSERT IGNORE INTO materiales_curso (id_material,id_curso,titulo,descripcion,tipo_material,url_archivo,url_material,orden,duracion_minutos) VALUES
  (26,6,'Sensores DHT11/DHT22','Integracion, calibracion y lectura de temperatura y humedad','video','','',1,30),
  (27,6,'Sensor ultrasonico HC-SR04','Tecnica de tiempo de vuelo y aplicaciones en robotica','video','','',2,28),
  (28,6,'Control de motores DC y servomotores','Driver L298N, libreria Servo y control PID basico','video','','',3,38),
  (29,6,'Displays LCD I2C y OLED SSD1306','Interfaz de usuario con pantallas alfanumericas y graficas','video','','',4,35),
  (30,6,'Proyecto: Robot seguidor de linea','Robot autonomo con sensores IR y control diferencial','video','','',5,55);

-- Curso 7
INSERT IGNORE INTO materiales_curso (id_material,id_curso,titulo,descripcion,tipo_material,url_archivo,url_material,orden,duracion_minutos) VALUES
  (31,7,'Interrupciones de hardware en x86','IDT, IVT, handlers de interrupciones y el PIC 8259','video','','',1,40),
  (32,7,'Llamadas al sistema Linux (syscall)','Interfaz con el kernel: read, write, open, fork','video','','',2,45),
  (33,7,'Manipulacion de cadenas: MOVS, CMPS, SCAS','Instrucciones de string x86 con prefijos REP','video','','',3,35),
  (34,7,'Acceso a archivos en ensamblador','Abrir, leer, escribir y cerrar archivos via syscalls','texto','','',4,38),
  (35,7,'Optimizacion: instrucciones SIMD y SSE','Procesamiento paralelo con SSE2/SSE3','video','','',5,50);

-- Curso 8
INSERT IGNORE INTO materiales_curso (id_material,id_curso,titulo,descripcion,tipo_material,url_archivo,url_material,orden,duracion_minutos) VALUES
  (36,8,'Gestion de energia y modos Sleep','Reducir consumo: idle mode, power-down, watchdog timer','video','','',1,35),
  (37,8,'RTOS basico con FreeRTOS en Arduino','Tareas, semaforos, mutexes y comunicacion entre tareas','video','','',2,50),
  (38,8,'Comunicacion I2C y SPI profesional','Topologia, timing, multi-master y resolucion de conflictos','video','','',3,42),
  (39,8,'Bootloaders y programacion in-circuit','Personaliza el bootloader y programa sin el IDE','texto','','',4,38),
  (40,8,'Proyecto Final: Sistema de Monitoreo IoT','Dispositivo IoT completo con MQTT, WiFi y dashboard web','video','','',5,65);

-- ============================================================
-- INSCRIPCIONES
-- ============================================================
INSERT IGNORE INTO inscripciones (id_inscripcion,id_curso,id_alumno,estado,progreso,fecha_inscripcion) VALUES
  (1,1,1,'Certificado',100.00,DATE_SUB(NOW(),INTERVAL 30 DAY)),
  (2,5,1,'En curso',   60.00, DATE_SUB(NOW(),INTERVAL 15 DAY)),
  (3,1,2,'Completado', 100.00,DATE_SUB(NOW(),INTERVAL 20 DAY)),
  (4,2,2,'En curso',   40.00, DATE_SUB(NOW(),INTERVAL 10 DAY)),
  (5,4,3,'En curso',   80.00, DATE_SUB(NOW(),INTERVAL 12 DAY)),
  (6,6,4,'Inscrito',   0.00,  DATE_SUB(NOW(),INTERVAL 2 DAY)),
  (7,1,6,'En curso',   40.00, DATE_SUB(NOW(),INTERVAL 5 DAY));

-- ============================================================
-- PAGOS
-- ============================================================
INSERT IGNORE INTO pagos (id_pago,id_inscripcion,monto,metodo_pago,referencia_pago,estado_pago,fecha_pago) VALUES
  (1,2,449.00,'tarjeta',      'TF-DEMO0000001','completado',DATE_SUB(NOW(),INTERVAL 15 DAY)),
  (2,4,299.00,'paypal',       'TF-DEMO0000002','completado',DATE_SUB(NOW(),INTERVAL 10 DAY)),
  (3,5,299.00,'tarjeta',      'TF-DEMO0000003','completado',DATE_SUB(NOW(),INTERVAL 12 DAY)),
  (4,6,499.00,'efectivo',     'TF-DEMO0000004','completado',DATE_SUB(NOW(),INTERVAL 2 DAY));

-- ============================================================
-- CERTIFICADO demo
-- ============================================================
INSERT IGNORE INTO certificados (id_certificado,id_inscripcion,codigo_certificado) VALUES
  (1,1,'TFDEMOARDUINO01');

-- ============================================================
-- PROGRESO DE LECCIONES
-- ============================================================
INSERT IGNORE INTO progreso_lecciones (id_inscripcion,id_material,completado,fecha_completado) VALUES
  (1,1,1,DATE_SUB(NOW(),INTERVAL 29 DAY)),(1,2,1,DATE_SUB(NOW(),INTERVAL 28 DAY)),
  (1,3,1,DATE_SUB(NOW(),INTERVAL 27 DAY)),(1,4,1,DATE_SUB(NOW(),INTERVAL 26 DAY)),
  (1,5,1,DATE_SUB(NOW(),INTERVAL 25 DAY)),
  (2,21,1,DATE_SUB(NOW(),INTERVAL 14 DAY)),(2,22,1,DATE_SUB(NOW(),INTERVAL 13 DAY)),
  (2,23,1,DATE_SUB(NOW(),INTERVAL 12 DAY)),
  (3,1,1,DATE_SUB(NOW(),INTERVAL 19 DAY)),(3,2,1,DATE_SUB(NOW(),INTERVAL 18 DAY)),
  (3,3,1,DATE_SUB(NOW(),INTERVAL 17 DAY)),(3,4,1,DATE_SUB(NOW(),INTERVAL 16 DAY)),
  (3,5,1,DATE_SUB(NOW(),INTERVAL 15 DAY)),
  (5,16,1,DATE_SUB(NOW(),INTERVAL 11 DAY)),(5,17,1,DATE_SUB(NOW(),INTERVAL 10 DAY)),
  (5,18,1,DATE_SUB(NOW(),INTERVAL 9 DAY)),(5,19,1,DATE_SUB(NOW(),INTERVAL 8 DAY));

SELECT 'Seed data inserted successfully!' AS status;
