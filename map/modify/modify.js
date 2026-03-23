const addForms = document.getElementsByClassName("addForms");
const tables = document.getElementsByClassName("table");
const selectForm = document.getElementsByClassName("selectForm");
const selectTable = document.getElementsByClassName("selectTable");

window.onload = () => {
    setup();
}
const setup = () => {
    deselectAllForms();
    deselectAllTables();
    //forms
    if(localStorage.getItem("selectedForm") == "border"){
        document.getElementById("borderForm").style.display = "flex";
        document.getElementById("selectBorder").style.backgroundColor = "rgb(195, 189, 189)";
    }else if(localStorage.getItem("selectedForm") == "street"){
        document.getElementById("streetForm").style.display = "flex";
        document.getElementById("selectStreet").style.backgroundColor = "rgb(195, 189, 189)";
    }else if(localStorage.getItem("selectedForm") == "poly"){
        document.getElementById("polyForm").style.display = "flex";
        document.getElementById("selectPoly").style.backgroundColor = "rgb(195, 189, 189)";
    }else{
        document.getElementById("labelForm").style.display = "flex";
        document.getElementById("selectLabel").style.backgroundColor = "rgb(195, 189, 189)";
    }

    //tables
    if(localStorage.getItem("selectedTable") == "border"){
        document.getElementById("borderTable").style.display = "table";
        document.getElementById("borderTableSwitch").style.backgroundColor = "rgb(195, 189, 189)";
    }else if(localStorage.getItem("selectedTable") == "street"){
        document.getElementById("streetTable").style.display = "table";
        document.getElementById("streetTableSwitch").style.backgroundColor = "rgb(195, 189, 189)";
    }else if(localStorage.getItem("selectedTable") == "poly"){
        document.getElementById("polyTable").style.display = "table";
        document.getElementById("polyTableSwitch").style.backgroundColor = "rgb(195, 189, 189)";
    }else{
        document.getElementById("labelTable").style.display = "table";
        document.getElementById("labelTableSwitch").style.backgroundColor = "rgb(195, 189, 189)";
    }
}

const deselectAllForms = () => {
    [].forEach.call(addForms, (e) => {
        e.style.display = "none";
    });

    [].forEach.call(selectForm, (e) => {
        e.style.backgroundColor = "transparent"
    });
}

const deselectAllTables = () => {
    [].forEach.call(tables, (e) => {
        e.style.display = "none";
    });

    [].forEach.call(selectTable, (e) => {
        e.style.backgroundColor = "transparent"
    });
}

const changeForm = id => {
    deselectAllForms();
    switch(id){
        case "selectLabel":
            document.getElementById("labelForm").style.display = "flex";
            document.getElementById("selectLabel").style.backgroundColor = "rgb(195, 189, 189)";
            localStorage.setItem("selectedForm", "label");
            break;
        case "selectBorder":
            document.getElementById("borderForm").style.display = "flex";
            document.getElementById("selectBorder").style.backgroundColor = "rgb(195, 189, 189)";
            localStorage.setItem("selectedForm", "border");
            break;
        case "selectStreet":
            document.getElementById("streetForm").style.display = "flex";
            document.getElementById("selectStreet").style.backgroundColor = "rgb(195, 189, 189)";
            localStorage.setItem("selectedForm", "street");
            break;
        case "selectPoly":
            document.getElementById("polyForm").style.display = "flex";
            document.getElementById("selectPoly").style.backgroundColor = "rgb(195, 189, 189)";
            localStorage.setItem("selectedForm", "poly");
    }
}

const changeTable = id => {
    deselectAllTables();
    switch(id){
        case "labelTableSwitch":
            document.getElementById("labelTable").style.display = "table";
            document.getElementById("labelTableSwitch").style.backgroundColor = "rgb(195, 189, 189)";
            localStorage.setItem("selectedTable", "label");
            break;
        case "borderTableSwitch":
            document.getElementById("borderTable").style.display = "table";
            document.getElementById("borderTableSwitch").style.backgroundColor = "rgb(195, 189, 189)";
            localStorage.setItem("selectedTable", "border");
            break;
        case "streetTableSwitch":
            document.getElementById("streetTable").style.display = "table";
            document.getElementById("streetTableSwitch").style.backgroundColor = "rgb(195, 189, 189)";
            localStorage.setItem("selectedTable", "street");
            break;
        case "polyTableSwitch":
            document.getElementById("polyTable").style.display = "table";
            document.getElementById("polyTableSwitch").style.backgroundColor = "rgb(195, 189, 189)";
            localStorage.setItem("selectedTable", "poly");
    }
}