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

var runesOptions = {
   maximumSelectionLength: 5
}

$('.runes').select2(runesOptions);

$('#monster').select2();

function updateGear(gearType, clearStats = false, refreshRunes = false)
{
   $('#'+gearType+'-stats').html();

   var gearName = $('#'+gearType).val();

   $.ajax({url:'/simulation/variable/gearstats.php?clear='+clearStats+'&type='+gearType+'&name='+gearName, async:true}).done(function(data) { $('#'+gearType+'-stats').html(data); $('.stats').select2(statsOptions); });

   if (refreshRunes) { updateRunes(); }
}

function updateRunes()
{
   $('#rune-section').html();

   var equipState = {}
   const gearTypes = ['weapon','shield','ring','amulet','weapon-skin','shield-skin'];

   gearTypes.forEach((gearType) => {
      equipState[gearType] = $('#'+gearType).val();;
   });

   $.ajax({url:'/simulation/variable/runes.php?state='+JSON.stringify(equipState), async:true}).done(function(data) { $('#rune-section').html(data); $('.runes').select2(runesOptions); });
}

function loadResults() 
{
   $('#results').html();

   $.ajax({url:'/simulation/variable/start.php', async:true}).done(function(data) { $('#results').html(data); });
}
