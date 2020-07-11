// add css classes to select light and dark color schemes
// reference: https://zocada.com/dark-and-light-theme-switcher-using-css-variables-and-pure-javascript/
// date: July 4, 2020
//
// requires 2 css classes in your css file that include your color vars
// .darkMode {}
// .lightMode {}
//
// example css:
// .lightMode {--ios-label: #000000ff;}
// .darkMode {--ios-label: #ffffffff;}
//
// requires checkbox element in your html file, like in a settings div
// id='darkModeToggle'
// onclick="toggleDarkModeTheme()"
//
// add this near the bottom of your html file
// <script src="./darkMode.js"></script>

// Immediately invoked function to set the theme on initial load
(function () {
    //document.documentElement.className = 'darkMode';
    if (localStorage.getItem('darkModeTheme') === 'darkMode') {
        setDarkModeTheme('darkMode');
        document.getElementById("darkModeToggle").checked = true;
    } else {
        setDarkModeTheme('lightMode');
    }
})();

// function to set a given theme/color-scheme
function setDarkModeTheme(themeName) {
    localStorage.setItem('darkModeTheme', themeName);
    //This adds a class to the root <html>
    document.documentElement.className = themeName;
}

// function to toggle between light and dark theme
function toggleDarkModeTheme() {
    if (localStorage.getItem('darkModeTheme') === 'darkMode') {
        setDarkModeTheme('lightMode');
    } else {
        setDarkModeTheme('darkMode');
    }
}