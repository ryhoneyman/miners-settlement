/*
    Color Picker Script:
*/
let colorPicker = document.getElementById('color-picker');
var sliderR = document.getElementById('picker-slider-r');
var sliderG = document.getElementById('picker-slider-g');
var sliderB = document.getElementById('picker-slider-b');
var sliderGray = document.getElementById('picker-slider-gray');

var pickerOutputColor = document.getElementById('picker-output-color');
var pickerOutputText = document.getElementById('picker-output-field');

function validateUserinput() {
    // Assign Value on Execution
    const input = pickerOutputText.value;

    // Use Regex to Validate Input
    let hexRegex = new RegExp(/^<#([A-Fa-f0-9]{3})>$/);

    // If Value is Hexcode, Update the UI, Else Reset UI
    if (hexRegex.test(input) == true) {
        let parsedHexcode = input.replace(/[^a-z0-9]/gi, '');

        // If Clean Value is 3 digits, Update Output Color
        if (parsedHexcode.length === 3) {
            pickerOutputColor.style.backgroundColor = `#${parsedHexcode}`;
        }
    } else {
        pickerOutputText.classList.add('picker-validation-border');
    }
}

function updateGrayscale() {
    // Convert Int to String for Grayscale Value
    var hexGrayInt = sliderGray.value;
    var hexGrayStr = parseInt(sliderGray.value).toString(16)

    // Convert Individual Color to Hexcode
    var hexcodeValue = hexGrayStr + hexGrayStr + hexGrayStr;
    var hexCodeCSS = `#${hexcodeValue}`;
    var hexCodeMarkup = `<#${hexcodeValue}>`;

    // Update Slider Accent Color
    sliderR.style.accentColor = `#${hexGrayStr}00`;
    sliderG.style.accentColor = `#0${hexGrayStr}0`;
    sliderB.style.accentColor = `#00${hexGrayStr}`;
    sliderGray.style.accentColor = hexCodeCSS;

    // Update Slider RGB Values
    sliderR.value = hexGrayInt;
    sliderG.value = hexGrayInt;
    sliderB.value = hexGrayInt;

    // Update Output Colors
    pickerOutputColor.style.backgroundColor = hexCodeCSS;
    pickerOutputText.value = hexCodeMarkup;
}

function updateColor() {
    // Reset Gray Slider on RGB Update
    sliderGray.value = 0;

    // Convert Int to String for RGB Values
    var hexStrR = parseInt(sliderR.value).toString(16);
    var hexStrG = parseInt(sliderG.value).toString(16);
    var hexStrB = parseInt(sliderB.value).toString(16);

    // Convert Individual Colors to Hexcode
    var hexcodeValue = hexStrR + hexStrG + hexStrB;
    var hexCodeCSS = `#${hexcodeValue}`;
    var hexCodeMarkup = `<#${hexcodeValue}>`;

    // Update Slider Accent Color
    sliderR.style.accentColor = `#${hexStrR}00`;
    sliderG.style.accentColor = `#0${hexStrG}0`;
    sliderB.style.accentColor = `#00${hexStrB}`;

    // Update Output Colors
    pickerOutputColor.style.backgroundColor = hexCodeCSS;
    pickerOutputText.value = hexCodeMarkup;
}

function resetColor() {
    pickerOutputText.classList.remove('picker-validation-border');
    pickerOutputText.value = '<#f00>';
    sliderR.style.accentColor = `#F00`;
    sliderR.value = 15;
    sliderG.value = 0;
    sliderB.value = 0;
    sliderGray.value = 0;
}

colorPicker.addEventListener('input', (event) => {
    // Remove Validation on Input Event
    pickerOutputText.classList.remove('picker-validation-border');

    // Determine Element ID
    var element = event.target;
    var elementId = element.getAttribute('id');

    // Process by Element ID
    if (elementId === 'picker-output-field') {
        setTimeout(() => {
            validateUserinput();
        }, 1250);
    } else if (elementId === 'picker-slider-gray') {
        updateGrayscale();
    } else {
        updateColor();
    }
});

$(function () {
  resetColor();
})

