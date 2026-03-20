<?php

session_start();

if ($_SESSION['modify'] !== true) {
    header("Location: ../map.php");
    exit;
}

//acces files

require_once '../../config/db_connect.php';
require_once '../../config/map_queries.php';
require_once './completeStreet.php';

//declare variables

$errorsLabel = ['nameLabel' => '', 'x' => '', 'y' => '', 'type' => ''];
$nameLabel = '';
$x = '';
$y = '';
$type = '';

$errorsBorder = ['nameBorder' => '', 'coordsBorder' => ''];
$nameBorder = '';
$coordsBorder = '';

$errorsStreet = ['nameStreet' => '', 'coordsStreet' => ''];
$nameStreet = '';
$coordsStreet = '';
$colorStreet = '#000000';

//get data

$elements = getLabels($conn);
$borders = getBorders($conn);
$streets = getStreets($conn);

//call functions

if (isset($_POST['submitLabel'])) {
    $labelResult = addLabel($conn);
    $errorsLabel = $labelResult['errors'];
    $nameLabel = $labelResult['labels']['nameLabel'];
    $x = $labelResult['labels']['x'];
    $y = $labelResult['labels']['y'];
    $type = $labelResult['labels']['type'];
}
if (isset($_POST['submitBorder'])) {
    $borderResult = addBorder($conn);
    $errorsBorder = $borderResult['errors'];
    $nameBorder = $borderResult['values']['nameBorder'];
    $coordsBorder = $borderResult['values']['coordsBorder'];
}
if (isset($_POST['submitStreet'])) {
    $streetResult = addStreet($conn);
    $errorsStreet = $streetResult['errors'];
    $nameStreet = $streetResult['values']['nameStreet'];
    $coordsStreet = $streetResult['values']['coordsStreet'];
    $colorStreet = $streetResult['values']['colorStreet'];
}

if (isset($_POST['deleteLabel']) || isset($_POST['deleteBorder']) || isset($_POST['deleteStreet'])) {
    deleteMarkup($conn);
}

//add Label
function addLabel($conn)
{
    $errors = array('nameLabel' => '', 'x' => '', 'y' => '', 'type' => '');
    $labels = array('nameLabel' => '', 'x' => '', 'y' => '', 'type' => '');

    if (empty($_POST['nameLabel'])) {
        $errors['nameLabel'] = 'Enter a name';
    } else {
        $labels['nameLabel'] = $_POST['nameLabel'];
    }
    if (empty($_POST['x']) && $_POST['x'] != 0) {
        $errors['x'] = 'Enter a x-coordinate';
    } else {
        $labels['x'] = $_POST['x'];
    }
    if (empty($_POST['y']) && $_POST['y'] != 0) {
        $errors['y'] = 'Enter a y-coordinate';
    } else {
        $labels['y'] = $_POST['y'];
    }
    if (empty($_POST['type'])) {
        $errors['type'] = 'Choose a type';
    } else {
        if ($_POST['type'] != 'town' && $_POST['type'] != 'waters' && $_POST['type'] != 'landscape' && $_POST['type'] != 'point' && $_POST['type'] != 'province') {
            $errors['type'] = 'Choose a valid type';
        }
        $labels['type'] = $_POST['type'];
    }

    if (!array_filter($errors)) {
        $labels['name'] = mysqli_real_escape_string($conn, $_POST['nameLabel']);
        $labels['x'] = mysqli_real_escape_string($conn, $_POST['x']);
        $labels['y'] = mysqli_real_escape_string($conn, $_POST['y']);
        $labels['type'] = mysqli_real_escape_string($conn, $_POST['type']);

        $sql = "INSERT INTO maplabels(name, x, y, type) VALUES ('{$labels['name']}', '{$labels['x']}', '{$labels['y']}', '{$labels['type']}')";

        modifyData($conn, $sql);
    }

    return ['errors' => $errors, 'labels' => $labels];
}

