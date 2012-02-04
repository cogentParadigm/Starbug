//Starbug CSS Build Script
var buildTimerStart = (new Date()).getTime();
buildScriptsPath = "../../../core/app/public/js/dojo/util/buildscripts/";
load(buildScriptsPath + "jslib/logger.js");
load(buildScriptsPath + "jslib/fileUtil.js");
load(buildScriptsPath + "jslib/buildUtil.js");
load(buildScriptsPath + "jslib/buildUtilXd.js");
load(buildScriptsPath + "jslib/i18nUtil.js");

//NOTE: See buildUtil.DojoBuildOptions for the list of build options.

//*****************************************************************************
//Convert arguments to keyword arguments.
var kwArgs = buildUtil.makeBuildOptions(arguments);

//Set logging level.
logger.level = kwArgs["log"];

//Execute the requested build actions
var action = kwArgs.action;
for(var i = 0; i < action.length; i ++){
	logger.logPrefix = action[i] + ": ";
	this[action[i]]();
	logger.logPrefix = "";
}

var buildTime = ((new Date().getTime() - buildTimerStart) / 1000);
logger.info("Build time: " + buildTime + " seconds");
//*****************************************************************************

//********* Start help ************
function help(){
	var buildOptionText = "";
	for(var param in buildUtil.DojoBuildOptions){
		buildOptionText += param + "=" + buildUtil.DojoBuildOptions[param].defaultValue + "\n"
			+ buildUtil.DojoBuildOptions[param].helpText + "\n\n";
	}

	var helpText = "To run the build, you must have Java 1.4.2 or later installed.\n"
		+ "To run a build run the following command from this directory:\n\n"
		+ "> java -classpath ../shrinksafe/js.jar:../shrinksafe/shrinksafe.jar "
		+ "org.mozilla.javascript.tools.shell.Main build.js [name=value...]\n\n"
		+ "Here is an example of a typical release build:\n\n"
		+ "> java -classpath ../shrinksafe/js.jar:../shrinksafe/shrinksafe.jar " 
		+ "org.mozilla.javascript.tools.shell.Main  build.js profile=base action=release\n\n"
		+ "If you get a 'java.lang.OutOfMemoryError: Java heap space' error, try increasing the "
		+ "memory Java can use for the command:\n\n"
		+ "> java -Xms256m -Xmx256m -classpath ../shrinksafe/js.jar:../shrinksafe/shrinksafe.jar "
		+ "org.mozilla.javascript.tools.shell.Main build.js profile=base action=release\n\n"
		+ "Change the 256 number to the number of megabytes you want to give Java.\n\n"
		+ "The possible name=value build options are shown below with the defaults as their values:\n\n"
		+ buildOptionText;
	
	print(helpText);
}
//********* End help *********

//********* Start release *********
function release(){
	logger.info("Optimizing CSS files");
	logger.info("Using version number: " + kwArgs.version + " for the release.");
	var releasePath = "../../../var/public/stylesheets";
	buildUtil.optimizeCss(releasePath, kwArgs.cssOptimize, kwArgs.cssImportIgnore);
}
//********* End release *********

