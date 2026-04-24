const total = document.getElementById("total");
const average = document.getElementById("average");
const exit = document.getElementById("exitToStartpage");
const currentLevel = +document.getElementById("levelData0").content;
const xpToNextLevel = +document.getElementById("levelData1").content;
const userXp = +document.getElementById("userXp").content;
const missingXp = xpToNextLevel - (userXp - 25 * currentLevel ** 2);

let roundData = JSON.parse(localStorage.getItem("roundData"));
let totalPoints = +localStorage.getItem("totalP");
let timePlayed = +localStorage.getItem("timePlayed");
let averageDistance = 0;

window.onload = async () => {
    displaySummary();

    await fetch("./save_game_data.php", {
        method: "POST",
        headers: {
        "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `totalPoints=${encodeURIComponent(totalPoints)}&timePlayed=${encodeURIComponent(timePlayed)}`
    });
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
    if(newXp(totalPoints) < missingXp){
        document.getElementById("currentBar").style.width = `${((userXp + newXp(totalPoints)) - (25 * (currentLevel) ** 2)) / xpToNextLevel * 100}%`;
        setTimeout(() => {
            document.getElementById("progress").innerHTML = `+${Math.round(newXp(totalPoints))}xp | ${Math.round((userXp + newXp(totalPoints) - (25 * currentLevel ** 2)) / xpToNextLevel * 100)}%`;
        }, 1500);
    }else{
        const newLevel = Math.floor(0.2 * (userXp + newXp(totalPoints)) ** 0.5);
        const newXpToNextLevel = (25 * (newLevel + 1) ** 2) - (25 * (newLevel) ** 2);
        document.getElementById("currentBar").style.width = `${((userXp + newXp(totalPoints)) - (25 * newLevel ** 2)) / newXpToNextLevel * 100}%`;
        setTimeout(() => {
            document.getElementById("progress").innerHTML = `+${Math.round(newXp(totalPoints))}xp | ${Math.round((userXp + newXp(totalPoints) - (25 * (newLevel) ** 2)) / newXpToNextLevel * 100)}%`;
            document.getElementById("currentLevel").innerHTML = newLevel;
            document.getElementById("nextLevel").innerHTML = newLevel + 1;
        }, 1500);
    }
    
}

const newXp = totalPoints => {
    if(totalPoints == 25000) return totalPoints / 200 + 50;
    return totalPoints / 200;
}

document.addEventListener("keydown", (e) => {
    e.key = "Space" ? window.location.href = "../startpage/startpage.php" : null;
    return;
})

exit.addEventListener("click", () => {
    window.location.href = "../startpage/startpage.php";
})