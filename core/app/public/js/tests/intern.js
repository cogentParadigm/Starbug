// Learn more about configuring this file at <https://github.com/theintern/intern/wiki/Configuring-Intern>.
// These default settings work OK for most people. The options that *must* be changed below are the
// packages, suites, excludeInstrumentation, and (if you want functional tests) functionalSuites.
define({
	// The port on which the instrumenting proxy will listen
	proxyPort: 9000,

	// A fully qualified URL to the Intern proxy
	proxyUrl: 'http://localhost:9000/',

	// Default desired capabilities for all environments. Individual capabilities can be overridden by any of the
	// specified browser environments in the `environments` array below as well. See
	// https://code.google.com/p/selenium/wiki/DesiredCapabilities for standard Selenium capabilities and
	// https://saucelabs.com/docs/additional-config#desired-capabilities for Sauce Labs capabilities.
	// Note that the `build` capability will be filled in with the current commit ID from the Travis CI environment
	// automatically
	capabilities: {
		'selenium-version': '2.45.0'
	},

	// Browsers to run integration testing against. Note that version numbers must be strings if used with Sauce
	// OnDemand. Options that will be permutated are browserName, version, platform, and platformVersion; any other
	// capabilities options specified for an environment will be copied as-is
	environments: [
		{browserName:'firefox'},
		{browserName:'chrome'}
	],

	// Maximum number of simultaneous integration tests that should be executed on the remote WebDriver service
	maxConcurrency: 3,
	useSauceConnect: false,
	tunnel:'SauceLabsTunnel',


	// Configuration options for the module loader; any AMD configuration options supported by the specified AMD loader
	// can be used here
	loader: {
		requestProvider: 'dojo/request/registry',
		// Packages that should be registered with the loader in each testing environment
		packages: [
			{name:"dojo", location:"libraries/dojo/dojo"},
			{name:"dijit", location:"libraries/dojo/dijit"},
			{name:"dojox", location:"libraries/dojo/dojox"},
			{name:"dstore", location:"libraries/dojo/dstore"},
			{name:"dgrid", location:"libraries/dojo/dgrid"},
			{name:"put-selector", location:"libraries/dojo/put-selector"},
			{name:"xstyle", location:"libraries/dojo/xstyle"},
			{name:"dbootstrap", location:"libraries/dojo/dbootstrap"},
			{name:"bootstrap", location:"libraries/dojo/bootstrap"},
   		{name:"sb", location:"core/app/public/js/sb"},
    	{name:"starbug", location:"core/app/public/js/starbug"}
		]
	},

	// Non-functional test suite(s) to run in each browser
	suites: [
		'core/app/public/js/tests/unit/sb/data'
	],

	// Functional test suite(s) to run in each browser once non-functional tests are completed
	functionalSuites: [
		'core/app/public/js/tests/functional/starbug/form/Autocomplete',
		'core/app/public/js/tests/functional/starbug/form/editable'
	],

	// A regular expression matching URLs to files that should not be included in code coverage analysis
	excludeInstrumentation: /^(?:core\/app\/public\/js\/tests|node_modules|libraries)\//
});
