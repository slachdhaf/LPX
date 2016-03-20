<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Log Processor X</title>
        <meta name="description" content="Try to control a new power !">
        <link rel="stylesheet" href="style/index.css">
        <link href='https://fonts.googleapis.com/css?family=Roboto:100' rel='stylesheet' type='text/css'>
    </head>
    <body>
        <div id="menu">
            <div href="" id="board" class="icon"><a href="" class="item rotate" data-container="">DASHBOARD</a></div>
            <div href="" id="analyse" class="icon"><a href="#analyse_content" class="item rotate">ANALYSE</a></div>
            <div href="" id="stats" class="icon"><a href="#stats_content" class="item rotate">STATISTIQUES</a></div>
            <div href="" id="clock" class="icon"><a href="" class="item rotate">ARCHIVES</a></div>
            <div href="" id="parameter" class="icon"><a href="" class="item rotate">CONFIGURATION</a></div>
            <hr class="delimiter"/>
            <div id="capgemini"><span class="rotate">LOG PROCESSOR X - LPX</span></div>

            <div id="elevator"></div>
        </div>
        <div id="content">
            <div id="analyse_content">
                <div id="folder_choice" class="step">
                    <img src="img/folder.png" class="title_icon"/><h1>Choix du dossier</h1>
                    <hr/>
                    <div id="folder_selection">
                        <div class="contentTitle">Dossier à analyser</div>
                        <div action="" method="post" id="folder_form">
                            <input list="folders" type="text" name="path" value="/Users/samilachdhaf/Desktop/ClaimCenter/logs" placeholder="Chemin du dossier à analyser" id="folder_input">
                            <datalist id="folders">
                                <option value="REC" label="Recette">
                                <option value="DEV11" label="Développement">
                                <option value="PR" label="Production">
                                <option value="PP" label="Préproduction">
                                <option value="/Users/samilachdhaf/Desktop/ClaimCenter/logs" label="Local MAC">
                                <option value="C:\documents\s819044\Desktop\ClaimCenter\logs" label="Local Windows">
                            </datalist>                    
                            <button type="submit" class="next_step" href="#folder_results" data-container="#folder_choice">OK</button>
                        </div>
                        <!--<div>Le dossier spécifié n'existe pas !</div>
<div>Le dossier spécifié a été trouvé !</div>

<div id="progressBar">
<div id="timer"></div>
<div id="bar"><div id="progress" style="width:0%;"></div><div id="percentage">0%</div><span id="logName">Traitements préliminaires...</span></div>
</div>-->
                    </div>
                </div>
                <div id="folder_results" class="step"></div>
                <div id="file_content"   class="step"></div>
                <div id="log_content"    class="step"></div>
                <h1 class="prev"><<</h1>
            </div>
            <div id="stats_content">
                <img src="img/stats_content.png" class="title_icon"/><h1>Statistiques</h1>
                <hr/>
                <div id="pieChart" class="chart" data-value="67 150 20 10 50 100 70 300" data-label="Darva Editique Webmail Child ROI RDU PFEL Chilc">
                </div>
                <div id="linearChart" data-value="0 1 2 3 4 5 6">
                    <div data-value="1 2 3 4 3 2 1"></div>
                    <div data-value="2 4 6 8 6 4 9"></div>
                    <div data-value="2 4 1 10 6 2 7"></div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/navigation.js"></script>
        <script type="text/javascript" src="js/processor.js"></script>
        <script type="text/javascript" src="js/results.js"></script>
        <script type="text/javascript" src="js/content.js"></script>
        <script type="text/javascript" src="js/analysis.js"></script>
		<script type="text/javascript" src="js/update.js"></script>
        <script type="text/javascript" src="js/pieCharts.js"></script>
        <script type="text/javascript" src="js/linearCharts.js"></script>
        <script type="text/javascript" src="js/please.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    </body>
</html>