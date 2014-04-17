<div id="param">
    <fieldset>
        <legend>Définir le jour off sur une période</legend>
        <form method="POST" action="#">
            <div>
                <table>
                    <tr>
                        <td><span>Du :</span></td>
                        <td><input type="text" id="datepickerDeb" class="datepicker datejoff" name="datedeb"></td>
                        <td><img id="calDeb" class="dpicker" src="<?php echo Router::webroot("img/calendrier.png");?>" alt="calendrier" >  </td>
                        <td><span>Au :</span></td>
                        <td><input type="text" id="datepickerFin" class="datepicker datejoff" name="datefin"></td>
                        <td><img id="calFin" class="dpicker" src="<?php echo Router::webroot("img/calendrier.png");?>" alt="calendrier"></td>
                    </tr>  
                </table>
            </div>
            <div>
<!--                 <ul>
                    <li>-->
                    <p>    
                        <select name="joff1" id="jour1">
                            <option value ="lu">Lundi</option>
                            <option value ="ma">mardi</option>
                            <option value ="me">mercredi</option>
                            <option value ="je">jeudi</option>
                            <option value ="ve" selected="selected">Vendredi</option>
                        </select>
                        <input type="radio" name="mat1" id="mat1_am" value="am" onChange="toogleAffichage();">
                        <label for="mat1_am">AM</label>
                        <input type="radio" name="mat1" id="mat1_pm" value="pm" onChange="toogleAffichage();">
                        <label for="mat1_pm">PM</label>
                        <input type="radio" name="mat1" id="mat1_all" value="all" checked="checked"  onChange="toogleAffichage();">
                        <label for="mat1_all">Journée complète</label>
                    </p>

                    <p id="deuxieme">
                        <select name="joff2" id="jour2">
                        <!-- <select name="joff2" id="jour2" onChange="checkCollision();"> -->
                            <option value ="lu" selected="selected">Lundi</option>
                            <option value ="ma">mardi</option>
                            <option value ="me">mercredi</option>
                            <option value ="je">jeudi</option>
                            <option value ="ve">Vendredi</option>
                        </select>
                        <input type="radio" name="mat2" id="mat2_am" value="am" >
                        <!-- <input type="radio" name="mat2" id="mat2_am" value="am" onChange="toogleAffichage();"> -->
                        <label for="mat2_am">AM</label>
                        <input type="radio" name="mat2" id="mat2_pm" value="pm" >
                        <!-- <input type="radio" name="mat2" id="mat2_pm" value="pm" onChange="toogleAffichage();"> -->
                        <label for="mat2_pm">PM</label>
                    </p>
        </div>
                            <input type="submit"  value="Sauvegarder ce choix" id="valider">

        </form>
    </fieldset>
</div>

<div id="dialog-confirm" title="Définir le jour off pour toute l'année ?">
    <p>Voulez-vous définir le <span id="jouroff"></span> comme jour off pour toute cette période ?</p>
</div>


<script type="text/javascript" src="<?php echo Router::webroot("/js/defJoff.js")?>"></script>