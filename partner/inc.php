<?php
/**
 * Copyright (c) 2012, Gemorroj
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * @author Sea, Gemorroj
 */
/**
 * Sea Downloads
 *
 * @author  Sea, Gemorroj
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */


// Префикс
define('PREFIX', 'lsxxxx');
// Ответ сервиса
define('ANSWER', 'Password: {password} ' . "\n" . 'To enter go to: http://' . $_SERVER['HTTP_HOST'] . dirname(
    dirname(dirname(dirname($_SERVER['PHP_SELF'])))
) . '/?password={password}');
// Что делаем, если номер не найден в массиве $partner
// Если указать срок, например, 3 Day или 5 Day, то сервис обработает смс как верную и даст доступ на указанный срок
// Если указать пустую строку, то вернется сообщение об ошибке
define('UNKNOWN_NUMBER', '3 Day');

/*
'id оператора' => 
  array (
    0 => 
    array (
      0 => 'короткий номер',
      1 => 'количество дней, которое действует регистрация. Следить за синтаксисом SQL! Например, 5 Day, 10 Day и т.д.',
      2 => 'Оператор',
      3 => 'Страна',
      4 => 'Цена смс',
    ),
    1 => 
    array (
      0 => 'короткий номер',
      1 => 'количество дней, которое действует регистрация. Следить за синтаксисом SQL! Например, 5 Day, 10 Day и т.д.',
      2 => 'Оператор',
      3 => 'Страна',
      4 => 'Цена смс',
    ),
    и т.д.
)

*/


$partner = array(
    104 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Оренбург GSM',
            3 => 'Russian Federation',
            4 => '228.82 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'Оренбург GSM',
//      3 => 'Russian Federation',
//      4 => '173.73 rur',
//    ),

    ),
    105 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'МТС',
            3 => 'Russian Federation',
            4 => '258.30 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'МТС',
//      3 => 'Russian Federation',
//      4 => '172.20 rur',
//    ),
    ),
    106 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'UTEL',
            3 => 'Russian Federation',
            4 => '260.00 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '5 Day',
//      2 => 'UTEL',
//      3 => 'Russian Federation',
//      4 => '150.00 rur',
//    ),
    ),
    107 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'TELE2',
            3 => 'Russian Federation',
            4 => '228.82 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'TELE2',
//      3 => 'Russian Federation',
//      4 => '173.00 rur',
//    ),
    ),
    109 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Мегафон - Северный Кавказ',
            3 => 'Russian Federation',
            4 => '150.00 rur',
        ),
//    1 => 
//    array (
//      0 => 1161,
//      1 => '3 Day',
//      2 => 'Мегафон - Северный Кавказ',
//      3 => 'Russian Federation',
//      4 => '125.00 rur',
//    ),
    ),
    110 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Мегафон - Поволжье',
            3 => 'Russian Federation',
            4 => '150.00 rur',
        ),
//    1 => 
//    array (
//      0 => 1161,
//      1 => '3 Day',
//      2 => 'Мегафон - Поволжье',
//      3 => 'Russian Federation',
//      4 => '125.00 rur',
//    ),
    ),
    /*  111 =>
      array (
        0 =>
        array (
          0 => 'NUMBER',
          1 => '5 Day',
          2 => 'Теле2 В.Новгород',
          3 => 'Russian Federation',
          4 => 'PAYMENT',
        ),
      ),*/
    112 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Мегафон - Москва',
            3 => 'Russian Federation',
            4 => '150.00 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'Мегафон - Москва',
//      3 => 'Russian Federation',
//      4 => '125.00 rur',
//    ),
    ),
    113 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Мегафон - Дальний Восток',
            3 => 'Russian Federation',
            4 => '150.00 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'Мегафон - Дальний Восток',
//      3 => 'Russian Federation',
//      4 => '125.00 rur',
//    ),
    ),
    115 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'МОТИВ',
            3 => 'Russian Federation',
            4 => '228.82 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'МОТИВ',
//      3 => 'Russian Federation',
//      4 => '173.73 rur',
//    ),
    ),
    116 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Мегафон - Северо-Запад',
            3 => 'Russian Federation',
            4 => '150.00 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'Мегафон - Северо-Запад',
//      3 => 'Russian Federation',
//      4 => '125.00 rur',
//    ),
    ),
    118 =>
    array(
        0 =>
        array(
            0 => 1171,
            1 => '5 Day',
            2 => 'НСС - Нижний Новгород',
            3 => 'Russian Federation',
            4 => '169.49 rur',
        ),
//    1 => 
//    array (
//      0 => 3649,
//      1 => '3 Day',
//      2 => 'НСС - Нижний Новгород',
//      3 => 'Russian Federation',
//      4 => '112.48 rur',
//    ),
    ),
    119 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Мегафон - Урал',
            3 => 'Russian Federation',
            4 => '150.00 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'Мегафон - Урал',
