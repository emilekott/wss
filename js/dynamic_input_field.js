//Add more fields dynamically.
function addFields(area,field,limit,prefix,sufix) {
	if(!document.getElementById) return; //Prevent older browsers from getting any further.
	var field_area = document.getElementById(area);
	var all_inputs = field_area.getElementsByTagName("input"); //Get all the input fields in the given area.
	//Find the count of the last element of the list. It will be in the format '<field><number>'. If the 
	//		field given in the argument is 'friend_' the last id will be 'friend_4'.
	var last_item = all_inputs.length - 1;
	var last = all_inputs.item(last_item).name;
	
	var count = Number(last.split("[")[1].replace(/].*/, "")) + 1;
	
	//If the maximum number of elements have been reached, exit the function.
	//		If the given limit is lower than 0, infinite number of fields can be created.
	if(count > limit && limit > 0) return;
 	
	if(document.createElement) { //W3C Dom method.
		var input={type:'input',attributes:{type:'text',name:field+count+']'}};
		addSingleField(field_area, prefix);
		addSingleField(field_area, input);
		addSingleField(field_area, sufix);
	} else { //Older Method
		//field_area.innerHTML += "<input name='"+(field+count)+"' id='"+(field+count)+"' type='text' />";
		  field_area.innerHTML += prefix+"<input name='"+(field+count)+"]' type='text' />"+sufix;	
	}
}
function addSingleField(field_area, new_field){
	// Create the field 
	var new_element = createElement(new_field);
	// Append
	field_area.appendChild(new_element);
}
function createElement(prefix){
	var prefix_element = document.createElement(prefix['type']);
	if(prefix['innerText'])
		prefix_element.appendChild(document.createTextNode(prefix['innerText']));
		
	for(var keyVar in prefix['attributes']) {
		prefix_element[keyVar]=prefix['attributes'][keyVar];
	}
	return prefix_element;
}