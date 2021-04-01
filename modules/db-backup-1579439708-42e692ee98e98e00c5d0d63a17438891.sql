DROP TABLE IF EXISTS filters;

CREATE TABLE `filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `f_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO filters VALUES("1","Z370"),
("2","Z390"),
("3","B450"),
("4","Seagate"),
("5","WD"),
("6","Corsair"),
("7","Z400"),
("8","Z500"),
("9","nvidia"),
("10","nvidia2");



DROP TABLE IF EXISTS products;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL,
  `image` text NOT NULL,
  `f_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `f_id` (`f_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`f_id`) REFERENCES `filters` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8;

INSERT INTO products VALUES("60","Asus ROG STRIX Z370-F GAMING","Разъем LGA1151 для процессоров Intel Core восьмого поколения\nВстроенная цветная подсветка Aura, расширяемая с помощью светодиодных лент и синхронизируемая с другими Aura-совместимыми устройствами\n","2700000","Asus ROG STRIX Z370-F GAMING.png","1"),
("61","Asus ROG STRIX Z370-E GAMING","Разъем LGA1151 для процессоров Intel Core восьмого поколения\nВстроенная цветная подсветка Aura, синхронизируемая с другими Aura-совместимыми устройствами","2920000","Asus ROG STRIX Z370-E GAMING.png","1"),
("62","Asus ROG STRIX Z390-H GAMING","В материнской плате ROG Strix Z390-H Gaming вновь используется ставшая классической красно-черная цветовая схема ROG, которая теперь сочетается с футуристическим рисунком в виде текста. Эта плата обладает множеством передовых технологий и инноваций, в том числе высокоскоростными интерфейсами и эксклюзивным программным обеспечением ROG","2400000","Asus ROG STRIX Z390-H GAMING.png","1"),
("63","Asus ROG STRIX Z390-F GAMING","Разъем LGA1151 для процессоров Intel Core восьмого поколения\nВстроенная цветная подсветка Aura, расширяемая с помощью светодиодных лент и синхронизируемая с другими Aura-совместимыми устройствами","2550000","Asus ROG STRIX Z390-F GAMING.png","1"),
("64","Asus ROG STRIX Z390-E GAMING","Разъем LGA1151 для процессоров Intel Core восьмого и девятого поколений\nСовременные интерфейсы, включая M.2 (два разъема) и USB 3.1 Gen2 Type-A\nСетевой контроллер Gigabit Ethernet от Intel с эксклюзивными технологиями ASUS LANGuard и GameFirst","3000000","Asus ROG STRIX Z390-E GAMING.png","1"),
("65","Asus ROG MAXIMUS X HERO (WI-FI AC)","Разъем LGA1151 для процессоров Intel Core восьмого поколения\nСинхронизируемая подсветка Aura Sync – теперь с поддержкой программируемых светодиодных лент\nРадиатор для твердотельного накопителя в слоте M.2\nВысококачественный встроенный аудиокодек SupremeFX и программное обеспечение Sonic Studio III для настройки звучания","3800000","Asus ROG MAXIMUS X HERO (WI-FI AC).png","1"),
("66","Asus ROG MAXIMUS XI HERO ","Предустановленная панель ROG\nКнопка Clr CMOS\nКнопка BIOS Flashback\n1 x разъем PS/2\nдля клавиатуры и мыши\n\nDisplayPort\n‧ HDMI, DisplayPort\nIntel® I219-V (Gigabit Ethernet)\n‧ ROG GameFirst V\n‧ LANGuard\n\n4 x USB 3.1 Gen 2\n‧ 3 x Type-A, 1 x Type-C\n\n2 x USB 2.0\n2 x USB 3.1 Gen1","3350000","Asus ROG MAXIMUS XI HERO.png","1"),
("67","Asus ROG MAXIMUS XI HERO (WI-FI)","Предустановленная панель ROG\nКнопка Clr CMOS\nКнопка BIOS Flashback\n1 x разъем PS/2\nдля клавиатуры и мыши\nDisplayPort\nHDMI, DisplayPort\nIntel® I219-V (Gigabit Ethernet)\n ROG GameFirst V\n LANGuard\n4 x USB 3.1 Gen 2\n‧ 3 x Type-A, 1 x Type-C\n2 x USB 2.0\n2 x USB 3.1 Gen1\nIntel® Wireless-AC 9560\n Wi-Fi 802.11ac с MU-MIMO (2х2)\nАудиокодек SupremeFX S1220\n ЦАП ESS® ES9023P\n Определение импеданса подключаемых наушников\nСтереовыход с соотношением сигнал/шум 120 дБ\n‧ Линейный вход с соотношением сигнал/шум 113 дБ\nSonic Studio III\n・ Sonic Studio Link\nSonic Radar III\n","3700000","Asus ROG MAXIMUS XI HERO (WI-FI).png","1"),
("68","Asus ROG MAXIMUS XI FORMULA","Ватерблок CrossChill EK III\nПредустановленная заглушка\nКнопка Clear CMOS\nКнопка BIOS Flashback\nHDMI 1.4b\n5G Ethernet (Aquantia AQC111C)\nGigabit Ethernet (Intel I219-V)\nROG GameFirst V\nLANGuard\n10 портов USB 3.1 Gen1\n4 порта USB 3.1 Gen2\n(Type-A + Type-C)\nIntel® Wireless-AC 9560\nWi-Fi 802.11a/b/g/n/ac с MU-MIMO (2х2)","5550000","Asus ROG MAXIMUS XI FORMULA.png","1"),
("70","Asus ROG RAMPAGE VI APEX","ЭКСТРЕМАЛЬНОЕ РЕШЕНИЕ ДЛЯ ЛЮБИТЕЛЕЙ РАЗГОНА\nРазработанная специалистами ROG в тесном сотрудничестве с ведущими оверклокерами, материнская плата Rampage VI Apex предназначена для того, чтобы ставить новые рекорды разгона. Оригинальные инженерные решения, такие как оптимизированная 4-канальная подсистема памяти и инновационные модули DIMM.2 для подключения и охлаждения твердотельных накопителей, сочетаются в этой модели со смелым дизайном, ключевыми элементами которого являются X-образная форма печатной платы и красочная подсветка Aura. Rampage VI Apex – идеальное решение для разгонных экспериментов на базе платформы X299.","5600000","Asus ROG RAMPAGE VI APEX.png","1"),
("71","Asus PRIME AMD B450-PLUS","Материнская плата ASUS серии Prime B450 не только станет надежным фундаментом для вашего первого компьютера, но и откроет широкие перспективы его апгрейда по мере увеличения ваших амбиций. Поддерживая всю функциональность новейших процессоров AMD Ryzen, она дополняет ее инновационными решениями от ASUS, такими как высококачественный встроенный звук, автоматическая оптимизация параметров системы, гибкая регулировка вентиляторов. Материнские платы ASUS Prime B450 – прекрасный выбор для всех, кто хочет легко, быстро и недорого собрать свой собственный ПК.","1400000","Asus PRIME AMD B450-PLUS.png","1"),
("72","Asus PRIME AMD X399-A ","Разъем Socket TR4 для процессоров AMD Ryzen Threadripper\n5-сторонняя оптимизация компьютера одним щелчком мыши\nИнтеллектуальная система охлаждения с регулировкой вентиляторов и водяных помп через утилиту Fan Xpert 4 или интерфейс UEFI BIOS\nСовременные интерфейсы (U.2, M.2, USB 3.1 Gen2)\n","3700000","Asus PRIME AMD X399-A.png","1"),
("73","Asus TUF AMD B450-PLUS GAMING","Транзисторы TUF\nDIGI+\nДроссели TUF\nКонденсаторы TUF\nHDMI\nПорт USB 3.1 Gen2\nРазъем USB Type-C\nЗащитный кожух TUF\nСлот PCI Express 3.0\nПоддержка CrossFireX\nТехнология SafeSlot\nАудиокодек Realtek ALC887\nПространственное звучание DTS Custom\n‧Защитный кожух на аудиокодеке\n‧Экранирование\n‧Разделение аудиоканалов\n Высококачественные японские конденсаторы\nПоддержка памяти DDR4-3200 (в режиме разгона)\nПроцессорный разъем AM4\nЧипсет AMD B450\nИнтерфейс M.2 (32 Гбит/с)\n(режимы SATA и PCIe x4)\nПорты SATA 6 Гбит/с\nФронтальный интерфейс USB 3.1 Gen1","1350000","Asus TUF AMD B450-PLUS GAMING.png","1"),
("74","Asus TUF AMD B450-PRO GAMING","Транзисторы TUF\nDIGI+\nДроссели TUF\nКонденсаторы TUF\nHDMI\nПорт USB 3.1 Gen2\nРазъем USB Type-C\nЗащитный кожух TUF\nСлот PCI Express 3.0\nПоддержка CrossFireX\nТехнология SafeSlot\nАудиокодек Realtek ALC887\nПространственное звучание DTS Custom\n‧ Защитный кожух на аудиокодеке\n‧ Экранирование\n‧ Разделение аудиоканалов\n‧ Высококачественные японские конденсаторы\nПоддержка памяти DDR4-3200 (в режиме разгона)\nПроцессорный разъем AM4\nЧипсет AMD B450\nИнтерфейс M.2 (32 Гбит/с)\n(режимы SATA и PCIe x4)\nПорты SATA 6 Гбит/с\nФронтальный интерфейс USB 3.1 Gen1","1450000","Asus TUF AMD B450-PRO GAMING.png","1"),
("75","Asus ROG STRIX AMD B450-F GAMING","Предустановленная заглушка\n2 порта USB 3.1 Gen2\n‧ 2 разъема Type-A\nВидеоинтерфейсы\n‧ HDMI 2.0\n‧ DisplayPort\nКонтроллер Intel I211-AT (Gigabit Ethernet)\n‧ ROG GameFirst\n‧ LANGuard\nПоддержка CrossFireX\n2 слота PCIe 3.0 x16 (режимы x16, x8/x4)\n1 слот PCIe 2.0 x16 (режим x4)\n3 слота PCIe 2.0 x1\nАудиокодек SupremeFX S1220A\n‧ Определение импеданса\n‧ Высококачественные аудиовходы\nи выходы\n‧ Экранирование\n‧ Двойной усилитель\nПоддержка памяти DDR4-3200 (в режиме разгона)\n‧ Четыре слота DIMM (2 канала)\nРазъем AM4 для процессоров AMD: Ryzen 2-го и 1-го поколений, Ryzen с графическим ядром Radeon Vega\n6 портов SATA 6 Гбит/с\n2 разъема M.2 Socket 3\n‧ 1 разъем формата 2242-2280, режимы SATA и PCIe 3.0 x4\n‧ 1 разъем формата 2242-22110, режим PCIe 3.0 x4\n1 разъем для термодатчика\n2 разъема для неадресуемых светодиодных лент","1600000","Asus ROG STRIX AMD B450-F GAMING.png","1"),
("76","Asus ROG STRIX AMD B450-I GAMING","HDMI 2.0\n4 x USB 3.1 Gen 1\nIntel® I211-AT\n‧ ROG GameFirst IV\n‧ LANGuard\n2 x USB 3.1 Gen 2\n‧ 2 x Type-A\nWi-Fi 802.11 a/b/g/n/ac\n‧Антенная конфигурация 2х2,\nтехнология MU-MIMO\n‧Bluetooth v4.2\n3 аудиоразъема со\nсветодиодной подсветкой\nАудиокодек SupremeFX S1220A\n‧ Определение импеданса\n‧ Высококачественные аудиовходы\nи выходы\n‧ Двойной усилитель\n1 x PCIe 3.0 x 16\n1 адресуемый разъем системы подсветки Aura\n1 неадресуемый разъем системы подсветки Aura\nРазъем AM4 для процессоров AMD: Ryzen 2-го и 1-го поколений\nRyzen с графическим ядром Radeon Vega\nПоддержка памяти DDR4-3600 (в режиме разгона)\n‧ 2 x DIMM (2 канала)\n4 x SATA 6 Гбит/с\nРазъем для термодатчика\n1 x USB 3.1 Gen1\n1 x USB 2.0\nКомбо-карта (звук + M.2)\n‧ 1 разъем M.2 для устройств формата 2242-2280, режимы SATA и PCIe 3.0 x4\n 1 разъем M.2 для устройств формата 2242-2280, режим PCIe 3.0 x4","2000000","Asus ROG STRIX AMD B450-I GAMING.png","1"),
("77","Asus ROG STRIX AMD B450-E GAMING","Предустановленная заглушка\nDisplay port\n‧ HDMI 2.0\n‧ DP\n2 x USB 3.1 Gen2\n‧ 2 x Type A\nIntel I211-AT\n(Gigabit Ethernet)\n‧ ROG GameFirst\n‧ LANGuard\nПоддержка технологии\nCrossFireX\n2 x PCIe 3.0 x 16\n(режим x16 или x8/x4)\n1 x PCIe 2.0 x 16 (режим x4)\n3 x PCIe 2.0 x 1\nСлот M.2 (с ключом E Key)\nдля беспроводного адаптера\nБеспроводной адаптер Intel\nWireless-AC 9260 в комплекте\nАудиокодек SupremeFX S1220A\n‧ Определение импеданса\n‧ Высококачественные разъемы\n‧ Экранирование\n‧ Двойной усилитель\nПоддержка памяти DDR4-3533 (в режиме разгона)\n‧ Четыре слота DIMM (2 канала)\nРазъем AM4 для процессоров AMD: Ryzen 2-го и 1-го поколений\nRyzen/Ahtlon с графическим ядром Radeon Vega\n2 x M.2 Socket 3\n‧ 1 разъем формата 2242-2280, режимы SATA и PCIe 3.0 x4\n‧ 1 разъем формата 2242-22110, режим PCIe 3.0 x4\n6 x SATA 6Гбит/с\n1 радиатор для слота M.2\n1 x Разъем для термодатчика\n2 разъема для неадресуемых светодиодных лент\n1 разъем для адресуемой светодиодной ленты","2050000","Asus ROG STRIX AMD B450-E GAMING.png","1"),
("79"," GigaByte H110M-H DDR4 LGA1151","Двухканальный режим работы ОЗУ DDR4, 2 DIMM-разъема\nСовместимость с процессорами Intel® Core™ 6- и 7-поколения\nПорты HDMI 1.4 и D-sub с поддержкой мультидисплейных конфигураций\nЭкранирование звуковой подсистемы и только высококачественные конденсаторы серии Audio\nRealtek® GbE LAN-контроллер и утилита cFosSpeed Internet Accelerator\nНабор фирменных утилит и приложений GIGABYTE™ APP Center\nПлата совместима с технологией Intel® Small Business Basics","550000","GigaByte H110M-H DDR4 LGA1151.png","3"),
("80","GigaByte GA-B250-FinTech DDR4 LGA1151","материнская плата форм-фактора microATX\nсокет LGA1151\nчипсет Intel B250\n4 слота DDR4 DIMM, 2133-2400 МГц\nразъемы SATA: 6 Гбит/с - 6","590000","GigaByte GA-B250-FinTech DDR4 LGA1151.png","3"),
("81","GigaByte Z270P-D3 DDR4 LGA1151","Совместимость с процессорами Intel® Core™ 6- и 7-поколения\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\nСовместима с технологией Intel® Optane™ Memory\nМасштабируемая видеоподсистема, доступен режим 2-way CrossFire™\nПредусмотрена возможность установки двух NVMe PCIe SSD-накопителей и организация режима RAID 0\nUltra-Fast M.2 (шина PCIe 3.0 x4) и SATA интерфейсы\nВысококачественные аудиоконденсаторы, экранирование шумов и LED-трассировка зоны размещения аудиоподсистемы\nГигабитный сетевой адаптер с поддержкой ПО cFosSpeed\nНабор фирменных приложений APP Center (утилиты EasyTune™ и Cloud Station™)\nТехнология GIGABYTE UEFI DualBIOS™\nФирменная функция Smart Fan 5, датчики температуры и гибридные FAN-разъемы","820000","GigaByte Z270P-D3 DDR4 LGA1151.png","3"),
("83"," GigaByte H310М-H DDR4 LGA1151","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\nДвухканальный режим работы Non-ECC ОЗУ DDR4 без буфферизации\nHD-аудиоподсистема (8 каналов, высококачественные аудиоконденсаторы)\nЭксклюзивный LAN-контроллер GIGABYTE 8118 Gaming, контроль и управление пропускной способностью\nСоответствие требованиям стандарта CEC 2019 Ready, активация функций энергосбережения кнопкой мыши\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nНабор фирменных утилит и приложений GIGABYTE™ APP Center\nРезисторы, устойчивые к воздействию окислов серы","650000","GigaByte H310М-H DDR4 LGA1151.png","3"),
("84","GigaByte H310М-DS2 DDR4 LGA1151","12 PCIe Slots Perfect for Mining\nDual 12V AUX power connector for PCIe slot\nСовместимость с процессорами Intel® Core™ 6- и 7-поколения\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\nHD-аудиоподсистема (8 каналов, высококачественные аудиоконденсаторы)\nАнтишумовой экран, LED-трассировка зоны аудиоподсистемы\nRealtek GbE LAN with cFosSpeed Internet Accelerator Software\nНабор фирменных приложений APP Center (утилиты EasyTune™ и Cloud Station™)\nФирменная функция Smart Fan 5, датчики температуры и гибридные FAN-разъемы","720000","MB GigaByte H310М-DS2 DDR4 LGA1151.png","3"),
("85","MB GigaByte B365-H DDR4 LGA1151","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\nДвухканальный режим работы Non-ECC ОЗУ DDR4 без буфферизации\nНовый дизайн цифрового гибридного PWM-модуля\nВысококачественные аудиоконденсаторы, экранирование шумов и LED-трассировка зоны размещения аудиоподсистемы\nUltra-Fast M.2 (шина PCIe 3.0 x4) и SATA интерфейсы\nФункция RGB FUSION, поддержка RGB LED-линеек (7 цветовых оттенков)\nЭксклюзивный LAN-контроллер GIGABYTE 8118 Gaming, контроль и управление пропускной способностью\nСоответствие стандартам CEC 2019, расширенные функции энергосбережения\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nРезисторы, устойчивые к воздействию окислов серы\nUltra Durable™ 15KV Surge LAN Protection\nСовместима с технологией Intel® Optane™ Memory\nТехнология GIGABYTE UEFI DualBIOS™\n","890000","GigaByte B365-H DDR4 LGA1151.png","3"),
("86","GigaByte B360-HD3  DDR4 LGA1151","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\nДвухканальный режим работы Non-ECC ОЗУ DDR4 без буфферизации\nНовый дизайн цифрового гибридного PWM-модуля\nIntel® CNVi 802.11ac Wave2 2T2R WIFI Upgradable\nВысококачественные аудиоконденсаторы, экранирование шумов и LED-трассировка зоны размещения аудиоподсистемы\nDual Ultra-Fast M.2 with PCIe Gen3 X4/X2 & SATA interface\nФункция RGB FUSION, поддержка RGB LED-линеек (7 цветовых оттенков)\nIntel® Native USB 3.1 Gen2 USB Type-A\nЭксклюзивный LAN-контроллер GIGABYTE 8118 Gaming, контроль и управление пропускной способностью\nСоответствие стандартам CEC 2019, расширенные функции энергосбережения\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nРезисторы, устойчивые к воздействию окислов серы\nЗащита Ultra Durable™ 25 кВ от электростатики и превышения напряжения 15 кВ LAN-соединения\nСовместима с технологией Intel® Optane™ Memory\nПоддержка функции Lightning-Fast Intel® Thunderbolt™ 3 AIC\n","1180000","MB GigaByte B360-HD3  DDR4 LGA1151.png","3"),
("87","GigaByte H370-HD3 DDR4 LGA1151","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\nДвухканальный режим работы Non-ECC ОЗУ DDR4 без буфферизации\nНовый дизайн цифрового гибридного PWM-модуля\nIntel® CNVi 802.11ac Wave2 2T2R WIFI Upgradable\nВысококачественные аудиоконденсаторы, экранирование шумов и LED-трассировка зоны размещения аудиоподсистемы\nDual Ultra-Fast M.2 with PCIe Gen3 X4/X2 & SATA interface\nФункция RGB FUSION, поддержка RGB LED-линеек (7 цветовых оттенков)\nIntel® Native USB 3.1 Gen2 USB Type-A\nIntel® USB 3.1 Gen1 USB Type-C™\nКонтроллер Intel® GbE LAN и утилита cFosSpeed Internet Accelerator\nСоответствие стандартам CEC 2019, расширенные функции энергосбережения\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nРезисторы, устойчивые к воздействию окислов серы\nЗащита Ultra Durable™ 25 кВ от электростатики и превышения напряжения 15 кВ LAN-соединения\nСовместима с технологией Intel® Optane™ Memory\nПоддержка функции Lightning-Fast Intel® Thunderbolt™ 3 AIC","1200000","MB GigaByte H370-HD3 DDR4 LGA11518.png","3"),
("88","GigaByte Z370P-D3 DDR4 LGA1151","Разъем LGA1151 для ЦП Intel® Core™ 8-поколения\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\nСовместима с технологией Intel® Optane™ Memory\nМасштабируемая видеоподсистема, доступен режим 2-way CrossFire™\nGigabit LAN with cFosSpeed Internet Accelerator Software\nЗащита Ultra Durable™ 25 кВ от электростатики и превышения напряжения 15 кВ LAN-соединения\nВысококачественные аудиоконденсаторы, экранирование шумов и LED-трассировка зоны размещения аудиоподсистемы\nUltra-Fast M.2 (шина PCIe 3.0 x4) и SATA интерфейсы\nФункция RGB FUSION, поддержка RGB LED-линеек (7 цветовых оттенков)\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nРезисторы, устойчивые к воздействию окислов серы\nНабор фирменных приложений APP Center (утилиты EasyTune™ и Cloud Station™)","1350000","MB GigaByte Z370P-D3 DDR4 LGA1151яф.png","3"),
("89"," GigaByte B360-Aorus Gaming 3  WiFi DDR4 LGA1151","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\nДвухканальный режим работы Non-ECC ОЗУ DDR4 без буфферизации\nНовый дизайн цифрового гибридного PWM-модуля\nМодуль Intel® CNVi 802.11ac Wave2 2T2R WIFI\nВысококачественные аудиоконденсаторы, экранирование шумов и LED-трассировка зоны размещения аудиоподсистемы\nДва разъема Dual Ultra-Fast M.2 (1 радиатор Thermal Guard, интерфейсы PCIe Gen3 X4/X2 и SATA)\nRGB FUSION 2.0 with Multi-Zone Digital LED Light Show design\nIntel® Native USB 3.1 Gen2 USB Type-A\nIntel® USB 3.1 Gen1 USB Type-C™\nКонтроллер Intel® GbE LAN и утилита cFosSpeed Internet Accelerator\nСоответствие стандартам CEC 2019, расширенные функции энергосбережения\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nСовместима с технологией Intel® Optane™ Memory\n","1450000","GigaByte B360-Aorus Gaming 3  WiFi DDR4 LGA1151 рап.png","3"),
("90","GigaByte H370 AORUS GAMING WiFi DDR4 LGA1151","материнская плата форм-фактора ATX\nсокет LGA1151 v2\nчипсет Intel H370\n4 слота DDR4 DIMM, 2133-2666 МГц\nподдержка CrossFire X\nразъемы SATA: 6 Гбит/с - 6\nWi-Fi 802.11ac","1650000","GigaByte H370 AORUS GAMING WiFi DDR4 LGA1151 ро.png","3"),
("91"," GigaByte Z390-UD DDR4 LGA1151 ","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\nNew 10+2 Phases Digital PWM Design\nNVMe PCIe Gen3 x4 22110 M.2 Connector\nМасштабируемая видеоподсистема, дизайн Dual Armor, концепция Ultra Durable™\nЭксклюзивный LAN-контроллер GIGABYTE 8118 Gaming, контроль и управление пропускной способностью\nSupport RGB Light Strip in Full Colors\nСоответствие требованиям стандарта CEC 2019 Ready, активация функций энергосбережения кнопкой мыши\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nПоддержка функции Lightning-Fast Intel® Thunderbolt™ 3 AIC\nСовместима с технологией Intel® Optane™ Memory\n","1600000","MB GigaByte Z390-UD DDR4 LGA1151  со.png","3"),
("92","GigaByte Z390-GamingX DDR4 LGA1151 ","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\nДвухканальный режим работы Non-ECC ОЗУ DDR4 без буфферизации\nNew 10+2 Phases Digital PWM Design\nDual Ultra-Fast M.2 with PCIe Gen3 X4 (1 with Thermal Guard) & SATA interface\n2-Way CrossFire™ Multi-Graphics Support with PCIe Armor and Ultra Durable™ Design\nIntel® Native USB 3.1 Gen2 Type-A\nКонтроллер Intel® GbE LAN и утилита cFosSpeed Internet Accelerator\nЗащита Ultra Durable™ 25 кВ от электростатики и превышения напряжения 15 кВ LAN-соединения\nВысококачественные аудиоконденсаторы, экранирование шумов и LED-трассировка зоны размещения аудиоподсистемы\nSupport RGB Lighting Effect in Full Color\nСоответствие требованиям стандарта CEC 2019 Ready, активация функций энергосбережения кнопкой мыши\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nПоддержка функции Lightning-Fast Intel® Thunderbolt™ 3 AIC\nСовместима с технологией Intel® Optane™ Memory","1890000","MB GigaByte Z390-GamingX DDR4 LGA1151  хо.png","3"),
("93","GigaByte Z390-AORUS Elite DDR4 LGA1151 ","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\nСовместима с технологией Intel® Optane™ Memory\n12+1 фазный модуль питания VRM на базе DrMOS\nУлучшенная система охлаждения с винтовым креплением\nАудиокодек ALC1220-VB с расширенным динамическим диапазоном: 114дБ(тыловые порты)/ 110дБ(фронтальные порты) для микрофона, и с аудиофильскими конденсаторами WIMA\nКонтроллер Intel® Gigabit LAN и утилита cFosSpeed\nПоддержка технологии RGB FUSION 2.0 с мультизональной светодиодной подстветкой и эффектами, поддержка адрессуемых и аналоговых светодиодных лент RGB подсветки.\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nРазъем на плате для подключения фронтальных портов USB 3.1 Gen 2 Type-C™\nДва разъема Ultra-Fast M.2 (шинный интерфейс PCIe 3.0 x4)\nСоответствие требованиям стандарта CEC 2019 Ready, активация функций энергосбережения кнопкой мыши","2350000","MB GigaByte Z390-AORUS Elite DDR4 LGA1151  мо.png","3"),
("94","GigaByte Z390-AORUS PRO DDR4 LGA1151 ","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\nСовместима с технологией Intel® Optane™ Memory\n12+1 фазный модуль питания VRM на базе DrMOS\nУлучшенная система охлаждения с радиаторами сложной формы и тепловой трубкой\nАудиокодек ALC1220-VB с расширенным динамическим диапазоном: 114дБ(тыловые порты)/ 110дБ(фронтальные порты) для микрофона, и с аудиофильскими конденсаторами WIMA\nКонтроллер Intel® Gigabit LAN и утилита cFosSpeed\nПоддержка технологии RGB FUSION 2.0 с мультизональной светодиодной подстветкой и эффектами, поддержка адрессуемых и аналоговых светодиодных лент RGB подсветки.\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nРазъем на плате для подключения фронтальных портов USB 3.1 Gen 2 Type-C™\nДва разъёма NVMe PCIe Gen3 x4 M.2 с радиаторами Thermal Guards\nМасштабируемая видеоподсистема, дизайн Dual Armor, концепция Ultra Durable™\nСоответствие требованиям стандарта CEC 2019 Ready, активация функций энергосбережения кнопкой мыши","2400000","GigaByte Z390-AORUS PRO DDR4 LGA1151 кар.png","3"),
("95","GigaByte Z390-AORUS PRO WiFi DDR4 LGA1151 ","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\nСовместима с технологией Intel® Optane™ Memory\n12+1 фазный модуль питания VRM на базе DrMOS\nУлучшенная система охлаждения с радиаторами сложной формы и тепловой трубкой\nБеспроводной сетевой контроллер Intel® CNVi 802.11ac 2x2 Wave 2 Wi-Fi\nАудиокодек ALC1220-VB с расширенным динамическим диапазоном: 114дБ(тыловые порты)/ 110дБ(фронтальные порты) для микрофона, и с аудиофильскими конденсаторами WIMA\nКонтроллер Intel® Gigabit LAN и утилита cFosSpeed\nПоддержка технологии RGB FUSION 2.0 с мультизональной светодиодной подстветкой и эффектами, поддержка адрессуемых и аналоговых светодиодных лент RGB подсветки.\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nРазъем на плате для подключения фронтальных портов USB 3.1 Gen 2 Type-C™\nДва разъёма NVMe PCIe Gen3 x4 M.2 с радиаторами Thermal Guards\nМасштабируемая видеоподсистема, дизайн Dual Armor, концепция Ultra Durable™\nСоответствие требованиям стандарта CEC 2019 Ready, активация функций энергосбережения кнопкой мыши","2650000","MB GigaByte Z390-AORUS PRO WiFi DDR4 LGA1151  кур.png","3"),
("96","GigaByte Z390-AORUS Ultra DDR4 LGA1151 ","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\nСовместима с технологией Intel® Optane™ Memory\n12+1 фазный модуль питания VRM на базе DrMOS\nУлучшенная система охлаждения с тепловой трубкой прямого контакта\nВстроенный Wi-Fi модуль Intel® CNVi 802.11ac 2x2 Wave 2\nАудиокодек ALC1220-VB с расширенным динамическим диапазоном: 114дБ(тыловые порты)/ 110дБ(фронтальные порты) для микрофона, и с аудиофильскими конденсаторами WIMA\nКонтроллер Intel® Gigabit LAN и утилита cFosSpeed\nПоддержка технологии RGB FUSION 2.0 с мультизональной светодиодной подстветкой и эффектами, поддержка адрессуемых и аналоговых светодиодных лент RGB подсветки.\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nРазъем на плате для подключения фронтальных портов USB 3.1 Gen 2 Type-C™\nТри M.2-разъема для Ultra-Fast NVMe-накопителей (шина PCIe 3.0 x4) и фирменные радиаторы Thermal Guard\nПорт USB DAC-UP 2 с адаптивной схемой регулировки напряжения\nСоответствие требованиям стандарта CEC 2019 Ready, активация функций энергосбережения кнопкой мыши\nМасштабируемая видеоподсистема, дизайн Dual Armor, концепция Ultra Durable™","3150000","GigaByte Z390-AORUS Ultra DDR4 LGA1151  топ.png","3"),
("97"," GigaByte Z390-AORUS Designare DDR4 LGA1151 ","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\n12+1 фазный модуль питания VRM на базе DrMOS\nУлучшенная система охлаждения с тепловой трубкой прямого контакта\nКонтроллер Intel® Thunderbolt™ 3, весь функционал USB Type-C\nCPU attached PCIe 3.0 x4/x4 NVMe RAID\nDual Ultra-Fast M.2 with PCIe Gen3 x4 interface with Thermal Guards\nМасштабируемая видеоподсистема, дизайн Dual Armor, концепция Ultra Durable™\nDual Intel® Gigabit LAN with cFosSpeed\nOnboard Intel CNVi 160MHz 802.11ac 2x2 WIFI\nРазъем на плате для подключения фронтальных портов USB 3.1 Gen 2 Type-C™\nАудиокодек ALC1220-VB с расширенным динамическим диапазоном: 114дБ(тыловые порты)/ 110дБ(фронтальные порты) для микрофона, и с аудиофильскими конденсаторами WIMA\nПорт USB DAC-UP 2 с адаптивной схемой регулировки напряжения\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nSupport RGB Lighting Effect in Full Color\nСоответствие стандартам CEC 2019, расширенные функции энергосбережения\nСовместима с технологией Intel® Optane™ Memory","3350000","MB GigaByte Z390-AORUS Designare DDR4 LGA1151  Блнк.png","3"),
("98","GigaByte Z390-AORUS MASTER DDR4 LGA1151 ","Разъем LGA для процессоров Intel® Core™ 8- и 9-поколения\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\nСовместима с технологией Intel® Optane™ Memory\n12-фазный цифровой VRM-модуль на базе МС PowIRstage\nПрогрессивная система охлаждения, массив радиаторов Fins-Array и прямой контакт тепловых трубок с ключевыми компонентами\nВстроенный Wi-Fi модуль Intel® CNVi 802.11ac 2x2 Wave 2\nАудиоподсистема AMP-UP на базе кодека Realtek ALC1220 (соотношение сигнал/шум 125 дБ) и высококачественный ЦАП ES9118 SABRE, аудоконденсаторы WIMA\nФункция быстрой зарядки USB-устройств средствами TurboCharger\nКонтроллер Intel® Gigabit LAN и утилита cFosSpeed\nПоддержка технологии RGB FUSION 2.0 с мультизональной и адрессуемой светодиодной подстветкой и эффектами, поддержка адрессуемых и аналоговых светодиодных лент RGB подсветки.\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nРазъем на плате для подключения фронтальных портов USB 3.1 Gen 2 Type-C™\nТри M.2-разъема для Ultra-Fast NVMe-накопителей (шина PCIe 3.0 x4) и фирменные радиаторы Thermal Guard\nПорт USB DAC-UP 2 с адаптивной схемой регулировки напряжения\nСоответствие требованиям стандарта CEC 2019 Ready, активация функций энергосбережения кнопкой мыши","3450000","GigaByte Z390-AORUS MASTER DDR4 LGA1151  топ 2.png","3"),
("99","GigaByte X299 AORUS Gaming DDR4 LGA2066","Совместима с процессорами семейства Intel® Core™ X-серии\n8 DIMM-разъемов для модулей памяти DDR4 без ECC и буферизации, четырехканальный режим работы, 4400+ МГц (OC)\nСовместима с технологией Intel® Optane™ Memory\nКонтроллер ASMedia 3142 USB 3.1 Gen 2 (тип соединения USB Type-C™ and Type-A)\nРазъем для подключения фронтальных портов USB 3.1 Gen 2\nПорт USB DAC-UP 2 с адаптивной схемой регулировки напряжения\nВысоконадежные цифровые предохранители USB портов, обеспечивающие повышенную защиту\nТехнология GIGABYTE UEFI DualBIOS™, функция Q-Flash Plus (реализована средствами специального USB-порта)\nМасштабируемая 3-Way графика, дизайн Dual Armor (концепция Ultra Durable™)\nТри разъема Ultra-Fast M.2 с интерфейсом PCIe Gen3 x4, функция термозащиты Thermal Guard\nПоддержка накопителей U.2 NVMe PCIe 3.0 x4 (через адаптер)\nСхемотехника модуля питания,\nуровень серверного оборудования","2100000","GigaByte X299 AORUS Gaming DDR4 LGA2066 пас.png","3"),
("100","Gigabyte AMD AM4 B450 AORUS Elite DDR4 (GAB45ARSE-00-G)","Supports AMD Ryzen™ 2nd Generation/ Ryzen™ with Radeon™ Vega Graphics/ Athlon™ with Radeon™ Vega Graphics/ Ryzen™ 1st Generation Processors\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\nHDMI, DVI-D Ports for Multiple Display\nIntegrated I/O Shield of Ultra Durable™ Design\nDual Ultra-Fast NVMe PCIe Gen3 M.2 (x4, x2) with One Thermal Guard\nВысококачественные аудиоконденсаторы, экранирование шумов и LED-трассировка зоны размещения аудиоподсистемы\nRGB FUSION 2.0 with Multi-Zone LED Light Show Design, Supports Digital LED & RGB LED Strips\nЭксклюзивный LAN-контроллер GIGABYTE 8118 Gaming, контроль и управление пропускной способностью\nSmart Fan 5 Features 6 Temperature Sensors and 4 Hybrid Fan Headers with FAN STOP\nНабор фирменных приложений APP Center (утилиты EasyTune™ и Cloud Station™)\nСоответствие требованиям стандарта CEC 2019 Ready, активация функций энергосбережения кнопкой мыши","1450000","7000.png","3"),
("101","Gigabyte AMD AM4 B450 AORUS PRO DDR4 (GAB45ARSP-00-G)","Supports AMD Ryzen™ 2nd Generation/ Ryzen™ with Radeon™ Vega Graphics/ Athlon™ with Radeon™ Vega Graphics/ Ryzen™ 1st Generation Processors\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\nHDMI, DVI-D Ports for Multiple Display\nUltra-Fast NVMe PCIe Gen3 x4 M.2 with Thermal Guard\nВысококачественные аудиоконденсаторы, экранирование шумов и LED-трассировка зоны размещения аудиоподсистемы\nRGB FUSION 2.0 with Multi-Zone LED Light Show Design, Supports Digital LED & RGB LED Strips\nЭксклюзивный LAN-контроллер GIGABYTE 8118 Gaming, контроль и управление пропускной способностью\nSmart Fan 5 Features 5 Temperature Sensors and 3 Hybrid Fan Headers with FAN STOP\nНабор фирменных приложений APP Center (утилиты EasyTune™ и Cloud Station™)\nРезисторы, устойчивые к воздействию окислов серы\nЗащита Ultra Durable™ 25 кВ от электростатики и превышения напряжения 15 кВ LAN-соединения\nСоответствие требованиям стандарта CEC 2019 Ready, активация функций энергосбережения кнопкой мыши","1650000",".png","3"),
("102","Gigabyte AMD AM4 X470 AORUS Ultra Gaming DDR4 (GAX47ARUG-00-G)","Совместима с процессорами AMD Ryzen™ 2-поколения / AMD Ryzen™ с видеоядром Radeon™ Vega / AMD Athlon™ с видеоядром Radeon™ Vega / AMD Ryzen™ 1-поколения / процессорами AMD 7-поколения A-серии и процессорами AMD Athlon X4\n8+3 Phase Hybrid Digital PWM Design\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\nРежимы 2-Way CrossFire/ SLI для масштабирования видеоподсистемы, дизайн Dual Armor и концепция Ultra Durable™\nАудиокодек ALC1220-VB с расширенным динамическим диапазоном: 114дБ(тыловые порты)/ 110дБ(фронтальные порты) для микрофона, и с аудиофильскими конденсаторами WIMA\nDual Ultra-Fast M.2 with NVMe PCIe X4 with One Thermal Guard\nRGB FUSION 2.0 with Multi-Zone LED Light Show Design, Supports Digital LED & RGB LED Strips\nКонтроллер Intel® Ethernet LAN и утилита cFOS Speed Internet Accelerator\nПорт USB DAC-UP 2 с адаптивной схемой регулировки напряжения\nРезисторы, устойчивые к воздействию окислов серы\nЗащита Ultra Durable™ 25 кВ от электростатики и превышения напряжения 15 кВ LAN-соединения\nСоответствие требованиям стандарта CEC 2019 Ready, активация функций энергосбережения кнопкой мыши","1980000","701.png","3"),
("103","Gigabyte AMD AM4 X570 Gaming X DDR4 ","Supports AMD 3rd Gen Ryzen™/ 2nd Gen Ryzen™/ 2nd Gen Ryzen™ with Radeon™ Vega Graphics/ Ryzen™ with Radeon™ Vega Graphics Processors\n4 DIMM-разъема для ОЗУ DDR4, двухканальный режим работы модулей памяти без ECC и буферизации\n10+2 Phases Digital VRM Solution\nAdvanced Thermal Design with Enlarge Heatsink\nDual Ultra-Fast NVMe PCIe 4.0/3.0 x4 M.2 with one Thermal Guard\nHigh Quality Audio Capacitors and Audio Noise Guard for Ultimate Audio Quality\nGIGABYTE Gaming GbE LAN with Bandwidth Management\nПоддержка технологии RGB FUSION 2.0 с мультизональной и адрессуемой светодиодной подстветкой и эффектами, поддержка адрессуемых и аналоговых светодиодных лент RGB подсветки.\nФирменная функция Smart Fan 5, датчики температуры и гибридные Fan-разъемы с функцией Fan Stop\nIntegrated I/O Shield Armor & HDMI 2.0 support\nФункция Q-Flash Plus для обновления микрокода BIOS без необходимости установливать в систему ЦП, модули ОЗУ и графическую плату\n","2400000","800.png","3"),
("104","Gigabyte AMD AM4 X570 AORUS Ultra Gaming  DDR4 ","Supports AMD 3rd Gen Ryzen™/ 2nd Gen Ryzen™/ 2nd Gen Ryzen™ with Radeon™ Vega Graphics/ Ryzen™ with Radeon™ Vega Graphics Processors\n4 DIMM-разъема для DDR4-модулей ОЗУ ECC/ Non-ECC без буферизации (двухканальный режим работы)\n12+2 Phases IR Digital VRM Solution with Power Stage\nПрогрессивная система охлаждения, массив радиаторов Fins-Array и прямой контакт тепловых трубок с ключевыми компонентами\nTriple Ultra-Fast NVMe PCIe 4.0/3.0 M.2 with Triple Thermal Guards\nМодуль Intel® WiFi 6 802.11ax 2T2R и BT 5\nАудиокодек ALC1220-VB с расширенным динамическим диапазоном: 114дБ(тыловые порты)/ 110дБ(фронтальные порты) для микрофона, и с аудиофильскими конденсаторами WIMA\nIntel® Gigabit LAN with cFosSpeed Internet Accelerator Networking\nПоддержка технологии RGB FUSION 2.0 с мультизональной и адрессуемой светодиодной подстветкой и эффектами, поддержка адрессуемых и аналоговых светодиодных лент RGB подсветки.\nSmart Fan 5 features Multiple Temperature Sensors, Hybrid Fan Headers with FAN STOP\nFront & Rear USB 3.1 Gen2 Type-C™ Header & HDMI 2.0 support\nAdopts a high quality ball bearing fan which guarantees 60,000 working hours.\nIntegrated I/O Shield Armor\nФункция Q-Flash Plus для обновления микрокода BIOS без необходимости установливать в систему ЦП, модули ОЗУ и графическую плату","3450000","900.png","3"),
("115","Seagate BarraCuda 500gb 7200","Seagate BarraCuda 500gb 7200","400000","sb500.jpg","4"),
("116","Seagate BarraCuda 1tb 7200","Seagate BarraCuda 1tb 7200","500000","sb1000.jpg","4"),
("129","NVIDIA GEFORCE® GTX 1050 Ti 4 GB GDDR5","Каждый заслуживает отличного игрового процесса. Вот почему мы создали видеокарту GeForce® GTX 1050. Теперь вы можете превратить свой ПК в игровой, на основе NVIDIA, самой технически продвинутой архитектуре GPU в истории. Видеокарта оснащена игровыми технологиями NVIDIA, которые позволяют наслаждаться современными играми. #GameReady.","1900000","5940090449735_6.jpg","9"),
("132","Tovar","fgdfgdfgdfg","1111111","859525mail-img.png","1"),
("133","Corsair","dfgfdgdf","1000","343155Screenshot_20180305-191745.jpg","2");



