import { drawLabels, drawStreetLabels} from "./map.js";

export const checkboxes = document.querySelectorAll("input[type=checkbox]");
const menu = document.getElementById("menu");
const settingsContainer = document.getElementById("settingsContainer");
const incorrectPwd = document.getElementById("wrongPwd").content;
const pwdInput = document.getElementById("pwd");

let currentState = 0;

// settings box ---------------------------------------------------------------------------
export const setupListener = () => {
    //opening/closing
    menu.addEventListener("click", settingsAnimation);
    //setup checkboxes
    for(const checkbox of checkboxes){
        checkbox.addEventListener("change", () => {
        drawLabels(false);
        drawStreetLabels(false);
        })
    }
    if(+incorrectPwd == 1){
        settingsAnimation();
        pwdInput.style.backgroundColor = "red";
        setTimeout(() => {
            pwdInput.style.backgroundColor = "white";
        }, 5000);
    }
}

export const settingsAnimation = () => {
    settingsContainer.style.transform = `translateX(${100 * currentState}%)`;
    currentState == 1 ? currentState = 0 : currentState = 1;
}