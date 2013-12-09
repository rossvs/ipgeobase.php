<?php
/*
	Class for working with ipgeobase.ru geo database.

	Copyright (C) 2013, Vladislav Ross

	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.

	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public
	License along with this library; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

    E-mail: vladislav.ross@gmail.com
	URL: https://github.com/rossvs/ipgeobase.php
	
*/
/*
 * @class IPGeoBase
 * @brief Класс для работы с текстовыми базами ipgeobase.ru
 * @see example.php
 *
 * Определяет страну, регион и город по IP для России и Украины
 */
class IPGeoBase 
{
	private $fhandleCIDR, $fhandleCities, $fSizeCIDR, $fsizeCities;

    /*
     * @brief Конструктор
     *
     * @param CIDRFile файл базы диапазонов IP (cidr_optim.txt)
     * @param CitiesFile файл базы городов (cities.txt)
     */
	function __construct($CIDRFile = false, $CitiesFile = false)
	{
		if(!$CIDRFile)
		{
			$CIDRFile = dirname(__FILE__) . '/cidr_optim.txt';			
		}
		if(!$CitiesFile)
		{
			$CitiesFile = dirname(__FILE__) . '/cities.txt';			
		}
		$this->fhandleCIDR = fopen($CIDRFile, 'r') or die("Cannot open $CIDRFile");
		$this->fhandleCities = fopen($CitiesFile, 'r') or die("Cannot open $CitiesFile");
		$this->fSizeCIDR = filesize($CIDRFile);
		$this->fsizeCities = filesize($CitiesFile);
	}

    /*
     * @brief Получение информации о городе по индексу
     * @param idx индекс города
     * @return массив или false, если не найдено
     */
	private function getCityByIdx($idx)
	{
		rewind($this->fhandleCities);
		while(!feof($this->fhandleCities))
		{
			$str = fgets($this->fhandleCities);
			$arRecord = explode("\t", trim($str));
			if($arRecord[0] == $idx)
			{
				return array(	'city' => $arRecord[1],
								'region' => $arRecord[2],
								'district' => $arRecord[3],
								'lat' => $arRecord[4],
								'lng' => $arRecord[5]);
			}
		}
		return false;
	}

    /*
     * @brief Получение гео-информации по IP
     * @param ip IPv4-адрес
     * @return массив или false, если не найдено
     */
	function getRecord($ip)
	{
		$ip = sprintf('%u', ip2long($ip));
		
		rewind($this->fhandleCIDR);
		$rad = floor($this->fSizeCIDR / 2);
		$pos = $rad;
		while(fseek($this->fhandleCIDR, $pos, SEEK_SET) != -1)			
		{
			if($rad) 
			{
				$str = fgets($this->fhandleCIDR);				
			}
			else
			{
				rewind($this->fhandleCIDR);
			}
			
			$str = fgets($this->fhandleCIDR);
			
			if(!$str)
			{
				return false;
			}
			
			$arRecord = explode("\t", trim($str));

			$rad = floor($rad / 2);
			if(!$rad && ($ip < $arRecord[0] || $ip > $arRecord[1]))
			{
				return false;
			}
			
			if($ip < $arRecord[0])
			{
				$pos -= $rad;
			}
			elseif($ip > $arRecord[1])
			{
				$pos += $rad;
			}
			else
			{
				$result = array('range' => $arRecord[2], 'cc' => $arRecord[3]);
											
				if($arRecord[4] != '-' && $cityResult = $this->getCityByIdx($arRecord[4]))
				{
					$result += $cityResult;
				}
				
				return $result;
			}
		}
		return false;		
	}
}

