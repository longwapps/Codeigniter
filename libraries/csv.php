<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Csv {

	protected $fields;        /* columns names retrieved after parsing */
	protected $max_row_size;  /* maximum row size to be used for decoding */
	protected $separator;     /* separator used to explode each line */
	protected $enclosure;     /* enclosure used to decorate each field */

	public function __construct($config = array('max_row_size' => 4096, 'separator' => ';', 'enclosure' => '"'))
	{
		if(isset($config['max_row_size']) && isset($config['separator']) && isset($config['enclosure'])){
			$this->max_row_size = $config['max_row_size'];
			$this->separator = $config['separator'];
			$this->enclosure = $config['enclosure'];
		}else{
			throw new Exception("you have errors in your configuration");
		}
	}

	public function read($file_path) 
	{
		$file = fopen($file_path, 'r');
		$this->fields = fgetcsv($file, $this->max_row_size, $this->separator, $this->enclosure);
		$keys_values = explode(',', $this->fields[0]);

		$content = array();
		$keys = $this->escape_string($keys_values);

		$i = 1;
		while(($row = fgetcsv($file, $this->max_row_size, $this->separator, $this->enclosure)) != false ) {
			if( $row != null ) { // skip empty lines
				$values = explode(',',$row[0]);
				if(count($keys) == count($values)) {
					$arr = array();
					$new_values = array();
					$new_values = $this->escape_string($values);
					for($j=0; $j < count($keys); $j++) {
						if($keys[$j] != "") {
							$arr[$keys[$j]] =   $new_values[$j];
						}
					}
					$content[$i]    =   $arr;
					$i++;
				}
			}
		}

		fclose($file);
		return $content;
	}

	public function create($file_path, $headers, $data)
	{
		$file = fopen($file_path, "w");

		// output the column headings
		fputcsv($file, $headers);

		// loop over the rows, outputting them
		foreach($data as $row){
			$row = explode(",", $row);
			fputcsv($file, $row);
		}
	}

	private function escape_string($data)
	{
		$result = array();
		foreach($data as $row){
			$result[] = str_replace('"', '', $row);
		}
		return $result;
	}
}