DROP TABLE IF EXISTS tree;

CREATE TABLE `tree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `group_id` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `link` text COLLATE utf8_unicode_ci NOT NULL,
  `translit` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL,
  `enabled` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO tree VALUES("1","PCMARKET","0","0","","","","1","1"),
("2","Игровой профи","1","1","play-profy","play-profy","","0","1"),
("3","Системный блок","2","2","play-profy","","","0","1"),
("4","Монитор","2","2","play-profy","","","0","1"),
("5","Аксессуары","2","2","play-profy","","","0","1"),
("6","Материнская плата","3","3","play-profy","","materinskaya_plata","0","1"),
("7","Процессор","3","3","play-profy","","protsessor","0","1"),
("8","Жесткий диск","3","3","play-profy","","jestkiy_disk","0","1"),
("9","Блок питания","3","3","play-profy","","blok_pitaniya","0","1"),
("10","Видеокарта","3","3","play-profy","","videokarta","0","1"),
("11","Монитор","4","3","play-profy","","monitor","0","1"),
("12","Клавиатура","5","3","play-profy","","klaviatura","0","1"),
("13","Мышь","5","3","play-profy","","mishj","0","1"),
("14","Офис мини ","1","1","ofis_mini","ofis_mini","","0","1"),
("15","Системный блок","14","2","ofis_mini","","","0","1"),
("16","Монитор","14","2","ofis_mini","","","0","1"),
("17","Аксессуары","14","2","ofis_mini","","","0","1"),
("18","Материнская плата","15","3","ofis_mini","","materinskaya_plata","0","1"),
("19","Процессор","15","3","ofis_mini","","protsessor","0","1"),
("20","Жесткий диск","15","3","ofis_mini","","jestkiy_disk","0","1"),
("21","Блок питания","15","3","ofis_mini","","blok_pitaniya","0","1"),
("22","Видеокарта","15","3","ofis_mini","","videokarta","0","1"),
("23","Монитор","16","3","ofis_mini","","monitor","0","1"),
("24","Клавиатура","17","3","ofis_mini","","klaviatura","0","1"),
("25","Мышь","17","3","ofis_mini","","mishj","0","1"),
("26","Игровой мини","1","1","igrovoy_mini","igrovoy_mini","","0","1"),
("31","Кресла","5","3","play-profy","","kresla","0","1");



DROP TABLE IF EXISTS tree1;

CREATE TABLE `tree1` (
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `group_id` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `link` text COLLATE utf8_unicode_ci NOT NULL,
  `translit` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL,
  `enabled` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `tree1_ibfk_1` FOREIGN KEY (`id`) REFERENCES `tree1` (`parent_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;




