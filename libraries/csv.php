<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CSV {

	protected $fields;        /* columns names retrieved after parsing */
	protected $max_row_size;  /* maximum row size to be used for decoding */
	protected $separator;     /* separator used to explode each line */
	protected $enclosure;     /* enclosure used to decorate each field */

	public function __construct($config = array())
	{
		$config = array_merge(array('max_row_size' => 4096, 'separator' => ',', 'enclosure' => '"'), $config);
		$this->max_row_size = $config['max_row_size'];
		$this->separator = $config['separator'];
		$this->enclosure = $config['enclosure'];
	}

	public function read($file_path) 
	{
		$file = fopen($file_path, 'r');
		$line = fgets($file);
		$keys_values = str_getcsv($line, $this->separator, $this->enclosure);

		$content = array();
		$keys = $this->escape_string($keys_values);
		
		$i = 1;
		while(($row = fgets($file)) != false ) {
			if( $row != null ) {
				$values = str_getcsv($row, $this->separator, $this->enclosure);
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

		fputcsv($file, $headers);

		foreach($data as $row){
			$row = str_getcsv($row, $this->separator, $this->enclosure);
			$row = $this->escape_string($row);
			fputcsv($file, $row);
		}
	}
	
	public function download_csv($filename, $headers, $data)
	{
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Disposition: attachment; filename={$filename}");

		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');

		fputcsv($output, $headers);

		foreach($data as $row){
			$row = str_getcsv($row, $this->separator, $this->enclosure);
			$row = $this->escape_string($row);
			fputcsv($output, $row);
		}
	}
	
	public function download_excel($filename, $data)
	{
		// Header info settings
		header("Content-Type: application/xls");
		header("Content-Disposition: attachment; filename={$filename}.xls");
		header("Pragma: no-cache");
		header("Expires: 0");

		/***** Start of Formatting for Excel *****/
		// Define separator (defines columns in excel &amp; tabs in word)
		$sep = "\t"; // tabbed character

		// Start of printing column names as names of MySQL fields
		foreach ($data[0] as $key => $val){
			echo $key . "\t";
		}
		print("\n");
		// End of printing column names

		// Start while loop to get data
		foreach($data as $row){
			$schema_insert = "";
			foreach($row as $rec){
				if(!isset($rec)) {
					$schema_insert .= "NULL".$sep;
				}
				elseif ($rec != "") {
					$schema_insert .= '"'.$rec.'"'.$sep;
				}
				else {
					$schema_insert .= "".$sep;
				}
			}
			$schema_insert = str_replace($sep."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			print(trim($schema_insert));
			print "\n";
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
