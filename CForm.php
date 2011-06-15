<?php

/* 
CForm. Basic HTML Form creator.
Copyright (C) 2010, 2011 Aleksi Räsänen <aleksi.rasanen@runosydan.net>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

error_reporting( E_ALL );

// **************************************************
//	CForm
/*!
	@brief Form generation class for PHP.
	@author Aleksi Räsänen 2010
	@email aleksi.rasanen@runosydan.net
*/
// **************************************************
class CForm
{
	//! Here we save our form 
	private $form = '';

	//! Goes to form action-field
	private $url;

	//! Method, POST, GET, FILE etc.
	private $method;

	//! Here we store elements before we create array.
	private $form_items = array();

	//! Form name what we generate here.
	private $form_name;

	// **************************************************
	//	Class constructor
	/*!
		@brief Class constructor
		@param $url URL what goes to from action field.
		@param $method POST, GET, FILE and so on.
		@return None.
	*/
	// **************************************************
	public function __construct( $url, $method )
	{
		$this->method = $method;
		$this->url = $url;
		$this->form_name = 'form_' . basename( $url );
	}

	// **************************************************
	//	createTable
	/*!
		@brief Create HTML table and add rows CSS class
		  'odd' and 'even'.
		@param $values Array of values.
		@return Generated HTML in string.
	*/
	// **************************************************
	public function createTable( $values )
	{
		$out = '<table>';
		$tmp = 0;

		foreach( $values as $val )
		{
			if( $tmp == 2 )
				$tmp = 0;

			if( $tmp == 0 )
				$out .= '<tr class="odd">';
			else
				$out .= '<tr class="even">';

			$num_vals = count( $val );
			for( $i=0; $i < $num_vals; $i++ )
			{
				$out .= '<td>';
				$out .= $val[$i];
				$out .= '</td>';
			}

			$out .= '</tr>';
			$tmp++;
		}

		$out .= '</table>';
		return $out;
	}

	// ************************************************** 
	//  addFileButton
	/*!
		@brief Add File selection button to form
		@param $caption Caption to show in button
		@param $name HTML element name
		@param $id Unique HTML element ID
	*/
	// ************************************************** 
	public function addFileButton( $caption, $name, $id='' )
	{
		$this->form_items[] = array(
			'type' => 'file',
			'caption' => $caption,
			'name' => $name,
			'id' => $id,
			'value' => '' );
	}

	// **************************************************
	//	addTextField
	/*!
		@brief Add text field to form.
		@param $caption Caption to show in a HTML table.
		@param $name HTML element name.
		@param $id Unique ID form HTML id-element.
		@param $value Default text to element.
		@return None.
	*/
	// **************************************************
	public function addTextField( $caption, $name, $id='', $value='' )
	{
		if( $id == '' )
			$id = $name . '_id';

		$this->form_items[] = array(
			'type' => 'text',
			'caption' => $caption,
			'name' => $name,
			'id' => $id,
			'value' => $value );
	}

	// **************************************************
	//	addHiddenField
	/*!
		@brief Add hidden form field.
		@param $name Hidden field name
		@param $value Hidden field value.
		@return None.
	*/
	// **************************************************
	public function addHiddenField( $name, $value )
	{
		$this->form_items[] = array(
			'type' => 'hidden',
			'caption' => '',
			'name' => $name,
			'id' => '',
			'value' => $value );
	}

	// **************************************************
	//	addCombobox
	/*!
		@brief Create a crombobox
		@param $caption Combobox caption
		@param $name Name of combobox.
		@param $fields Fields to add in array.
		@param $id Optional. ID of this combobox.
	*/
	// **************************************************
	public function addCombobox( $caption, $name, $fields, $id='' )
	{
		if( $id == '' )
			$id = $name . '_id';

		$this->form_items[] = array( 
			'type' => 'option',
			'caption' => $caption,
			'name' => $name,
			'id' => $id,
			'fields' => $fields );
	}

	// **************************************************
	//	addPasswordField
	/*!
		@brief Add password field to form.
		@param $caption Caption to show in a HTML table.
		@param $name HTML element name.
		@param $id Unique ID form HTML id-element.
		@return None.
	*/
	// **************************************************
	public function addPasswordField( $caption, $name, $id='' )
	{
		if( $id == '' )
			$id = $name . '_id';

		$this->form_items[] = array(
			'type' => 'password',
			'caption' => $caption,
			'name' => $name,
			'id' => $id );
	}

	// **************************************************
	//	addSubmit
	/*!
		@brief Add Submit button to form.
		@param $value Text what will be shown in Submit-button.
		@param $name HTML element name.
		@param $id Unique ID form HTML id-element.
		@return None.
	*/
	// **************************************************
	public function addSubmit( $value, $name, $id='')
	{
		if( $id == '' )
			$id = $name . '_id';

		$this->form_items[] = array(
			'type' => 'submit',
			'caption' => '',
			'name' => $name,
			'id' => $id,
			'value' => $value );
	}

	// **************************************************
	//	setFormName
	/*!
		@brief Set form name what will be in <form name="something"
		@param $name Form name
		@return None.
	*/
	// **************************************************
	public function setFormName( $name )
	{
		$this->form_name = $name;
	}

