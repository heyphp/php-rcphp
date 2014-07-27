<?php
/**
 * Location class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Library.Util
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class Location
{

	/**
	 * QQWry.Dat�ļ�ָ��
	 *
	 * @var resource
	 */
	private $fp;

	/**
	 * ��һ��IP��¼��ƫ�Ƶ�ַ
	 *
	 * @var int
	 */
	private $firstip;

	/**
	 * ���һ��IP��¼��ƫ�Ƶ�ַ
	 *
	 * @var int
	 */
	private $lastip;

	/**
	 * IP��¼�����������������汾��Ϣ��¼��
	 *
	 * @var int
	 */
	private $totalip;

	/**
	 * ���캯������ QQWry.Dat �ļ�����ʼ�����е���Ϣ
	 *
	 * @param string $filename
	 * @return void
	 */
	public function __construct($filename = "UTFWry.dat")
	{
		$this->fp = 0;
		if(($this->fp = fopen(DATA_PATH . $filename, 'rb')) !== false)
		{
			$this->firstip = $this->getlong();
			$this->lastip = $this->getlong();
			$this->totalip = ($this->lastip - $this->firstip) / 7;
		}
	}

	/**
	 * ���ض�ȡ�ĳ�������
	 *
	 * @access private
	 * @return int
	 */
	private function getlong()
	{
		//����ȡ��little-endian�����4���ֽ�ת��Ϊ��������
		$result = unpack('Vlong', fread($this->fp, 4));

		return $result['long'];
	}

	/**
	 * ���ض�ȡ��3���ֽڵĳ�������
	 *
	 * @return int
	 */
	private function getlong3()
	{
		//����ȡ��little-endian�����3���ֽ�ת��Ϊ��������
		$result = unpack('Vlong', fread($this->fp, 3) . chr(0));

		return $result['long'];
	}

	/**
	 * ����ѹ����ɽ��бȽϵ�IP��ַ
	 *
	 * @param string $ip
	 * @return string
	 */
	private function packip($ip)
	{
		// ��IP��ַת��Ϊ���������������PHP5�У�IP��ַ�����򷵻�False��
		// ��ʱintval��Flaseת��Ϊ����-1��֮��ѹ����big-endian������ַ���
		return pack('N', intval(ip2long($ip)));
	}

	/**
	 * ���ض�ȡ���ַ���
	 *
	 * @param string $data
	 * @return string
	 */
	private function getstring($data = "")
	{
		$char = fread($this->fp, 1);
		while(ord($char) > 0)
		{ // �ַ�������C��ʽ���棬��\0����
			$data .= $char; // ����ȡ���ַ����ӵ������ַ���֮��
			$char = fread($this->fp, 1);
		}

		return $data;
	}

	/**
	 * ���ص�����Ϣ
	 *
	 * @return string
	 */
	private function getarea()
	{
		$byte = fread($this->fp, 1); // ��־�ֽ�
		switch(ord($byte))
		{
			case 0: // û��������Ϣ
				$area = "";
				break;
			case 1:
			case 2: // ��־�ֽ�Ϊ1��2����ʾ������Ϣ���ض���
				fseek($this->fp, $this->getlong3());
				$area = $this->getstring();
				break;
			default: // ���򣬱�ʾ������Ϣû�б��ض���
				$area = $this->getstring($byte);
				break;
		}

		return $area;
	}

	/**
	 * �������� IP ��ַ�������������ڵ�����Ϣ
	 *
	 * @param string $ip
	 * @return array
	 */
	public function getlocation($ip = '')
	{
		if(!$this->fp) return null; // ��������ļ�û�б���ȷ�򿪣���ֱ�ӷ��ؿ�
		if(empty($ip))
		{
			$ip = Http::get_ip();
		}
		$location['ip'] = gethostbyname($ip); // �����������ת��ΪIP��ַ
		$ip = $this->packip($location['ip']); // �������IP��ַת��Ϊ�ɱȽϵ�IP��ַ
		// ���Ϸ���IP��ַ�ᱻת��Ϊ255.255.255.255
		// �Է�����
		$l = 0; // �������±߽�
		$u = $this->totalip; // �������ϱ߽�
		$findip = $this->lastip; // ���û���ҵ��ͷ������һ��IP��¼��QQWry.Dat�İ汾��Ϣ��
		while($l <= $u)
		{ // ���ϱ߽�С���±߽�ʱ������ʧ��
			$i = floor(($l + $u) / 2); // ��������м��¼
			fseek($this->fp, $this->firstip + $i * 7);
			$beginip = strrev(fread($this->fp, 4)); // ��ȡ�м��¼�Ŀ�ʼIP��ַ
			// strrev����������������ǽ�little-endian��ѹ��IP��ַת��Ϊbig-endian�ĸ�ʽ
			// �Ա����ڱȽϣ�������ͬ��
			if($ip < $beginip)
			{ // �û���IPС���м��¼�Ŀ�ʼIP��ַʱ
				$u = $i - 1; // ���������ϱ߽��޸�Ϊ�м��¼��һ
			}
			else
			{
				fseek($this->fp, $this->getlong3());
				$endip = strrev(fread($this->fp, 4)); // ��ȡ�м��¼�Ľ���IP��ַ
				if($ip > $endip)
				{ // �û���IP�����м��¼�Ľ���IP��ַʱ
					$l = $i + 1; // ���������±߽��޸�Ϊ�м��¼��һ
				}
				else
				{ // �û���IP���м��¼��IP��Χ��ʱ
					$findip = $this->firstip + $i * 7;
					break; // ���ʾ�ҵ�������˳�ѭ��
				}
			}
		}

		//��ȡ���ҵ���IP����λ����Ϣ
		fseek($this->fp, $findip);
		$location['beginip'] = long2ip($this->getlong()); // �û�IP���ڷ�Χ�Ŀ�ʼ��ַ
		$offset = $this->getlong3();
		fseek($this->fp, $offset);
		$location['endip'] = long2ip($this->getlong()); // �û�IP���ڷ�Χ�Ľ�����ַ
		$byte = fread($this->fp, 1); // ��־�ֽ�
		switch(ord($byte))
		{
			case 1: // ��־�ֽ�Ϊ1����ʾ���Һ�������Ϣ����ͬʱ�ض���
				$countryOffset = $this->getlong3(); // �ض����ַ
				fseek($this->fp, $countryOffset);
				$byte = fread($this->fp, 1); // ��־�ֽ�
				switch(ord($byte))
				{
					case 2: // ��־�ֽ�Ϊ2����ʾ������Ϣ�ֱ��ض���
						fseek($this->fp, $this->getlong3());
						$location['country'] = $this->getstring();
						fseek($this->fp, $countryOffset + 4);
						$location['area'] = $this->getarea();
						break;
					default: // ���򣬱�ʾ������Ϣû�б��ض���
						$location['country'] = $this->getstring($byte);
						$location['area'] = $this->getarea();
						break;
				}
				break;
			case 2: // ��־�ֽ�Ϊ2����ʾ������Ϣ���ض���
				fseek($this->fp, $this->getlong3());
				$location['country'] = $this->getstring();
				fseek($this->fp, $offset + 8);
				$location['area'] = $this->getarea();
				break;
			default: // ���򣬱�ʾ������Ϣû�б��ض���
				$location['country'] = $this->getstring($byte);
				$location['area'] = $this->getarea();
				break;
		}
		if(trim($location['country']) == 'CZ88.NET')
		{ // CZ88.NET��ʾû����Ч��Ϣ
			$location['country'] = 'δ֪';
		}
		if(trim($location['area']) == 'CZ88.NET')
		{
			$location['area'] = '';
		}

		return $location;
	}

	/**
	 * ����������������ҳ��ִ�н������Զ��رմ򿪵��ļ���
	 *
	 * @return void
	 */
	public function __destruct()
	{
		if($this->fp)
		{
			fclose($this->fp);
		}
		$this->fp = 0;
	}
}