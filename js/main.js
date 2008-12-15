//------------------------------------------------------------------------
// Document Ready
//------------------------------------------------------------------------
$(document).ready(function() {
    //edit/add task dialog
    $("#task-dialog").dialog({
	autoOpen: false, 
	resizable: false, 
	modal: true,
        buttons: { 
        	'Close': function(){ 
            		$(this).dialog('close') 
        	},
        	'Save': function() {
        	    var task = {
        	                id: $("#task-id").val(),
        	                description: $("#task-description").val(),
        	                status: $("#task-status").val(),
        	        }
        	        commitTask( task );
        	        buildTaskLists();
        	        $("#task-dialog").dialog( "close" );   
        	}
    	},
    	overlay:{background:'#000000', opacity:0.4}
    });
    
    //create sortable task lists
    buildTaskLists();
    $(".tasklist > tbody").sortable({ 
        update: function() { saveAllTasks() },
        cursor: 'move' });


    
});

//------------------------------------------------------------------------
// Event Handling
//------------------------------------------------------------------------
function showEditTask( oTask ) {
    var b = ( oTask != null );
    $("#task-id").val( b ? oTask.id  : '');
    $("#task-priority").val( b ? oTask.priority: taskCache.cache.length );
    $("#task-description").val( b ? oTask.description : '' );
    $("#task-status").val( b ? oTask.status : 'NEW' );
    $("#task-dialog").dialog( "open" );

    $("#task-dialog > button.save").click( function() {
        
    });
}


function commitTask( oTask ) {
    taskCache.write( oTask );
    $.post( 'rest.php?element=task', oTask );
}


// appends a task to a task list
function appendTask( jqTaskList, oTask ) {
    jqTaskList.append( 
        '<tr id="task-' + oTask.id + '">' +
        '  <td class="description">' + oTask.description + '</td>' +
        '  <td class="status">' + oTask.status + "</td>" +
        '  <td><button type="button" onclick="showEditTask(taskCache.read(' + oTask.id + '));">EDIT</button></td>' +
        '</tr>' 
    );    
}

function saveAllTasks() {
    var tasks = $('.tasklist > tbody').children();
    for( var i = 0; i < tasks.length; i++ ) {
	   var task = {
	            id: tasks.eq( i ).attr( "id" ).substring( 5 ),
	            description: $(".description", tasks.eq( i ) ).text(),
	            priority: i,
	            status: $(".status", tasks.eq( i ) ).text()
	   }
	   commitTask( task );
    }
}

function buildTaskLists() {
    $('.tasklist > tbody').empty();
    taskCache.clear();
    $.getJSON(
       "rest.php?element=task",
	   function( oResponse ) {
	       $.each( oResponse.tasks, function(i, oTask) {
	       	   if ( oTask.status == 'COMPLETE' ) {
                   appendTask( $('#closed-tasks > tbody'), oTask );
	           } else {
	        	   appendTask( $('#open-tasks > tbody'), oTask );
	           }
               taskCache.write( oTask );
	       });
	});
}

//------------------------------------------------------------------------
// Task Cache
//------------------------------------------------------------------------
function TaskCache() {
    this.cache = [];
        
    this.read = function( iTaskId ) {
       if ( this.cache[ iTaskId ] != null ) {
           return this.cache[ iTaskId ];
       } else {
           alert( 'Task #' + iTaskId + ' does not exist.' );
           return false;
       }
   };

   this.write = function( oTask ) {
       this.cache[ oTask.id ] = oTask;
   };

   this.clear = function() {
       this.cache = []
   };
}

var taskCache = new TaskCache();