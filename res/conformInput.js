/**********************************************************
 * Conforms form inputs
 * JS Functions can be called on event: onChange, onKeyUp....
 *
 *********************************************************/

/**
 * Conform date
 *
 * @param	Element		element		Element
 * @return void
*/
function conformDate(element) {
	var dateL, dateR;	
	var d = element.value;
	d.toString(); 
	if ( ( (d.slice(2,3)) != ("-") ) && (d.length >= 3) ){
		if (d.slice(0,2)>31) { 
			dateL = '31'; 
		} else { 
			dateL = d.slice(0,2); 
		}
		dateR = d.slice(2);
		element.value = dateL + "-" + dateR;
	}
	if ( ( (d.slice(5,6)) != ("-") ) && (d.length >= 6) ){
		if (d.slice(3,5)>12) { 
			dateL = d.slice(0,3)+'12'; 
		} else { 
			dateL = d.slice(0,5); 
		}
		dateR = d.slice(5);
		element.value = dateL + "-" + dateR;
	}
	if (d.slice(6).length >4) {
		var value = element.value;
		dateL = value.slice(0,5);
		dateR = value.slice(6,10);
		element.value = dateL + "-" + dateR;
	}	
}

 /**
 * Conform datetime
 *
 * @param	Element		element		Element
 * @return void
*/
function conformDatetime(element) {
	var dateL, dateR;	
	var d = element.value;
	d.toString();
	if ( ( (d.slice(2,3)) != (":") ) && (d.length >= 3) ){
		if (d.slice(0,2)>23) { 
			dateL = '23'; 
		} else { 
			dateL = d.slice(0,2); 
		}
		dateR = d.slice(2);
		element.value = dateL + ":" + dateR;
	}
	if ( ( (d.slice(5,6)) != (" ") ) && (d.length >= 6) ){
		if (d.slice(3,5)>59) { 
			dateL = d.slice(0,3)+'59'; 
		} else { 
			dateL = d.slice(0,5); 
		}
		dateR = d.slice(5);
		element.value = dateL + " " + dateR;
	}
	if ( ( (d.slice(8,9)) != ("-") ) && (d.length >= 9) ){
		if (d.slice(6,8)>31) { 
			dateL = d.slice(0,6)+'31'; 
		} else { 
			dateL = d.slice(0,8); 
		}
		dateR = d.slice(8);
		element.value = dateL + "-" + dateR;
	}
	if ( ( (d.slice(11,12)) != ("-") ) && (d.length >= 12) ){
		if (d.slice(9,11)>12) { 
			dateL = d.slice(0,9)+'12'; 
		} else { 
			dateL = d.slice(0,11); 
		}
		dateR = d.slice(11);
		element.value = dateL + "-" + dateR;
	}
	if (d.slice(12).length >4) {
		var value = element.value;
		dateL = value.slice(0,11);
		dateR = value.slice(12,16);
		element.value = dateL + "-" + dateR;
	}
}

 /**
 * Conform time
 *
 * @param	Element		element		Element
 * @return void
*/
function conformTime(element) {
	var timeL, timeR;	
	var t = element.value;
	t.toString();
	
	if ( ( (t.slice(2,3)) != (":") ) && (t.length >= 3) ){
		if (t.slice(0,2)>23) { 
			timeL = '23'; 
		} else { 
			timeL = t.slice(0,2); 
		}
		timeR = t.slice(3);
		element.value = timeL + ":" + timeR;
	}
	if (t.slice(3).length >2) {
		var value = element.value;
		timeL = value.slice(0,2);
		timeR = value.slice(3,5);
		element.value = timeL + ":" + timeR;
	}
}

 /**
 * Conform timesec
 *
 * @param	Element		element		Element
 * @return void
*/
function conformTimesec(element) {
	var timeL, timeR;	
	var t = element.value;
	t.toString();
	
	if ( ( (t.slice(2,3)) != (":") ) && (t.length >= 3) ){
		if (t.slice(0,2)>23) { 
			timeL = '23'; 
		} else { 
			timeL = t.slice(0,2); 
		}
		timeR = t.slice(3);
		element.value = timeL + ":" + timeR;
	}
	if ( ( (t.slice(5,6)) != (":") ) && (t.length >= 6) ){
		if (t.slice(3,5)>59) { 
			timeL = '59'; 
		} else { 
			timeL = t.slice(0,5); 
		}
		timeR = t.slice(3);
		element.value = timeL + ":" + timeR;
	}
	if (t.slice(6).length >2) {
		var value = element.value;
		timeL = value.slice(0,5);
		timeR = value.slice(6,8);
		element.value = timeL + ":" + timeR;
	}
}

/**
 * Conform int
 *
 * @param	Element		element		Element
 * @return void
 */
function conformInt(element) {
	if (element.value != '') {
		var number = parseInt(element.value);
		if (isNaN(number))
			element.value = 0;
		else 
			element.value = number;
	}
}

/**
 * Conform double
 *
 * @param	Element		element		Element
 * @return void
 */
function conformFloat(element) {
	if (element.value != '') {
		var mount = parseFloat(element.value);
		if (isNaN(mount))
			element.value = 0;
		else
			element.value = mount;
	}
}

/**
 * Conform alphanum
 *
 * @param	Element		element		Element
 * @return void
 */
function conformAlphanum(element) {
	if (element.value != '') {
		var reg = new RegExp('[^a-zA-Z0-9]+', 'g');
		var theStrings = element.value.split(reg);
		var result = '';
		for (i=0; i<theStrings.length; i++) {
			result += theStrings[i];
		}
		element.value = result;
	}
}

/**
 * Conform upper case
 *
 * @param	Element		element		Element
 * @return void
 */
function conformUpper(element) {
	if (element.value != '') {
		element.value = element.value.toUpperCase();
	}
}

/**
 * Conform lower case
 *
 * @param	Element		element		Element
 * @return void
 */
function conformLower(element) {
	if (element.value != '') {
		element.value = element.value.toLowerCase();
	}
}

/**
 * Conform nospace
 *
 * @param	Element		element		Element
 * @return void
 */
function conformNospace(element) {
	if (element.value != '') {
		var strings = element.value.split(' ');
		var result = '';
		for (i=0; i<strings.length; i++) {
			result += strings[i];
		}
		element.value = result;
	}
}