//add Border
function addBorder($conn)
{
    $errors = ['nameBorder' => '', 'coordsBorder' => ''];
    $borders = ['nameBorder' => '', 'coordsBorder' => ''];

    if (empty($_POST['nameBorder'])) {
        $errors['nameBorder'] = 'Enter a name';
    } else {
        $borders['nameBorder'] = $_POST['nameBorder'];
    }

    if (empty($_POST['coordsBorder'])) {
        $errors['coordsBorder'] = 'Enter coordinates';
    } else {
        if (!preg_match('/^-?\d+,-?\d+(?:\s-?\d+,-?\d+)*$/', $_POST['coordsBorder'])) {
            $errors['coordsBorder'] = 'Wrong format';
        }
        $borders['coordsBorder'] = $_POST['coordsBorder'];
    }

    if (!array_filter($errors)) {
        $borders['nameBorder'] = mysqli_real_escape_string($conn, $borders['nameBorder']);
        $borders['coordsBorder'] = mysqli_real_escape_string($conn, $borders['coordsBorder']);

        $sql = "INSERT INTO mapborders(name, coords) VALUES ('{$borders['nameBorder']}', '{$borders['coordsBorder']}')";
        modifyData($conn, $sql);
    }

    return ['errors' => $errors, 'values' => $borders];
}

//add street

function addStreet($conn)
{
    $errors = ['nameStreet' => '', 'coordsStreet' => ''];
    $streets = [
        'nameStreet' => '',
        'coordsStreet' => '',
        'colorStreet' => isset($_POST['colorStreet']) ? $_POST['colorStreet'] : '#000000'
    ];

    if (empty($_POST['nameStreet'])) {
        $errors['nameStreet'] = 'Enter a name';
    } else {
        $streets['nameStreet'] = $_POST['nameStreet'];
    }

    if (empty($_POST['coordsStreet'])) {
        $errors['coordsStreet'] = 'Enter coordinates';
    } else {
        if (!preg_match('/^-?\d+,-?\d+(?:\s-?\d+,-?\d+)*$/', $_POST['coordsStreet'])) {
            $errors['coordsStreet'] = 'Wrong format';
        }
        $streets['coordsStreet'] = $_POST['coordsStreet'];
    }

    if (!array_filter($errors)) {
        $streets['coordsStreet'] = completeStreet($streets['coordsStreet']);

        $streets['nameStreet'] = mysqli_real_escape_string($conn, $streets['nameStreet']);
        $streets['colorStreet'] = mysqli_real_escape_string($conn, $streets['colorStreet']);
        $streets['coordsStreet'] = mysqli_real_escape_string($conn, $streets['coordsStreet']);

        $sql = "INSERT INTO mapstreets(name, color, coords) VALUES ('{$streets['nameStreet']}', '{$streets['colorStreet']}', '{$streets['coordsStreet']}')";
        modifyData($conn, $sql);
    }

    return ['errors' => $errors, 'values' => $streets];
}

//delete

function deleteMarkup($conn)
{
    $id = mysqli_real_escape_string($conn, $_POST['deleteId']);

    if (isset($_POST['deleteLabel'])) {
        $sql = "DELETE FROM maplabels WHERE id = $id";
    } elseif (isset($_POST['deleteBorder'])) {
        $sql = "DELETE FROM mapborders WHERE id = $id";
    } elseif (isset($_POST['deleteStreet'])) {
        $sql = "DELETE FROM mapstreets WHERE id = $id";
    }
    modifyData($conn, $sql);
}


//count elements

