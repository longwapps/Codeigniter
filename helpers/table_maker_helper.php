<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * make_table
 *
 * generate html table block
 *
 * @head_part(array) table head field values
 * @body_part(array) table body field values
 * @return (string)
 */
if(!function_exists('make_table')){
	
	function make_table($head_part, $body_part, $tfoot = TRUE, $class = '', $id = '')
	{
		$head_html = 		"<tr>";
		foreach($head_part as $head){
			$head_html .= 		"<th class='text-center'>{$head}</th>";
		}
		$head_html .= 		"</tr>";
		
		$body_html = "<tbody>";
		foreach($body_part as $body){
			$body_html .=	"<tr>";
			foreach($body as $body_item){
				$body_html .=	"<td style='vertical-align: middle;' class='text-center'>{$body_item}</td>";
			}
			$body_html .=	"</tr>";
		}
		$body_html .= "</tbody>";
		
		$class = $class != '' ? " ".$class : $class;
		$id = $id != '' ? "id='{$id}'" : $id;
		
		$table = "<table class='table{$class}' {$id}>
					      <thead>";
		$table .= $head_html;
		$table .= 	"</thead>";
		
		$table .= $body_html;
		
		if($tfoot){
			$table .=	"<tfoot>";
			$table .= $head_html;
			$table .=	"</tfoot>";
		}
		
		$table .= "</table>";
				
		return $table;
	}
	
}
