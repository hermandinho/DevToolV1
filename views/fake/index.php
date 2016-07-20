<div style="width: 80%;float: right">
    <center class="tile-area-grayed" style="width: 98%"><h1><strong><i class="icon-code fg-white"></i> Fake Date Generator</strong></h1></center>

    <?php
    $test = Session::get("selected_db");
    if (!isset($test)) {
        echo "<div class='tile-area-darkRed'><h1>Please select a data base <a href='http://" . URL . "DBManager'>Here</a> </h1></div>";
    } else {
    ?>

    <form method="post" action="" class="main_form">
        <table class="table hovered" style="font-family: monospace;text-align: left">
            <thead class="bg-steel" style="font-weight: bolder;">
            <th style="font-size: 2em">Labels</th>
            <th style="font-size: 2em">Options</th>
            </thead>
            <tbody>
            <tr>
                <td>Curent Data base</td>
                <td><strong><?php echo ucfirst(Session::get("selected_db")); ?></strong></td>
            </tr>

            <tr style="">
                <td style="width: 30%">Select Tables</td>
                <td style="width: 70%;">
                    <div style="max-height: 300px;overflow: auto;text-align: left">


                        <?php
                        $db_fields = $this->DBFields;
                        if(count($db_fields) <= 0){
                            $hideGenerateButton = "disabled";
                        }else{
                            for($i=0;$i<count($db_fields);$i++){
                                ?>
                                <div class="input-control switch" data-role="input-control">
                                    <label class="inline-block" style="margin-right: 20px">
                                        <input type="checkbox" class="getSelectedField" name="selectedClasseFields[<?php echo $i; ?>]" value="<?php echo $db_fields[$i]; ?>" checked="">
                                        <span class="check"></span>
                                        <strong><?php echo $db_fields[$i]; ?></strong>
                                    </label>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </td>
            </tr>

            <tr>
                <td><label for="data_type">Data Type</label></td>
                <td>
                    <div class="input-control text">
                        <select name="data_type" id="data_type">
                            <option value="json">JSON</option>
                            <option value="sql">Sql</option>
                            <option value="xml">XML</option>
                        </select>
                    </div>
                </td>
            </tr>

            <tr>
                <td></td>
                <td colspan="2">
                    <button type="submit" id="generate" class="command-button primary">
                        <i class="icon-cog on-left"></i>
                        Generate
                        <small></small>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
<?php
}
?>


<div class="window" style="height: 200px;overflow: auto">
    <div class="caption">
        <span class="icon icon-windows"></span>
        <div class="title">Generation output ...</div>
        <button class="btn-min disabled"></button>
        <button class="btn-max disabled"></button>
        <button class="btn-close" onclick='$("#output").html("Cleared ...")'  title="Clear Console"></button>
    </div>
    <div class="content">
        <textarea readonly style="width: 100%;height: 200px;border: none;" id="output"></textarea>
    </div>
</div>

<script>
    $(document).ready(function(){
        $(".window").hide();
        $("#generate").click(function(e){
            e.preventDefault();
            var data = $(".main_form").serializeArray();
            var selectedFieldNumber=0;
            $(".getSelectedField").each(function(){
                if($(this).is(":checked")){
                    selectedFieldNumber++;
                }
            });


            $(".window").slideDown("medium");
            $("#output").html("");
            for(var i=0;i<selectedFieldNumber;i++){
                $.post(
                    "FakeDataGenerator/generate",
                    {
                        data:data[i].value,
                        projectName:$("#projectName").val(),
                        Tour:i//si = 0, creer la structure de base
                    },
                    function(donnee){
                        //console.log(donnee);
                        $("#output").append(donnee);
                    }
                );
                //break;
            }
        });
    });
</script>