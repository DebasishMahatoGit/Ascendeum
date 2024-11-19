<?php
    ini_set('display_errors', 1);
    class Board{
        private $size;
        private $position_coordinates;
        function __construct($size){
            $this->size = $size;
            $this->position_coordinates = [];
        }
        function getSize(){
            return $this->size;
        }
        function numBlocks(){
            return $this->size*$this->size;
        }
        function getPositionCoordinates(){
            
            $cntr = 1;
            for($i = 0;$i<$this->size;$i++){
                
                if($i%2 == 0){
                    $x=0;
                    $y = $i;
                    for($j = 0;$j<$this->size;$j++){
                        $this->position_coordinates[$cntr] = "(".$x.", ".$y.")";
                        $x++;
                        $cntr++;
                    }
                }else{
                    $x=$this->size - 1;
                    $y = $i;
                    for($j = $this->size - 1;$j>=0;$j--){
                        $this->position_coordinates[$cntr] = "(".$x.", ".$y.")";
                        $cntr++;
                        $x--;
                    }
                }
            }
            return $this->position_coordinates;
        }
    }
    


    class Player{
        private $name;
        private $position;
        private $isWinner;
        private $movesLog;
        private $positionLog;
        private $positionCoordinates;
        function __construct($name, $position = 0, $isWinner = false, $movesLog = []){
            $this->name = $name;
            $this->position = $position;
            $this->isWinner = false;
            $this->movesLog = [];
            $this->positionLog = [];
            $this->positionCoordinates = [];
        }
        function getName(){
            return $this->name;
        }
        function getPosition(){
            return $this->position;
        }
        function rollDice(){
            $num_moves = rand(1, 6);
            return $num_moves;
        }
        function setNewPosition($moves, $size, $position_coordinates){
            $old_position = $this->position;
            $new_position = $old_position + $moves;
            if($new_position > $size){
                $this->position = $old_position;
            }else{
                $this->position = $new_position;
            }
            $this->positionLog[] = $this->position;
            $this->positionCoordinates[] = $position_coordinates[$this->position];
        }
        function setWinner($isWin){
            $this->isWinner = $isWin;
        }
        function updateMovesLog($moves){
            $this->movesLog[] = $moves;
        }
        function getMovesLog(){
            return $this->movesLog;
        }
        function getPlayerPositionsCoordinates(){
            return $this->positionCoordinates;
        }
        function getPositionLog(){
            return $this->positionLog;
        }
        function getWinStatus(){
            return $this->isWinner;
        }
    }
    if(!empty($_POST['submit'])){
        $size = $_POST['grid'];
        $num_players = $_POST['player_num'];
        $players = [];
        $players_obj = [];
        for($i = 1;$i<=$num_players;$i++){
            // $player$i = new Player('Player'.$i);
            $players[] = "Player".$i;
        }
        foreach($players as $key => $player){
            // print_r($player);exit;
            $players_obj[] = new Player($player);
        }
        // $player1 = new Player('Player1');
        // $player2 = new Player('Player2');
        // $player3 = new Player('Player3');
        // $players = [$player1, $player2, $player3];
        // print_r($players);exit;
        $winner = false;
        $board = new Board($size);
        $target_position = $board->numBlocks();
        $position_coord = $board->getPositionCoordinates();
        // print_r($position_coord);exit;
        $data = [];
        while(!$winner){
            foreach($players_obj as $player){
                $moves = $player->rollDice();
                $player->updateMovesLog($moves);
                $player->setNewPosition($moves, $target_position, $position_coord);
                $current_position = $player->getPosition();
                if($current_position == $target_position){
                    $winner = true;
                    $player->setWinner(true);
                    // print_r($player->getMovesLog());
                    // echo "<br>";
                    // print_r($player->getPlayerPositionsCoordinates());
                    // echo $player->getName();
                    break;
                }
            }
        }
        foreach($players_obj as $player){
            $name = $player->getName();
            $movesLog = $player->getMovesLog();
            $coord_log = $player->getPlayerPositionsCoordinates();
            $pos_logs = $player->getPositionLog();
            $winner = $player->getWinStatus();
            $data[] = ['name' => $name, 'moves' => implode(", ", $movesLog), 'pos' => implode(", ", $pos_logs), 'cords' => implode(", ", $coord_log), 'winner' => ($winner)?"Winner":""];
        }
        // echo "<pre>";print_r($data);exit;
    }
?>

<html>
<form method = "post">
    <div style="display:flex;">
    <label>Grid</label><input type = "number" name ="grid" id="grid">
        <label>Player Number</label><input type = "number" name ="player_num" id="player_num">
        <button type="submit" name="submit" value="1">Start</button>
    </div>
    <!-- <div style="text-align:center;"><button type="submit" name="submit" value="1">Start</button><br> -->
</form>
<div>
    <?php if(!empty($data)){?>
    <table style="border: 1px solid black">
        <thead>
            <tr>
                <th style="border: 1px solid black">Player Number</th>
                <th style="border: 1px solid black">Dice Roll History</th>
                <th style="border: 1px solid black">Position History</th>
                <th style="border: 1px solid black">Coordinates History</th>
                <th style="border: 1px solid black">Winner Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $d){?>
                <tr>
                    <td style="border: 1px solid black"><?= $d['name']?></td>
                    <td style="border: 1px solid black"><?= $d['moves']?></td>
                    <td style="border: 1px solid black"><?= $d['pos']?></td>
                    <td style="border: 1px solid black"><?= $d['cords']?></td>
                    <td style="border: 1px solid black"><?= $d['winner']?></td>
                </tr>
            <?php }?>
        </tbody>
                    
    </table>
    <?php }?>
</div>
</html>
