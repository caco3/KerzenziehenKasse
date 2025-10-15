// Source: https://github.com/gayanSandamal/easy-numpad

$(document).ready(function () {
    $('.easy-get').on('click', () => {
        show_easy_numpad();
    });
});

var articleId;
var value;
var prefix;
var suffix;
var type;
var initialValue;


function show_easy_numpad(id, newType, initialValue1, header, showDecimalPoint, newPrefix, newSuffix) {
    console.log(id, newType, initialValue1, showDecimalPoint, newPrefix, newSuffix);
    articleId = id;
    //value = initialValue;
	initialValue = initialValue1;
	value = 0;
    prefix = newPrefix;
    suffix = newSuffix;
    type = newType;

    if (type == "articleQuantity" && suffix.trim() == "g") {
        var scale_div = `
                    <div id="scale-div" class="scale-div">
                        <p><br></p>
                        <hr>
                        <h3>Waage:</h3>
                        <p class=scale-output>&nbsp;</p>
                        <div style="display: flex; justify-content: space-between;">
                            <p id="scale-output" class="scale-output"></p>
                            <p id="scale-button"><a href="scale_done" class="scale_done" id="scale_done" style="background-color: #388E3C; padding: 32px 64px; color: white;" onclick="scale_done()">&Uuml;bernehmen</a></p>
                        </div>
                        <p><br><br></p>
                        <hr>
                    </div>
                    `;
        var sub_header = `Manuelle Eingabe:`;
    }
    else {
        var scale_div = ``;
        if (initialValue != 0) {
            var sub_header = `Korrektur:`;
        }
        else {
            var sub_header = ``;
        }
    }

    var easy_numpad = `
        <div class="easy-numpad-frame" id="easy-numpad-frame">
            <div class="easy-numpad-container">
                <div class="easy-numpad-output-container">`+ header +`
                    ` + scale_div + `<h3>` + sub_header + `</h3>
                    <p class="easy-numpad-output" id="easy-numpad-output"></p>
                </div>
                <div class="easy-numpad-number-container">
                    <table>
                        <tr>
                            <td><a href="7" onclick="easynum()">7</a></td>
                            <td><a href="8" onclick="easynum()">8</a></td>
                            <td><a href="9" onclick="easynum()">9</a></td>
                            <td><a href="Del" class="del" id="del" onclick="easy_numpad_del()">&#8678; Zurück</a></td>
                        </tr>
                        <tr>
                            <td><a href="4" onclick="easynum()">4</a></td>
                            <td><a href="5" onclick="easynum()">5</a></td>
                            <td><a href="6" onclick="easynum()">6</a></td>
                            <td><a href="Cancel" class="cancel" id="cancel" onclick="easy_numpad_cancel()">&#10008 Abbrechen</a></td>
                        </tr>
                        <tr>
                            <td><a href="1" onclick="easynum()">1</a></td>
                            <td><a href="2" onclick="easynum()">2</a></td>
                            <td><a href="3" onclick="easynum()">3</a></td>
                            <td rowspan="2"><a href="Done" class="done" id="done" onclick="easy_numpad_done()">OK</a></td>
                        </tr>
                        <tr>`;
                        
                        if (showDecimalPoint == true) {
                            easy_numpad += `<td colspan="2" onclick="easynum()"><a href="0">0</a></td>
                                            <td onclick="easynum()"><a href=".">.</a></td>`;
                        }
                        else {
                            easy_numpad += `<td colspan="3" onclick="easynum()"><a href="0">0</a></td>`;
                        }
                        
                        easy_numpad += `
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    `;
    $('body').append(easy_numpad);    
    updateValueField(value);
}


function easy_numpad_close() {
    $('#easy-numpad-frame').remove();
}


function updateValueField(value) {	
	if (initialValue != 0) {

		$('#easy-numpad-output').html("<span style=\"color: gray\">" + prefix + initialValue + suffix + "&nbsp;&nbsp;&#x2192;&nbsp;&nbsp;</span>" + prefix + value + suffix);
	}
	else {
		$('#easy-numpad-output').html(prefix + value + suffix);
	}
}


