const path = require('path');
const DojoWebpackPlugin = require('dojo-webpack-plugin');
const webpack = require('webpack');

module.exports = (config) => {
  config.plugins.push(
    new DojoWebpackPlugin({
      async: true,
      loaderConfig: require(path.join(config.context, "./var/etc/dojo.profile.js")),
      locales: ["en"],
      loader: path.join(config.context, "./libraries/dist/dojo.js"),
      environment: {
        dojoRoot: "libraries/dist",
        baseUrl: "/"
      },
      buildEnvironment: {
        dojoRoot: "libraries/dojo",
        baseUrl: config.context
      }
    }),
    new webpack.NormalModuleReplacementPlugin(
      /^dojo\/domReady!/, (data) => {
        const match = /^dojo\/domReady!(.*)$/.exec(data.request);
        data.request = "dojo/loaderProxy?loader=dojo/domReady!" + match[1];
      }
    )
  );
};
