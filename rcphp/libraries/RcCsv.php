<?php
/**
 * RcCsv class file.
 *
 * @author         RcPHP Dev Team
 * @version        $Id: RcCsv.php 0.2 2013-08-14 23:05 zhangwj $
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        libraries
 * @since          1.0
 * @filesource
 */
defined('IN_RCPHP') or exit('Access denied');

class RcCsv extends RcBase
{

	/**
	 * 读取CSV
	 *
	 * @param string $fileName
	 * @param string $delimiter
	 * @return array
	 */
	public function read($fileName, $delimiter = ',')
	{
		if(empty($fileName))
		{
			RcController::halt('The CSV file name is empty');
		}

		setlocale(LC_ALL, 'en_US.UTF-8');

		//读取csv文件内容
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
	 * 生成CSV文件
	 *
	 * @param string $fileName
	 * @param array  $data
	 * @return void
	 */
	public function write($fileName, $data)
	{
		//参数分析
		if(empty($fileName))
		{
			RcController::halt('The CSV file name is empty');
		}

		if(empty($data) || !is_array($data))
		{
			RcController::halt('The CSV data file is not correct');
		}

		//判断文件名称是否含有csv的扩展名
		if(stripos($fileName, '.csv') === false)
		{
			$fileName .= '.csv';
		}

		//分析$data内容
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