function easynum() {
    event.preventDefault();

    navigator.vibrate = navigator.vibrate || navigator.webkitVibrate || navigator.mozVibrate || navigator.msVibrate;
    if (navigator.vibrate) {
        navigator.vibrate(60);
    }

    var easy_num_button = $(event.target);
    var easy_num_value = easy_num_button.text();
    value += easy_num_value;
    if (easy_num_value == ".") {        
        var dotCount = value.split(".").length-1; // count dots
        if (dotCount > 1) {
            value = value.slice(0, -1); // undo adding dot
        }
    }
    else {
       // value = value * 1;
		// Check that we have max 2 digits behind the dot
        var dotCount = value.split(".").length-1; // count dots
		if (dotCount > 0) {
			charactersBehindDot = value.length - value.indexOf(".") - 1;
			if (charactersBehindDot > 2) {
				value = value.slice(0, 2-charactersBehindDot);
			}
		}
    }
	
	// Remove leading zero
	if (value.length > 1 && value.charAt(0) == "0") {
		value = value.slice(1);  // Cut off first character
		console.log("Removed leading 0");
	}
	
    updateValueField(value);
}


function easy_numpad_del() {
    event.preventDefault();
    console.log(value);
    var value_del = ("" + value).slice(0, -1);
    value = value_del;
	console.log(value);
	if (value == "") {
		value = "0";
	}
    updateValueField(value);    
}


function easy_numpad_clear() {
    event.preventDefault();   
    value = "0";    
    updateValueField(value);
}


function easy_numpad_cancel() {
    event.preventDefault();
    $('#easy-numpad-frame').remove();
}


function easy_numpad_done() {
    event.preventDefault();
	
	if (value == "") {
		value = 0;
	}
	    
    error = false;
    if((type == "articleQuantity") || (type == "basketQuantity")) {
        if (value == 0) {
            error = true;
			if (suffix == " g") {
				firework.launch("Gewicht kann nicht 0 sein!", 'error', 3000);
			}
			else {
				firework.launch("Menge kann nicht 0 sein!", 'error', 3000);
			}
        }
    }
    else if(value === "") {
        error = true;
        firework.launch("Ungültige Eingabe!", 'error', 3000);
    }
    
    if (error == true) {
        console.log("Invalid input!");
        return; // Do not close numpad   
    }
    
    console.log("articleId: " + articleId + ", value: " + value);
//     $('.easy-put').val(easy_numpad_output_val);
    
    if(type == "articleQuantity") {
        addArticleWithQuantityToBasket(articleId, value);
    }
    else if ((type == "basketQuantity") || (type == "basketTotal") || (type == "basketDonation")) {
        updateBasketEntry(articleId, value);
    }
    else {
        console.log("Invalid type!");
    }
    
    easy_numpad_close();
}



var scale_value = 0;

function update_display(value, text, is_value) {
    // console.log("is_value: ", is_value, text);
    output = document.getElementById("scale-output");
    button = document.getElementById("scale-button");

    scale_value = value;

    if (output != null) {
        output.innerHTML = text;
    }

    if (button != null) {
        if (is_value) {
            // console.log("Show scale button");
            button.style.display = 'inline-block';
        }
        else {
            // console.log("Hide scale button");
            button.style.display = 'none';
        }
    }
}


function scale_done() {
    event.preventDefault();

    if (scale_value == "" || scale_value == null) {
        scale_value = 0;
    }

    error = false;

    if (scale_value == 0) {
        error = true;
        firework.launch("Gewicht kann nicht 0 sein!", 'error', 3000);
    }
    else if (scale_value < 0) {
        error = true;
        firework.launch("Bitte zuerst tarieren (Gewicht darf nicht negativ sein)!", 'error', 3000);
    }

    if (error == true) {
        console.log("Invalid input!");
        return; // Do not close numpad
    }

    console.log("articleId: " + articleId + ", value: " + scale_value);

    addArticleWithQuantityToBasket(articleId, scale_value);

    easy_numpad_close();
}
