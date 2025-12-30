const path = require('path');

module.exports = (config) => {
  config.output = {
    path: path.resolve(config.context, 'libraries/dist'),
    publicPath: "libraries/dist/",
    filename: '[name].js'
  };
};