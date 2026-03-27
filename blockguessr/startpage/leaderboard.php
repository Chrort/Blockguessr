<?php

require '../../api/games_data.php';
require '../../api/users_data.php';

function displayLeaderboard(mysqli $conn, int $mapId)
{

    $games = getGamesDataByMapId($conn, $mapId);

    $registeredGames = count($games);

    $registeredGames >= 3 ? $registeredGames = 3 : $registeredGames = count($games);

?>
    <div class="leaderboard">
        <h2>Leaderboard</h2>
        <table>
            <tbody>
                <?php for ($i = 0; $i < $registeredGames; $i++): ?>
                    <tr class="row_<?= $i + 1 ?>">
                        <td><?= getUsernameById($conn, $games[$i]['player_id']) ?></td>
                        <td><?= $games[$i]['score'] ?></td>
                        <td><?= $games[$i]['time'] ?>s</td>
                        <td><?= $games[$i]['played_at'] ?></td>
                    </tr>
                <?php endfor; ?>
                <?php for ($j = 3 - $registeredGames; $j > 0; $j--): ?>
                    <tr class="row_unset">
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
<?php
}