//      3 => 'Russian Federation',
//      4 => '125.00 rur',
//    ),
    ),
    120 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Билайн',
            3 => 'Russian Federation',
            4 => '254.24 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'Билайн',
//      3 => 'Russian Federation',
//      4 => '144.07 rur',
//    ),
    ),
    121 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Мегафон - Сибирь',
            3 => 'Russian Federation',
            4 => '150.00 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'Мегафон - Сибирь',
//      3 => 'Russian Federation',
//      4 => '125.00 rur',
//    ),
    ),
    122 =>
    array(
        0 =>
        array(
            0 => 1171,
            1 => '5 Day',
            2 => 'БайкалВестКом',
            3 => 'Russian Federation',
            4 => '172.88 rur',
        ),
//    1 => 
//    array (
//      0 => 1161,
//      1 => '3 Day',
//      2 => 'БайкалВестКом',
//      3 => 'Russian Federation',
//      4 => '110.00 rur',
//    ),
    ),
    125 =>
    array(
        0 =>
        array(
            0 => 1171,
            1 => '5 Day',
            2 => 'СМАРТС - Волгоград GSM',
            3 => 'Russian Federation',
            4 => '140.00 rur',
        ),
//    1 => 
//    array (
//      0 => 3649,
//      1 => '3 Day',
//      2 => 'СМАРТС - Волгоград GSM',
//      3 => 'Russian Federation',
//      4 => '101.69 rur',
//    ),
    ),
    126 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'НТК',
            3 => 'Russian Federation',
            4 => '260.00 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'НТК',
//      3 => 'Russian Federation',
//      4 => '145.00 rur',
//    ),
    ),
    127 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Мегафон - Центр',
            3 => 'Russian Federation',
            4 => '150.00 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'Мегафон - Центр',
//      3 => 'Russian Federation',
//      4 => '125.00 rur',
//    ),
    ),
    128 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'МТС - РеКом',
            3 => 'Russian Federation',
            4 => '258.30 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'МТС - РеКом',
//      3 => 'Russian Federation',
//      4 => '172.20 rur',
//    ),
    ),
    /*  129 =>
      array (
        0 =>
        array (
          0 => 'NUMBER',
          1 => '5 Day',
          2 => 'Цифровая экспансия',
          3 => 'Russian Federation',
          4 => 'PAYMENT',
        ),
      ),*/
    133 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Мегафон - Центр - Южный филиал',
            3 => 'Russian Federation',
            4 => '150.00 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'Мегафон - Центр - Южный филиал',
//      3 => 'Russian Federation',
//      4 => '125.00 rur',
//    ),
    ),
    137 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Мегафон - Центр - Северный филиал',
            3 => 'Russian Federation',
            4 => '150.00 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'Мегафон - Центр - Северный филиал',
//      3 => 'Russian Federation',
//      4 => '125.00 rur',
//    ),
    ),
    138 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'МТС - Москва',
            3 => 'Russian Federation',
            4 => '258.30 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'МТС - Москва',
//      3 => 'Russian Federation',
//      4 => '172.20 rur',
//    ),
    ),
    140 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'СМАРТС - Астрахань',
            3 => 'Russian Federation',
            4 => '254.24 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'СМАРТС - Астрахань',
//      3 => 'Russian Federation',
//      4 => '150.00 rur',
//    ),
    ),
    141 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'СМАРТС - Самара',
            3 => 'Russian Federation',
            4 => '254.24 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'СМАРТС - Самара',
//      3 => 'Russian Federation',
//      4 => '144.07 rur',
//    ),
    ),
    143 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'СТеК Джи Эс Эм',
            3 => 'Russian Federation',
            4 => '228.82 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'СТеК Джи Эс Эм',
//      3 => 'Russian Federation',
//      4 => '145.00 rur',
//    ),
    ),
    145 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'СМАРТС - Иваново',
            3 => 'Russian Federation',
            4 => '254.24 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'СМАРТС - Иваново',
//      3 => 'Russian Federation',
//      4 => '150.00 rur',
//    ),
    ),
    148 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'СМАРТС - Шупашкар',
            3 => 'Russian Federation',
            4 => '254.24 rur',
        ),
