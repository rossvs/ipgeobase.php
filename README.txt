НАЗНАЧЕНИЕ

Этот скрипт предназначен для использования текстовых баз сервиса гео-локации 
ipgeobase.ru на PHP. Ipgeobase.ru предоставляет подробную информацию 
по IP-адресу: город, регион, федеральный округ, координаты - по городам России 
и Украины. 
По этим странам сервис работает точнее MaxMind GeoIP: на тестовой выборке в
30000 IP Ipgeobase смог определить регион в 98,6% случаев, а GeoIP - только
в 78% (подробнее тут: http://ross.vc/?p=204).


ИСПОЛЬЗОВАНИЕ 

1. Скачайте архив http://ipgeobase.ru/cgi-bin/Archive.cgi 
   (хорошая идея настроить переодическое скачивание с помощью wget).
2. Распакуйте cidr_optim.txt и cities.txt.
2. Подключите ipgeobase.php.
3. Используйте класс IPGeoBase (см. example.php).

Скрипт работает в кодировке windows-1251, т.к. в этой кодировке поставляются 
файлы cities.txt и cidr_optim.txt.
Если предполагается высокая частота обращений к скрипту, возможно, хорошей 
идеей будет разместить файлы cidr_optim.txt и cities.txt на RAM-диске или 
вообще отказаться от этой библиотеки и разместить базу в SQL РСУБД.
Если не требуется определение зарубежных стран, можно удалить из базы 
диапазоны, не относящиеся к России, например, командой sed:
sed -e '/RU/!d' cidr_optim.txt > cidr_optim_RU.txt


ИСПОЛЬЗОВАНИЕ СОВМЕСТНО С MAXMIND GEOIP

Чтобы получать информацию о городе и регионе по всем странам можно 
дополнительно использовать базу GeoLite сервиса MaxMind GeoIP.

1. Скачайте базу GeoLite http://dev.maxmind.com/geoip/legacy/geolite/
2. Скачайте библитеку PHP http://dev.maxmind.com/geoip/legacy/downloadable/
3. Пример функции для одновременной работы Ipgeobase и GeoIP дан 
   в example-geoip.php. Пример предполагает следующую структуру 
   папок:
   
   |_geoip                  папка API GeoIP
   |_cidr_optim.txt         база диапазонов IP Ipgeobase
   |_cities.txt             база городов Ipgeobase
   |_example-geoip.php      файл примера
   |_geoipregionvars.ru.php перевод регионов на русский для GeoIP
   |_GeoLiteCity.dat        база GeoIP
   |_ipgeobase.php          класс IPGeoBase


КОНТАКТЫ

Владислав Росс vladislav.ross@gmail.com