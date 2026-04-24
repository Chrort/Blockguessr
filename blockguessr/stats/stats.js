const expandBtn = document.querySelector("#recentGames > button");

expandBtn.addEventListener("click", () => {
    const hiddenRows = document.querySelectorAll(".tr_hide");
    for(let i = hiddenRows.length - 1; i > hiddenRows.length - 25; i--){
        try{
            hiddenRows[hiddenRows.length - i].classList.remove("tr_hide");
        }
        catch{
            expandBtn.style.display = "none";
        }
    }
});