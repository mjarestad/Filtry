<?php

namespace Mjarestad\Filtry;

/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
|
| Filter and sanitize input data in Laravel 4 or as a standalone package.
|
*/
class Filtry
{
	/**
	 * Input data
	 * @var array
	 */
	protected $data = array();

	/**
	 * Filtered data
	 * @var array
	 */
	protected $filteredData = array();

	/**
	 * Filters to apply to data
	 * @var array
	 */
	protected $filters = array();

	/**
	 * Filter extensions
	 * @var array
	 */
	protected $extensions = array();

	/**
	 * Filter recursive on input array
	 * @var boolean
	 */
	protected $recursive;

	/**
	 * Set input data and filters
	 * @param  array $data
	 * @param  string|array $filters
	 * @param boolean $recursive
	 * @return Filtry
	 */
	public function make($data, $filters, $recursive = true)
	{
		$this->data 		= $data;
		$this->filters 		= $this->explodeFilters($filters);
		$this->recursive 	= $recursive;
		$this->filteredData	= array();

		return $this;
	}

	/**
	 * Get filtered data
	 * @return array
	 */
	public function getFiltered()
	{
		$this->filter();

		return array_merge($this->data, $this->filteredData);
	}

	/**
	 * Get unfiltered data
	 * @return array
	 */
	public function getOld()
	{
		return $this->data;
	}

	/**
	 * Extend with custom filters
	 * @param  string $filter
	 * @param  $extension
	 * @return void
	 */
	public function extend($filter, $extension)
	{
		$this->extensions[$filter] = $extension;
	}

	/**
	 * Trim
	 * @param  string $data
	 * @param string $charlist
	 * @return string
	 */
	public function trim($data, $charlist = ' ')
	{
		return trim($data, $charlist);
	}

	/**
	 * Left trim
	 * @param  string $data
	 * @param string $charlist
	 * @return string
	 */
	public function ltrim($data, $charlist = ' ')
	{
		return ltrim($data, $charlist);
	}

	/**
	 * Right trim
	 * @param  string $data
	 * @param string $charlist
	 * @return string
	 */
	public function rtrim($data, $charlist = ' ')
	{
		return rtrim($data, $charlist);
	}

	/**
	 * String to lower
	 * @param  string $data
	 * @return string
	 */
	public function lower($data)
	{
		return mb_strtolower($data);
	}

	/**
	 * String to upper
	 * @param  string $data
	 * @return string
	 */
	public function upper($data)
	{
		return mb_strtoupper($data);
	}

	/**
	 * UC first
	 * @param  string $data
	 * @return string
	 */
	public function ucfirst($data)
	{
		return ucfirst($data);
	}

	/**
	 * UC words
	 * @param  string $data
	 * @return string
	 */
	public function ucwords($data)
	{
		return ucwords($data);
	}

	/**
	 * Strip slashes
	 * @param  string $data
	 * @return string
	 */
	public function stripslashes($data)
	{
		return stripslashes($data);
	}

    /**
     * Replace string
     * @param $data
     * @param $search
     * @param $replace
     * @return mixed
     */
	public function replace($data, $search, $replace)
	{
		return str_replace($search, $replace, $data);
	}

