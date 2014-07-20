<?php
/**
 * String class file.
 *
 * @author         RcPHP Dev Team
 * @copyright      Copyright (c) 2013,RcPHP Dev Team
 * @license        Apache License 2.0 {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @package        Library.Util
 * @since          1.0
 */
defined('IN_RCPHP') or exit('Access denied');

class String
{

	/**
	 * Create uuid.
	 *
	 * @return string
	 */
	public static function uuid()
	{
		$charid = md5(uniqid(mt_rand(), true));
		$hyphen = chr(45); // "-"
		$uuid = chr(123); // "{"
		$uuid .= substr($charid, 0, 8) . $hyphen;
		$uuid .= substr($charid, 8, 4) . $hyphen;
		$uuid .= substr($charid, 12, 4) . $hyphen;
		$uuid .= substr($charid, 16, 4) . $hyphen;
		$uuid .= substr($charid, 20, 12);
		$uuid .= chr(125); // "}"
		return $uuid;
	}

	/**
	 * Substring.
	 *
	 * @param  string $str
	 * @param int     $start
	 * @param int     $length
	 * @param string  $charset
	 * @param string  $suffix
	 * @return string
	 */
	public static function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = '...')
	{
		if(function_exists("mb_substr"))
		{
			$result = mb_substr($str, $start, $length, $charset);
		}
		else if(function_exists('iconv_substr'))
		{
			$result = iconv_substr($str, $start, $length, $charset);
			if(false === $result)
			{
				$result = '';
			}
		}
		else
		{
			$regExp['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
			$regExp['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
			$regExp['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
			$regExp['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
			preg_match_all($regExp[$charset], $str, $match);
			$result = join("", array_slice($match[0], $start, $length));
		}

		return $suffix ? $result . $suffix : $result;
	}

	/**
	 * Automatic conversion character set support array conversion.
	 *
	 * @param string|array $string
	 * @param string       $from
	 * @param string       $to
	 * @return string|array
	 */
	public static function auto_charset($string, $from = 'GBK', $to = 'UTF-8')
	{
		if(empty($string))
		{
			return false;
		}

		$from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
		$to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
		if(strtoupper($from) === strtoupper($to) || empty($string) || (is_scalar($string) && !is_string($string)))
		{
			return $string;
		}

		if(is_string($string))
		{
			if(function_exists('mb_convert_encoding'))
			{
				return mb_convert_encoding($string, $to, $from);
			}
			else if(function_exists('iconv'))
			{
				return iconv($from, $to, $string);
			}
			else
			{
				return $string;
			}
		}
		else if(is_array($string))
		{
			foreach($string as $key => $val)
			{
				$_key = self::auto_charset($key, $from, $to);
				$fContents[$_key] = self::auto_charset($val, $from, $to);
				if($key != $_key)
				{
					unset($fContents[$key]);
				}
			}

			return $string;
		}
		else
		{
			return $string;
		}
	}

	/**
	 * Rand string code.
	 *
	 * @param int    $len
	 * @param string $type
	 * @param string $chars
	 * @return string
	 */
	public static function rand_string($len = 6, $type = '', $chars = '')
	{
		$str = '';
		switch($type)
		{
			case 0:
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $chars;
				break;
			case 1:
				$chars = str_repeat('0123456789', 3);
				break;
			case 2:
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $chars;
				break;
			case 3:
				$chars = 'abcdefghijklmnopqrstuvwxyz' . $chars;
				break;
			case 4:
				$chars = "�����ҵ�������ʱҪ��������һ�ǹ�������巢�ɲ���ɳ��ܷ������˲����д�����������Ϊ����������ѧ�¼��ظ���ͬ����˵�ֹ����ȸ�����Ӻ������С��Ҳ�����߱������������ʵ�Ҷ������ˮ������������������ʮս��ũʹ��ǰ�ȷ���϶�·ͼ�ѽ�������¿���֮��ӵ���Щ�������¶�����������˼�����ȥ�����������ѹԱ��ҵ��ȫ�������ڵ�ƽ��������ëȻ��Ӧ�����������ɶ������ʱ�չ�������û���������ϵ������Ⱥͷ��ֻ���ĵ����ϴ���ͨ�����Ͽ��ֹ�����������ϯλ����������ԭ�ͷ�������ָ��������ںܽ̾��ش˳�ʯǿ�������Ѹ���ֱ��ͳʽת�����о���ȡ������������־�۵���ôɽ�̰ٱ��������汣��ί�ָĹܴ�������֧ʶ�������Ϲ�רʲ���;�ʾ������ÿ�����������Ϲ����ֿƱ�������Ƹ��������������༯������װ����֪���е�ɫ����ٷ�ʷ����������֯�������󴫿ڶϿ��ɾ����Ʒ�вβ�ֹ��������ȷ������״��������Ŀ����Ȩ�Ҷ����֤��Խ�ʰ��Թ�˹��ע�첼�����������ر��̳�������ǧʤϸӰ�ð׸�Ч���ƿ��䵶Ҷ������ѡ���»������ʼƬʩ���ջ�������������ҩ����Ѵ��ʿ���Һ��׼��ǽ�ά�������������״����ƶ˸������ش幹���ݷǸ���ĥ�������ʽ���ֵ��̬���ױ�������������̨���û������ܺ���ݺ����ʼ��������Ͼ��ݼ���ҳ�����Կ�Ӣ��ƻ���Լ�Ͳ�ʡ���������ӵ۽�����ֲ������������ץ���縱����̸Χʳ��Դ�������ȴ����̻����������׳߲��зۼ������濼�̿�������ʧ��ס��֦�־����ܻ���ʦ������Ԫ����ɰ�⻻̫ģƶ�����ｭ��Ķľ����ҽУ���ص�����Ψ�们վ�����ֹĸ�д��΢�Է�������ĳ�����������൹�������ù�Զ���Ƥ����ռ����Ȧΰ��ѵ�ؼ��ҽ��ƻ���������ĸ�����ֶ���˫��������ʴ����˿Ůɢ��������Ժ�䳹����ɢ�����������������Ѫ��ȱ��ò�����ǳ���������������̴���������������Ͷ��ū����ǻӾഥ�����ͻ��˶��ٻ����δͻ�ܿ���ʪƫ�Ƴ�ִ����կ�����ȶ�Ӳ��Ŭ�����Ԥְ������Э�����ֻ���ì������ٸ�������������ͣ����Ӫ�ո���Ǯ��������ɳ�˳��ַ�е�ذ����İ��������۵��յ���ѽ�ʰɿ��ֽ�������������ĩ������ڱ������������������𾪶ټ�����ķ��ɭ��ʥ���մʳٲ��ھؿ��������԰ǻ�����������������ӡ�伱�����˷�¶��Ե�������������Ѹ��������ֽҹ������׼�����ӳ��������ɱ���׼辧�尣ȼ��������ѿ��������̼��������ѿ����б��ŷ��˳������͸˾Σ������Ц��β��׳����������������ţ��Ⱦ�����������Ƽ�ֳ�����ݷô���ͭ��������ٺ�����Դ��ظ���϶¯����úӭ��ճ̽�ٱ�Ѯ�Ƹ�������Ը���������̾䴿������������³�෱�������׶ϣ�ذܴ�����ν�л��ܻ���ڹ��ʾ����ǳ���������Ϣ������������黭�������������躮ϲ��ϴʴ���ɸ���¼������֬ׯ��������ҡ���������������Ű²��ϴ�;�������Ұ�ž�ıŪ�ҿ�����ʢ��Ԯ���Ǽ���������Ħæ�������˽����������������Ʊܷ�������Ƶ�������Ҹ�ŵ����Ũ��Ϯ˭��л�ڽ���Ѷ���鵰�պ������ͽ˽������̹����ù�����ո��伨���ܺ���ʹ�������������ж�����׷���ۺļ���������о�Ѻպ��غ���Ĥƪ��פ������͹�ۼ���ѩ�������������߲��������ڽ������˹�̿������������ǹ���ð������Ͳ���λ�����Ϳζ����Ϻ�½�����𶹰�Ī��ɣ�·쾯���۱�����ɶ���ܼ��Ժ��浤�ɶ��ٻ���ϡ���������ǳӵѨ������ֽ����������Ϸ��������ò�����η��ɰ���������ˢ�ݺ���������©�������Ȼľ��з������Բ����ҳ�����ײ����ȳ����ǵ������������ɨ������оү���ؾ����Ƽ��ڿ��׹��ð��ѭ��ף���Ͼ����������ݴ���ι�������Ź�ó����ǽ���˽�ī������ж����������ƭ�ݽ�" . $chars;
				break;
			default:
				// Ĭ��ȥ�������׻������ַ�oOLl������01��Ҫ�����ʹ��addChars����
				$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $chars;
				break;
		}
		if($len > 10)
		{
			//λ�������ظ��ַ���һ������
			$chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
		}
		if($type != 4)
		{
			$chars = str_shuffle($chars);
			$str = substr($chars, 0, $len);
		}
		else
		{
			// ���������
			for($i = 0; $i < $len; $i++)
			{
				$str .= self::msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
			}
		}

		return $str;
	}
}