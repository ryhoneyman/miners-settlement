var gearOptions = {
    templateSelection: select2_template,
    templateResult: select2_template,
}

$('.gear').select2(gearOptions);

var statsOptions = {
    templateSelection: select2_template,
    templateResult: select2_template,
    minimumResultsForSearch: -1,
}

$('.stats').select2(statsOptions);

$('#monster').select2();
$('#iterations').select2({minimumResultsForSearch: -1});

function updateScalableGear(gearType, clearStats = false, refreshRunes = false)
{
   $('#'+gearType+'-stats').html();

   var gearHash = $('#'+gearType).val();

   $.ajax({url:'/simulation/scalable/gearstats.php?clear='+clearStats+'&type='+gearType+'&hash='+gearHash, async:true}).done(function(data) { $('#'+gearType+'-stats').html(data); $('.stats').select2(statsOptions); });

   if (refreshRunes) { updateRunes('scalablesim'); }
}

function loadScalableSimulationResults() 
{
   $('#results').html();

   $.ajax({url:'/simulation/scalable/start.php', async:true}).done(function(data) { $('#results').html(data); });
}

function loadBuildSimulationResults()
{
   $('#results').html();

   $.ajax({url:'/simulation/build/start.php', async:true}).done(function(data) { $('#results').html(data); });
}