//    1 => 
//    array (
//      0 => 1171,
//      1 => '3 Day',
//      2 => 'СМАРТС - Шупашкар',
//      3 => 'Russian Federation',
//      4 => '175.00 rur',
//    ),
    ),
    /*  150 =>
      array (
        0 =>
        array (
          0 => 'NUMBER',
          1 => '5 Day',
          2 => 'К-Мобайл',
          3 => 'Казахстан',
          4 => 'PAYMENT',
        ),
      ),*/
    151 =>
    array(
        0 =>
        array(
            0 => 1171,
            1 => '5 Day',
            2 => 'Индиго(Таджикистан)',
            3 => 'Tajikistan',
            4 => '5.00 usd',
        ),
    ),
    152 =>
    array(
        0 =>
        array(
            0 => 1171,
            1 => '5 Day',
            2 => 'MLT(Таджикистан)',
            3 => 'Tajikistan',
            4 => '5.00 usd',
        ),
    ),
    153 =>
    array(
        0 =>
        array(
            0 => 1171,
            1 => '5 Day',
            2 => 'Билайн (Киргизия)',
            3 => 'Kyrgyzstan',
            4 => '5.00 usd',
        ),
    ),
    154 =>
    array(
        0 =>
        array(
            0 => 1171,
            1 => '5 Day',
            2 => 'МегаКом',
            3 => 'Kyrgyzstan',
            4 => '5.00 usd',
        ),
    ),
    156 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Дельта Телеком (skylink)',
            3 => 'Russian Federation',
            4 => '300.00 rur',
        ),
    ),
    158 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'АКОС',
            3 => 'Russian Federation',
            4 => '228.82 rur',
        ),
    ),
    159 =>
    array(
        0 =>
        array(
            0 => 4171,
            1 => '5 Day',
            2 => 'Киевстар (Украина)',
            3 => 'Ukraine',
            4 => '25.00 uah',
        ),
    ),
    160 =>
    array(
        0 =>
        array(
            0 => 4171,
            1 => '5 Day',
            2 => 'МТС Украина',
            3 => 'Ukraine',
            4 => '25.00 uah',
        ),
    ),
    161 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Ульяновск GSM',
            3 => 'Russian Federation',
            4 => '254.24 rur',
        ),
    ),
    162 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'СМАРТС - Пенза',
            3 => 'Russian Federation',
            4 => '169.49 rur',
        ),
    ),
    163 =>
    array(
        0 =>
        array(
            0 => 1645,
            1 => '5 Day',
            2 => 'Bite (Литва)',
            3 => 'Lithuania',
            4 => '8.73 ltl',
        ),
    ),
    164 =>
    array(
        0 =>
        array(
            0 => 1645,
            1 => '5 Day',
            2 => 'Omnitel (Литва)',
            3 => 'Lithuania',
            4 => '8.47 ltl',
        ),
    ),
    166 =>
    array(
        0 =>
        array(
            0 => 1645,
            1 => '5 Day',
            2 => 'Tele-2 (Литва)',
            3 => 'Lithuania',
            4 => '8.47 ltl',
        ),
    ),
    167 =>
    array(
        0 =>
        array(
            0 => 1874,
            1 => '5 Day',
            2 => 'Lmt (Латвия)',
            3 => 'Latvia',
            4 => '2.54 lvl',
        ),
    ),
    168 =>
    array(
        0 =>
        array(
            0 => 1874,
            1 => '5 Day',
            2 => 'Tele-2 (Латвия)',
            3 => 'Latvia',
            4 => '2.54 lvl',
        ),
    ),
    169 =>
    array(
        0 =>
        array(
            0 => 17013,
            1 => '5 Day',
            2 => 'Emt (Эстония)',
            3 => 'Estonia',
            4 => '42.37 eek',
        ),
    ),
    170 =>
    array(
        0 =>
        array(
            0 => 17013,
            1 => '5 Day',
            2 => 'Elisa (Эстония)',
            3 => 'Estonia',
            4 => '42.37 eek',
        ),
    ),
    172 =>
    array(
        0 =>
        array(
            0 => 17013,
            1 => '5 Day',
            2 => 'Tele-2 (Эстония)',
            3 => 'Estonia',
            4 => '42.37 eek',
        ),
    ),
    175 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'МТС - МР Дальневосточный',
            3 => 'Russian Federation',
            4 => '258.30 rur',
        ),
    ),
    176 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'МТС - МР Центральный',
            3 => 'Russian Federation',
            4 => '258.30 rur',
        ),
    ),
    177 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'МТС - МР Уральский',
            3 => 'Russian Federation',
            4 => '258.30 rur',
        ),
    ),
    178 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'МТС - МР Северо-Западный',
            3 => 'Russian Federation',
            4 => '258.30 rur',
        ),
    ),
    179 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'МТС - МР Сибирский',
            3 => 'Russian Federation',
            4 => '258.30 rur',
        ),
    ),
    180 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'МТС - МР Приволжский',
            3 => 'Russian Federation',
            4 => '258.30 rur',
        ),
    ),
    181 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'МТС - МР Северо-кавказский',
            3 => 'Russian Federation',
            4 => '258.30 rur',
        ),
    ),
    184 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Енисей Телеком',
            3 => 'Russian Federation',
            4 => '228.81 rur',
        ),
    ),
    186 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Саратов-Мобайл',
            3 => 'Russian Federation',
            4 => '211.86 rur',
        ),
    ),
    197 =>
    array(
        0 =>
        array(
            0 => 9915,
            1 => '5 Day',
            2 => 'K-Cell (Казахстан)',
            3 => 'Kazakhstan',
            4 => '474.14 kzt',
        ),
    ),
    199 =>
    array(
        0 =>
        array(
            0 => 5014,
            1 => '5 Day',
            2 => 'МТС',
            3 => 'Belarus',
            4 => '9,900.00 byr',
        ),
    ),
    /*  200 =>
      array (
        0 =>
        array (
          0 => 'NUMBER',
          1 => '5 Day',
          2 => 'БелСел',
          3 => 'Belarus',
          4 => 'PAYMENT',
        ),
      ),*/
    203 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'СМАРТС - Ярославль',
            3 => 'Russian Federation',
            4 => '254.24 rur',
        ),
    ),
    207 =>
    array(
        0 =>
        array(
            0 => 9915,
            1 => '5 Day',
            2 => 'КарТел',
            3 => 'Kazakhstan',
            4 => '474.14 kzt',
        ),
    ),
    /*  208 =>
      array (
        0 =>
        array (
          0 => 'NUMBER',
          1 => '5 Day',
          2 => 'ОСМП',
          3 => 'Терминалы',
          4 => 'PAYMENT',
        ),
      ),*/
    214 =>
    array(
        0 =>
        array(
            0 => 1171,
            1 => '5 Day',
            2 => 'Вавилон-М',
            3 => 'Tajikistan',
            4 => '5.00 usd',
        ),
    ),
    215 =>
    array(
        0 =>
        array(
            0 => 4171,
            1 => '5 Day',
            2 => 'Life',
            3 => 'Ukraine',
            4 => '25.00 uah',
        ),
    ),
    216 =>
    array(
        0 =>
        array(
            0 => 4171,
            1 => '5 Day',
            2 => 'Билайн (Украина)',
            3 => 'Ukraine',
            4 => '25.00 uah',
        ),
    ),
    217 =>
    array(
        0 =>
        array(
            0 => 179479,
            1 => '5 Day',
            2 => 'Sonera',
            3 => 'Finland',
            4 => '1.64 eur',
        ),
    ),
    218 =>
    array(
        0 =>
        array(
            0 => 179479,
            1 => '5 Day',
            2 => 'DNA',
            3 => 'Finland',
            4 => '1.64 eur',
        ),
    ),
    219 =>
    array(
        0 =>
        array(
            0 => 179479,
            1 => '5 Day',
            2 => 'Elisa',
            3 => 'Finland',
            4 => '1.64 eur',
        ),
    ),
    220 =>
    array(
        0 =>
        array(
            0 => 179479,
            1 => '5 Day',
            2 => 'Saunalahti',
            3 => 'Finland',
            4 => '1.64 eur',
        ),
    ),
    222 =>
    array(
        0 =>
        array(
            0 => 72170,
            1 => '5 Day',
            2 => 'Telia',
            3 => 'Sweden',
            4 => '16.00 sek',
        ),
    ),
    223 =>
    array(
        0 =>
        array(
            0 => 72170,
            1 => '5 Day',
            2 => 'Telenor',
            3 => 'Sweden',
            4 => '16.00 sek',
        ),
    ),
    224 =>
    array(
        0 =>
        array(
            0 => 72170,
            1 => '5 Day',
            2 => 'Tre',
            3 => 'Sweden',
            4 => '16.00 sek',
        ),
    ),
    225 =>
    array(
        0 =>
        array(
            0 => 72170,
            1 => '5 Day',
            2 => 'Tele2',
            3 => 'Sweden',
            4 => '16.00 sek',
        ),
    ),
    227 =>
    array(
        0 =>
        array(
            0 => 2322,
            1 => '5 Day',
            2 => 'Telenor',
            3 => 'Norway',
            4 => '16.00 nok',
        ),
    ),
    228 =>
    array(
        0 =>
        array(
            0 => 2322,
            1 => '5 Day',
            2 => 'NetCom',
            3 => 'Norway',
            4 => '16.00 nok',
        ),
    ),
    229 =>
    array(
        0 =>
        array(
            0 => 2322,
            1 => '5 Day',
            2 => 'Ventelo',
            3 => 'Norway',
            4 => '16.00 nok',
        ),
    ),
    230 =>
    array(
        0 =>
        array(
            0 => 2322,
            1 => '5 Day',
            2 => 'Tele2',
            3 => 'Norway',
            4 => '16.00 nok',
        ),
    ),
    /*  231 =>
      array (
        0 =>
        array (
          0 => 'NUMBER',
          1 => '5 Day',
          2 => 'Vodafone',
          3 => 'United Kingdom',
          4 => 'PAYMENT',
        ),
      ),*/
    /*  232 =>
      array (
        0 =>
        array (
          0 => 'NUMBER',
          1 => '5 Day',
          2 => 'O2',
          3 => 'United Kingdom',
          4 => 'PAYMENT',
        ),
      ),*/
    /*  233 =>
      array (
        0 =>
        array (
          0 => 'NUMBER',
          1 => '5 Day',
          2 => 'Orange',
          3 => 'United Kingdom',
          4 => 'PAYMENT',
        ),
      ),*/
    /*  234 =>
      array (
        0 =>
        array (
          0 => 'NUMBER',
          1 => '5 Day',
          2 => 'T-Mobile/Virgin',
          3 => 'United Kingdom',
          4 => 'PAYMENT',
        ),
      ),*/
    /*  235 =>
      array (
        0 =>
        array (
          0 => 'NUMBER',
          1 => '5 Day',
          2 => 'Three',
          3 => 'United Kingdom',
          4 => 'PAYMENT',
        ),
      ),*/
    236 =>
    array(
        0 =>
        array(
            0 => 7117,
            1 => '5 Day',
            2 => 'KPN',
            3 => 'Netherlands',
            4 => '1.26 eur',
        ),
    ),
    237 =>
    array(
        0 =>
        array(
            0 => 7117,
            1 => '5 Day',
            2 => 'T-Mobile',
            3 => 'Netherlands',
            4 => '1.26 eur',
        ),
    ),
    238 =>
    array(
        0 =>
        array(
            0 => 7117,
            1 => '5 Day',
            2 => 'Vodafone',
            3 => 'Netherlands',
            4 => '1.26 eur',
        ),
    ),
    239 =>
    array(
        0 =>
        array(
            0 => 7117,
            1 => '5 Day',
            2 => 'Orange',
            3 => 'Netherlands',
            4 => '1.26 eur',
        ),
    ),
    240 =>
    array(
        0 =>
        array(
            0 => 7117,
            1 => '5 Day',
            2 => 'Telfort',
            3 => 'Netherlands',
            4 => '1.26 eur',
        ),
    ),
    241 =>
    array(
        0 =>
        array(
            0 => 7117,
            1 => '5 Day',
            2 => 'Tele2',
            3 => 'Netherlands',
            4 => '1.26 eur',
        ),
    ),
    242 =>
    array(
        0 =>
        array(
            0 => 80888,
            1 => '5 Day',
            2 => 'T-Mobile',
            3 => 'Germany',
            4 => '1.67 eur',
        ),
    ),
    243 =>
    array(
        0 =>
        array(
            0 => 80888,
            1 => '5 Day',
            2 => 'Vodafone',
            3 => 'Germany',
            4 => '1.67 eur',
        ),
    ),
    244 =>
    array(
        0 =>
        array(
            0 => 80888,
            1 => '5 Day',
            2 => 'E-Plus',
            3 => 'Germany',
            4 => '1.67 eur',
        ),
    ),
    245 =>
    array(
        0 =>
        array(
            0 => 80888,
            1 => '5 Day',
            2 => 'O2',
            3 => 'Germany',
            4 => '1.67 eur',
        ),
    ),
    246 =>
    array(
        0 =>
        array(
            0 => 80888,
            1 => '5 Day',
            2 => 'debitel',
            3 => 'Germany',
            4 => '1.67 eur',
        ),
    ),
    247 =>
    array(
        0 =>
        array(
            0 => 80888,
            1 => '5 Day',
            2 => 'Mobilcom',
            3 => 'Germany',
            4 => '1.67 eur',
        ),
    ),
    248 =>
    array(
        0 =>
        array(
            0 => 4565,
            1 => '5 Day',
            2 => 'TMN',
            3 => 'Portugal',
            4 => '1.65 eur',
        ),
    ),
    249 =>
    array(
        0 =>
        array(
            0 => 4565,
            1 => '5 Day',
            2 => 'Vodafone',
            3 => 'Portugal',
            4 => '1.65 eur',
        ),
    ),
    250 =>
    array(
        0 =>
        array(
            0 => 4565,
            1 => '5 Day',
            2 => 'Optimus',
            3 => 'Portugal',
            4 => '1.65 eur',
        ),
    ),
    251 =>
    array(
        0 =>
        array(
            0 => 5339,
            1 => '5 Day',
            2 => 'Movistar',
            3 => 'Spain',
            4 => '1.20 eur',
        ),
    ),
    252 =>
    array(
        0 =>
        array(
            0 => 5339,
            1 => '5 Day',
            2 => 'Vodafone',
            3 => 'Spain',
            4 => '1.20 eur',
        ),
    ),
    253 =>
    array(
        0 =>
        array(
            0 => 5339,
            1 => '5 Day',
            2 => 'Orange',
            3 => 'Spain',
            4 => '1.20 eur',
        ),
    ),
    254 =>
    array(
        0 =>
        array(
            0 => 5339,
            1 => '5 Day',
            2 => 'Yoigo',
            3 => 'Spain',
            4 => '1.20 eur',
        ),
    ),
    255 =>
    array(
        0 =>
        array(
            0 => 5339,
            1 => '5 Day',
            2 => 'Euskaltel',
            3 => 'Spain',
            4 => '1.20 eur',
        ),
    ),
    256 =>
    array(
        0 =>
        array(
            0 => 5339,
            1 => '5 Day',
            2 => 'R',
            3 => 'Spain',
            4 => '1.20 eur',
        ),
    ),
    257 =>
    array(
        0 =>
        array(
            0 => 5339,
            1 => '5 Day',
            2 => 'Telecable',
            3 => 'Spain',
            4 => '1.20 eur',
        ),
    ),
    258 =>
    array(
        0 =>
        array(
            0 => 4545,
            1 => '5 Day',
            2 => 'Израиль',
            3 => 'Israel',
            4 => '8.66 ils',
        ),
    ),
    259 =>
    array(
        0 =>
        array(
            0 => 80888,
            1 => '5 Day',
            2 => 'Phonehouse',
            3 => 'Germany',
            4 => '1.67 eur',
        ),
    ),
    260 =>
    array(
        0 =>
        array(
            0 => 9090199,
            1 => '5 Day',
            2 => 'O2',
            3 => 'Czech Republic',
            4 => '83.19 czk',
        ),
    ),
    261 =>
    array(
        0 =>
        array(
            0 => 9090199,
            1 => '5 Day',
            2 => 'T-Mobile',
            3 => 'Czech Republic',
            4 => '83.19 czk',
        ),
    ),
    262 =>
    array(
        0 =>
        array(
            0 => 9090199,
            1 => '5 Day',
            2 => 'Vodafone',
            3 => 'Czech Republic',
            4 => '83.19 czk',
        ),
    ),
    264 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Татинком',
            3 => 'Russian Federation',
            4 => '112.48 rur',
        ),
    ),
    265 =>
    array(
        0 =>
        array(
            0 => 7910,
            1 => '5 Day',
            2 => 'Orange',
            3 => 'Poland',
            4 => '9.00 pln',
        ),
    ),
    266 =>
    array(
        0 =>
        array(
            0 => 7910,
            1 => '5 Day',
            2 => 'Plus',
            3 => 'Poland',
            4 => '9.00 pln',
        ),
    ),
    267 =>
    array(
        0 =>
        array(
            0 => 7910,
            1 => '5 Day',
            2 => 'Era',
            3 => 'Poland',
            4 => '9.00 pln',
        ),
    ),
    268 =>
    array(
        0 =>
        array(
            0 => 7910,
            1 => '5 Day',
            2 => 'Play',
            3 => 'Poland',
            4 => '9.00 pln',
        ),
    ),
    269 =>
    array(
        0 =>
        array(
            0 => 1874,
            1 => '5 Day',
            2 => 'Bite',
            3 => 'Latvia',
            4 => '2.50 lvl',
        ),
    ),
    270 =>
    array(
        0 =>
        array(
            0 => 1171,
            1 => '5 Day',
            2 => 'Билайн',
            3 => 'Tajikistan',
            4 => '5.00 usd',
        ),
    ),
    /*  273 =>
      array (
        0 =>
        array (
          0 => 'NUMBER',
          1 => '5 Day',
          2 => 'Roboexchange',
          3 => 'Терминалы',
          4 => 'PAYMENT',
        ),
      ),*/
    274 =>
    array(
        0 =>
        array(
            0 => '0930399999',
            1 => '5 Day',
            2 => 'Mobilkom',
            3 => 'Austria',
            4 => '1.60 eur',
        ),
    ),
    275 =>
    array(
        0 =>
        array(
            0 => '0930399999',
            1 => '5 Day',
            2 => 'T-Mobile',
            3 => 'Austria',
            4 => '1.60 eur',
        ),
    ),
    276 =>
    array(
        0 =>
        array(
            0 => '0930399999',
            1 => '5 Day',
            2 => 'One',
            3 => 'Austria',
            4 => '1.60 eur',
        ),
    ),
    277 =>
    array(
        0 =>
        array(
            0 => '0930399999',
            1 => '5 Day',
            2 => 'Telering',
            3 => 'Austria',
            4 => '1.60 eur',
        ),
    ),
    278 =>
    array(
        0 =>
        array(
            0 => '0930399999',
            1 => '5 Day',
            2 => 'Hutchinson',
            3 => 'Austria',
            4 => '1.60 eur',
        ),
    ),
    279 =>
    array(
        0 =>
        array(
            0 => '0930399999',
            1 => '5 Day',
            2 => 'Tele2',
            3 => 'Austria',
            4 => '1.60 eur',
        ),
    ),
    281 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'НСС - Мордовия',
            3 => 'Russian Federation',
            4 => '112.48 rur',
        ),
    ),
    282 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'НСС - Чувашия',
            3 => 'Russian Federation',
            4 => '112.48 rur',
        ),
    ),
    287 =>
    array(
        0 =>
        array(
            0 => 90645045,
            1 => '5 Day',
            2 => 'Pannon',
            3 => 'Hungary',
            4 => '550.00 huf',
        ),
    ),
    288 =>
    array(
        0 =>
        array(
            0 => 7796,
            1 => '5 Day',
            2 => 'Base',
            3 => 'Belgium',
            4 => '1.58 eur',
        ),
    ),
    289 =>
    array(
        0 =>
        array(
            0 => 7796,
            1 => '5 Day',
            2 => 'Mobistar',
            3 => 'Belgium',
            4 => '1.58 eur',
        ),
    ),
    290 =>
    array(
        0 =>
        array(
            0 => 7796,
            1 => '5 Day',
            2 => 'Proximus',
            3 => 'Belgium',
            4 => '1.58 eur',
        ),
    ),
    291 =>
    array(
        0 =>
        array(
            0 => 90645045,
            1 => '5 Day',
            2 => 'T-Mobile',
            3 => 'Belgium',
            4 => '550.00 huf',
        ),
    ),
    292 =>
    array(
        0 =>
        array(
            0 => 90645045,
            1 => '5 Day',
            2 => 'Vodafone',
            3 => 'Belgium',
            4 => '550.00 huf',
        ),
    ),
    293 =>
    array(
        0 =>
        array(
            0 => 1945,
            1 => '5 Day',
            2 => 'TDC',
            3 => 'Denmark',
            4 => '16.00 dkk',
        ),
    ),
    294 =>
    array(
        0 =>
        array(
            0 => 1945,
            1 => '5 Day',
            2 => 'Sonofon',
            3 => 'Denmark',
            4 => '16.00 dkk',
        ),
    ),
    295 =>
    array(
        0 =>
        array(
            0 => 1945,
            1 => '5 Day',
            2 => 'Telia',
            3 => 'Denmark',
            4 => '16.00 dkk',
        ),
    ),
    297 =>
    array(
        0 =>
        array(
            0 => 1945,
            1 => '5 Day',
            2 => 'Tre',
            3 => 'Denmark',
            4 => '16.00 dkk',
        ),
    ),
    298 =>
    array(
        0 =>
        array(
            0 => 9014,
            1 => '5 Day',
            2 => 'Azercell',
            3 => 'Azerbaijan',
            4 => '5.00 azn',
        ),
    ),
    299 =>
    array(
        0 =>
        array(
            0 => 1121,
            1 => '5 Day',
            2 => 'Armentel',
            3 => 'Armenia',
            4 => '1,416.61 amd',
        ),
    ),
    300 =>
    array(
        0 =>
        array(
            0 => 1121,
            1 => '5 Day',
            2 => 'Vivacell',
            3 => 'Armenia',
            4 => '1,416.61 amd',
        ),
    ),
    301 =>
    array(
        0 =>
        array(
            0 => 83868,
            1 => '5 Day',
            2 => 'Bouygues',
            3 => 'France',
            4 => '2.41 eur',
        ),
    ),
    302 =>
    array(
        0 =>
        array(
            0 => 83868,
            1 => '5 Day',
            2 => 'Orange',
            3 => 'France',
            4 => '2.41 eur',
        ),
    ),
    303 =>
    array(
        0 =>
        array(
            0 => 83868,
            1 => '5 Day',
            2 => 'Sfr',
            3 => 'France',
            4 => '2.41 eur',
        ),
    ),
    /*  304 =>
      array (
        0 =>
        array (
          0 => 83868,
          1 => '5 Day',
          2 => 'Терминалы',
          3 => 'Терминалы',
          4 => 'PAYMENT',
        ),
      ),*/
    308 =>
    array(
        0 =>
        array(
            0 => 3649,
            1 => '5 Day',
            2 => 'Сибирьтелеком',
            3 => 'Russian Federation',
            4 => '228.00 rur',
        ),
    ),
    309 =>
    array(
        0 =>
        array(
            0 => 1098,
            1 => '5 Day',
            2 => 'Mtel',
            3 => 'Bulgaria',
            4 => '2.00 bgn',
        ),
    ),
    310 =>
    array(
        0 =>
        array(
            0 => 1098,
            1 => '5 Day',
            2 => 'GloBul',
            3 => 'Bulgaria',
            4 => '2.00 bgn',
        ),
    ),
    311 =>
    array(
        0 =>
        array(
            0 => 1098,
            1 => '5 Day',
            2 => 'Vivatel',
            3 => 'Bulgaria',
            4 => '2.00 bgn',
        ),
    ),
);