	// **************************************************
	//	createForm
	/*!
		@brief Create form with already added elements.
		@return HTML Form as a string.
	*/
	// **************************************************
	public function createForm()
	{
		// Process form_items array to HTML format so we can
		// create HTML table.
		$items = $this->items_to_html();

		$this->form = '<form action="' . $this->url . '"';

		if( $this->method == 'file' )
		{
			$this->method = 'post';
			$this->form .= ' enctype="multipart/form-data"';
		}

		$this->form .= ' name="' . $this->form_name . '"'
			. ' method="' . $this->method . '">' . "\n";

		// Create HTML table with elements we have added to form.
		$this->form .= $this->createTable( $items );

		$this->form .= '</form>';
		$this->form .= "\n";
		$this->form .= '<script type="text/javascript">' . "\n";
		$this->form .= 'document.forms["' . $this->form_name 
			. '"].elements["' . $this->form_items[0]['name'] 
			. '"].focus();';
		$this->form .= '</script>' . "\n";

		return $this->form;
	}

	// **************************************************
	//	text_to_html
	/*!
		@brief Convert text inputbox element array to HTML input.
		@return String.
	*/
	// **************************************************
	private function text_to_html( $items )
	{
		return '<input type="text" name="' . $items['name'] . '" '
			. 'id="' . $items['id'] . '" value="' . $items['value'] . '">';
	}

	// **************************************************
	//	password_to_html
	/*!
		@brief Convert password element array to HTML password.
		@return String.
	*/
	// **************************************************
	private function password_to_html( $items )
	{
		return '<input type="password" name="' . $items['name'] . '" '
			. 'id="' . $items['id'] . '">';
	}

	// **************************************************
	//	submit_to_html
	/*!
		@brief Convert Submit-button array to HTML Submit.
		@return String.
	*/
	// **************************************************
	private function submit_to_html( $items )
	{
		return '<input type="submit" name="' . $items['name'] . '" '
			. 'id="' . $items['id'] . '" value="' . $items['value'] . '">';
	}

	// **************************************************
	//	hidden_to_html
	/*!
		@brief Convert hidden input field to HTML.
		@return String.
	*/
	// **************************************************
	private function hidden_to_html( $items )
	{
		return '<input type="hidden" name="' . $items['name'] . '" '
			. 'value="' . $items['value'] . '">';
	}

	// ************************************************** 
	//  file_to_html
	/*!
		@brief Creates a file selector to HTML String
		@param $items Item informations array
		@return HTML String
	*/
	// ************************************************** 
	private function file_to_html( $items )
	{
		return '<input type="file" name="' . $items['name'] . '" '
			. 'value="' . $items['value'] . '">';
	}

	// **************************************************
	//	option_to_html
	/*!
		@brief Convert Option-Select to HTML combobox.
		@return String.
	*/
	// **************************************************
	private function option_to_html( $items )
	{
		$ret = '<select name="' . $items['name'] . '">' . "\n";

		// We have array inside $items array where must be
		// key and value pairs. In $key we must have name of selection
		// for "value" field in HTML, in $value we have visible part
		// what will be inside <option></option> tags.
		foreach( $items['fields'] as $key => $value )
		{
			$ret .= "\t";

			// If this text fitst letter is _, then we remove that
			// underscore and make this item as "selected".
			if( substr( $value, 0, 1 ) == '_' )
			{
				$value = substr( $value, 1 );
				$ret .= '<option value="' . $key . '" selected>';
			}
			else
			{
				$ret .= '<option value="' . $key . '">';
			}

			$ret .= $value;
			$ret .= '</option>';
			$ret .= "\n";
		}

		$ret .= '</select>';
		$ret .= "\n";
		return $ret;
	}

	// **************************************************
	//	items_to_html
	/*!
		  @brief This method checks class private variable
		    $form_items array, check what kind of element it is,
		    eg. textbox or password field and then calls correct
		    method to convert it to HTML. Then this method
		    add converted HTML to return array where we add
		    two array items per array row, eg. Caption and
		    item like inputbox.
		    This method must be called before we create a form,
		    because table creation cannot use $form_items
		    array directly!
		  @return Array.
	 */
	// **************************************************
	private function items_to_html()
	{
		$items = array();
		$max = count( $this->form_items );
		$fi = $this->form_items;
		$caption = '';

		for( $i=0; $i < $max; $i++ )
		{
			// In first index of form_items there is defined
			// if this is a textfield, passwordfield, submit or so on.
			switch( $fi[$i]['type'] )
			{
				case 'text':
					$ret = $this->text_to_html( $fi[$i] );
					break;

				case 'password':
					$ret = $this->password_to_html( $fi[$i] );
					break;

				case 'submit':
					$ret = $this->submit_to_html( $fi[$i] );
					break;
				
				case 'file':
					$ret = $this->file_to_html( $fi[$i] );
					break;

				case 'option':
					$ret = $this->option_to_html( $fi[$i] );
					break;

				case 'hidden':
					$ret = $this->hidden_to_html( $fi[$i] );
					break;
			}

			// Add new array in $items array. We must add array
			// with two elements. First one is the caption what
			// will be used in table, second one is our html what
			// we generated above to variable $ret.
			$items[] = array( $fi[$i]['caption'], $ret );
		}

		return $items;
	}
}

?>
