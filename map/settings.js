import { drawLabels, drawStreetLabels} from "./map.js";

export const checkboxes = document.querySelectorAll("input[type=checkbox]");

// settings box ---------------------------------------------------------------------------
export const setupListener = () => {
    //opening/closing
    document.getElementById("settingsContainer").addEventListener("mouseover", openSettings);
    document.getElementById("settingsContainer").addEventListener("mouseout", closeSettings);
    //setup checkboxes
    for(const checkbox of checkboxes){
        checkbox.addEventListener("change", () => {
        drawLabels(false);
        drawStreetLabels(false);
        })
    }
    //modifyLink animation
    document.getElementById("modifyLinkDiv").addEventListener("mouseover", () => {
        document.getElementById("pwd").style.display = "flex";
    })

    document.getElementById("modifyLinkDiv").addEventListener("mouseout", () => {
        document.getElementById("pwd").style.display = "none";
    })
}

export const openSettings = () => {
    const labels = document.getElementsByClassName("labelCheckbox");

    [].forEach.call(labels, (e) => {
        e.style.display = "flex";
    })

    const input = document.getElementsByClassName("inputCheckbox");

    [].forEach.call(input, (e) => {
        e.style.display = "flex";
    })

    document.getElementById("settingsContainer").style.width = "300px";
    document.getElementById("settingsContainer").style.height = "620px";
    setTimeout(() => {
        document.getElementById("pwd").style.backgroundColor = "white";
    }, 1500);

    document.getElementById("settings").style.transform = "rotateZ(-180deg)";
    document.getElementById("modifyLink").style.display = "flex";
}

//closing animation
export const closeSettings = () => {
    const labels = document.getElementsByClassName("labelCheckbox");

    [].forEach.call(labels, (e) => {
        e.style.display = "none";
    })

    const input = document.getElementsByClassName("inputCheckbox");

    [].forEach.call(input, (e) => {
        e.style.display = "none";
    })

    document.getElementById("settingsContainer").style.width = "67px";
    document.getElementById("settingsContainer").style.height = "67px";

    document.getElementById("settings").style.transform = "rotateZ(180deg)";
    document.getElementById("modifyLink").style.display = "none";
}