mysql_query('DELETE FROM `passwords` WHERE `end_date` < NOW()');

function partner_input($buff)
{
    return preg_replace('/<input class="enter" size="\d+" type="text" value="http:\/\/.+"\/>/', '', $buff);
}

function partner_yes($buff)
{
    $language = Language::getInstance()->getLanguage();

    return str_replace(
        '<body>',
        '<body><div class="iblock"><div class="yes">' . $language['partner_yes_auth'] . '<br/></div></div>',
        $buff
    );
}

function partner_no($buff)
{
    $language = Language::getInstance()->getLanguage();

    return str_replace(
        '<body>',
        '<body><div class="iblock"><div class="no">' . $language['partner_no_auth'] . '<br/></div></div>',
        $buff
    );
}


if (isset($_GET['password']) && is_numeric($_GET['password'])
    && mysql_num_rows(
        mysql_query('SELECT 1 FROM `passwords` WHERE `password` = ' . $_GET['password'], $mysql)
    )
) {
    ob_start('partner_yes');
    $_SESSION['password'] = $_GET['password'];
} else {
    if (isset($_SESSION['password'])
        && !mysql_num_rows(
            mysql_query('SELECT 1 FROM `passwords` WHERE `password` = ' . $_SESSION['password'], $mysql)
        )
    ) {
        unset($_SESSION['password']);
    } else {
        if (isset($_GET['password'])) {
            ob_start('partner_no');
        }
    }
}

