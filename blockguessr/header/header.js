const userIcon = document.getElementById("userIcon");
const dropdown = document.getElementById("dropdown");
const nav = document.querySelector("nav");

userIcon.addEventListener("mouseover", () => {
    dropdown.style.display = "flex";
    nav.style.height = `calc(7vh + ${dropdown.clientHeight}px)`;
    nav.style.width = `${dropdown.clientWidth}px`;
    userIcon.style.minWidth = `${dropdown.clientWidth}px`;
    userIcon.innerHTML = document.querySelector("meta[name='username']").content;
    userIcon.style.justifyContent = "flex-end";
})

const mouseLeave = () => {
    userIcon.innerHTML = document.querySelector("meta[name='username']").content[0];
    userIcon.style.minWidth = "5vh";
    userIcon.style.justifyContent = "center";
    nav.style.height = `5vh`;
    nav.style.width = `5vh`;
    dropdown.style.display = "none";
}

nav.addEventListener("mouseleave", mouseLeave);