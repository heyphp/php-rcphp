<?php
/**
 * Csv class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Util
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Csv
{

	/**
	 * ��ȡCSV
	 *
	 * @param string $fileName
	 * @param string $delimiter
	 * @return array|bool
	 */
	public static function read($fileName, $delimiter = ',')
	{
		if(!file_exists($fileName))
		{
			return false;
		}

		setlocale(LC_ALL, 'en_US.UTF-8');

		//��ȡcsv�ļ�����
		$handle = fopen($fileName, 'r');
		$csvData = array();
		$row = 0;
		while($data = fgetcsv($handle, 1000, $delimiter))
		{
			$num = count($data);
			for($i = 0; $i < $num; $i++)
			{
				$csvData[$row][$i] = $data[$i];
			}
			$row++;
		}
		fclose($handle);

		return $csvData;
	}

	/**
	 * ����CSV�ļ�
	 *
	 * @param string $fileName
	 * @param array  $data
	 * @return void
	 */
	public static function write($fileName, $data)
	{
		if(empty($data) || !is_array($data))
		{
			Controller::halt('The CSV data file is not correct');
		}

		//�ж��ļ������Ƿ���csv����չ��
		if(stripos($fileName, '.csv') === false)
		{
			$fileName .= '.csv';
		}

		//����$data����
		$content = '';
		foreach($data as $lines)
		{
			if($lines && is_array($lines))
			{
				foreach($lines as $key => $value)
				{
					if(is_string($value))
					{
						$lines[$key] = '"' . $value . '"';
					}
				}
				$content .= implode(",", $lines) . "\n";
			}
		}

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Expires:0');
		header('Pragma:public');
		header("Cache-Control: public");
		header("Content-type:text/csv");
		header("Content-Disposition:attachment;filename=" . $fileName);

		echo $content;
	}
}