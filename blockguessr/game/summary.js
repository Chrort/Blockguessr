const total = document.getElementById("total");
const average = document.getElementById("average");
const exit = document.getElementById("exitToStartpage");

let roundData = JSON.parse(localStorage.getItem("roundData"));
let totalPoints = +localStorage.getItem("totalP");
let averageDistance = 0;

window.onload = () => {
    displaySummary();

    fetch("./summary.php", {
        method: "POST",
        headers: {
        "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `totalPoints=${encodeURIComponent(totalPoints)}`
    })
}

const displaySummary = () => {
    for(let i = 0; i < 5; i++){
        let tr = document.getElementById(`tr_${i}`);
        
        let td1 = document.createElement("td");
        let td2 = document.createElement("td");
        let td3 = document.createElement("td");

        td1.innerHTML = `${i + 1}`;
        td2.innerHTML = `${roundData[i].d}`;
        td3.innerHTML = `${roundData[i].p}`;

        tr.appendChild(td1);
        tr.appendChild(td2);
        tr.appendChild(td3);

        averageDistance += roundData[i].d;
    }

    average.innerHTML = `${Math.round((averageDistance / 5) * 100) / 100}`;
    total.innerHTML = `${totalPoints}`;
}

document.addEventListener("keydown", (e) => {
    e.key = "Space" ? window.location.href = "../startpage/startpage.php" : null;
    return;
})

exit.addEventListener("click", () => {
    window.location.href = "../startpage/startpage.php";
})