<html lang="">
<?php
require "tmt.php";
$survey = populateSurvey();
?>
<template>
  <div id="app">
    <div id='container' style="margin:15px auto 0; width:250px;">
        <br>
        <ejs-multiselect id='multiselect' :dataSource='sportsData' placeholder="Find a game" mode="CheckBox" :fields='fields'></ejs-multiselect>
    </div>
  </div>
</template>
<script>
import Vue from 'vue';
import { MultiSelectPlugin } from "@syncfusion/ej2-vue-dropdowns";
import { MultiSelect, CheckBoxSelection } from '@syncfusion/ej2-dropdowns';
MultiSelect.Inject(CheckBoxSelection);
Vue.use(MultiSelectPlugin);
export default {
  data (){
    return {
      sportsData: [
        { Id: 'game1', Game: 'Badminton' },
        { Id: 'game2', Game: 'Football' },
        { Id: 'game3', Game: 'Tennis' },
        { Id: 'game4', Game: 'Golf' },
        { Id: 'game5', Game: 'Cricket' },
        { Id: 'game6', Game: 'Handball' },
        { Id: 'game7', Game: 'Karate' },
        { Id: 'game8', Game: 'Fencing' },
        { Id: 'game9', Game: 'Boxing' }
      ],
      fields : { text: 'Game', value: 'Id' }
    }
  }
}

</script>
<style>
@import "../../node_modules/@syncfusion/ej2-base/styles/material.css";
@import "../../node_modules/@syncfusion/ej2-inputs/styles/material.css";
@import "../../node_modules/@syncfusion/ej2-vue-dropdowns/styles/material.css";
@import "../../node_modules/@syncfusion/ej2-buttons/styles/material.css";
</style>
<?php
foreach ($survey as $row) {
    echo "<tr>";
    $survey[1];
    echo "</tr><br>";
}

function populateSurvey() {
    try {
        $db = db();
        $statement = $db->prepare("SELECT question_id, question FROM surveyQuestions");
        $statement->execute();

        return $statement->fetchAll();

    } catch (PDOException $e) {
        print "ERROR! " . $e->getMessage() . "<br/>";
        die();
    }
}

?>
</html>