$basename = basename($_SERVER['PHP_SELF']);
$language = Language::getInstance()->getLanguage();

if (($basename == 'load.php' || $basename == 'txt_jar.php' || $basename == 'txt_zip.php' || $basename == 'cut.php'
    || $basename == 'jad.php'
    || ($basename == 'im.php' && isset($_REQUEST['W']) || isset($_REQUEST['H']))
    || ($basename == 'zip.php' && @$_GET['action'] == 'preview')
    || ($basename == 'read.php' && @$_GET['page'] > 1))
    && !isset($_SESSION['password'])
) {
    require_once dirname(__FILE__) . '/../moduls/header.php';


    require dirname(__FILE__) . '/geoip/geoip.inc';

    $gi = geoip_open(dirname(__FILE__) . '/geoip/GeoIP.dat', GEOIP_STANDARD);
    $country = isset($_GET['country']) ? $_GET['country'] : geoip_country_name_by_addr($gi, $_SERVER['REMOTE_ADDR']);
    geoip_close($gi);

    $title = $language['partner_auth'] . ' - ' . htmlspecialchars($country, ENT_NOQUOTES);


    $pay = '<div class="mblock">';
    $count = array();
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    foreach ($partner as $v) {
        if ($country == $v[0][3]) {
            ksort($v);
            foreach ($v as $p) {
                $pay .= $p[0] . ' (' . $p[2] . ', ' . $p[4] . ', ' . str_replace(
                    '%day%',
                    ((strtotime($p[1]) - $_SERVER['REQUEST_TIME']) / 86400),
                    $language['partner_time']
                ) . ') <a href="smsto:' . $p[0] . '?body=' . PREFIX . '">SMS1</a> / <a href="sms:' . $p[0] . '?body='
                    . PREFIX . '">SMS2</a><br/>';
            }
            $count[] = '&#187; ' . $v[0][3];
        } else {
            $count[] = '<a href="' . DIRECTORY . 'load/' . $id . '/?country=' . rawurlencode($v[0][3]) . '">' . $v[0][3]
                . '</a>';
        }
    }
    $count = array_unique($count);
    //sort($count);
    $pay .= '</div>';


    echo'<div class="iblock"><div class="no">' . $language['partner_no_auth'] . '<br/></div>' . str_replace(
        '%prefix%',
        '<strong>' . PREFIX . '</strong>',
        $language['partner_prefix']
    ) . '<br/></div><div class="iblock">' . implode('<br/>', $count) . '</div>' . $pay
        . '<div class="mblock"><form action="' . DIRECTORY . '" method="get"><div>' . $language['partner_enter']
        . '<br/><input class="enter" type="password" name="password"/> <input class="buttom" type="submit" value="'
        . $language['go'] . '"/></div></form></div><div class="iblock"><a href="' . DIRECTORY . 'view/' . $id . '">'
        . $language['go to the description of the file'] . '</a><br/><a href="' . DIRECTORY . '">' . $language['home']
        . '</a><br/></div>';
    exit;
} else {
    if ($basename == 'view.php' && !isset($_SESSION['password'])) {
        ob_start('partner_input');
    }
}
