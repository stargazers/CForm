<?php

/* 
CForm. Basic HTML Form creator.
Copyright (C) 2010 Aleksi Räsänen <aleksi.rasanen@runosydan.net>

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

	// **************************************************
	//	Class constructor
	/*!
		@brief $url URL what goes to from action field.

		@brief $method POST, GET, FILE and so on.

		@return None.
	*/
	// **************************************************
	public function __construct( $url, $method )
	{
		$this->method = $method;
		$this->url = $url;

		$this->form = '<form action="' . $url . '"'
			. ' method="' . $method . '">';
		$this->form .= "\n";
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

		$this->form_items[] = array( 'text', $caption, 
			$name, $id, $value );
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

		$this->form_items[] = array( 'password', $caption, 
			$name, $id, $value );
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

		$this->form_items[] = array( 'submit', $name, 
			$id, $value );
	}

	// **************************************************
	//	textToHTML
	/*!
		@brief Convert text inputbox element array to HTML input.

		@return String.
	*/
	// **************************************************
	private function textToHTML( $items )
	{
		return '<input type="text" name="' . $items[2] . '" '
			. 'id="' . $items[3] . '" value="' . $items[4] . '">';
	}

	// **************************************************
	//	passwordToHTML
	/*!
		@brief Convert password element array to HTML password.

		@return String.
	*/
	// **************************************************
	private function passwordToHTML( $items )
	{
		return '<input type="password" name="' . $items[2] . '" '
			. 'id="' . $items[3] . '">';
	}

	// **************************************************
	//	submitToHTML
	/*!
		@brief Convert Submit-button array to HTML Submit.

		@return String.
	*/
	// **************************************************
	private function submitToHTML( $items )
	{
		return '<input type="submit" name="' . $items[1] . '" '
			. 'id="' . $items[2] . '" value="' . $items[3] . '">';
	}

	// **************************************************
	//	itemsToHTML
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
	private function itemsToHTML()
	{
		$items = array();
		$max = count( $this->form_items );
		$fi = $this->form_items;
		$caption = '';

		for( $i=0; $i < $max; $i++ )
		{
			// In first index of form_items there is defined
			// if this is a textfield, passwordfield, submit or so on.
			switch( $fi[$i][0] )
			{
				case 'text':
					$caption = $fi[$i][1];
					$ret = $this->textToHTML( $fi[$i] );
					break;

				case 'password':
					$caption = $fi[$i][1];
					$ret = $this->passwordToHTML( $fi[$i] );
					break;

				case 'submit':
					$caption = '';
					$ret = $this->submitToHTML( $fi[$i] );
					break;
			}

			// Add new array in $items array. We must add array
			// with two elements. First one is the caption what
			// will be used in table, second one is our html what
			// we generated above to variable $ret.
			$items[] = array( $caption, $ret );
		}

		return $items;
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
		$items = $this->itemsToHTML();

		// Create HTML table with elements we have added to form.
		$this->form .= $this->createTable( $items );

		$this->form .= '</form>';
		$this->form .= "\n";
		return $this->form;
	}
}

?>
