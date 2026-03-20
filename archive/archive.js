const maxImage = document.getElementById("maxImage");
const closeBtn = document.getElementById("close");
const currentSymbol = document.getElementById("currentSymbol");

window.onload = () => {
    setup();
}

const max = path => {
    maxImage.style.backgroundImage = `url('${path}')`;
    maxImage.style.display = "block";
}

const closeImg = () => {
    maxImage.style.backgroundImage = "none";
    maxImage.style.display = "none";
}

const showExtra = (dateId, deleteId, containerId, downloadId) => {
    document.getElementById(dateId).style.display = "block";
    document.getElementById(deleteId).style.display = "flex";
    document.getElementById(downloadId).style.display = "block";

    document.getElementById(containerId).addEventListener("mouseleave", () => {
        setTimeout(() => {
            document.getElementById(dateId).style.display = "none";
            document.getElementById(deleteId).style.display = "none";
            document.getElementById(downloadId).style.display = "none";
        }, 100);
    })
}

const edit = () => {
    currentSymbol.style.display = "none";
    document.getElementById("editForm").style.visibility = "visible";
}

const setup = () => {
    setTimeout(() => {
        document.getElementById("pwd").style.backgroundColor = "white";
    }, 1800);
}

closeBtn.addEventListener("click", closeImg);
document.addEventListener("keydown", (e) => {
    e.key === 'Escape' ? closeImg() : null;
})

currentSymbol.addEventListener("click", (e) => {
    edit();
})