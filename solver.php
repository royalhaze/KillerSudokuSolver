<?php
/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/15/21
 * Time: 01:35
 */
class solver
{
    private $table;
    private $table_rules;
    private $try,$old = 0;
    
    public function __construct(array $table,array $table_rules)
    {
        $this->table = $table;
        $this->table_rules = $table_rules;
    }

    public function start()
    {
        $start_time = time();

        $this->fill_cells_has_one_rule();

        $this->fill_rules_has_only_one_empty_cell();

        if ($this->solve() == true){
            $this->show_me_answer();
        }

        echo 'solve at '.$this->try.' round'.PHP_EOL;
        echo 'time: '.(time()-$start_time).' seconds'.PHP_EOL;
    }

    private function fill_cells_has_one_rule(){
        foreach ($this->table_rules as $rule){
            if (count($rule[1]) == 1){
                $this->table[$rule[1][0][0]][$rule[1][0][1]] = $rule[0];
            }
        }
    }

    private function fill_rules_has_only_one_empty_cell(){
        foreach ($this->table_rules as $rule){
            $cell_count = count($rule[1]);
            $filled_count = 0;
            $empty_cell_address = [];

            foreach ($rule[1] as $address){
                if ($this->table[$address[0]][$address[1]] === 0) {
                    $empty_cell_address[] = $address;
                }
            }

            if (count($empty_cell_address) == 1){
                $this->table[$empty_cell_address[0][0]][$empty_cell_address[0][1]] = $rule[0] - $this->sum_of_rule($rule[1]);
            }
        }
    }

    private function sum_of_rule($rule_addresses){
        $sum = 0;
        foreach ($rule_addresses as $address){
            $sum += $this->table[$address[0]][$address[1]];
        }
        return $sum;
    }

    private function count(){
        $this->try++;

        if ($this->try == $this->old + 10000){
            echo $this->try.PHP_EOL;
            $this->old = $this->try;
        }
    }

    private function solve()
    {
        $this->count();

        $free = $this->find_free();

        if ($free == null){
            return true;
        }

        for ($i = 1; $i < 10 ; $i++){
            if ($this->is_valid($i,$free)){
                $this->table[$free[0]][$free[1]] = $i;
                if ($this->solve()){
                    return true;
                }
                $this->table[$free[0]][$free[1]] = 0;
            }
        }

        return false;
    }

    private function is_valid($num,$location)
    {
        for ($i = 0;$i<9;$i++){
            if ($this->table[$location[0]][$i] == $num){
                return false;
            }
        }

        for ($i = 0;$i<9;$i++){
            if ($this->table[$i][$location[1]] == $num){
                return false;
            }
        }

        $start = $this->find_start_box($location);

        for ($i = 0 ; $i<3 ; $i ++){
            for ($j = 0 ; $j<3 ; $j ++){
                if ($this->table[$start[0] + $i][$start[1] + $j] == $num){
                    return false;
                }
            }
        }

        return $this->is_valid_killer($num,$location);
    }

    public function is_valid_killer($num,$address)
    {
        $found_rule = null;
        foreach ($this->table_rules as $rule){
            foreach ($rule[1] as $rule_address){
                if ($rule_address == $address){
                    $found_rule = $rule;
                }
            }
        }

        $empty_cell_address = [];

        foreach ($found_rule[1] as $address){
            if ($this->table[$address[0]][$address[1]] === 0) {
                $empty_cell_address[] = $address;
            }
        }

        if (count($empty_cell_address) == 1){
            return ($found_rule[0] == $num + $this->sum_of_rule($found_rule[1]));
        }

        return  true;
    }

    private function find_start_box($location)
    {
        $tmp = [];

        foreach ($location as $item){
            $float = $item/3;
            $myint = (int) $float;
            $tmp[] = $myint*3;
        }

        return $tmp;
    }

    private function find_free()
    {
        for ($i = 0;$i<9;$i++){
            for ($j = 0;$j<9;$j++){
                if ($this->table[$i][$j] == 0){
                    return [$i,$j];
                }
            }
        }

        return null;
    }

    private function show_me_answer()
    {
        for ($i = 0;$i<9;$i++){
            if ($i % 3 == 0){echo '------------------------'.PHP_EOL;}
            for ($j = 0;$j<9;$j++){
                if ($j % 3 == 0){echo '|';}
                echo $this->table[$i][$j].' ';
                if ($j == 8){
                    echo PHP_EOL;
                }
            }
        }
        echo '------------------------'.PHP_EOL;
    }
}