	/**
	 * Snake case
	 * @param  string $value
     * @param string $delimiter
	 * @return string
	 */
	public function snakeCase($value, $delimiter = '_')
	{
		$replace = '$1'.$delimiter.'$2';

		return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)([A-Z])/', $replace, $value));
	}

	/**
	 * Camel case
	 * @param  string $data
	 * @return string
	 */
	public function camelCase($data)
	{
		return lcfirst($this->studlyCase($data));
	}

	/**
	 * Studly case
	 * @param  string $data
	 * @return string
	 */
	public function studlyCase($data)
	{
		$data = ucwords(str_replace(array('-', '_'), ' ', $data));

		return str_replace(' ', '', $data);
	}

	/**
	 * XSS clean with htmlspecialchars
	 * @param  string $data
	 * @return string
	 */
	public function xssClean($data)
	{
		return htmlspecialchars($data);
	}

	/**
	 * Remove whitespace
	 * @param  string $data
	 * @return string
	 */
	public function removeWhitespace($data)
	{
		return $this->stripWhitespaces($data);
	}

    /**
     * Strip all white spaces
     * @param $data
     * @return mixed
     */
    public function stripWhitespaces($data)
    {
        return str_replace(' ', '', $data);
    }

    /**
     * Strip all dashes
     * @param $data
     * @return mixed
     */
    public function stripDashes($data)
    {
        return str_replace('-', '', $data);
    }

	/**
	 * Remove all special chars and make it url-friendly
	 * @param  string $data
	 * @param  string $separator
	 * @return string
	 */
	public function slug($data, $separator = '-')
	{
		$data = static::ascii($data);

		// Convert all dashes/underscores into separator
		$flip = $separator == '-' ? '_' : '-';

		$data = preg_replace('!['.preg_quote($flip).']+!u', $separator, $data);

		// Remove all characters that are not the separator, letters, numbers, or whitespace.
		$data = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($data));

		// Replace all separator characters and whitespace by a single separator
		$data = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $data);

		return trim($data, $separator);
	}

	/**
	 * Add http or https if missing
	 * @param  string  $url
	 * @return string
	 */
	public function prepUrl($url)
	{
        if (empty($url)) {
            return '';
        }

        return parse_url($url, PHP_URL_SCHEME) === null ? 'http://' . $url : $url;
	}

	/**
	 * Transliterate a UTF-8 value to ASCII.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function ascii($value)
	{
		foreach (static::charsArray() as $key => $val) {
			$value = str_replace($val, $key, $value);
		}

		return preg_replace('/[^\x20-\x7E]/u', '', $value);
	}

	/**
	 * Returns the replacements for the ascii method.
	 *
	 * Note: Adapted from Stringy\Stringy.
	 *
	 * @see https://github.com/danielstjules/Stringy/blob/2.3.1/LICENSE.txt
	 *
	 * @return array
	 */
	protected static function charsArray()
	{
		static $charsArray;

		if (isset($charsArray)) {
			return $charsArray;
		}

		return $charsArray = [
			'0'    => ['°', '₀', '۰'],
			'1'    => ['¹', '₁', '۱'],
			'2'    => ['²', '₂', '۲'],
			'3'    => ['³', '₃', '۳'],
			'4'    => ['⁴', '₄', '۴', '٤'],
			'5'    => ['⁵', '₅', '۵', '٥'],
			'6'    => ['⁶', '₆', '۶', '٦'],
			'7'    => ['⁷', '₇', '۷'],
			'8'    => ['⁸', '₈', '۸'],
			'9'    => ['⁹', '₉', '۹'],
			'a'    => ['à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'ā', 'ą', 'å', 'ä', 'α', 'ά', 'ἀ', 'ἁ', 'ἂ', 'ἃ', 'ἄ', 'ἅ', 'ἆ', 'ἇ', 'ᾀ', 'ᾁ', 'ᾂ', 'ᾃ', 'ᾄ', 'ᾅ', 'ᾆ', 'ᾇ', 'ὰ', 'ά', 'ᾰ', 'ᾱ', 'ᾲ', 'ᾳ', 'ᾴ', 'ᾶ', 'ᾷ', 'а', 'أ', 'အ', 'ာ', 'ါ', 'ǻ', 'ǎ', 'ª', 'ა', 'अ', 'ا'],
			'b'    => ['б', 'β', 'Ъ', 'Ь', 'ب', 'ဗ', 'ბ'],
			'c'    => ['ç', 'ć', 'č', 'ĉ', 'ċ'],
			'd'    => ['ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ', 'д', 'δ', 'د', 'ض', 'ဍ', 'ဒ', 'დ'],
			'e'    => ['é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'ë', 'ē', 'ę', 'ě', 'ĕ', 'ė', 'ε', 'έ', 'ἐ', 'ἑ', 'ἒ', 'ἓ', 'ἔ', 'ἕ', 'ὲ', 'έ', 'е', 'ё', 'э', 'є', 'ə', 'ဧ', 'ေ', 'ဲ', 'ე', 'ए', 'إ', 'ئ'],
			'f'    => ['ф', 'φ', 'ف', 'ƒ', 'ფ'],
			'g'    => ['ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ဂ', 'გ', 'گ'],
			'h'    => ['ĥ', 'ħ', 'η', 'ή', 'ح', 'ه', 'ဟ', 'ှ', 'ჰ'],
			'i'    => ['í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į', 'ı', 'ι', 'ί', 'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ', 'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ', 'ῑ', 'ῒ', 'ΐ', 'ῖ', 'ῗ', 'і', 'ї', 'и', 'ဣ', 'ိ', 'ီ', 'ည်', 'ǐ', 'ი', 'इ'],
			'j'    => ['ĵ', 'ј', 'Ј', 'ჯ', 'ج'],
			'k'    => ['ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك', 'က', 'კ', 'ქ', 'ک'],
			'l'    => ['ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل', 'လ', 'ლ'],
			'm'    => ['м', 'μ', 'م', 'မ', 'მ'],
			'n'    => ['ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن', 'န', 'ნ'],
			'o'    => ['ö', 'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő', 'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ', 'ὃ', 'ὄ', 'ὅ', 'ὸ', 'ό', 'о', 'و', 'θ', 'ို', 'ǒ', 'ǿ', 'º', 'ო', 'ओ'],
			'p'    => ['п', 'π', 'ပ', 'პ', 'پ'],
			'q'    => ['ყ'],
			'r'    => ['ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر', 'რ'],
			's'    => ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص', 'စ', 'ſ', 'ს'],
			't'    => ['ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط', 'ဋ', 'တ', 'ŧ', 'თ', 'ტ'],
			'u'    => ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'û', 'ū', 'ů', 'ű', 'ŭ', 'ų', 'µ', 'у', 'ဉ', 'ု', 'ူ', 'ǔ', 'ǖ', 'ǘ', 'ǚ', 'ǜ', 'უ', 'उ'],
			'v'    => ['в', 'ვ', 'ϐ'],
			'w'    => ['ŵ', 'ω', 'ώ', 'ဝ', 'ွ'],
			'x'    => ['χ', 'ξ'],
			'y'    => ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ', 'ϋ', 'ύ', 'ΰ', 'ي', 'ယ'],
			'z'    => ['ź', 'ž', 'ż', 'з', 'ζ', 'ز', 'ဇ', 'ზ'],
			'aa'   => ['ع', 'आ', 'آ'],
			'ae'   => ['æ', 'ǽ'],
			'ai'   => ['ऐ'],
			'at'   => ['@'],
			'ch'   => ['ч', 'ჩ', 'ჭ', 'چ'],
			'dj'   => ['ђ', 'đ'],
			'dz'   => ['џ', 'ძ'],
			'ei'   => ['ऍ'],
			'gh'   => ['غ', 'ღ'],
			'ii'   => ['ई'],
			'ij'   => ['ĳ'],
			'kh'   => ['х', 'خ', 'ხ'],
			'lj'   => ['љ'],
			'nj'   => ['њ'],
			'oe'   => ['œ', 'ؤ'],
			'oi'   => ['ऑ'],
			'oii'  => ['ऒ'],
			'ps'   => ['ψ'],
			'sh'   => ['ш', 'შ', 'ش'],
			'shch' => ['щ'],
			'ss'   => ['ß'],
			'sx'   => ['ŝ'],
			'th'   => ['þ', 'ϑ', 'ث', 'ذ', 'ظ'],
			'ts'   => ['ц', 'ც', 'წ'],
			'ue'   => ['ü'],
			'uu'   => ['ऊ'],
			'ya'   => ['я'],
			'yu'   => ['ю'],
			'zh'   => ['ж', 'ჟ', 'ژ'],
			'(c)'  => ['©'],
			'A'    => ['Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Å', 'Ä', 'Ā', 'Ą', 'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ', 'Ἄ', 'Ἅ', 'Ἆ', 'Ἇ', 'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ', 'Ᾱ', 'Ὰ', 'Ά', 'ᾼ', 'А', 'Ǻ', 'Ǎ'],
			'B'    => ['Б', 'Β', 'ब'],
			'C'    => ['Ç', 'Ć', 'Č', 'Ĉ', 'Ċ'],
			'D'    => ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ'],
			'E'    => ['É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'Ë', 'Ē', 'Ę', 'Ě', 'Ĕ', 'Ė', 'Ε', 'Έ', 'Ἐ', 'Ἑ', 'Ἒ', 'Ἓ', 'Ἔ', 'Ἕ', 'Έ', 'Ὲ', 'Е', 'Ё', 'Э', 'Є', 'Ə'],
			'F'    => ['Ф', 'Φ'],
			'G'    => ['Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ'],
			'H'    => ['Η', 'Ή', 'Ħ'],
			'I'    => ['Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Î', 'Ï', 'Ī', 'Ĭ', 'Į', 'İ', 'Ι', 'Ί', 'Ϊ', 'Ἰ', 'Ἱ', 'Ἳ', 'Ἴ', 'Ἵ', 'Ἶ', 'Ἷ', 'Ῐ', 'Ῑ', 'Ὶ', 'Ί', 'И', 'І', 'Ї', 'Ǐ', 'ϒ'],
			'K'    => ['К', 'Κ'],
			'L'    => ['Ĺ', 'Ł', 'Л', 'Λ', 'Ļ', 'Ľ', 'Ŀ', 'ल'],
			'M'    => ['М', 'Μ'],
			'N'    => ['Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν'],
			'O'    => ['Ö', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ø', 'Ō', 'Ő', 'Ŏ', 'Ο', 'Ό', 'Ὀ', 'Ὁ', 'Ὂ', 'Ὃ', 'Ὄ', 'Ὅ', 'Ὸ', 'Ό', 'О', 'Θ', 'Ө', 'Ǒ', 'Ǿ'],
			'P'    => ['П', 'Π'],
			'R'    => ['Ř', 'Ŕ', 'Р', 'Ρ', 'Ŗ'],
			'S'    => ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ'],
			'T'    => ['Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ'],
			'U'    => ['Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'Û', 'Ū', 'Ů', 'Ű', 'Ŭ', 'Ų', 'У', 'Ǔ', 'Ǖ', 'Ǘ', 'Ǚ', 'Ǜ'],
			'V'    => ['В'],
			'W'    => ['Ω', 'Ώ', 'Ŵ'],
			'X'    => ['Χ', 'Ξ'],
			'Y'    => ['Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ', 'Ы', 'Й', 'Υ', 'Ϋ', 'Ŷ'],
			'Z'    => ['Ź', 'Ž', 'Ż', 'З', 'Ζ'],
			'AE'   => ['Æ', 'Ǽ'],
			'CH'   => ['Ч'],
			'DJ'   => ['Ђ'],
			'DZ'   => ['Џ'],
			'GX'   => ['Ĝ'],
			'HX'   => ['Ĥ'],
			'IJ'   => ['Ĳ'],
			'JX'   => ['Ĵ'],
			'KH'   => ['Х'],
			'LJ'   => ['Љ'],
			'NJ'   => ['Њ'],
			'OE'   => ['Œ'],
			'PS'   => ['Ψ'],
			'SH'   => ['Ш'],
			'SHCH' => ['Щ'],
			'SS'   => ['ẞ'],
			'TH'   => ['Þ'],
			'TS'   => ['Ц'],
			'UE'   => ['Ü'],
			'YA'   => ['Я'],
			'YU'   => ['Ю'],
			'ZH'   => ['Ж'],
			' '    => ["\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81", "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84", "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87", "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A", "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80"],
		];
	}

	/**
	 * Dynamically call extension filters
	 * @param  string $filter
	 * @param  array $data
	 * @return mixed
     * @throws \Exception
	 */
	public function __call($filter, $data)
	{
		$filter = $this->snakeCase($filter);

		if (isset($this->extensions[$filter])) {
			return call_user_func($this->extensions[$filter], $data[0]);
		}

		throw new \Exception("Filter extension '$filter' does not exist.");
	}

	/**
	 * Run provided filters on data
	 * @return array
     * @throws \Exception
	 */
	protected function filter()
	{
		foreach ($this->filters as $attribute => $filters) {
			// Check if the attribute is present in the input data
			if (!array_key_exists($attribute, $this->data)) {
				continue;
			}

			$data = $this->data[$attribute];

			foreach ($filters as $filter) {
				// The format for specifying filter and parameters follows an
				// easy {filter}:{parameters} formatting convention.
				if (strpos($filter, ':') !== false) {
					list($filter, $parameterList) = explode(':', $filter, 2);
					$parameters = explode(',', $parameterList);
				} else {
					$parameters = [];
				}
				
				if (method_exists($this, $this->camelCase($filter))) {
					$data = $this->filterWalker($filter, $data, $parameters);
				} elseif (isset($this->extensions[$filter])) {
					$data = $this->filterExtensionWalker($filter, $data, $parameters);
				} else {
					throw new \Exception("'$filter' is not a valid filter.");
				}
			}

			$this->filteredData[$attribute] = $data;
		}

		return $this->filteredData;
	}

	/**
	 * Walk through filters
	 * @param  string $filter
	 * @param  array $data
	 * @param array $parameters
	 * @return array|string
	 */
	protected function filterWalker($filter, $data, $parameters = [])
	{
		$filter = $this->camelCase($filter);
		array_unshift($parameters, $data);

		if (is_array($data) and $this->recursive === true) {
			foreach ($data as $key => $value) {
				$parameters[0] = $value;
				$data[$key] = call_user_func_array([$this, $filter], $parameters);
			}
		} else {
			$data = call_user_func_array(array($this, $filter), $parameters);
		}

		return $data;
	}

	/**
	 * Walk through extension filters
	 * @param  string $filter
	 * @param  array $data
	 * @return string|array
	 */
	protected function filterExtensionWalker($filter, $data, $parameters = [])
	{
		array_unshift($parameters, $data);

		if (is_array($data) and $this->recursive === true) {
			foreach ($data as $key => $value) {
				$parameters[0] = $value;
				$data[$key] = call_user_func_array($this->extensions[$filter], $parameters);
			}
		} else {
			$data = call_user_func_array($this->extensions[$filter], $parameters);
		}

		return $data;
	}

	/**
	 * Explode the filters into an array of filters.
	 * @param  string|array  $filters
	 * @return array
	 */
	protected function explodeFilters($filters)
	{
		foreach ($filters as $key => &$filter) {
			$filter = (is_string($filter)) ? explode('|', $filter) : $filter;
		}

		return $filters;
	}
}