function countLabels($elements)
{
    $labelAmounts = array('province' => 0, 'town' => 0, 'waters' => 0, 'landscape' => 0, 'point' => 0);
    for ($i = 0; $i < count($elements); $i++) {
        switch ($elements[$i]['type']) {
            case 'town':
                $labelAmounts['town']++;
                break;
            case 'waters':
                $labelAmounts['waters']++;
                break;
            case 'landscape':
                $labelAmounts['landscape']++;
                break;
            case 'point':
                $labelAmounts['point']++;
                break;
            case 'province':
                $labelAmounts['province']++;
                break;
            default;
        }
    }
    return $labelAmounts;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../img/fullServerMap.png" type="image/x-icon">
    <link rel="stylesheet" href="modify.css">
    <title>Blockguessr - Modify</title>
</head>

<body>
    <header>
        <a href="../map.php" id="goBack">
            <svg xmlns="http://www.w3.org/2000/svg" height="85%" viewBox="0 -960 960 960" width="85%" fill="#1f1f1f" style="position: absolute">
                <path d="M400-240 160-480l240-240 56 58-142 142h486v80H314l142 142-56 58Z" />
            </svg>
        </a>BlockGuessr
    </header>
    <main>
        <div id="topPage">
            <div id="forms">
                <div id="switchForm">
                    <div id="selectLabel" class="selectForm" onclick="changeForm(id)">Add Label</div>
                    <div id="selectBorder" class="selectForm" onclick="changeForm(id)">Add Border</div>
                    <div id="selectStreet" class="selectForm" onclick="changeForm(id)">Add Street</div>
                </div>
                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" id="labelForm" class="addForms">
                    <fieldset>
                        <div class="formI">
                            <label for="name">Choose name:</label>
                            <input type="text" name="nameLabel" id="name" value="<?php echo htmlspecialchars($nameLabel) ?>" class="addI">
                            <div class="error"><?php echo $errorsLabel['nameLabel'] ?></div>
                        </div>
                        <div class="formI">
                            <label for="x">x-coordinate:</label>
                            <input type="number" name="x" id="x" step="1" value="<?php echo htmlspecialchars($x) ?>" class="addI">
                            <div class="error"><?php echo $errorsLabel['x'] ?></div>
                        </div>
                        <div class="formI">
                            <label for="y">y-coordinate:</label>
                            <input type="number" name="y" id="y" step="1" value="<?php echo htmlspecialchars($y) ?>" class="addI">
                            <div class="error"><?php echo $errorsLabel['y'] ?></div>
                        </div>
                        <div class="formI">
                            <label for="type">Choose type:</label>
                            <input list="types" name="type" id="type" value="<?php echo htmlspecialchars($type) ?>" class="addI">
                            <div class="error"><?php echo $errorsLabel['type'] ?></div>
                            <datalist id="types">
                                <option value="town"></option>
                                <option value="waters"></option>
                                <option value="landscape"></option>
                                <option value="point"></option>
                                <option value="province"></option>
                            </datalist>
                        </div>
                        <div class="formI">
                            <input type="submit" value="Add Label" name="submitLabel" id="addSubmit">
                        </div>
                    </fieldset>
                </form>
                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" id="borderForm" class="addForms">
                    <fieldset>
                        <div class="formI">
                            <label for="name">Choose name:</label>
                            <input type="text" name="nameBorder" id="name" value="<?php echo htmlspecialchars($nameBorder) ?>" class="addI">
                            <div class="error"><?php echo $errorsBorder['nameBorder'] ?></div>
                        </div>
                        <div class="formI">
                            <label for="coordsBorder">Enter coordinates:</label>
                            <input type="text" name="coordsBorder" id="coordsBorder" placeholder="eg.: x₁,y₁ x₂,y₂ ... xₙ,yₙ" value="<?php echo htmlspecialchars($coordsBorder) ?>" class="addI">
                            <div class="error"><?php echo $errorsBorder['coordsBorder'] ?></div>
                        </div>
                        <div class="formI">
                            <input type="submit" value="Add Border" name="submitBorder" id="addSubmit">
                        </div>
                    </fieldset>
                </form>
                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" id="streetForm" class="addForms">
                    <fieldset>
                        <div class="formI">
                            <label for="name">Choose name:</label>
                            <input type="text" name="nameStreet" id="name" value="<?php echo htmlspecialchars($nameStreet) ?>" class="addI">
                            <div class="error"><?php echo $errorsStreet['nameStreet'] ?></div>
                        </div>
                        <div class="formI">
                            <label for="colorStreet">Choose color:</label>
                            <input type="color" name="colorStreet" id="colorStreet" value="<?php echo htmlspecialchars($colorStreet) ?>" class="addI">
                        </div>
                        <div class="formI">
                            <label for="name">Enter coordinates:</label>
                            <input type="text" name="coordsStreet" id="coordsStreet" placeholder="eg.: x₁,y₁ x₂,y₂ ... xₙ,yₙ" value="<?php echo htmlspecialchars($coordsStreet) ?>" class="addI">
                            <div class="error"><?php echo $errorsStreet['coordsStreet'] ?></div>
                        </div>
                        <div class="formI">
                            <input type="submit" value="Add Street" name="submitStreet" id="addSubmit">
                        </div>
                    </fieldset>
                </form>
            </div>
            <div id="stats">
                <p id="header">Statistics:</p>
                <hr>
                <ul>
                    <li class="stat">
                        <p>PROVINCE labels: </p>
                        <p><?php echo countLabels($elements)['province'] ?></p>
                    </li>
                    <li class="stat">
                        <p>Town labels: </p>
                        <p><?php echo countLabels($elements)['town'] ?></p>
                    </li>
                    <li class="stat" style="color: blue">
                        <p>Waters labels: </p>
                        <p><?php echo countLabels($elements)['waters'] ?></p>
                    </li>
                    <li class="stat" style="color: green">
                        <p>Landscape labels: </p>
                        <p><?php echo countLabels($elements)['landscape'] ?></p>
                    </li>
                    <li class="stat">
                        <p>▪Point labels: </p>
                        <p><?php echo countLabels($elements)['point'] ?></p>
                    </li>
                    <hr>
                    <li class="stat" id="total">
                        <p>Total labels: </p>
                        <p><?php echo count($elements) ?></p>
                    </li>
                </ul>
            </div>
        </div>
        <div id="tables">
            <div id="switchTable">
                <div id="labelTableSwitch" class="selectTable" onclick="changeTable(id)">Labels</div>
                <div id="borderTableSwitch" class="selectTable" onclick="changeTable(id)">Borders</div>
                <div id="streetTableSwitch" class="selectTable" onclick="changeTable(id)">Streets</div>
            </div>
            <table class="table" id="labelTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>X</th>
                        <th>Y</th>
                        <th>Type</th>
                        <th>ID</th>
                        <th>DeleteHard</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($elements); $i++): echo "<tr>" ?>
                        <td><?php echo htmlspecialchars($elements[$i]['name']) ?></td>
                        <td><?php echo htmlspecialchars($elements[$i]['x']) ?></td>
                        <td><?php echo htmlspecialchars($elements[$i]['y']) ?></td>
                        <td><?php echo htmlspecialchars($elements[$i]['type']) ?></td>
                        <td><?php echo htmlspecialchars($elements[$i]['id']) ?></td>
                        <td>
                            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" id="deleteForm">
                                <input type="hidden" name="deleteId" value="<?php echo htmlspecialchars($elements[$i]['id']) ?>">
                                <input type="submit" name="deleteLabel" value="Delete" class="deleteBtn">
                            </form>
                        </td>
                        <?php echo "</tr>" ?>
                    <?php endfor ?>
                </tbody>
            </table>
            <table class="table" id="borderTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>ID</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($borders); $i++): echo "<tr>" ?>
                        <td><?php echo htmlspecialchars($borders[$i]['name']) ?></td>
                        <td><?php echo htmlspecialchars($borders[$i]['id']) ?></td>
                        <td>
                            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" id="deleteForm">
                                <input type="hidden" name="deleteId" value="<?php echo htmlspecialchars($borders[$i]['id']) ?>">
                                <input type="submit" name="deleteBorder" value="Delete" class="deleteBtn">
                            </form>
                        </td>
                        <?php echo "</tr>" ?>
                    <?php endfor ?>
                </tbody>
            </table>
            <table class="table" id="streetTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Color</th>
                        <th>ID</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($streets); $i++): echo "<tr>" ?>
                        <td><?php echo htmlspecialchars($streets[$i]['name']) ?></td>
                        <td><?php echo htmlspecialchars($streets[$i]['color']) ?></td>
                        <td><?php echo htmlspecialchars($streets[$i]['id']) ?></td>
                        <td>
                            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" id="deleteForm">
                                <input type="hidden" name="deleteId" value="<?php echo htmlspecialchars($streets[$i]['id']) ?>">
                                <input type="submit" name="deleteStreet" value="Delete" class="deleteBtn">
                            </form>
                        </td>
                        <?php echo "</tr>" ?>
                    <?php endfor ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
<script src="modify.js"></script>

</html>