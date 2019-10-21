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

function show_easy_numpad(id, newType, initialValue, header, showDecimalPoint, newPrefix, newSuffix) {
    articleId = id;
    value = initialValue;
    prefix = newPrefix;
    suffix = newSuffix;
    type = newType;
    var easy_numpad = `
        <div class="easy-numpad-frame" id="easy-numpad-frame">
            <div class="easy-numpad-container">
                <div class="easy-numpad-output-container">`+ header +`
                    <p class="easy-numpad-output" id="easy-numpad-output"></p>
                </div>
                <div class="easy-numpad-number-container">
                    <table>
                        <tr>
                            <td><a href="7" onclick="easynum()">7</a></td>
                            <td><a href="8" onclick="easynum()">8</a></td>
                            <td><a href="9" onclick="easynum()">9</a></td>
                            <td><a href="Del" class="del" id="del" onclick="easy_numpad_del()">Zurück</a></td>
                        </tr>
                        <tr>
                            <td><a href="4" onclick="easynum()">4</a></td>
                            <td><a href="5" onclick="easynum()">5</a></td>
                            <td><a href="6" onclick="easynum()">6</a></td>
                            <td><a href="Clear" class="clear" id="clear" onclick="easy_numpad_clear()">Löschen</a></td>
                        </tr>
                        <tr>
                            <td><a href="1" onclick="easynum()">1</a></td>
                            <td><a href="2" onclick="easynum()">2</a></td>
                            <td><a href="3" onclick="easynum()">3</a></td>
                            <td><a href="Cancel" class="cancel" id="cancel" onclick="easy_numpad_cancel()">Abbrechen</a></td>
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
                            <td><a href="Done" class="done" id="done" onclick="easy_numpad_done()">OK</a></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    `;
    $('body').append(easy_numpad);
    $('#easy-numpad-output').text(prefix + value + suffix);
}

function easy_numpad_close() {
    $('#easy-numpad-frame').remove();
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
    $('#easy-numpad-output').text(prefix + value + suffix);
}
function easy_numpad_del() {
    event.preventDefault();
    
    var value_del = value.slice(0, -1);
    value = value_del;
    $('#easy-numpad-output').text(prefix + value + suffix);
    
}
function easy_numpad_clear() {
    event.preventDefault();   
    value = "";
    $('#easy-numpad-output').text(prefix + value + suffix);
}
function easy_numpad_cancel() {
    event.preventDefault();
    $('#easy-numpad-frame').remove();
}
function easy_numpad_done() {
    event.preventDefault();
    
    if (value == 0 || value == "") {
        console.log("Invalid input!");
        firework.launch("Ungültige Eingabe!", 'error', 3000);
        return; // Do not close numpad
    }
    
    console.log("articleId: " + articleId + ", value: " + value);
//     $('.easy-put').val(easy_numpad_output_val);
    
    if(type == "articleQuantity") {
        addArticleWithQuantityToBasket(articleId, value);
    }
    else if (type == "basketQuantity") {
        updateArticleQuantityInBasket(articleId, value);
    }
    else {
        console.log("Invalid type!");
    }
    
    easy_numpad_close();
}
