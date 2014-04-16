<?php

ini_set('display_errors', '1');
error_reporting(E_ALL | E_STRICT);

class Project
{
	public $images;
	public $name;

	function __construct($dir_name, $files){
		$this->name = $dir_name;
		$this->images = $files;
	}
}

// Get original directory contents
$first_scan = scandir('.');
$test_var = scandir('signup');

// Get the directory names inside that
$directories = array();
$projects = [];

foreach ($first_scan as $result) {
    if ($result === '.' or $result === '..') continue;

    if (is_dir($result)) {
       array_push($directories, $result);
    }
}

function find_img($directory){
		$dir_scan = scandir($directory);
		$result = array();
		foreach ($dir_scan as $val) {
			if(strpos($val, '.jpg') !== false ){
				array_push($result, $val);
				// var_dump($val);
			}
		};
		return $result;
};

foreach ($directories as $directory) {
	$dir_files = find_img($directory);
    $project = new Project($directory, $dir_files);
    array_push($projects, $project);
};

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Designer Homepage</title>
  <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style.css">
</head>


<body>
	<nav>
		<div class="container"><div class="row">
			<a href="/#"><div class="logo"></div></a>
		</div></div>
	</nav>
	
	<script type="text/template" id="thumbnail-template">
		<div class="container">
		<div class="heading row">
			<h2>Available Projects</h2>
		</div>
		<div class="row">
		<% _.each(projects, function(project){ %>
			
				<div class="col-lg-4">
					<div class="project">
						<% console.log(project) %>
						<a href="#/project/<%= project.id %>"><img src="<%= project.id %>/<%= project.get('images')[0]%>"></a>
					</div>
					<h4><%= project.id %></h4>
				</div>
		<% }) %>
		</div>
		</div>
	</script>

	<script type="text/template" id="view-template">
		<div class="container">
		<div class="heading row">
			<h2><%= project.id %></h2><a class="a" href="/#">Back to projects</a>
		</div>
		<div id="what" class="row showcase">

			<img src="<%= project.id %>/<%= project.get('images')[0] %>"></a>

		</div>
		</div>

		<% var counter=0; %>
		<% $('#page').on('click', function(){
			console.log(counter);
			counter += 1;
			if(counter > project.get('images').length - 1){
				counter = 0;
			}
			$('#what').html('<img src="'+ project.id +'/' + project.get('images')[counter] + '">');
		}); %>
	</script>

	<div id="page"></div>
	<!-- ========= -->
	<!-- Libraries -->
	<!-- ========= -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.3.3/underscore-min.js" type="text/javascript"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.1.2/backbone-min.js" type="text/javascript"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/backbone-localstorage.js/1.1.7/backbone.localStorage-min.js" type="text/javascript"></script>
<script>
	"use strict";
	window.projects = <?php echo json_encode($projects) ?>;
	
	var Project = Backbone.Model.extend({
		name: null,
		images: [],
		id: null
	});

	var Projects = Backbone.Collection.extend({
		model: Project
	});

	var ProjectView = Backbone.View.extend({
		tagName: 'div',

		initialize: function(){

		},

		render: function(){

		}
	});

	window.AppView = Backbone.View.extend({
		el: $('#page'),

		initialize: function(){
			var that = this;
			this.project_collection = new Projects();
			window.project_collection = this.project_collection;

			projects.forEach(function(project){
				var project_model = new Project({
					name: project.name,
					images: project.images,
					id: project.name
				});
				that.project_collection.add(project_model);
			});

		},

		render: function(){
			var $obj = this.$el;
			var template = _.template($('#thumbnail-template').html(), {projects: this.project_collection.models});
			this.$el.html(template);
		}
	});

	window.ProjectThumb = Backbone.View.extend({
	el: $('#page'),
		initialize: function(){
			// var that = this;	
		},
		
		render: function(model){
			var that = this;
			var template = _.template($('#view-template').html(), {project: model});
			that.$el.html(template); 

			
			
		}
	});

	var app_view = new AppView();
	var projectThumb = new ProjectThumb();

	var Router = Backbone.Router.extend({
		routes: {
			'': 'home',
			'project/:name':'projects'
		}
	});
	var router = new Router();

	router.on("route:home", function(){
		app_view.render();
	});

	router.on("route:projects", function(name){
		var model = project_collection.get(name);
		projectThumb.render(model);
	});

	Backbone.history.start();
</script>
</body>