DROP TABLE IF EXISTS tree_filter;

CREATE TABLE `tree_filter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tree_id` int(11) NOT NULL,
  `filter_id` int(11) NOT NULL,
  PRIMARY KEY (`tree_id`,`filter_id`),
  UNIQUE KEY `id` (`id`),
  KEY `tree_filter_ibfk_2` (`filter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

INSERT INTO tree_filter VALUES("16","18","1"),
("17","0","0"),
("18","6","1"),
("19","6","3"),
("20","6","2");



DROP TABLE IF EXISTS tree_prod;

CREATE TABLE `tree_prod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tree_id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL,
  PRIMARY KEY (`tree_id`,`prod_id`),
  UNIQUE KEY `id` (`id`),
  KEY `prod_id` (`prod_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO tree_prod VALUES("1","6","60"),
("2","6","61"),
("3","6","62"),
("4","6","63"),
("5","6","64"),
("6","6","66"),
("7","6","67"),
("8","6","70"),
("9","6","73"),
("11","6","75"),
("12","6","76"),
("13","6","77"),
("14","6","79"),
("15","6","80"),
("16","6","81"),
("17","6","83"),
("18","6","84"),
("19","6","85"),
("20","6","86"),
("21","6","87"),
("22","6","88"),
("23","6","89"),
("24","6","90"),
("25","6","91"),
("26","6","92"),
("27","6","93"),
("28","6","94"),
("29","6","95"),
("30","6","96"),
("31","6","97"),
("32","6","98"),
("33","6","99"),
("34","6","100"),
("35","6","101"),
("36","6","102"),
("37","6","103"),
("54","18","132"),
("55","0","0");



DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `last_login` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO users VALUES("1","admin","56f844d968b2ce9c07a388a2a28c92b6f0cc74f3","");



